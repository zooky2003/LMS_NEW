<?php
/**
 * All Classes Widget - Displays all administrator-added classes
 * This widget can be included in dashboard or other pages
 */

require_once __DIR__ . '/config.php';

// Get all active classes with enrollment and payment statistics
$all_classes_sql = "
    SELECT 
        c.*,
        COUNT(DISTINCT e.id) as enrollment_count,
        COUNT(DISTINCT p.id) as payment_count,
        COUNT(DISTINCT cr.recording_id) as recording_count,
        MAX(e.enrollment_date) as last_enrollment,
        MAX(p.created_at) as last_payment
    FROM classes c
    LEFT JOIN enrollments e ON c.id = e.class_id AND e.status = 'Active'
    LEFT JOIN payments p ON c.id = p.class_id AND p.status = 'Paid'
    LEFT JOIN class_recordings cr ON c.id = cr.class_id
    WHERE c.status = 'Active'
    GROUP BY c.id
    ORDER BY c.created_at DESC
";

try {
    $all_classes_stmt = $pdo->prepare($all_classes_sql);
    $all_classes_stmt->execute();
    $all_classes = $all_classes_stmt->fetchAll();
} catch (PDOException $e) {
    $all_classes = [];
}

// Get user's enrollment status if logged in
$user_enrollments = [];
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    try {
        $user_enrollments_sql = "
            SELECT 
                class_id,
                'enrolled' as type,
                status,
                enrollment_date as date
            FROM enrollments 
            WHERE user_id = :user_id
            UNION ALL
            SELECT 
                class_id,
                'payment' as type,
                status,
                created_at as date
            FROM payments 
            WHERE user_id = :user_id
        ";
        $user_enrollments_stmt = $pdo->prepare($user_enrollments_sql);
        $user_enrollments_stmt->execute([':user_id' => $user_id]);
        $user_data = $user_enrollments_stmt->fetchAll();
        
        foreach ($user_data as $data) {
            $user_enrollments[$data['class_id']][] = $data;
        }
    } catch (PDOException $e) {
        $user_enrollments = [];
    }
}

function getUserClassStatus($class_id, $user_enrollments) {
    if (!isset($user_enrollments[$class_id])) {
        return ['status' => 'available', 'text' => 'Available', 'color' => 'blue'];
    }
    
    $data = $user_enrollments[$class_id];
    
    // Check for active enrollment
    foreach ($data as $item) {
        if ($item['type'] === 'enrolled' && $item['status'] === 'Active') {
            return ['status' => 'enrolled', 'text' => 'Enrolled', 'color' => 'green'];
        }
        if ($item['type'] === 'payment' && $item['status'] === 'Paid') {
            return ['status' => 'paid', 'text' => 'Paid', 'color' => 'green'];
        }
    }
    
    // Check for pending status
    foreach ($data as $item) {
        if ($item['status'] === 'Pending') {
            return ['status' => 'pending', 'text' => 'Pending', 'color' => 'yellow'];
        }
    }
    
    // Check for rejected status
    foreach ($data as $item) {
        if ($item['status'] === 'Rejected') {
            return ['status' => 'rejected', 'text' => 'Rejected', 'color' => 'red'];
        }
    }
    
    return ['status' => 'available', 'text' => 'Available', 'color' => 'blue'];
}
?>

<div class="all-classes-widget">
    <div class="widget-header">
        <div class="widget-title">
            <i class="fas fa-graduation-cap"></i>
            <h3>All Classes</h3>
            <span class="class-count"><?php echo count($all_classes); ?> Total</span>
        </div>
        <div class="widget-actions">
            <a href="classes.php" class="view-all-btn">
                <i class="fas fa-external-link-alt"></i>
                View All
            </a>
        </div>
    </div>
    
    <div class="widget-stats">
        <div class="stat-item">
            <div class="stat-value"><?php echo count($all_classes); ?></div>
            <div class="stat-label">Total Classes</div>
        </div>
        <div class="stat-item">
            <div class="stat-value"><?php echo array_sum(array_column($all_classes, 'enrollment_count')); ?></div>
            <div class="stat-label">Enrollments</div>
        </div>
        <div class="stat-item">
            <div class="stat-value"><?php echo count(array_filter($all_classes, function($c) { return strtotime($c['created_at']) > strtotime('-30 days'); })); ?></div>
            <div class="stat-label">New This Month</div>
        </div>
    </div>
    
    <div class="classes-list">
        <?php if (empty($all_classes)): ?>
            <div class="empty-state">
                <i class="fas fa-graduation-cap"></i>
                <p>No classes available yet</p>
            </div>
        <?php else: ?>
            <?php foreach ($all_classes as $class): 
                $user_status = getUserClassStatus($class['id'], $user_enrollments);
                $thumbnail_path = !empty($class['thumbnail_url']) ? substr($class['thumbnail_url'], 3) : 'assets/images/placeholder.jpg';
            ?>
                <div class="class-item" data-class-id="<?php echo $class['id']; ?>">
                    <div class="class-thumbnail">
                        <img src="<?php echo htmlspecialchars($thumbnail_path); ?>" alt="<?php echo htmlspecialchars($class['title']); ?>">
                        <div class="class-overlay">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    
                    <div class="class-info">
                        <div class="class-header">
                            <h4 class="class-title"><?php echo htmlspecialchars($class['title']); ?></h4>
                            <span class="class-status status-<?php echo $user_status['color']; ?>">
                                <?php echo $user_status['text']; ?>
                            </span>
                        </div>
                        
                        <div class="class-meta">
                            <span class="class-price">Rs. <?php echo number_format($class['price'], 0); ?></span>
                            <span class="class-level level-<?php echo $class['difficulty']; ?>">
                                <?php echo ucfirst($class['difficulty']); ?>
                            </span>
                        </div>
                        
                        <div class="class-stats">
                            <span class="stat">
                                <i class="fas fa-users"></i>
                                <?php echo $class['enrollment_count']; ?> enrolled
                            </span>
                            <span class="stat">
                                <i class="fas fa-video"></i>
                                <?php echo $class['recording_count']; ?> videos
                            </span>
                            <span class="stat">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('M j, Y', strtotime($class['created_at'])); ?>
                            </span>
                        </div>
                        
                        <div class="class-actions">
                            <?php if ($user_status['status'] === 'enrolled' || $user_status['status'] === 'paid'): ?>
                                <a href="paid.php?id=<?php echo $class['id']; ?>" class="action-btn primary">
                                    <i class="fas fa-play-circle"></i>
                                    View Class
                                </a>
                            <?php elseif ($user_status['status'] === 'pending'): ?>
                                <button class="action-btn disabled" disabled>
                                    <i class="fas fa-clock"></i>
                                    Pending Approval
                                </button>
                            <?php elseif ($user_status['status'] === 'rejected'): ?>
                                <a href="pM.php?class_id=<?php echo $class['id']; ?>" class="action-btn warning">
                                    <i class="fas fa-redo"></i>
                                    Retry Payment
                                </a>
                            <?php else: ?>
                                <a href="pM.php?class_id=<?php echo $class['id']; ?>" class="action-btn secondary">
                                    <i class="fas fa-credit-card"></i>
                                    Enroll Now
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if (count($all_classes) > 6): ?>
        <div class="widget-footer">
            <a href="classes.php?view=all" class="show-more-btn">
                <i class="fas fa-chevron-down"></i>
                Show All <?php echo count($all_classes); ?> Classes
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.all-classes-widget {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.widget-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.widget-title i {
    font-size: 1.25rem;
    color: #7c3aed;
}

.widget-title h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.class-count {
    background: #dbeafe;
    color: #2563eb;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #7c3aed;
    color: white;
    text-decoration: none;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.2s;
}

.view-all-btn:hover {
    background: #6d28d9;
}

.widget-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
}

.widget-stats .stat-item {
    text-align: center;
}

.widget-stats .stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.25rem;
}

.widget-stats .stat-label {
    font-size: 0.75rem;
    color: #6b7280;
}

.classes-list {
    max-height: 400px;
    overflow-y: auto;
}

.class-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s;
}

.class-item:hover {
    background: #f9fafb;
}

.class-item:last-child {
    border-bottom: none;
}

.class-thumbnail {
    position: relative;
    width: 80px;
    height: 60px;
    border-radius: 0.5rem;
    overflow: hidden;
    flex-shrink: 0;
}

.class-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.class-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.class-thumbnail:hover .class-overlay {
    opacity: 1;
}

.class-overlay i {
    color: white;
    font-size: 1.25rem;
}

.class-info {
    flex: 1;
    min-width: 0;
}

.class-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.class-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
    line-height: 1.25;
}

.class-status {
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    flex-shrink: 0;
}

.status-blue { background: #dbeafe; color: #2563eb; }
.status-green { background: #dcfce7; color: #166534; }
.status-yellow { background: #fef3c7; color: #92400e; }
.status-red { background: #fee2e2; color: #991b1b; }

.class-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.class-price {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
}

.class-level {
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.level-beginner { background: #dcfce7; color: #166534; }
.level-intermediate { background: #fef3c7; color: #92400e; }
.level-advanced { background: #fee2e2; color: #991b1b; }

.class-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.class-stats .stat {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.class-stats .stat i {
    font-size: 0.75rem;
}

.class-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn.primary {
    background: #10b981;
    color: white;
}

.action-btn.primary:hover {
    background: #059669;
}

.action-btn.secondary {
    background: #7c3aed;
    color: white;
}

.action-btn.secondary:hover {
    background: #6d28d9;
}

.action-btn.warning {
    background: #ef4444;
    color: white;
}

.action-btn.warning:hover {
    background: #dc2626;
}

.action-btn.disabled {
    background: #f59e0b;
    color: white;
    cursor: not-allowed;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #9ca3af;
}

.widget-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f3f4f6;
    background: #f9fafb;
    text-align: center;
}

.show-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #7c3aed;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: color 0.2s;
}

.show-more-btn:hover {
    color: #6d28d9;
}

@media (max-width: 768px) {
    .widget-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .widget-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .class-item {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .class-thumbnail {
        width: 100%;
        height: 120px;
    }
    
    .class-header {
        flex-direction: column;
        gap: 0.5rem;
        align-items: stretch;
    }
    
    .class-stats {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
}
</style>