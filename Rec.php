<?php 
require_once __DIR__ . '/session_helper.php';
safe_session_start();
require_once __DIR__ . '/config.php'; 
$site = $config['site_name'] ?? 'NextOra'; 

// Redirect to login if not authenticated
if (!is_logged_in()) {
    header('Location: login.php?redirect=Rec.php' . (isset($_GET['id']) ? '?id=' . $_GET['id'] : ''));
    exit();
}

$user_id = $_SESSION['user_id'];
$class_id = $_GET['id'] ?? null;

if (!$class_id) {
    header('Location: paid.php');
    exit();
}

// Verify user has access to this class
$access_check_sql = "
    SELECT 
        c.*,
        e.status as enrollment_status,
        p.status as payment_status
    FROM classes c
    LEFT JOIN enrollments e ON c.id = e.class_id AND e.user_id = :user_id
    LEFT JOIN payments p ON c.id = p.class_id AND p.user_id = :user_id
    WHERE c.id = :class_id 
    AND (
        (e.status = 'Active') OR 
        (p.status = 'Paid')
    )
";

try {
    $access_stmt = $pdo->prepare($access_check_sql);
    $access_stmt->execute([':user_id' => $user_id, ':class_id' => $class_id]);
    $class = $access_stmt->fetch();
    
    if (!$class) {
        $_SESSION['payment_error'] = 'You do not have access to this class or payment is still pending approval.';
        header('Location: paid.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['payment_error'] = 'Database error occurred.';
    header('Location: paid.php');
    exit();
}

// Get recordings for this class with categories
$recordings_sql = "
    SELECT 
        r.*,
        rt.thumbnail_url as custom_thumbnail
    FROM recordings r
    JOIN class_recordings cr ON r.id = cr.recording_id
    LEFT JOIN recording_thumbnails rt ON r.id = rt.recording_id
    WHERE cr.class_id = :class_id
    ORDER BY r.created_at ASC
";

try {
    $recordings_stmt = $pdo->prepare($recordings_sql);
    $recordings_stmt->execute([':class_id' => $class_id]);
    $recordings = $recordings_stmt->fetchAll();
} catch (PDOException $e) {
    $recordings = [];
}

// Group recordings by category/subject
$grouped_recordings = [];
$categories = [];

foreach ($recordings as $recording) {
    // Extract category from title (e.g., "CSS", "HTML", "Python", etc.)
    $title = $recording['title'];
    $category = 'General';
    
    // Simple category detection based on common patterns
    if (stripos($title, 'CSS') !== false) {
        $category = 'CSS';
    } elseif (stripos($title, 'HTML') !== false) {
        $category = 'HTML';
    } elseif (stripos($title, 'Python') !== false || stripos($title, 'PYTHON') !== false) {
        $category = 'Python';
    } elseif (stripos($title, 'JavaScript') !== false || stripos($title, 'JS') !== false) {
        $category = 'JavaScript';
    } elseif (stripos($title, 'Introduction') !== false || stripos($title, 'ICT') !== false) {
        $category = 'Introduction to ICT';
    } elseif (stripos($title, 'Lesson') !== false) {
        // Extract lesson number for better categorization
        if (preg_match('/Lesson\s*(\d+)/i', $title, $matches)) {
            $category = 'Lesson ' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        } else {
            $category = 'Lessons';
        }
    } elseif (stripos($title, 'System') !== false) {
        $category = 'Operating System';
    } elseif (stripos($title, 'Network') !== false) {
        $category = 'Networking';
    } elseif (stripos($title, 'Model Paper') !== false || stripos($title, 'Past Paper') !== false) {
        $category = 'Model Paper Discussions';
    } elseif (stripos($title, 'DBMS') !== false || stripos($title, 'Database') !== false) {
        $category = 'Database Management';
    }
    
    if (!isset($grouped_recordings[$category])) {
        $grouped_recordings[$category] = [];
        $categories[] = $category;
    }
    
    $grouped_recordings[$category][] = $recording;
}

// Function to extract YouTube video ID from URL
function getYouTubeVideoId($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    preg_match($pattern, $url, $matches);
    return isset($matches[1]) ? $matches[1] : null;
}

// Function to get YouTube thumbnail
function getYouTubeThumbnail($video_id) {
    return "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($class['title']); ?> — Recordings</title>
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
            margin-bottom: 2rem;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }
        
        .back-btn:hover {
            color: #374151;
        }
        
        .class-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .class-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .class-info h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            line-height: 1.3;
        }
        
        .class-info .video-count {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .zoom-section {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .zoom-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }
        
        .zoom-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .zoom-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .zoom-info h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0;
        }
        
        .zoom-info .badge {
            background: rgba(16, 185, 129, 0.9);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.25rem;
            display: inline-block;
        }
        
        .zoom-details {
            margin-bottom: 1rem;
        }
        
        .zoom-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .zoom-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }
        
        .zoom-id {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .join-zoom-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .join-zoom-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
        }
        
        .recordings-section {
            margin-bottom: 2rem;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .section-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        
        .section-count {
            background: #e0e7ff;
            color: #5b21b6;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .category-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .category-tab {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            border: 1px solid #e5e7eb;
            background: white;
            color: #6b7280;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .category-tab:hover,
        .category-tab.active {
            background: #8b5cf6;
            color: white;
            border-color: #8b5cf6;
        }
        
        .recordings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .recording-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            cursor: pointer;
        }
        
        .recording-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .recording-thumbnail {
            position: relative;
            height: 180px;
            overflow: hidden;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }
        
        .recording-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .recording-card:hover .play-overlay {
            background: rgba(139, 92, 246, 0.9);
            transform: translate(-50%, -50%) scale(1.1);
        }
        
        .duration-badge {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .recording-content {
            padding: 1rem;
        }
        
        .recording-title {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin: 0 0 0.5rem 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .recording-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #6b7280;
            font-size: 0.75rem;
        }
        
        .recording-date {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .recording-type {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-weight: 500;
        }
        
        .video-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
        
        .video-container {
            position: relative;
            width: 90%;
            max-width: 900px;
            aspect-ratio: 16/9;
        }
        
        .video-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 12px;
        }
        
        .close-video {
            position: absolute;
            top: -50px;
            right: 0;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .close-video:hover {
            color: #8b5cf6;
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
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .class-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .zoom-section {
                padding: 1rem;
            }
            
            .zoom-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            
            .zoom-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .category-tabs {
                gap: 0.25rem;
            }
            
            .category-tab {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }
            
            .recordings-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
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
            <a href="paid.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to My Classes
            </a>
            
            <div class="class-header">
                <div class="class-icon">
                    🎓
                </div>
                <div class="class-info">
                    <h1><?php echo htmlspecialchars($class['title']); ?></h1>
                    <div class="video-count"><?php echo count($recordings); ?> videos available</div>
                </div>
            </div>
        </div>

        <!-- Zoom Classes Section -->
        <div class="zoom-section">
            <div class="zoom-header">
                <div class="zoom-icon">
                    📹
                </div>
                <div class="zoom-info">
                    <h3>Zoom Classes</h3>
                    <span class="badge">Completed</span>
                </div>
            </div>
            
            <div class="zoom-details">
                <div class="zoom-title">PYTHON - CODING DAY 10</div>
                <div class="zoom-meta">
                    <span><i class="fas fa-calendar"></i> Sep 2, 2025, 09:00 AM</span>
                    <span><i class="fas fa-lock"></i> Password Protected</span>
                </div>
                <div class="zoom-id">ID: 947 9511 7998</div>
            </div>
            
            <a href="#" class="join-zoom-btn">
                <i class="fas fa-video"></i>
                Join Zoom Class
            </a>
        </div>

        <!-- Recordings Section -->
        <div class="recordings-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-play"></i>
                </div>
                <h2 class="section-title">Recordings</h2>
                <span class="section-count"><?php echo count($recordings); ?></span>
            </div>

            <?php if (!empty($categories)): ?>
                <div class="category-tabs">
                    <button class="category-tab active" onclick="showCategory('all')">All</button>
                    <?php foreach ($categories as $category): ?>
                        <button class="category-tab" onclick="showCategory('<?php echo htmlspecialchars($category); ?>')">
                            <?php echo htmlspecialchars($category); ?>
                            <span style="margin-left: 0.25rem; opacity: 0.7;"><?php echo count($grouped_recordings[$category]); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($recordings)): ?>
                <div class="recordings-grid" id="recordings-grid">
                    <?php foreach ($recordings as $recording): 
                        $video_id = getYouTubeVideoId($recording['video_url']);
                        $thumbnail_url = $recording['custom_thumbnail'] ?? ($video_id ? getYouTubeThumbnail($video_id) : 'assets/images/placeholder.jpg');
                        
                        // Determine category for filtering
                        $title = $recording['title'];
                        $category = 'General';
                        if (stripos($title, 'CSS') !== false) $category = 'CSS';
                        elseif (stripos($title, 'HTML') !== false) $category = 'HTML';
                        elseif (stripos($title, 'Python') !== false || stripos($title, 'PYTHON') !== false) $category = 'Python';
                        elseif (stripos($title, 'JavaScript') !== false) $category = 'JavaScript';
                        elseif (stripos($title, 'Introduction') !== false || stripos($title, 'ICT') !== false) $category = 'Introduction to ICT';
                        elseif (stripos($title, 'Lesson') !== false) {
                            if (preg_match('/Lesson\s*(\d+)/i', $title, $matches)) {
                                $category = 'Lesson ' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                            } else {
                                $category = 'Lessons';
                            }
                        }
                        elseif (stripos($title, 'System') !== false) $category = 'Operating System';
                        elseif (stripos($title, 'Network') !== false) $category = 'Networking';
                        elseif (stripos($title, 'Model Paper') !== false || stripos($title, 'Past Paper') !== false) $category = 'Model Paper Discussions';
                        elseif (stripos($title, 'DBMS') !== false) $category = 'Database Management';
                    ?>
                        <div class="recording-card" data-category="<?php echo htmlspecialchars($category); ?>" onclick="playVideo('<?php echo htmlspecialchars($recording['video_url']); ?>', '<?php echo htmlspecialchars($recording['title']); ?>')">
                            <div class="recording-thumbnail">
                                <img src="<?php echo htmlspecialchars($thumbnail_url); ?>" alt="<?php echo htmlspecialchars($recording['title']); ?>" onerror="this.src='assets/images/placeholder.jpg'">
                                <div class="play-overlay">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="duration-badge"><?php echo $recording['duration_minutes']; ?>m</div>
                            </div>
                            <div class="recording-content">
                                <h3 class="recording-title"><?php echo htmlspecialchars($recording['title']); ?></h3>
                                <div class="recording-meta">
                                    <div class="recording-date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('M j, Y', strtotime($recording['created_at'])); ?>
                                    </div>
                                    <div class="recording-type"><?php echo $recording['video_type']; ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>No Recordings Available</h3>
                    <p>This class doesn't have any recordings yet. Check back later for new content!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="video-modal">
        <div class="video-container">
            <span class="close-video" onclick="closeVideo()">&times;</span>
            <iframe id="videoFrame" src="" allowfullscreen></iframe>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script src="script.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/navbar-theme.js"></script>
    
    <script>
        function playVideo(url, title) {
            const modal = document.getElementById('videoModal');
            const iframe = document.getElementById('videoFrame');
            
            // Convert YouTube URL to embed format
            let embedUrl = url;
            if (url.includes('youtube.com/watch?v=')) {
                const videoId = url.split('v=')[1].split('&')[0];
                embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            } else if (url.includes('youtu.be/')) {
                const videoId = url.split('youtu.be/')[1].split('?')[0];
                embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            }
            
            iframe.src = embedUrl;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeVideo() {
            const modal = document.getElementById('videoModal');
            const iframe = document.getElementById('videoFrame');
            
            iframe.src = '';
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function showCategory(category) {
            const cards = document.querySelectorAll('.recording-card');
            const tabs = document.querySelectorAll('.category-tab');
            
            // Update active tab
            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
            
            // Show/hide cards based on category
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Close modal when clicking outside
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVideo();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVideo();
            }
        });
    </script>
</body>
</html>