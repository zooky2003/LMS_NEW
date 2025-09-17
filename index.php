<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextOra - Your Gateway to Knowledge</title>
    <!-- Tailwind CDN (preflight disabled to avoid global resets) -->
    <script>
      window.tailwind = window.tailwind || {};
      tailwind.config = { corePlugins: { preflight: false } };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="landing">
    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-container">
            <div class="loading-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>NextOra</span>
            </div>
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <div class="loading-text">
                <span class="loading-dots">Loading</span>
            </div>
        </div>
    </div>

    <!-- New Tailwind Navbar -->
    <?php include __DIR__ . '/components/tw-navbar.php'; ?>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-highlight">
                    <h1 class="hero-title animated-text">
                        Master New Skills with
                        <span class="highlight-text"> Interactive Learning</span>
                    </h1>
                </div>
                <p class="hero-description">
                    Join thousands of students worldwide in our comprehensive learning platform. 
                    Access expert-led courses, interactive content, and personalized learning paths.
                </p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary btn-large">Start Learning Today</a>
                    <button class="btn btn-outline btn-large" onclick="showDemo()">
                        <i class="fas fa-play"></i>
                        Introduction Video
                    </button>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <h3>50K+</h3>
                        <p>Active Students</p>
                    </div>
                    <div class="stat">
                        <h3>500+</h3>
                        <p>Expert Instructors</p>
                    </div>
                    <div class="stat">
                        <h3>1000+</h3>
                        <p>Courses Available</p>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="floating-card card-1">
                    <i class="fas fa-book"></i>
                    <span>Interactive Lessons</span>
                </div>
                <div class="floating-card card-2">
                    <i class="fas fa-users"></i>
                    <span>Live Sessions</span>
                </div>
                <div class="floating-card card-3">
                    <i class="fas fa-certificate"></i>
                    <span>Certificates</span>
                </div>
                <div class="hero-main-image">
                    <i class="fas fa-laptop-code"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Mentors Tooltip Section -->
    <?php include __DIR__ . '/components/mentors-tooltip.php'; ?>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose NextOra?</h2>
                <p>Discover the features that make learning engaging and effective</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Video Learning</h3>
                    <p>High-quality video content from industry experts with interactive quizzes and assignments.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Learning</h3>
                    <p>Learn anywhere, anytime with our mobile-optimized platform that works on all devices.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Progress Tracking</h3>
                    <p>Monitor your learning journey with detailed analytics and personalized recommendations.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Community Support</h3>
                    <p>Connect with fellow learners and get help from instructors through our active community.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Self-Paced Learning</h3>
                    <p>Learn at your own speed with flexible schedules that fit your lifestyle and commitments.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Certification</h3>
                    <p>Earn industry-recognized certificates upon course completion to boost your career.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section id="courses" class="courses">
        <div class="container">
            <div class="section-header">
                <h2>Popular Courses</h2>
                <p>Explore our most popular courses across various domains</p>
            </div>
            <div class="courses-grid">
                <div class="card-container">
                    <div class="course-card">
                        <div class="course-image">
                            <img src="assets/onetime.jpg" 
                                 alt="Web Development Course" class="course-thumbnail">
                            <div class="course-overlay">
                                <i class="fas fa-code"></i>
                            </div>
                        </div>
                        <div class="course-content">
                            <h3 class="card-item" data-translate="50">Web Development Bootcamp</h3>
                            <p class="card-item" data-translate="60">Master HTML, CSS, JavaScript, and modern frameworks</p>
                            <div class="course-meta">
                                <span class="course-duration">
                                    <i class="fas fa-clock"></i>
                                    12 weeks
                                </span>
                                <span class="course-level">Beginner</span>
                            </div>
                            <div class="course-rating">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span>4.9 (2,500+ reviews)</span>
                            </div>
                            <div class="course-price">
                                <span class="price">Rs.10,000</span>
                                <a href="register.php" class="btn btn-primary card-item" data-translate="20">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-container">
                    <div class="course-card">
                        <div class="course-image">
                            <img src="assets/test.png" 
                                 alt="Data Science Course" class="course-thumbnail">
                            <div class="course-overlay">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                        <div class="course-content">
                            <h3 class="card-item" data-translate="50">Data Science Mastery</h3>
                            <p class="card-item" data-translate="60">Learn Python, Machine Learning, and Data Analysis</p>
                            <div class="course-meta">
                                <span class="course-duration">
                                    <i class="fas fa-clock"></i>
                                    16 weeks
                                </span>
                                <span class="course-level">Intermediate</span>
                            </div>
                            <div class="course-rating">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span>4.8 (1,800+ reviews)</span>
                            </div>
                            <div class="course-price">
                                <span class="price">Rs.13,000</span>
                                <a href="register.php" class="btn btn-primary card-item" data-translate="20">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-container">
                    <div class="course-card">
                        <div class="course-image">
                            <img src="https://images.unsplash.com/photo-1558655146-d09347e92766?q=80&w=2560&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
                                 alt="UI/UX Design Course" class="course-thumbnail">
                            <div class="course-overlay">
                                <i class="fas fa-paint-brush"></i>
                            </div>
                        </div>
                        <div class="course-content">
                            <h3 class="card-item" data-translate="50">UI/UX Design Fundamentals</h3>
                            <p class="card-item" data-translate="60">Create beautiful and user-friendly digital experiences</p>
                            <div class="course-meta">
                                <span class="course-duration">
                                    <i class="fas fa-clock"></i>
                                    10 weeks
                                </span>
                                <span class="course-level">Beginner</span>
                            </div>
                            <div class="course-rating">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span>4.9 (1,200+ reviews)</span>
                            </div>
                            <div class="course-price">
                                <span class="price">Rs.15,000</span>
                                <a href="register.php" class="btn btn-primary card-item" data-translate="20">View Class</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <style>
    /* Hardening: ensure results section looks like marquee cards even if other styles conflict */
    section.results { 
        background:#fff !important; 
    }
    section.results .imc-scroller { 
        position:relative; 
        overflow:hidden; 
        padding:10px 0 20px; 
    }
    section.results .imc-track { 
        display:inline-flex !important; 
        white-space:nowrap; 
        gap:18px; 
        list-style:none; 
        margin:0; 
        padding:0; 
    }
    section.results .imc-track.imc-animate { 
        animation: imc-scroll var(--imc-duration,35s) linear infinite; 
        animation-direction: var(--imc-direction,normal); 
    }
    section.results .imc-track:not(.no-animate) { 
        animation: imc-scroll var(--imc-duration,35s) linear infinite; 
        animation-direction: var(--imc-direction,normal); 
    }
    @keyframes imc-scroll { 
        from { 
            transform: translate3d(0,0,0);} 
        to { 
            transform: translate3d(-50%,0,0);} 
    }
    section.results .imc-card { 
        width:340px; 
        max-width:90vw; 
        background:#fff; 
        border:1px solid #e5e7eb; 
        border-radius:16px; 
        box-shadow:0 10px 24px rgba(2,6,23,0.08); 
        overflow:hidden; 
        flex:0 0 auto; 
        display:inline-block; 
    }
    section.results .imc-card-header { 
        height:75px; 
        background:linear-gradient(135deg,#f59e0b,#fbbf24); 
        display:flex; 
        flex-direction:column; 
        align-items:center; 
        justify-content:center; 
        color:#111827; 
        position:relative; 
    }
    section.results .imc-card-body { 
        padding:18px 18px 20px; 
        text-align:center; 
        background:#fff; 
    }
    section.results .imc-avatar { 
        width:70px; 
        height:70px; 
        border-radius:9999px; 
        background: radial-gradient(circle at 30% 30%, #93c5fd, #60a5fa); 
        margin:-30px auto 10px; 
        display:grid; 
        place-items:center; 
        color:#fff; 
        box-shadow:0 6px 16px rgba(59,130,246,.25); 
        position:relative; 
        z-index:2; 
        border:4px solid #fff; 
    }
    </style>
    <section id="results" class="results">
        <div class="container">
            <div class="section-header">
                <h2>Top Results</h2>
                <p>Celebrating our district ranks and achievements</p>
            </div>

            <?php
            // Results data 
            $results = [
                [ 'rank' => 5, 'name' => 'HASHEN CHAMINDU', 'district' => 'KALUTHARA District', 'index' => '1651374', 'stream' => 'E.TECH' ],
                [ 'rank' => 55, 'name' => 'SANDITHI MEEGAHAWATHTHA', 'district' => 'KALUTHARA District', 'index' => '4161670', 'stream' => 'E.TECH' ],
                [ 'rank' => 7, 'name' => 'RAVINDU HANSAKA', 'district' => 'KALUTHARA District', 'index' => '1651331', 'stream' => 'E.TECH' ],
                [ 'rank' => 8, 'name' => 'SANDUNI PERERA', 'district' => 'GALLE District', 'index' => '1651404', 'stream' => 'SCIENCE' ],
                [ 'rank' => 30, 'name' => 'ISHAN RANAWEERA', 'district' => 'COLOMBO District', 'index' => '1651201', 'stream' => 'ICT' ],
                [ 'rank' => 6, 'name' => 'ISURUNI DAMMINDI', 'district' => 'KURUNEGALA District', 'index' => '1651750', 'stream' => 'ARTS' ],
            ];
            ?>

            <!-- Infinite Moving Cards (pure PHP + JS) -->
            <div class="imc-scroller" data-direction="right" data-speed="slow" data-pause="true">
                <ul class="imc-track">
                    <?php foreach ($results as $r): ?>
                        <li class="imc-card">
                            <div class="imc-card-header">
                                <i class="fas fa-trophy"></i>
                                <div class="imc-rank">#<?php echo (int)$r['rank']; ?></div>
                                <div class="imc-rank-sub">District Rank</div>
                            </div>
                            <div class="imc-card-body">
                                <div class="imc-avatar"><i class="fas fa-user"></i></div>
                                <h4 class="imc-name"><?php echo htmlspecialchars($r['name']); ?></h4>
                                <div class="imc-badges">
                                    <span class="imc-badge"><i class="fas fa-location-dot"></i><?php echo htmlspecialchars($r['district']); ?></span>
                                </div>
                                <div class="imc-meta">
                                    <div class="imc-pill">
                                        <span class="imc-pill-label"># Index</span>
                                        <span class="imc-pill-value"><?php echo htmlspecialchars($r['index']); ?></span>
                                    </div>
                                    <div class="imc-pill">
                                        <span class="imc-pill-label">Stream</span>
                                        <span class="imc-pill-value"><?php echo htmlspecialchars($r['stream']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Second marquee: opposite direction -->
            <div class="imc-scroller" data-direction="left" data-speed="slow" data-pause="true">
                <ul class="imc-track">
                    <?php foreach ($results as $r): ?>
                        <li class="imc-card">
                            <div class="imc-card-header">
                                <i class="fas fa-trophy"></i>
                                <div class="imc-rank">#<?php echo (int)$r['rank']; ?></div>
                                <div class="imc-rank-sub">District Rank</div>
                            </div>
                            <div class="imc-card-body">
                                <div class="imc-avatar"><i class="fas fa-user"></i></div>
                                <h4 class="imc-name"><?php echo htmlspecialchars($r['name']); ?></h4>
                                <div class="imc-badges">
                                    <span class="imc-badge"><i class="fas fa-location-dot"></i><?php echo htmlspecialchars($r['district']); ?></span>
                                </div>
                                <div class="imc-meta">
                                    <div class="imc-pill">
                                        <span class="imc-pill-label"># Index</span>
                                        <span class="imc-pill-value"><?php echo htmlspecialchars($r['index']); ?></span>
                                    </div>
                                    <div class="imc-pill">
                                        <span class="imc-pill-label">Stream</span>
                                        <span class="imc-pill-value"><?php echo htmlspecialchars($r['stream']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>

    <script>
    // Infinite Moving Cards: vanilla JS version
    (function() {
        const containers = document.querySelectorAll('.imc-scroller');
        containers.forEach(container => {
            const track = container.querySelector('.imc-track');
            if (!track) return;

            const pauseOnHover = container.dataset.pause !== 'false';
            const direction = (container.dataset.direction === 'right') ? 'reverse' : 'normal';
            const speed = container.dataset.speed || 'normal';
            const durationMap = { fast: '20s', normal: '35s', slow: '60s' };
            container.style.setProperty('--imc-direction', direction);
            container.style.setProperty('--imc-duration', durationMap[speed] || durationMap.normal);

            // Duplicate children once for a seamless loop
            if (!track.dataset.duplicated) {
                const kids = Array.from(track.children);
                kids.forEach(node => track.appendChild(node.cloneNode(true)));
                track.dataset.duplicated = 'true';
            }

            // Pause on hover (optional)
            if (pauseOnHover) {
                container.addEventListener('mouseenter', () => track.style.animationPlayState = 'paused');
                container.addEventListener('mouseleave', () => track.style.animationPlayState = 'running');
            }

            requestAnimationFrame(() => track.classList.add('imc-animate'));
        });

        // Removed the static appender list per request
    })();
    </script>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2>What Our Students Say</h2>
                <p>Real feedback from our learning community</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"NextOra transformed my career. The interactive courses and expert instructors helped me land my dream job in tech."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <span>Software Developer</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>" ""ලංකාවේ ICT සරල කරණ නිර්වචන කේතය" නුඹ උගත්තසු හැකි අකුරකටම ගොඩාක් . පිං සර් #NextOra LK."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h4>Sandithi Meegahawaththa</h4>
                            <span>Data Analyst</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>" සර් මට පලවෙනි පාර විබාගේ කරදිදි හඹුනා නම් මම් දෙවෙනි කරන එකක් නැහැ... ict ඇරෙන්න අනිත් සට දෙකම හොද මට්ටමක හිටියා ICT අවුල්..."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h4>Chamith Hirusha</h4>
                            <span>UX Designer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="cta" class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Your Learning Journey?</h2>
                <p>Join thousands of students who are already advancing their careers with NextOra</p>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary btn-large">Get Started Free</a>
                    <a href="#courses" class="btn btn-outline btn-large">View All Courses</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <script src="script.js"></script>
    <script src="assets/js/navbar-theme.js"></script>
    <script>
        // Let the navbar theme controller automatically detect themes on landing page
        document.addEventListener('DOMContentLoaded', function() {
            // The navbar theme controller will automatically detect the background
            // and apply the appropriate theme (transparent for hero, light for white sections, etc.)
        });

        // Demo functionality
        function showDemo() {
            // Create demo modal
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
                backdrop-filter: blur(5px);
            `;
            
            const videoUrl = (window.DEMO_VIDEO_URL || 'https://www.youtube.com/embed/oqMmAfJjG-A?rel=0&autoplay=1');

            modal.innerHTML = `
                <div style="
                    background: white;
                    border-radius: 20px;
                    padding: 40px;
                    max-width: 850px;
                    width: 90%;
                    max-height: 90vh;
                    overflow-y: auto;
                    position: relative;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                ">
                    <button onclick="closeDemo()" style="
                        position: absolute;
                        top: 15px;
                        right: 15px;
                        background: none;
                        border: none;
                        font-size: 24px;
                        cursor: pointer;
                        color: #666;
                    ">&times;</button>
                    
                    <h2 style="
                        font-size: 28px;
                        font-weight: 700;
                        margin-bottom: 20px;
                        color: #333;
                        text-align: center;
                    ">NextOra Introduction</h2>
                    
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; background: #000; margin-bottom: 20px; box-shadow: 0 10px 24px rgba(0,0,0,0.12);">
                        <iframe src="${videoUrl}" title="NextOra Introduction Video" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; border-radius: 12px;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                    
                    <div style="
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                        gap: 20px;
                        margin-bottom: 30px;
                    ">
                        <div style="
                            background: #f0f4ff;
                            padding: 20px;
                            border-radius: 8px;
                            text-align: center;
                        ">
                            <i class="fas fa-video" style="font-size: 24px; color: #667eea; margin-bottom: 10px;"></i>
                            <h4 style="font-weight: 600; margin-bottom: 8px;">Video Learning</h4>
                            <p style="font-size: 14px; color: #666;">High-quality video content</p>
                        </div>
                        <div style="
                            background: #f0f4ff;
                            padding: 20px;
                            border-radius: 8px;
                            text-align: center;
                        ">
                            <i class="fas fa-chart-line" style="font-size: 24px; color: #667eea; margin-bottom: 10px;"></i>
                            <h4 style="font-weight: 600; margin-bottom: 8px;">Progress Tracking</h4>
                            <p style="font-size: 14px; color: #666;">Monitor your learning journey</p>
                        </div>
                        <div style="
                            background: #f0f4ff;
                            padding: 20px;
                            border-radius: 8px;
                            text-align: center;
                        ">
                            <i class="fas fa-certificate" style="font-size: 24px; color: #667eea; margin-bottom: 10px;"></i>
                            <h4 style="font-weight: 600; margin-bottom: 8px;">Certificates</h4>
                            <p style="font-size: 14px; color: #666;">Earn industry certificates</p>
                        </div>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="register.php" style="
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 12px 24px;
                            border-radius: 8px;
                            text-decoration: none;
                            font-weight: 600;
                            display: inline-block;
                            margin-right: 10px;
                        ">Get Started Free</a>
                        <button onclick="closeDemo()" style="
                            background: #f3f4f6;
                            color: #374151;
                            padding: 12px 24px;
                            border: 1px solid #d1d5db;
                            border-radius: 8px;
                            font-weight: 600;
                            cursor: pointer;
                        ">Close</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Close on escape key
            const handleEscape = (e) => {
                if (e.key === 'Escape') {
                    closeDemo();
                }
            };
            document.addEventListener('keydown', handleEscape);
            
            // Store the escape handler for cleanup
            modal._escapeHandler = handleEscape;
        }
        
        function closeDemo() {
            const modal = document.querySelector('div[style*="position: fixed"]');
            if (modal) {
                document.removeEventListener('keydown', modal._escapeHandler);
                modal.remove();
            }
        }
    </script>
</body>
</html>
