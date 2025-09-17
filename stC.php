<?php
require_once __DIR__ . '/session_helper.php';
safe_session_start();
$cfg = require __DIR__ . '/config.php';
$site = $cfg['site_name'] ?? 'NextOra';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connect with Us — <?php echo htmlspecialchars($site); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Inter', 'Poppins', sans-serif;
      background: linear-gradient(135deg, #eef2ff 0%, #ffffff 100%);
    }
    .glass {
      background: rgba(255,255,255,0.65);
      box-shadow: 0 8px 32px 0 rgba(31,38,135,0.10);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 1.25rem;
      border: 1px solid rgba(255,255,255,0.18);
    }
    /* Navbar text color override for bright background */
    #tw-nav-body, #tw-nav-body a, #tw-nav-body .fas, #tw-nav-body .nav-text {
      color: #374151 !important;
    }
    #tw-nav-body a.tw-active { color: #6366f1 !important; }
  </style>
</head>
<body data-nav-theme="light">
  <!-- Decorative blobs -->
  <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute top-[-12%] left-[-10%] w-[420px] h-[420px] bg-indigo-200 opacity-60 rounded-full filter blur-3xl animate-blob1"></div>
    <div class="absolute top-[65%] left-[65%] w-[360px] h-[360px] bg-fuchsia-200 opacity-50 rounded-full filter blur-2xl animate-blob2"></div>
    <div class="absolute top-[30%] left-[75%] w-[300px] h-[300px] bg-cyan-100 opacity-40 rounded-full filter blur-2xl animate-blob3"></div>
    <div class="absolute top-[80%] left-[-8%] w-[300px] h-[300px] bg-amber-100 opacity-40 rounded-full filter blur-2xl animate-blob4"></div>
  </div>

  <?php include __DIR__ . '/components/dashboard-navbar.php'; ?>

  <main class="max-w-6xl mx-auto px-4 py-12 md:py-16 mt-20">
    <!-- Hero -->
    <section class="text-center mb-12">
      <div class="flex flex-col items-center gap-4">
        <span class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100">
          <i class="fa-solid fa-share-nodes text-indigo-500 text-4xl"></i>
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-800">Connect with <?php echo htmlspecialchars($site); ?></h1>
        <p class="mt-2 text-lg text-slate-600 max-w-2xl">Join our communities, chat with us instantly, or book a live session. Stay updated and never miss a learning opportunity.</p>
      </div>
    </section>

    <!-- Primary actions -->
    <section class="mb-14">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- YouTube -->
        <a href="https://www.youtube.com/@pasinduprageeshaathukorala" target="_blank" rel="noopener" class="glass p-7 flex flex-col items-center text-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
          <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-red-100 mb-3">
            <i class="fa-brands fa-youtube text-red-500 text-2xl"></i>
          </span>
          <h3 class="font-semibold text-lg text-slate-800 mb-1">YouTube Channel</h3>
          <p class="text-slate-500 text-sm mb-3">Watch lessons, tips, and exam strategies.</p>
          <span class="text-red-600 font-medium">Visit Channel <i class="fa fa-arrow-right ml-1"></i></span>
        </a>
        <!-- WhatsApp -->
        <a id="whatsapp-direct" href="https://wa.me/+94763830866?text=Hi%20I%20need%20help%20with%20<?php echo rawurlencode($site); ?>" target="_blank" rel="noopener" class="glass p-7 flex flex-col items-center text-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
          <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-3">
            <i class="fa-brands fa-whatsapp text-green-600 text-2xl"></i>
          </span>
          <h3 class="font-semibold text-lg text-slate-800 mb-1">WhatsApp Chat</h3>
          <p class="text-slate-500 text-sm mb-3">Connect instantly with our support team.</p>
          <span class="text-green-700 font-medium">Start Chat <i class="fa fa-arrow-right ml-1"></i></span>
        </a>
        <!-- Book Live Chat -->
        <button id="open-booking" type="button" class="glass p-7 flex flex-col items-center text-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl">
          <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-purple-100 mb-3">
            <i class="fa-solid fa-video text-purple-600 text-2xl"></i>
          </span>
          <h3 class="font-semibold text-lg text-slate-800 mb-1">Book a Live Chat</h3>
          <p class="text-slate-500 text-sm mb-3">Schedule a one-on-one session at your time.</p>
          <span class="text-purple-700 font-medium">Book Now <i class="fa fa-arrow-right ml-1"></i></span>
        </button>
      </div>
    </section>

    <!-- Telegram channels grid -->
    <section class="mb-16">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-2xl font-bold text-slate-800">Telegram Channels</h2>
        <a href="https://t.me/" target="_blank" rel="noopener" class="text-slate-500 hover:text-slate-700 text-sm">Explore Telegram <i class="fa fa-arrow-right ml-1"></i></a>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="https://t.me/NextOra_2025_revision" target="_blank" rel="noopener" class="glass p-6 hover:shadow-2xl hover:scale-[1.02] transition">
          <div class="flex items-center gap-4">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100">
              <i class="fa-brands fa-telegram text-indigo-500 text-xl"></i>
            </span>
            <div>
              <p class="text-sm text-slate-500">Live</p>
              <h3 class="text-slate-800 font-semibold">2025 Revision</h3>
            </div>
          </div>
        </a>
        <a href="https://t.me/NextOra_2026_theory" target="_blank" rel="noopener" class="glass p-6 hover:shadow-2xl hover:scale-[1.02] transition">
          <div class="flex items-center gap-4">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100">
              <i class="fa-brands fa-telegram text-emerald-500 text-xl"></i>
            </span>
            <div>
              <p class="text-sm text-slate-500">Theory</p>
              <h3 class="text-slate-800 font-semibold">2026 Theory</h3>
            </div>
          </div>
        </a>
        <a href="https://t.me/NextOra_2027_theory" target="_blank" rel="noopener" class="glass p-6 hover:shadow-2xl hover:scale-[1.02] transition">
          <div class="flex items-center gap-4">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-fuchsia-100">
              <i class="fa-brands fa-telegram text-fuchsia-500 text-xl"></i>
            </span>
            <div>
              <p class="text-sm text-slate-500">Theory</p>
              <h3 class="text-slate-800 font-semibold">2027 Theory</h3>
            </div>
          </div>
        </a>
      </div>
    </section>

    <!-- Secondary: contact options -->
    <section class="mb-10">
      <div class="glass p-8 text-center">
        <h2 class="text-xl font-semibold text-slate-800 mb-2">Prefer email or a call?</h2>
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
          <a href="mailto:<?php echo htmlspecialchars($cfg['contact_email'] ?? 'support@example.com'); ?>" class="flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-full shadow transition-all focus:outline-none focus:ring-2 focus:ring-blue-200">
            <i class="fa-solid fa-envelope"></i> Email Us
          </a>
          <a href="tel:0763830866" class="flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-medium py-3 px-6 rounded-full shadow transition-all focus:outline-none focus:ring-2 focus:ring-amber-200">
            <i class="fa-solid fa-phone"></i> Call Us
          </a>
        </div>
      </div>
    </section>
  </main>

  <!-- Booking Modal -->
  <div id="booking-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
    <div class="w-full max-w-lg glass p-6 relative">
      <button id="close-booking" type="button" class="absolute top-3 right-3 text-slate-400 hover:text-slate-600">
        <i class="fa fa-times text-xl"></i>
      </button>
      <h3 class="text-xl font-semibold text-slate-800 mb-1">Book a Live Chat</h3>
      <p class="text-slate-500 text-sm mb-5">Pick a time and tell us what you’d like to discuss. We’ll confirm on WhatsApp.</p>
      <form id="booking-form" class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-sm text-slate-600 mb-1">Your Name</label>
          <input id="bk-name" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-200" placeholder="Hemal Pramuditha">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-600 mb-1">Email</label>
            <input id="bk-email" type="email" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-200" placeholder="hpramuditha@example.com">
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Phone (WhatsApp)</label>
            <input id="bk-phone" type="tel" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-200" placeholder="+94 7X XXX XXXX">
          </div>
        </div>
        <div>
          <label class="block text-sm text-slate-600 mb-1">Preferred Date</label>
          <input id="bk-date" type="date" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-200">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-600 mb-1">Time</label>
            <input id="bk-time" type="time" required class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-200">
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Timezone</label>
            <select id="bk-tz" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-200">
              <option value="GMT+5:30" selected>GMT+5:30 (Sri Lanka)</option>
              <option value="GMT+5:45">GMT+5:45</option>
              <option value="GMT+6">GMT+6</option>
              <option value="GMT+0">GMT+0</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-600 mb-1">Topic</label>
            <input id="bk-topic" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-200" placeholder="e.g., 2025 Revision plan">
          </div>
          <div>
            <label class="block text-sm text-slate-600 mb-1">Course</label>
            <select id="bk-course" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-200">
              <option value="2025 Revision">2025 Revision</option>
              <option value="2026 Theory">2026 Theory</option>
              <option value="2027 Theory">2027 Theory</option>
            </select>
          </div>
        </div>
        <button type="submit" class="mt-2 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2.5 px-6 rounded-full shadow transition-all focus:outline-none focus:ring-2 focus:ring-purple-200">Request via WhatsApp</button>
      </form>
    </div>
  </div>

  <script src="assets/js/navbar-theme.js"></script>
  <script>
    // Tailwind animations
    tailwind.config = {
      theme: {
        extend: {
          animation: {
            blob1: 'blobMove1 18s ease-in-out infinite',
            blob2: 'blobMove2 22s ease-in-out infinite',
            blob3: 'blobMove3 20s ease-in-out infinite',
            blob4: 'blobMove4 25s ease-in-out infinite',
          },
          keyframes: {
            blobMove1: {
              '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
              '33%': { transform: 'translate(40px, -60px) scale(1.08)' },
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

    // Force light theme
    document.addEventListener('DOMContentLoaded', function() {
      if (window.navbarThemeController) {
        window.navbarThemeController.forceTheme('light');
      }
    });

    // Booking modal logic
    const modal = document.getElementById('booking-modal');
    const openBtn = document.getElementById('open-booking');
    const closeBtn = document.getElementById('close-booking');
    openBtn && openBtn.addEventListener('click', () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); });
    closeBtn && closeBtn.addEventListener('click', () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); });
    modal && modal.addEventListener('click', (e) => { if (e.target === modal) { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }});
    document.addEventListener('keydown', (e) => { if (!modal.classList.contains('hidden') && e.key === 'Escape') { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }});

    /*
     * Locked submission logic (commented out per request):
     *
     * // Prevent multiple requests in the same browser (simple session/local lock)
     * const SENT_KEY = 'booking_request_sent';
     * const markSent = () => { try { localStorage.setItem(SENT_KEY, '1'); } catch (_) {} };
     * const isSent = () => { try { return localStorage.getItem(SENT_KEY) === '1'; } catch (_) { return false; } };
     *
     * // If already sent, disable the open button and indicate state
     * const updateOpenButtonState = () => {
     *   if (!openBtn) return;
     *   if (isSent()) {
     *     openBtn.setAttribute('disabled', 'true');
     *     openBtn.classList.add('opacity-60', 'cursor-not-allowed');
     *     openBtn.querySelector('span.text-purple-700') && (openBtn.querySelector('span.text-purple-700').textContent = 'Request Sent');
     *   }
     * };
     * updateOpenButtonState();
     */

    // Submit booking via WhatsApp
    /*
     * Original locked submit handler (commented):
     * const bookingForm = document.getElementById('booking-form');
     * bookingForm && bookingForm.addEventListener('submit', function(e) {
     *   e.preventDefault();
     *   if (isSent() || this.dataset.sending === '1') { return; }
     *   this.dataset.sending = '1';
     *   const submitBtn = this.querySelector('button[type=\"submit\"]');
     *   if (submitBtn) { submitBtn.setAttribute('disabled', 'true'); submitBtn.classList.add('opacity-70', 'cursor-not-allowed'); submitBtn.textContent = 'Opening WhatsApp...'; }
     *   // ...build msg and open WhatsApp...
     *   markSent();
     *   if (submitBtn) { submitBtn.textContent = 'Request Sent'; }
     *   modal.classList.add('hidden');
     *   document.body.classList.remove('overflow-hidden');
     *   updateOpenButtonState();
     *   this.dataset.sending = '1';
     * });
     */

    // New unlocked submit handler (allows multiple requests)
    const bookingForm = document.getElementById('booking-form');
    bookingForm && bookingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      if (this.dataset.sending === '1') { return; }
      this.dataset.sending = '1';
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) { submitBtn.setAttribute('disabled', 'true'); submitBtn.classList.add('opacity-70', 'cursor-not-allowed'); submitBtn.textContent = 'Opening WhatsApp...'; }
      const name = document.getElementById('bk-name').value.trim();
      const email = document.getElementById('bk-email').value.trim();
      const phone = document.getElementById('bk-phone').value.trim();
      const date = document.getElementById('bk-date').value;
      const time = document.getElementById('bk-time').value;
      const tz = document.getElementById('bk-tz').value;
      const topic = document.getElementById('bk-topic').value.trim();
      const course = document.getElementById('bk-course').value;
      const msg = `Hello, I'd like to book a live chat.\nName: ${name}\nEmail: ${email}\nPhone: ${phone}\nDate: ${date}\nTime: ${time} (${tz})\nCourse: ${course}\nTopic: ${topic || 'General'}\nFrom: <?php echo addslashes($site); ?>`;
      const url = 'https://wa.me/94763830866?text=' + encodeURIComponent(msg);
      window.open(url, '_blank');
      
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
      setTimeout(() => {
        this.dataset.sending = '1';
        if (submitBtn) { submitBtn.removeAttribute('disabled'); submitBtn.classList.remove('opacity-70', 'cursor-not-allowed'); submitBtn.textContent = 'Request via WhatsApp'; }
      }, 800);
    });
  </script>
</body>
</html>


