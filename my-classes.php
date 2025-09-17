<?php 
require_once __DIR__ . '/session_helper.php';
safe_session_start();
require_once __DIR__ . '/config.php'; 
$site = $config['site_name'] ?? 'NextOra'; 

// Redirect to login if not authenticated
if (!is_logged_in()) {
    header('Location: login.php?redirect=my-classes.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's enrolled classes with payment status and recording count
$my_classes_sql = "
    SELECT 
        c.*,
        e.status as enrollment_status,
        e.enrollment_date,
        p.status as payment_status,
        p.created_at as payment_date,
        p.reference_number,
        p.payment_date as approved_date,
        COUNT(DISTINCT cr.recording_id) as recording_count,
        u.name as instructor_name
    FROM classes c
    LEFT JOIN enrollments e ON c.id = e.class_id AND e.user_id = :user_id
    LEFT JOIN payments p ON c.id = p.class_id AND p.user_id = :user_id
    LEFT JOIN class_recordings cr ON c.id = cr.class_id
    LEFT JOIN users u ON c.instructor_id = u.id
    WHERE (e.user_id = :user_id OR p.user_id = :user_id)
    AND (e.user_id IS NOT NULL OR p.user_id IS NOT NULL)
    GROUP BY c.id, c.title, c.description, c.price, c.status, c.thumbnail_url, c.difficulty, 
             e.status, e.enrollment_date, p.status, p.created_at, p.reference_number, p.payment_date, u.name
    ORDER BY 
        CASE 
            WHEN (e.status = 'Active' OR p.status = 'Paid') THEN 1
            WHEN (e.status = 'Pending' OR p.status = 'Pending') THEN 2
            WHEN p.status = 'Rejected' THEN 3
            ELSE 4
        END,
        COALESCE(e.enrollment_date, p.created_at) DESC
";

try {
    $my_classes_stmt = $pdo->prepare($my_classes_sql);
    $my_classes_stmt->execute([':user_id' => $user_id]);
    $my_classes = $my_classes_stmt->fetchAll();
} catch (PDOException $e) {
    $my_classes = [];
}

// Separate classes by status for better organization
$approved_classes = [];
$pending_classes = [];
$rejected_classes = [];

foreach ($my_classes as $class) {
    $enrollment_status = isset($class['enrollment_status']) ? $class['enrollment_status'] : null;
    $payment_status = $class['payment_status'];
    
    if ($enrollment_status === 'Active' || $payment_status === 'Paid') {
        $approved_classes[] = $class;
    } elseif ($payment_status === 'Pending' || $enrollment_status === 'Pending') {
        $pending_classes[] = $class;
    } elseif ($payment_status === 'Rejected') {
        $rejected_classes[] = $class;
    }
}

// Handle success/error messages
$success_message = $_SESSION['payment_success'] ?? null;
$error_message = $_SESSION['payment_error'] ?? null;
unset($_SESSION['payment_success'], $_SESSION['payment_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes — <?php echo htmlspecialchars($site); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        window.tailwind = window.tailwind || {};
        tailwind.config = { corePlugins: { preflight: false } };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .my-classes-container { 
            padding: 2rem 0; 
            min-height: 100vh; 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
        }
        .my-classes-header { 
            text-align: center; 
            margin-bottom: 3rem; 
        }
        .my-classes-header h1 { 
            font-size: 3rem; 
            font-weight: 800; 
            background: linear-gradient(135deg, #7c3aed, #ec4899); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            background-clip: text; 
            margin-bottom: 1rem; 
        }
        .my-classes-header p { 
            font-size: 1.125rem; 
            color: #6b7280; 
            max-width: 42rem; 
            margin: 0 auto; 
        }
        
        /* Status Summary Cards */
        .status-summary { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 1rem; 
            margin-bottom: 2rem; 
        }
        .summary-card { 
            background: white; 
            padding: 1.5rem; 
            border-radius: 0.75rem; 
            border: 1px solid #e5e7eb; 
            text-align: center; 
        }
        .summary-card .number { 
            font-size: 2rem; 
            font-weight: 700; 
            margin-bottom: 0.5rem; 
        }
        .summary-card .label { 
            color: #6b7280; 
            font-size: 0.875rem; 
        }
        .summary-approved .number { 
            color: #10b981; 
        }
        .summary-pending .number { 
            color: #f59e0b; 
        }
        .summary-rejected .number { 
            color: #ef4444; 
        }
        
        /* Section Headers */
        .section-header { display: flex; align-items: center; gap: 0.75rem; margin: 2rem 0 1rem 0; }
        .section-header h2 { font-size: 1.5rem; font-weight: 600; color: #111827; margin: 0; }
        .section-header .count { background: #f3f4f6; color: #6b7280; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; }
        
        /* Enhanced Course Cards */
        .courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .course-card { background: white; border-radius: 1rem; border: 1px solid #e5e7eb; overflow: hidden; position: relative; transition: all 0.3s ease; }
        .course-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(2,6,23,0.12); }
        .course-card.approved { border-left: 4px solid #10b981; }
        .course-card.pending { border-left: 4px solid #f59e0b; }
        .course-card.rejected { border-left: 4px solid #ef4444; }
        
        .course-image { position: relative; height: 200px; overflow: hidden; }
        .course-image img { width: 100%; height: 100%; object-fit: cover; }
        .status-overlay { position: absolute; top: 12px; right: 12px; padding: 0.5rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; backdrop-filter: blur(10px); }
        .status-overlay.approved { background: rgba(16, 185, 129, 0.9); color: white; }
        .status-overlay.pending { background: rgba(245, 158, 11, 0.9); color: white; }
        .status-overlay.rejected { background: rgba(239, 68, 68, 0.9); color: white; }
        
        .course-content { padding: 1.5rem; }
        .course-title { font-size: 1.25rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem; line-height: 1.4; }
        .course-description { color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        .course-details { margin-bottom: 1rem; }
        .detail-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; font-size: 0.875rem; }
        .detail-label { color: #6b7280; }
        .detail-value { color: #111827; font-weight: 500; }
        
        .course-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-top: 1rem; border-top: 1px solid #f3f4f6; }
        .course-stats { display: flex; gap: 1rem; }
        .stat-item { display: flex; align-items: center; gap: 0.25rem; color: #6b7280; font-size: 0.75rem; }
        .course-level { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .level-beginner { background: #dcfce7; color: #166534; }
        .level-intermediate { background: #fef3c7; color: #92400e; }
        .level-advanced { background: #fee2e2; color: #991b1b; }
        
        .course-footer { display: flex; justify-content: space-between; align-items: center; }
        .price { font-size: 1.25rem; font-weight: 700; color: #111827; }
        
        /* Enhanced Buttons */
        .btn-view { background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 25px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; text-align: center; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-view:hover { background: linear-gradient(135deg, #059669, #047857); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4); }
        .btn-pending { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 25px; font-size: 0.875rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-retry { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 25px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; text-align: center; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-retry:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4); }
        .btn-payment { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 25px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; text-align: center; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-payment:hover { background: linear-gradient(135deg, #6d28d9, #5b21b6); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4); }
        
        .empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 1rem; border: 1px solid #e5e7eb; }
        .empty-state i { font-size: 4rem; color: #9ca3af; margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.5rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem; }
        .empty-state p { color: #9ca3af; margin-bottom: 2rem; }
        .empty-state .btn { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; padding: 0.75rem 1.5rem; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; }
        .empty-state .btn:hover { background: linear-gradient(135deg, #6d28d9, #5b21b6); transform: translateY(-1px); }
        
        /* Real-time status indicator */
        .status-indicator { position: absolute; top: 12px; left: 12px; width: 12px; height: 12px; border-radius: 50%; }
        .status-indicator.approved { background: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3); }
        .status-indicator.pending { background: #f59e0b; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3); animation: pulse 2s infinite; }
        .status-indicator.rejected { background: #ef4444; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3); }
        
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        
        @media (max-width: 768px) { 
            .my-classes-header h1 { font-size: 2rem; } 
            .courses-grid { grid-template-columns: 1fr; } 
            .status-summary { grid-template-columns: 1fr; }
            .course-footer { flex-direction: column; gap: 1rem; align-items: stretch; }
            .btn-view, .btn-pending, .btn-retry, .btn-payment { justify-content: center; }
        }
    </style>
</head>
<body data-nav-theme="dark">
    <div id="liquidBg" class="liquid-bg" aria-hidden="true"></div>
    
    <?php $activeSlug = 'my-classes'; include __DIR__ . '/components/dashboard-navbar.php'; ?>
    <style>
      #tw-nav-body a, #tw-nav-body .fas, #tw-nav-body .nav-text, #tw-nav-body span { color: #9ca3af !important; } #tw-nav-body a:hover { color: #d1d5db !important; } #tw-nav-body a.tw-active, #tw-nav-body.tw-light a.tw-active { color: #8b5cf6 !important; } #tw-nav-body.tw-light a, #tw-nav-body.tw-light .fas, #tw-nav-body.tw-light .nav-text, #tw-nav-body.tw-light span { color: #4b5563 !important; }
    </style>

    <div class="my-classes-container">
        <div class="container">
            <div class="my-classes-header">
                <h1>My Classes</h1>
                <p>Track your enrolled courses and payment status</p>
            </div>

            <?php if ($error_message): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 text-red-700">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 text-green-700">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($success_message); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($my_classes)): ?>
            <!-- Status Summary -->
            <div class="status-summary">
                <div class="summary-card summary-approved">
                    <div class="number"><?php echo count($approved_classes); ?></div>
                    <div class="label">Approved Classes</div>
                </div>
                <div class="summary-card summary-pending">
                    <div class="number"><?php echo count($pending_classes); ?></div>
                    <div class="label">Pending Approval</div>
                </div>
                <div class="summary-card summary-rejected">
                    <div class="number"><?php echo count($rejected_classes); ?></div>
                    <div class="label">Rejected Payments</div>
                </div>
            </div>

            <!-- Approved Classes Section -->
            <?php if (!empty($approved_classes)): ?>
            <div class="section-header">
                <h2><i class="fas fa-check-circle" style="color: #10b981;"></i> Approved Classes</h2>
                <span class="count"><?php echo count($approved_classes); ?></span>
            </div>
            <div class="courses-grid">
                <?php foreach ($approved_classes as $class): ?>
                <div class="course-card approved">
                    <div class="course-image">
                        <div class="status-indicator approved"></div>
                        <div class="status-overlay approved">
                            <i class="fas fa-check-circle"></i> Approved
                        </div>
                        <?php
                        $thumbnail_path = 'assets/images/placeholder.jpg'; 
                        if (!empty($class['thumbnail_url'])) {
                            $thumbnail_path = substr($class['thumbnail_url'], 3);
                        }
                        ?>
                        <img src="<?= htmlspecialchars($thumbnail_path) ?>" alt="<?= htmlspecialchars($class['title']); ?>">
                    </div>
                    <div class="course-content">
                        <h3 class="course-title"><?php echo htmlspecialchars($class['title']); ?></h3>
                        <p class="course-description"><?php echo htmlspecialchars($class['description'] ?: 'Master the concepts and excel in your examinations with comprehensive study materials and expert guidance.'); ?></p>
                        
                        <div class="course-details">
                            <?php if ($class['instructor_name']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Instructor:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($class['instructor_name']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="detail-row">
                                <span class="detail-label">Exam Year:</span>
                                <span class="detail-value"><?php echo $class['exam_year'] ?: 'N/A'; ?> A/L</span>
                            </div>
                            <?php if ($class['approved_date']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Approved:</span>
                                <span class="detail-value"><?php echo date('M j, Y', strtotime($class['approved_date'])); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="course-meta">
                            <div class="course-stats">
                                <div class="stat-item">
                                    <i class="fas fa-video"></i>
                                    <?php echo $class['recording_count']; ?> videos
                                </div>
                            </div>
                            <div class="course-level level-<?php echo $class['difficulty']; ?>">
                                <?php echo ucfirst($class['difficulty']); ?>
                            </div>
                        </div>
                        
                        <div class="course-footer">
                            <div class="price">Rs. <?php echo number_format($class['price'], 0); ?></div>
                            <a href="Rec.php?id=<?= $class['id'] ?>" class="btn-view">
                                <i class="fas fa-play-circle"></i>
                                View Class
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Pending Classes Section -->
            <?php if (!empty($pending_classes)): ?>
            <div class="section-header">
                <h2><i class="fas fa-clock" style="color: #f59e0b;"></i> Pending Approval</h2>
                <span class="count"><?php echo count($pending_classes); ?></span>
            </div>
            <div class="courses-grid">
                <?php foreach ($pending_classes as $class): ?>
                <div class="course-card pending">
                    <div class="course-image">
                        <div class="status-indicator pending"></div>
                        <div class="status-overlay pending">
                            <i class="fas fa-clock"></i> Pending
                        </div>
                        <?php
                        $thumbnail_path = 'assets/images/placeholder.jpg'; 
                        if (!empty($class['thumbnail_url'])) {
                            $thumbnail_path = substr($class['thumbnail_url'], 3);
                        }
                        ?>
                        <img src="<?= htmlspecialchars($thumbnail_path) ?>" alt="<?= htmlspecialchars($class['title']); ?>">
                    </div>
                    <div class="course-content">
                        <h3 class="course-title"><?php echo htmlspecialchars($class['title']); ?></h3>
                        <p class="course-description"><?php echo htmlspecialchars($class['description'] ?: 'Your payment is being reviewed by our admin team. You will receive access once approved.'); ?></p>
                        
                        <div class="course-details">
                            <?php if ($class['instructor_name']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Instructor:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($class['instructor_name']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="detail-row">
                                <span class="detail-label">Exam Year:</span>
                                <span class="detail-value"><?php echo $class['exam_year'] ?: 'N/A'; ?> A/L</span>
                            </div>
                            <?php if ($class['reference_number']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Reference:</span>
                                <span class="detail-value font-mono"><?php echo htmlspecialchars($class['reference_number']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="detail-row">
                                <span class="detail-label">Submitted:</span>
                                <span class="detail-value"><?php echo date('M j, Y', strtotime($class['payment_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="course-meta">
                            <div class="course-stats">
                                <div class="stat-item">
                                    <i class="fas fa-video"></i>
                                    <?php echo $class['recording_count']; ?> videos
                                </div>
                            </div>
                            <div class="course-level level-<?php echo $class['difficulty']; ?>">
                                <?php echo ucfirst($class['difficulty']); ?>
                            </div>
                        </div>
                        
                        <div class="course-footer">
                            <div class="price">Rs. <?php echo number_format($class['price'], 0); ?></div>
                            <button class="btn-pending" disabled>
                                <i class="fas fa-clock"></i>
                                Pending Approval
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Rejected Classes Section -->
            <?php if (!empty($rejected_classes)): ?>
            <div class="section-header">
                <h2><i class="fas fa-times-circle" style="color: #ef4444;"></i> Rejected Payments</h2>
                <span class="count"><?php echo count($rejected_classes); ?></span>
            </div>
            <div class="courses-grid">
                <?php foreach ($rejected_classes as $class): ?>
                <div class="course-card rejected">
                    <div class="course-image">
                        <div class="status-indicator rejected"></div>
                        <div class="status-overlay rejected">
                            <i class="fas fa-times-circle"></i> Rejected
                        </div>
                        <?php
                        $thumbnail_path = 'assets/images/placeholder.jpg'; 
                        if (!empty($class['thumbnail_url'])) {
                            $thumbnail_path = substr($class['thumbnail_url'], 3);
                        }
                        ?>
                        <img src="<?= htmlspecialchars($thumbnail_path) ?>" alt="<?= htmlspecialchars($class['title']); ?>">
                    </div>
                    <div class="course-content">
                        <h3 class="course-title"><?php echo htmlspecialchars($class['title']); ?></h3>
                        <p class="course-description"><?php echo htmlspecialchars($class['description'] ?: 'Your payment was rejected. Please check your payment details and try again with a valid payment slip.'); ?></p>
                        
                        <div class="course-details">
                            <?php if ($class['instructor_name']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Instructor:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($class['instructor_name']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="detail-row">
                                <span class="detail-label">Exam Year:</span>
                                <span class="detail-value"><?php echo $class['exam_year'] ?: 'N/A'; ?> A/L</span>
                            </div>
                            <?php if ($class['reference_number']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Reference:</span>
                                <span class="detail-value font-mono"><?php echo htmlspecialchars($class['reference_number']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="course-meta">
                            <div class="course-stats">
                                <div class="stat-item">
                                    <i class="fas fa-video"></i>
                                    <?php echo $class['recording_count']; ?> videos
                                </div>
                            </div>
                            <div class="course-level level-<?php echo $class['difficulty']; ?>">
                                <?php echo ucfirst($class['difficulty']); ?>
                            </div>
                        </div>
                        
                        <div class="course-footer">
                            <div class="price">Rs. <?php echo number_format($class['price'], 0); ?></div>
                            <a href="pM.php?class_id=<?= $class['id'] ?>" class="btn-retry">
                                <i class="fas fa-redo"></i>
                                Retry Payment
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-graduation-cap"></i>
                <h3>No Classes Yet</h3>
                <p>You haven't enrolled in any classes yet. Browse our available courses and start your learning journey!</p>
                <a href="classes.php" class="btn">
                    <i class="fas fa-search mr-2"></i>Browse Classes
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script src="script.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/navbar-theme.js"></script>
    
    <!-- Real-time Status Updates -->
    <script>
        // Check for status updates every 30 seconds
        function checkStatusUpdates() {
            fetch('check_payment_status.php')
                .then(response => response.json())
                .then(data => {
                    if (data.updated) {
                        // Show notification
                        showStatusNotification(data.message, data.type);
                        
                        // Reload page after 2 seconds to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.log('Status check failed:', error);
                });
        }
        
        function showStatusNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
            
            if (type === 'approved') {
                notification.className += ' bg-green-50 border border-green-200 text-green-800';
                notification.innerHTML = `
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span>${message}</span>
                    </div>
                `;
            } else if (type === 'rejected') {
                notification.className += ' bg-red-50 border border-red-200 text-red-800';
                notification.innerHTML = `
                    <div class="flex items-center gap-2">
                        <i class="fas fa-times-circle text-red-600"></i>
                        <span>${message}</span>
                    </div>
                `;
            }
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        }
        
        // Start checking for updates when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check immediately
            checkStatusUpdates();
            
            // Then check every 30 seconds
            setInterval(checkStatusUpdates, 30000);
        });
        
        // Add smooth scrolling to sections
        document.querySelectorAll('.section-header').forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                const nextGrid = this.nextElementSibling;
                if (nextGrid && nextGrid.classList.contains('courses-grid')) {
                    nextGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
        
        // Add loading states to buttons
        document.querySelectorAll('.btn-view, .btn-retry').forEach(button => {
            button.addEventListener('click', function(e) {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                this.style.pointerEvents = 'none';
                
                // Reset after 3 seconds if page doesn't navigate
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                }, 3000);
            });
        });
    </script>
</body>
</html>