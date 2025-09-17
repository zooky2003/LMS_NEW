<?php
require_once __DIR__ . '/session_helper.php';
safe_session_start();
$cfg = require __DIR__ . '/config.php'; 
$site = $cfg['site_name'] ?? 'EduLearn'; 
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Help & Support — <?php echo htmlspecialchars($site); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Inter', 'Poppins', sans-serif;
      background: linear-gradient(135deg, #e0e7ff 0%, #fff 100%);
    }
    .glass {
      background: rgba(255,255,255,0.6);
      box-shadow: 0 8px 32px 0 rgba(31,38,135,0.10);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 1.5rem;
      border: 1px solid rgba(255,255,255,0.18);
    }
    .faq-toggle[aria-expanded="true"] .fa-chevron-down {
      transform: rotate(180deg);
    }
    /* Navbar text color override for help page */
    #tw-nav-body, #tw-nav-body a, #tw-nav-body .fas, #tw-nav-body .nav-text {
      color: #374151 !important; /* Tailwind gray-700 */
    }
    #tw-nav-body a.tw-active {
      color: #6366f1 !important; /* Tailwind indigo-500 for active */
    }
  </style>
</head>
<body data-nav-theme="light">
  <!-- Animated Blobs Background -->
  <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute top-[-10%] left-[-10%] w-[400px] h-[400px] bg-blue-200 opacity-60 rounded-full filter blur-3xl animate-blob1"></div>
    <div class="absolute top-[60%] left-[60%] w-[350px] h-[350px] bg-purple-200 opacity-50 rounded-full filter blur-2xl animate-blob2"></div>
    <div class="absolute top-[30%] left-[70%] w-[300px] h-[300px] bg-yellow-100 opacity-40 rounded-full filter blur-2xl animate-blob3"></div>
    <div class="absolute top-[80%] left-[-10%] w-[300px] h-[300px] bg-pink-100 opacity-40 rounded-full filter blur-2xl animate-blob4"></div>
  </div>
  <?php include __DIR__ . '/components/dashboard-navbar.php'; ?>
  <main class="max-w-4xl mx-auto px-4 py-12 md:py-16 mt-20">
    <!-- Header Section -->
    <header class="text-center mb-12">
      <div class="flex flex-col items-center gap-4">
        <span class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 mb-2">
          <i class="fa-solid fa-hands-helping text-blue-500 text-4xl"></i>
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-slate-800">Help & Support</h1>
        <p class="mt-2 text-lg text-slate-500">We’re here to guide you every step of the way</p>
      </div>
    </header>
    <!-- Search Bar -->
    <section class="mb-10">
      <form class="max-w-2xl mx-auto">
        <div class="flex items-center glass px-4 py-3 rounded-full shadow-md focus-within:ring-2 focus-within:ring-blue-200 transition">
          <i class="fa fa-search text-slate-400 mr-3 text-lg"></i>
          <input type="text" class="flex-1 bg-transparent outline-none text-slate-700 placeholder-slate-400 text-base" placeholder="Search help articles, FAQs, or contact support…" autocomplete="off">
        </div>
      </form>
    </section>
    <!-- Quick Support Options -->
    <section class="mb-14">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- FAQs Card -->
        <div class="glass p-7 flex flex-col items-center text-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl cursor-pointer">
          <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-100 mb-3">
            <i class="fa-solid fa-question-circle text-blue-500 text-2xl"></i>
          </span>
          <h3 class="font-semibold text-lg text-slate-800 mb-1">FAQs</h3>
          <p class="text-slate-500 text-sm mb-3">Browse answers to common questions about our LMS.</p>
          <a href="#faq" class="text-blue-600 font-medium hover:underline">View FAQs <i class="fa fa-arrow-right ml-1"></i></a>
        </div>
        <!-- Contact Support Card -->
        <div class="glass p-7 flex flex-col items-center text-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl cursor-pointer">
          <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-3">
            <i class="fa-solid fa-envelope-open-text text-green-500 text-2xl"></i>
          </span>
          <h3 class="font-semibold text-lg text-slate-800 mb-1">Contact Support</h3>
          <p class="text-slate-500 text-sm mb-3">Reach out to our team for personalized assistance.</p>
          <a href="stC.php" class="text-green-600 font-medium hover:underline">Contact Us <i class="fa fa-arrow-right ml-1"></i></a>
        </div>
        <!-- Live Chat Card -->
        <div class="glass p-7 flex flex-col items-center text-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl cursor-pointer">
          <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-purple-100 mb-3">
            <i class="fa-solid fa-comments text-purple-500 text-2xl"></i>
          </span>
          <h3 class="font-semibold text-lg text-slate-800 mb-1">Live Chat</h3>
          <p class="text-slate-500 text-sm mb-3">Chat with us in real-time for instant help.</p>
          <button class="bg-purple-500 hover:bg-purple-600 text-white font-medium py-2 px-6 rounded-full shadow transition-all focus:outline-none focus:ring-2 focus:ring-purple-200">Start Chat</button>
        </div>
        <!-- Guides & Tutorials Card -->
        <div class="glass p-7 flex flex-col items-center text-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl cursor-pointer">
          <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-yellow-100 mb-3">
            <i class="fa-solid fa-book-open text-yellow-500 text-2xl"></i>
          </span>
          <h3 class="font-semibold text-lg text-slate-800 mb-1">Guides & Tutorials</h3>
          <p class="text-slate-500 text-sm mb-3">Step-by-step guides to help you get the most out of our LMS.</p>
          <a href="stC.php" class="text-yellow-600 font-medium hover:underline">View Guides <i class="fa fa-arrow-right ml-1"></i></a>
        </div>
      </div>
    </section>
    <!-- FAQ Preview Section -->
    <section id="faq" class="mb-14">
      <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">Frequently Asked Questions</h2>
      <div class="space-y-4 max-w-2xl mx-auto">
        <?php
        $faqs = [
          [
            'q' => 'How do I reset my password?',
            'a' => 'Go to your profile settings, click on “Change Password”, and follow the instructions. If you forgot your password, use the “Forgot Password” link on the login page.'
          ],
          [
            'q' => 'How can I contact support?',
            'a' => 'You can contact us via the “Contact Support” card above, or use the Email/Chat/Call options below.'
          ],
          [
            'q' => 'Where can I find tutorials?',
            'a' => 'Visit the “Guides & Tutorials” section for step-by-step instructions and video walkthroughs.'
          ],
          [
            'q' => 'Is my data secure?',
            'a' => 'Yes, we use industry-standard security practices to keep your data safe and private.'
          ],
          [
            'q' => 'How do I enroll in a new course?',
            'a' => 'Browse available courses and click “Enroll”. If you need help, contact support.'
          ],
        ];
        foreach ($faqs as $i => $faq): ?>
        <div class="glass overflow-hidden">
          <button type="button" class="w-full flex justify-between items-center px-6 py-4 text-left faq-toggle focus:outline-none focus:ring-2 focus:ring-blue-200 transition" aria-expanded="false" data-faq="faq-<?php echo $i; ?>">
            <span class="font-medium text-slate-800"><?php echo htmlspecialchars($faq['q']); ?></span>
            <i class="fa fa-chevron-down text-slate-400 transition-transform"></i>
          </button>
          <div class="faq-answer px-6 pb-4 text-slate-600 text-sm hidden" id="faq-<?php echo $i; ?>">
            <?php echo htmlspecialchars($faq['a']); ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    <!-- Contact Section -->
    <section id="contact" class="mb-8">
      <div class="glass p-8 text-center">
        <h2 class="text-xl font-semibold text-slate-800 mb-2">Didn’t find what you’re looking for? We’re just a message away.</h2>
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
          <a href="mailto:support@example.com" class="flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-full shadow transition-all focus:outline-none focus:ring-2 focus:ring-blue-200">
            <i class="fa-solid fa-envelope"></i> Email Us
          </a>
          <a href="#" class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-6 rounded-full shadow transition-all focus:outline-none focus:ring-2 focus:ring-green-200">
            <i class="fa-solid fa-comments"></i> Chat Now
          </a>
          <a href="tel:0761262001" class="flex items-center justify-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-6 rounded-full shadow transition-all focus:outline-none focus:ring-2 focus:ring-yellow-200">
            <i class="fa-solid fa-phone"></i> Call Us
          </a>
        </div>
      </div>
    </section>
  </main>
  <script src="assets/js/navbar-theme.js"></script>
  <script>
    // Accordion for FAQ
    document.querySelectorAll('.faq-toggle').forEach(btn => {
      btn.addEventListener('click', function() {
        const answer = document.getElementById(this.dataset.faq);
        const expanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !expanded);
        if (!expanded) {
          answer.classList.remove('hidden');
          answer.classList.add('animate-fadeIn');
        } else {
          answer.classList.add('hidden');
          answer.classList.remove('animate-fadeIn');
        }
      });
    });
    // Optional: Add fadeIn animation
    tailwind.config = {
      theme: {
        extend: {
          animation: {
            fadeIn: 'fadeIn 0.3s ease-in',
            blob1: 'blobMove1 18s ease-in-out infinite',
            blob2: 'blobMove2 22s ease-in-out infinite',
            blob3: 'blobMove3 20s ease-in-out infinite',
            blob4: 'blobMove4 25s ease-in-out infinite',
          },
          keyframes: {
            fadeIn: {
              '0%': { opacity: 0 },
              '100%': { opacity: 1 },
            },
            blobMove1: {
              '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
              '33%': { transform: 'translate(40px, -60px) scale(1.1)' },
              '66%': { transform: 'translate(-30px, 20px) scale(0.95)' },
            },
            blobMove2: {
              '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
              '33%': { transform: 'translate(-50px, 30px) scale(1.05)' },
              '66%': { transform: 'translate(20px, -40px) scale(0.9)' },
            },
            blobMove3: {
              '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
              '33%': { transform: 'translate(-30px, 40px) scale(1.1)' },
              '66%': { transform: 'translate(40px, -20px) scale(0.95)' },
            },
            blobMove4: {
              '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
              '33%': { transform: 'translate(30px, -30px) scale(1.08)' },
              '66%': { transform: 'translate(-40px, 30px) scale(0.92)' },
            },
          },
        }
      }
    }
    // Force light theme for the help page
    document.addEventListener('DOMContentLoaded', function() {
      if (window.navbarThemeController) {
        window.navbarThemeController.forceTheme('light');
      }
    });
  </script>
</body>
</html>

