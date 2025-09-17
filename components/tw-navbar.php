<?php
// Tailwind-based navbar for the landing page (no React)
// Requires Tailwind CDN (with preflight disabled) in the <head>.

// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>
<div id="tw-navbar" class="sticky top-5 z-40 w-full">
  <div class="mx-auto w-full max-w-7xl px-4">
    <div id="tw-nav-body"
         class="relative flex items-center justify-between rounded-full border border-white/20 bg-white/10 px-4 py-2 shadow-[0_8px_24px_rgba(2,6,23,0.12)] backdrop-blur-md transition-all duration-300">

      <!-- Logo -->
      <a href="index.php#home" class="relative z-20 mr-4 flex items-center space-x-2 px-2 py-1 text-sm font-medium text-white">
        <i class="fas fa-graduation-cap"></i>
        <span>NextOra</span>
      </a>

      <!-- Center Nav Items (desktop) -->
      <nav class="absolute inset-0 z-0 hidden items-center justify-center lg:flex pointer-events-none">
        <div class="flex items-center gap-1 text-sm font-medium text-white/90">
          <?php
            $items = [
              ['name' => 'Home', 'href' => '#home'],
              ['name' => 'Features', 'href' => '#features'],
              ['name' => 'Courses', 'href' => '#courses'],
              ['name' => 'Testimonials', 'href' => '#testimonials'],
              ['name' => 'About Us', 'href' => 'AboutUs.php'],
              ['name' => 'Contact', 'href' => '#contact'],
              ['name' => 'Help', 'href' => 'help.php'], // Added Help link
            ];
          ?>
          <?php foreach ($items as $i => $item): ?>
            <a href="<?php echo htmlspecialchars($item['href']); ?>"
               class="group relative rounded-full px-4 py-2 text-white/90 transition-colors hover:text-white pointer-events-auto">
              <span class="relative z-10"><?php echo htmlspecialchars($item['name']); ?></span>
              <span class="absolute inset-0 rounded-full bg-white/10 opacity-0 transition-opacity group-hover:opacity-100"></span>
            </a>
          <?php endforeach; ?>
        </div>
      </nav>

      <!-- Right buttons (desktop) -->
      <div class="relative z-20 hidden items-center gap-3 lg:flex">
        <?php if ($isLoggedIn): ?>
          <span class="text-sm text-white/90">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
          <a href="logout.php" class="rounded-md px-4 py-2 text-sm font-bold text-white/90 transition hover:-translate-y-0.5 hover:text-white">Logout</a>
          <a href="dashboard.php" class="rounded-md bg-white px-4 py-2 text-sm font-bold text-black shadow-[0_0_24px_rgba(34,42,53,0.06),_0_1px_1px_rgba(0,0,0,0.05),_0_0_0_1px_rgba(34,42,53,0.04),_0_0_4px_rgba(34,42,53,0.08),_0_16px_68px_rgba(47,48,55,0.05),_0_1px_0_rgba(255,255,255,0.1)_inset] transition hover:-translate-y-0.5">Dashboard</a>
        <?php else: ?>
          <a href="login.php" class="rounded-md px-4 py-2 text-sm font-bold text-white/90 transition hover:-translate-y-0.5 hover:text-white">Login</a>
          <a href="register.php" class="rounded-md border border-white/30 px-4 py-2 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-white/10">Register</a>
        <?php endif; ?>
      </div>

      <!-- Mobile toggle -->
      <button id="twMobileToggle" class="relative z-20 ml-auto flex items-center justify-center rounded-md p-2 text-white lg:hidden">
        <i class="fas fa-bars text-lg" id="twMenuIcon"></i>
      </button>

      <!-- Mobile menu -->
      <div id="twMobileMenu"
           class="absolute left-0 right-0 top-16 z-50 hidden flex-col gap-4 rounded-lg border border-white/10 bg-white/90 p-4 text-slate-800 shadow-[0_0_24px_rgba(34,42,53,0.06),_0_1px_1px_rgba(0,0,0,0.05),_0_0_0_1px_rgba(34,42,53,0.04),_0_0_4px_rgba(34,42,53,0.08),_0_16px_68px_rgba(47,48,55,0.05),_0_1px_0_rgba(255,255,255,0.1)_inset] lg:hidden">
        <?php foreach ($items as $item): ?>
          <a href="<?php echo htmlspecialchars($item['href']); ?>" class="rounded-md px-2 py-2 hover:bg-slate-100">
            <?php echo htmlspecialchars($item['name']); ?>
          </a>
        <?php endforeach; ?>
        <div class="mt-2 grid grid-cols-2 gap-3">
          <?php if ($isLoggedIn): ?>
            <a href="logout.php" class="rounded-md border border-slate-300 px-3 py-2 text-center text-sm font-semibold">Logout</a>
            <a href="dashboard.php" class="rounded-md bg-slate-900 px-3 py-2 text-center text-sm font-semibold text-white">Dashboard</a>
          <?php else: ?>
            <a href="login.php" class="rounded-md border border-slate-300 px-3 py-2 text-center text-sm font-semibold">Login</a>
            <a href="register.php" class="rounded-md bg-slate-900 px-3 py-2 text-center text-sm font-semibold text-white">Register</a>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
// Tailwind navbar scroll behavior and mobile toggle
(function() {
  const body = document.getElementById('tw-nav-body');
  const toggle = document.getElementById('twMobileToggle');
  const menu = document.getElementById('twMobileMenu');
  const menuIcon = document.getElementById('twMenuIcon');
  let isOpen = false;

  function setScrolled(scrolled) {
    if (!body) return;
    if (scrolled) {
      body.classList.add('translate-y-1');
      body.classList.add('bg-white/80','backdrop-blur-md');
      body.classList.remove('bg-white/10');
      // Make text dark when on light background
      body.classList.add('tw-light');
    } else {
      body.classList.remove('translate-y-1');
      body.classList.remove('bg-white/80');
      body.classList.add('bg-white/10');
      body.classList.add('backdrop-blur-md');
      body.classList.remove('tw-light');
    }
  }

  window.addEventListener('scroll', function() {
    const y = window.scrollY || window.pageYOffset;
    setScrolled(y > 100);
  }, { passive: true });

  if (toggle && menu) {
    toggle.addEventListener('click', function() {
      isOpen = !isOpen;
      menu.classList.toggle('hidden', !isOpen);
      if (menuIcon) {
        menuIcon.classList.toggle('fa-bars', !isOpen);
        menuIcon.classList.toggle('fa-xmark', isOpen);
      }
    });

    // Close mobile menu on link click
    menu.addEventListener('click', function(e) {
      const a = e.target.closest('a');
      if (a) {
        isOpen = false;
        menu.classList.add('hidden');
        if (menuIcon) { menuIcon.classList.add('fa-bars'); menuIcon.classList.remove('fa-xmark'); }
      }
    });
  }
})();
</script>

<style>
/* Helper to flip text color when scrolled/light */
#tw-nav-body.tw-light a,
#tw-nav-body.tw-light .fas,
#tw-nav-body.tw-light .nav-text {
  color: #111827 !important;
}
</style>
