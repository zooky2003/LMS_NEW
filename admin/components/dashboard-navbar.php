<?php
// Dashboard-specific navbar with profile dropdown
require_once __DIR__ . '/../../session_helper.php';
safe_session_start();

$isLoggedIn = is_logged_in();
$user = app_current_user();
$userName = $user['name'];
$userEmail = $user['email'];

$currentFile = basename($_SERVER['PHP_SELF']);
$activeSlug = $activeSlug ?? ($currentFile === 'dashboard.php' ? 'dashboard' : ($currentFile === 'classes.php' ? 'classes' : null));
?>
<div id="tw-navbar" class="sticky top-5 z-40 w-full">
  <div class="mx-auto w-full max-w-7xl px-4">
    <div id="tw-nav-body" class="relative flex items-center justify-between rounded-full border border-white/20 bg-white/10 px-4 py-2 shadow-[0_8px_24px_rgba(2,6,23,0.12)] backdrop-blur-md transition-all duration-300">
      <a href="index.php#home" class="relative z-20 mr-4 flex items-center space-x-2 px-2 py-1 text-sm font-medium text-white"><i class="fas fa-graduation-cap"></i><span>EduLearn</span></a>
      <nav class="absolute inset-0 z-0 hidden items-center justify-center lg:flex pointer-events-none">
        <div class="flex items-center gap-1 text-sm font-medium text-white/90">
          <?php $items = [['slug' => 'dashboard', 'name' => 'Dashboard', 'href' => 'dashboard.php'], ['slug' => 'classes', 'name' => 'Classes', 'href' => 'classes.php'], ['slug' => 'results', 'name' => 'Results', 'href' => '#results'], ['slug' => 'payments', 'name' => 'Payments', 'href' => '#payments']]; ?>
          <?php foreach ($items as $i => $item): $isActive = ($activeSlug ?? '') === $item['slug']; $linkClasses = 'group relative rounded-full px-4 py-2 transition-colors pointer-events-auto'; $linkClasses .= $isActive ? ' tw-active font-semibold' : ' text-white/90 hover:text-white'; ?>
            <a href="<?php echo htmlspecialchars($item['href']); ?>" data-slug="<?php echo htmlspecialchars($item['slug']); ?>" class="<?php echo $linkClasses; ?>"><span class="relative z-10"><?php echo htmlspecialchars($item['name']); ?></span><span class="absolute inset-0 rounded-full bg-white/10 opacity-0 transition-opacity group-hover:opacity-100"></span></a>
          <?php endforeach; ?>
        </div>
      </nav>
      <div class="relative z-20 hidden items-center gap-3 lg:flex">
        <?php if ($isLoggedIn): ?>
          <div class="relative" id="profileDropdown">
            <button id="profileButton" class="flex items-center gap-2 rounded-full px-3 py-2 text-sm font-medium text-white/90 transition-colors hover:bg-white/10 hover:text-white"><div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fas fa-user text-sm"></i></div><span><?php echo htmlspecialchars($userName); ?></span><i class="fas fa-chevron-down text-xs transition-transform" id="dropdownIcon"></i></button>
            <div id="profileDropdownMenu" class="absolute right-0 top-full mt-2 w-64 rounded-lg border border-white/10 bg-white/90 p-2 text-slate-800 shadow-[0_0_24px_rgba(34,42,53,0.06),_0_1px_1px_rgba(0,0,0,0.05),_0_0_0_1px_rgba(34,42,53,0.04),_0_0_4px_rgba(34,42,53,0.08),_0_16px_68px_rgba(47,48,55,0.05),_0_1px_0_rgba(255,255,255,0.1)_inset] hidden">
              <div class="px-3 py-2 border-b border-slate-200"><p class="font-semibold text-slate-900"><?php echo htmlspecialchars($userName); ?></p><p class="text-sm text-slate-600"><?php echo htmlspecialchars($userEmail); ?></p></div>
              <div class="py-1"><a href="profile.php" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors"><i class="fas fa-user-edit w-4"></i>Edit Profile</a><a href="#settings" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors"><i class="fas fa-cog w-4"></i>Settings</a><a href="#help" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors"><i class="fas fa-question-circle w-4"></i>Help & Support</a></div>
              <div class="border-t border-slate-200 pt-1"><a href="logout.php" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-sign-out-alt w-4"></i>Logout</a></div>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php" class="rounded-md px-4 py-2 text-sm font-bold text-white/90 transition hover:-translate-y-0.5 hover:text-white">Login</a>
          <a href="register.php" class="rounded-md border border-white/30 px-4 py-2 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-white/10">Register</a>
        <?php endif; ?>
      </div>
      <button id="twMobileToggle" class="relative z-20 ml-auto flex items-center justify-center rounded-md p-2 text-white lg:hidden"><i class="fas fa-bars text-lg" id="twMenuIcon"></i></button>
      <div id="twMobileMenu" class="absolute left-0 right-0 top-16 z-50 hidden flex-col gap-4 rounded-lg border border-white/10 bg-white/90 p-4 text-slate-800 shadow-[0_0_24px_rgba(34,42,53,0.06),_0_1px_1px_rgba(0,0,0,0.05),_0_0_0_1px_rgba(34,42,53,0.04),_0_0_4px_rgba(34,42,53,0.08),_0_16px_68px_rgba(47,48,55,0.05),_0_1px_0_rgba(255,255,255,0.1)_inset] lg:hidden">
        <?php foreach ($items as $item): $isActive = ($activeSlug ?? '') === $item['slug']; ?>
          <a href="<?php echo htmlspecialchars($item['href']); ?>" data-slug="<?php echo htmlspecialchars($item['slug']); ?>" class="rounded-md px-2 py-2 hover:bg-slate-100 <?php echo $isActive ? 'tw-active font-semibold' : ''; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
        <?php endforeach; ?>
        <?php if ($isLoggedIn): ?>
          <div class="border-t border-slate-200 pt-2 mt-2">
            <div class="px-2 py-1 text-sm text-slate-600"><p class="font-semibold"><?php echo htmlspecialchars($userName); ?></p><p class="text-xs"><?php echo htmlspecialchars($userEmail); ?></p></div>
            <a href="profile.php" class="flex items-center gap-2 rounded-md px-2 py-2 text-sm text-slate-700 hover:bg-slate-100"><i class="fas fa-user-edit w-4"></i>Edit Profile</a>
            <a href="logout.php" class="flex items-center gap-2 rounded-md px-2 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt w-4"></i>Logout</a>
          </div>
        <?php else: ?>
          <div class="mt-2 grid grid-cols-2 gap-3"><a href="login.php" class="rounded-md border border-slate-300 px-3 py-2 text-center text-sm font-semibold">Login</a><a href="register.php" class="rounded-md bg-slate-900 px-3 py-2 text-center text-sm font-semibold text-white">Register</a></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script>
// ... (script for navbar functionality) ...
</script>
<style>
/* ... (styles for navbar) ... */
</style>