<?php
  // About Us page with lecturers grid and CV buttons
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  $site = 'EduLearn';
  $activePage = 'home';

  // Define lecturers (edit images and CV links as needed)
  $lecturers = [
    [
        'name' => 'Dhananjaya',
        'role' => 'Mobile Development',
        'img'  => 'mentors/kota.png',
        'cv'   => 'mentors/CV/Kota/index.html'
    ],
    [
      'name' => 'Hemal Pramuditha',
      'role' => 'Senior Software Engineering Lecturer',
      'img'  => 'mentors/hemal.png',
      'cv'   => 'mentors/CV/Hemal/index.html'
    ],
    [
      'name' => 'Heshan Gayantha',
      'role' => 'Data Science & AI',
      'img'  => 'mentors/heshan.png',
      'cv'   => 'mentors/CV/Heshan/Heshan/index.html'
    ],
    [
      'name' => 'Ishan Ranaweera',
      'role' => 'Cloud & DevOps',
      'img'  => 'mentors/ishan.png',
      'cv'   => 'mentors/CV/ishan/My-Details 36611/index.html'
    ],
    [
      'name' => 'Isira Imantha',
      'role' => 'Cybersecurity',
      'img'  => 'mentors/isira.png',
      'cv'   => 'mentors/CV/Isira/index.html'
    ],
    [
      'name' => 'Janani Chathurtha',
      'role' => 'UX/UI & Product Design',
      'img'  => 'mentors/janani.png',
      'cv'   => 'mentors/CV/Janani/My detail 36551/index.html'
    ],
    [
      'name' => 'Kaweesha Rathnayake',
      'role' => 'Full‑Stack Engineering',
      'img'  => 'mentors/rathnayake.png',
      'cv'   => 'mentors/CV/My-Details/index.html'
    ],
    // Placeholders – update with real images/CVs later
    [
      'name' => 'Minaya Gunasekara',
      'role' => 'Machine Learning',
      'img'  => 'mentors/minaya.jpg',
      'cv'   => 'mentors/CV/Minaya.html'
    ],
    [
      'name' => 'Tharushi Karunaratne',
      'role' => 'Frontend Engineering',
      'img'  => 'mentors/CV/Tharushi_CV/profile.jpg',
      'cv'   => 'mentors/CV/Tharushi_CV/cv.html'
    ],
    [
      'name' => 'Shanoshi',
      'role' => 'Backend Architecture',
      'img'  => 'mentors/CV/shanoshi/shohani.jpg',
      'cv'   => 'mentors/CV/shanoshi/mycvv.html'
    ],
  ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us – Lecturers | <?php echo htmlspecialchars($site); ?></title>
  <!-- Tailwind CDN (preflight disabled to avoid global resets) -->
  <script>
    window.tailwind = window.tailwind || {};
    tailwind.config = { corePlugins: { preflight: false } };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    .about-hero{padding:64px 16px 24px}
    .about-hero .container{display:flex;flex-direction:column;gap:12px;align-items:center;text-align:center}
    .eyebrow{color:#7c3aed;font-weight:800;letter-spacing:.08em;text-transform:uppercase;font-size:12px}
    .page-title{margin:6px 0 0;font-size:32px}
    .subtitle{color:#6b7280;max-width:820px}
    .values{display:flex;gap:14px;flex-wrap:wrap;justify-content:center;margin-top:10px}
    .chip{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:9999px;background:#fff;border:1px solid #e5e7eb;color:#374151;font-weight:600}

    .lecturers{padding:18px 16px 60px}
    .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px}
    .card{position:relative;background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;box-shadow:0 10px 28px rgba(2,6,23,.08);transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease}
    .card:hover{transform:translateY(-6px);box-shadow:0 14px 34px rgba(2,6,23,.12);border-color:#d1d5db}
    .banner{height:84px;background:linear-gradient(135deg, rgba(124,58,237,.14), rgba(20,184,166,.14))}
    .avatar{width:84px;height:84px;border-radius:9999px;border:4px solid #fff;object-fit:cover;position:absolute;top:42px;left:50%;transform:translateX(-50%);background:#f3f4f6;display:block}
    .avatar.initials{display:grid;place-items:center;font-weight:800;color:#7c3aed}
    .content{padding:56px 16px 16px;text-align:center}
    .name{font-weight:800;font-size:18px}
    .role{color:#6b7280;margin-top:4px}
    .meta{display:flex;gap:8px;justify-content:center;margin-top:10px}
    .meta .pill{font-size:12px;color:#374151;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:9999px;padding:4px 8px}
    .actions{display:flex;justify-content:center;padding:14px 16px 18px}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid #e5e7eb;background:linear-gradient(135deg,#7c3aed,#8b5cf6);color:#fff;text-decoration:none;font-weight:700;box-shadow:0 8px 24px rgba(124,58,237,.25)}
    .btn:hover{opacity:.95}
    .btn.secondary{background:#fff;color:#111827;border-color:#e5e7eb;box-shadow:none}

    @media (max-width: 1000px){.grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media (max-width: 730px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width: 480px){.grid{grid-template-columns:1fr}}
  </style>
  <style>
    /* Navbar readability overrides for this page */
    #tw-nav-body a,
    #tw-nav-body .fas,
    #tw-nav-body .nav-text,
    #tw-nav-body span { color: #374151 !important; }
    #tw-nav-body a:hover { color: #111827 !important; }
  </style>
</head>
<body class="landing">
  <?php include __DIR__ . '/components/tw-navbar.php'; ?>
  <script>
    // Force light text scheme for nav on this light page
    document.addEventListener('DOMContentLoaded', function(){
      var nb = document.getElementById('tw-nav-body');
      if (nb) nb.classList.add('tw-light');
    });
  </script>

  <section class="about-hero">
    <div class="container">
      <span class="eyebrow"><i class="fa-solid fa-graduation-cap"></i> About EduLearn</span>
      <h1 class="page-title">Learn from industry‑proven lecturers who create success</h1>
      <p class="subtitle">We are a learner‑first platform guided by a team of experienced lecturers across Software Engineering, Data Science, Design, Cloud, and more. Explore our faculty below and view each lecturer's CV to understand their background.</p>
      <div class="values">
        <span class="chip"><i class="fa-solid fa-shield-check"></i> Quality‑first</span>
        <span class="chip"><i class="fa-solid fa-people-group"></i> Community</span>
        <span class="chip"><i class="fa-solid fa-circle-check"></i> Job‑ready skills</span>
        <span class="chip"><i class="fa-solid fa-seedling"></i> Growth mindset</span>
      </div>
    </div>
  </section>

  <section class="lecturers">
    <div class="container">
      <div class="grid">
        <?php foreach ($lecturers as $lecturer): ?>
          <article class="card">
            <div class="banner"></div>
            <?php if (!empty($lecturer['img']) && file_exists(__DIR__ . '/' . $lecturer['img'])): ?>
              <img class="avatar" src="<?php echo htmlspecialchars($lecturer['img']); ?>" alt="<?php echo htmlspecialchars($lecturer['name']); ?>">
            <?php else: ?>
              <?php $initials = preg_replace('/[^A-Z]/', '', strtoupper(substr($lecturer['name'],0,1) . ' ' . substr(strrchr(' ' . $lecturer['name'],' '),1,1))); ?>
              <div class="avatar initials">
                <?php echo htmlspecialchars($initials ?: 'LX'); ?>
              </div>
            <?php endif; ?>
            <div class="content">
              <div class="name"><?php echo htmlspecialchars($lecturer['name']); ?></div>
              <div class="role"><?php echo htmlspecialchars($lecturer['role']); ?></div>
              <div class="meta">
                <span class="pill"><i class="fa-solid fa-star" style="color:#f59e0b"></i> Top Rated</span>
                <span class="pill"><i class="fa-solid fa-chalkboard-user" style="color:#3b82f6"></i> Mentor</span>
              </div>
            </div>
            <div class="actions">
              <a class="btn" href="<?php echo htmlspecialchars($lecturer['cv']); ?>" target="_blank" rel="noopener">
                <i class="fa-solid fa-file-lines"></i> View CV
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Footer (shared) -->
  <?php include __DIR__ . '/components/footer.php'; ?>

  <script src="script.js"></script>
  <script src="assets/js/navbar-theme.js"></script>
</body>
</html>


