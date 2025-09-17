<?php 
require_once __DIR__ . '/session_helper.php';
safe_session_start();
$cfg = require __DIR__ . '/config.php'; 
$site = $cfg['site_name'] ?? 'EduLearn'; 

// Redirect to login if not authenticated
require_login();

// --- DATA FOR DYNAMIC CONTENT ---
// This array holds the information for the Telegram channel cards.
$telegram_channels = [
    [
        'title' => 'ADVANCED LEVEL ICT 2027',
        'description' => 'Latest updates for 2027 A/L students',
        'link' => '#',
        'highlighted' => false
    ],
    [
        'title' => 'A/L ICT 2025 REVISION',
        'description' => 'Revision materials for 2025 A/L',
        'link' => '#',
        'highlighted' => false
    ],
    [
        'title' => 'ADVANCE LEVEL ICT 2026',
        'description' => 'Resources for 2026 A/L students',
        'link' => '#',
        'highlighted' => true // This card will have a different style
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Help & Support — <?php echo htmlspecialchars($site); ?></title>
  
  <!-- Styles from your dashboard for consistency -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="styles.css">
  
  <style>
    /* Base styles to match the help section's look */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc; /* A light slate/gray background */
    }
  </style>
</head>
<body data-nav-theme="light">
  
  <!-- Include your existing dashboard navbar -->
  <?php include __DIR__ . '/components/dashboard-navbar.php'; ?>

  <!-- Main Help & Support Content -->
  <main class="container mx-auto px-4 py-12 md:py-16 mt-20"> <!-- Added mt-20 to offset for the fixed navbar -->

    <!-- Section 1: Main Header -->
    <header class="text-center mb-12">
        <div class="flex justify-center items-center gap-4">
             <svg class="w-10 h-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 13-2.5 2.5c-.6.6-.5 1.7.2 2.3s1.7.5 2.3-.2L21 15"/><path d="m6 8 2.5-2.5c.6-.6.5-1.7-.2-2.3s-1.7-.5-2.3.2L3 6"/><path d="M14.5 6.5 12 9l-3 3 2.5 2.5"/><path d="m21.5 11.5-1.9-1.9c-.6-.6-.5-1.7.2-2.3s1.7-.5 2.3.2L22 8.4"/><path d="m4.5 17.5 1.9 1.9c.6.6.5 1.7-.2 2.3s-1.7.5-2.3-.2L4 20.6"/></svg>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-800">Help & Support</h1>
        </div>
        <p class="mt-3 text-lg text-slate-500">Connect with us on our social platforms</p>
    </header>

    <!-- Section 2: WhatsApp Support Card -->
    <section class="mb-16">
        <div class="max-w-4xl mx-auto bg-green-50/70 border border-green-200 rounded-2xl shadow-sm p-8 md:p-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-white p-4 rounded-full shadow-md">
                    <svg class="w-10 h-10 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M16.6 14.2c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.6.7-.8.9-.1.1-.3.1-.5 0-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5.1-.1.2-.2.4-.4.1-.1.2-.2.2-.4.1-.1.1-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.5-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2 1 2.4c.1.1 1.5 2.3 3.6 3.2.5.2.8.3 1.1.4.5.1 1 .1 1.3-.1.4-.2 1.2-1 1.4-1.9.2-.9.2-1.7.1-1.8s-.2-.3-.4-.4l-1.1-.5zM12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8z"/></svg>
                </div>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-800">WhatsApp Support</h2>
            <p class="mt-2 text-slate-600 max-w-lg mx-auto">For quick questions and direct support, connect with us instantly on WhatsApp</p>
            <a href="https://wa.me/+94752619155" target="_blank" class="inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:border-slate-300 transition-all text-slate-700 font-semibold py-3 px-6 rounded-full mt-6 shadow-sm">
                075-261-9155
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/><path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/></svg>
            </a>
            <div class="mt-6 flex justify-center items-center gap-2 text-sm text-green-700">
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                Available during working hours
            </div>
        </div>
    </section>

    <!-- Section 3: Follow Our Channel -->
    <section class="mb-16">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-slate-800">Follow Our Channel</h2>
            <p class="mt-2 text-lg text-slate-500">Stay updated with our latest educational content and tutorials</p>
        </div>
         <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-6 flex flex-col sm:flex-row items-center gap-6">
            <img src="https://placehold.co/80x80/e2e8f0/334155?text=Logo" alt="Channel Logo" class="w-20 h-20 rounded-full border-4 border-slate-100">
            <div class="flex-grow text-center sm:text-left">
                <h3 class="text-xl font-bold text-slate-800">Pasindu Prageesha Athukorala</h3>
                <p class="text-slate-500">@pasinduprageeshaathukorala</p>
                <p class="text-sm text-slate-500 mt-1">11.9K subscribers &middot; 135 videos</p>
            </div>
            <a href="#" class="w-full sm:w-auto flex-shrink-0 bg-red-600 hover:bg-red-700 transition-colors text-white font-semibold py-2.5 px-6 rounded-full">
                Subscribe
            </a>
        </div>
    </section>

    <!-- Section 4: Telegram Channels -->
    <section>
        <div class="text-center mb-10">
            <div class="flex justify-center items-center gap-3">
                <svg class="w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.88-2.34 11.1c-.15.7-.6.87-1.18.54l-3.6-2.65-1.74 1.67c-.2.2-.36.36-.72.36l.26-3.68L16.2 8.3c.36-.32-.08-.5-.52-.18L7.3 13.44l-3.52-1.1c-.68-.22-.7-1.08.13-1.6l12.1-4.63c.6-.23 1.12.14.93.87z"/></svg>
                <h2 class="text-3xl font-bold text-slate-800">Telegram Channels</h2>
            </div>
            <p class="mt-2 text-lg text-slate-500">Join our exclusive Telegram channels for updates, resources, and community discussions</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($telegram_channels as $channel): ?>
                <div class="card text-center p-8 rounded-2xl shadow-lg transition-all hover:shadow-xl hover:-translate-y-1
                    <?php echo $channel['highlighted'] ? 'bg-slate-50 border-2 border-blue-500' : 'bg-white'; ?>">
                    <div class="flex justify-center mb-5">
                        <div class="bg-blue-100 p-4 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.88-2.34 11.1c-.15.7-.6.87-1.18.54l-3.6-2.65-1.74 1.67c-.2.2-.36.36-.72.36l.26-3.68L16.2 8.3c.36-.32-.08-.5-.52-.18L7.3 13.44l-3.52-1.1c-.68-.22-.7-1.08.13-1.6l12.1-4.63c.6-.23 1.12.14.93.87z"/></svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2"><?php echo htmlspecialchars($channel['title']); ?></h3>
                    <p class="text-slate-500 mb-6"><?php echo htmlspecialchars($channel['description']); ?></p>
                    <a href="<?php echo htmlspecialchars($channel['link']); ?>" target="_blank" class="inline-flex items-center justify-center gap-2 w-full bg-blue-500 hover:bg-blue-600 transition-colors text-white font-semibold py-3 px-6 rounded-full">
                        Join Channel
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/><path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/></svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

  </main>
  
  <script src="assets/js/navbar-theme.js"></script>
  <script>
    // Force light theme for the help page to ensure proper text contrast
    document.addEventListener('DOMContentLoaded', function() {
      if (window.navbarThemeController) {
        window.navbarThemeController.forceTheme('light');
      }
    });
  </script>

</body>
</html>

