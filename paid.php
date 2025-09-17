<?php 
require_once __DIR__ . '/session_helper.php';
safe_session_start();
require_once __DIR__ . '/config.php'; 
$site = $config['site_name'] ?? 'NextOra'; 

// Redirect to login if not authenticated
if (!is_logged_in()) {
    header('Location: login.php?redirect=paid.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's paid/enrolled classes
$my_classes_sql = "
    SELECT 
        c.*,
        e.status as enrollment_status,
        e.enrollment_date,
        p.status as payment_status,
        p.created_at as payment_date,
        p.reference_number,
        p.payment_date as approved_date,
        COUNT(DISTINCT cr.recording_id) as recording_count
    FROM classes c
    LEFT JOIN enrollments e ON c.id = e.class_id AND e.user_id = :user_id
    LEFT JOIN payments p ON c.id = p.class_id AND p.user_id = :user_id
    LEFT JOIN class_recordings cr ON c.id = cr.class_id
    WHERE (e.user_id = :user_id OR p.user_id = :user_id)
    AND (e.status = 'Active' OR p.status = 'Paid')
    GROUP BY c.id, c.title, c.description, c.price, c.status, c.thumbnail_url, c.difficulty, 
             e.status, e.enrollment_date, p.status, p.created_at, p.reference_number, p.payment_date
    ORDER BY COALESCE(e.enrollment_date, p.created_at) DESC
";

try {
    $my_classes_stmt = $pdo->prepare($my_classes_sql);
    $my_classes_stmt->execute([':user_id' => $user_id]);
    $my_classes = $my_classes_stmt->fetchAll();
} catch (PDOException $e) {
    $my_classes = [];
}
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
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .header-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1.5rem 0;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        
        .back-btn:hover {
            color: #374151;
        }
        
        .page-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: 1rem;
        }
        
        .page-title .icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .page-title h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        
        .refresh-btn {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f3f4f6;
            color: #6b7280;
            text-decoration: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .refresh-btn:hover {
            background: #e5e7eb;
            color: #374151;
        }
        
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .class-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .class-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .class-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .class-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .class-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(255, 255, 255, 0.95);
            color: #374151;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .status-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .status-active {
            background: rgba(16, 185, 129, 0.9);
            color: white;
        }
        
        .class-content {
            padding: 1.5rem;
        }
        
        .class-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin: 0 0 0.5rem 0;
            line-height: 1.4;
        }
        
        .class-description {
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .class-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .class-stats {
            display: flex;
            gap: 1rem;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #6b7280;
            font-size: 0.75rem;
        }
        
        .stat-item i {
            font-size: 0.75rem;
        }
        
        .class-level {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .level-beginner {
            background: #dcfce7;
            color: #166534;
        }
        
        .level-intermediate {
            background: #fef3c7;
            color: #92400e;
        }
        
        .level-advanced {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .class-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .class-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }
        
        .view-class-btn {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .view-class-btn:hover {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
        }
        
        .empty-state .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: #9ca3af;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #6b7280;
            margin: 0 0 0.5rem 0;
        }
        
        .empty-state p {
            color: #9ca3af;
            margin-bottom: 2rem;
        }
        
        .browse-btn {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .browse-btn:hover {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            transform: translateY(-1px);
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .page-title {
                margin-left: 0;
            }
            
            .refresh-btn {
                margin-left: 0;
                align-self: flex-end;
            }
            
            .classes-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .class-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            
            .class-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .view-class-btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body data-nav-theme="dark">
    <div id="liquidBg" class="liquid-bg" aria-hidden="true"></div>
    
    <?php $activeSlug = 'my-classes'; include __DIR__ . '/components/dashboard-navbar.php'; ?>
    <style>
      #tw-nav-body a, #tw-nav-body .fas, #tw-nav-body .nav-text, #tw-nav-body span { color: #9ca3af !important; } 
      #tw-nav-body a:hover { color: #d1d5db !important; } 
      #tw-nav-body a.tw-active, #tw-nav-body.tw-light a.tw-active { color: #8b5cf6 !important; } 
      #tw-nav-body.tw-light a, #tw-nav-body.tw-light .fas, #tw-nav-body.tw-light .nav-text, #tw-nav-body.tw-light span { color: #4b5563 !important; }
    </style>

    <div class="main-container">
        <div class="header-section">
            <a href="my-classes.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to My Classes
            </a>
            
            <div class="page-title">
                <div class="icon">
                    🎓
                </div>
                <h1>My Classes</h1>
            </div>
            
            <a href="?refresh=1" class="refresh-btn">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </a>
        </div>

        <?php if (!empty($my_classes)): ?>
            <div class="classes-grid">
                <?php foreach ($my_classes as $class): 
                    $thumbnail_path = 'assets/images/placeholder.jpg'; 
                    if (!empty($class['thumbnail_url'])) {
                        $thumbnail_path = substr($class['thumbnail_url'], 3);
                    }
                ?>
                    <div class="class-card">
                        <div class="class-image">
                            <img src="<?= htmlspecialchars($thumbnail_path) ?>" alt="<?= htmlspecialchars($class['title']); ?>">
                            <div class="class-badge"><?= $class['exam_year'] ?> A/L</div>
                            <div class="status-badge status-active">
                                <i class="fas fa-check-circle"></i>
                                Active
                            </div>
                        </div>
                        
                        <div class="class-content">
                            <h3 class="class-title"><?php echo htmlspecialchars($class['title']); ?></h3>
                            <p class="class-description"><?php echo htmlspecialchars($class['description'] ?: 'Master the concepts and excel in your examinations with comprehensive study materials and expert guidance.'); ?></p>
                            
                            <div class="class-meta">
                                <div class="class-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-video"></i>
                                        <?php echo $class['recording_count']; ?> videos available
                                    </div>
                                </div>
                                <div class="class-level level-<?php echo $class['difficulty']; ?>">
                                    <?php echo ucfirst($class['difficulty']); ?>
                                </div>
                            </div>
                            
                            <div class="class-footer">
                                <div class="class-price">Rs. <?php echo number_format($class['price'], 0); ?></div>
                                <a href="Rec.php?id=<?= $class['id'] ?>" class="view-class-btn">
                                    <i class="fas fa-play-circle"></i>
                                    View Class
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>No Classes Yet</h3>
                <p>You haven't enrolled in any classes yet. Browse our available courses and start your learning journey!</p>
                <a href="classes.php" class="browse-btn">
                    <i class="fas fa-search"></i>
                    Browse Classes
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'components/footer.php'; ?>

    <script src="script.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/navbar-theme.js"></script>
</body>
</html>