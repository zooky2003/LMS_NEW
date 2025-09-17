<?php
// Reusable navbar component
// Usage:
//   $activePage = 'dashboard' | 'classes' | 'home'; // optional
//   include __DIR__ . '/components/navbar.php';

// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$activePage = $activePage ?? (basename($_SERVER['PHP_SELF']) === 'classes.php' ? 'classes' : (basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'dashboard' : 'home'));
$isLoggedIn = isset($_SESSION['user_id']);
?>
<div class="navbar-wrapper">
  <nav class="navbar" id="navbar">
    <!-- Desktop Navigation -->
    <div class="nav-body" id="navBody">
      <a href="index.php" class="navbar-logo">
        <i class="fa-solid fa-graduation-cap"></i>
        <span><?php echo htmlspecialchars(($site ?? 'EduLearn')); ?></span>
      </a>

      <div class="nav-items">
        <a href="index.php#home" class="nav-item <?php echo $activePage==='home' ? 'active' : '';?>" data-index="0">Home</a>
        <a href="index.php#features" class="nav-item" data-index="1">Features</a>
        <a href="index.php#courses" class="nav-item" data-index="2">Courses</a>
        <a href="index.php#testimonials" class="nav-item" data-index="3">Testimonials</a>
        <a href="AboutUs.php" class="nav-item" data-index="4">About Us</a>
        <a href="index.php#contact" class="nav-item" data-index="5">Contact</a>
        <div class="nav-hover-bg" id="navHoverBg"></div>
      </div>

      <div class="nav-buttons">
        <?php if ($isLoggedIn): ?>
          <span class="navbar-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
          <a href="logout.php" class="navbar-btn navbar-btn-secondary">Logout</a>
          <a href="dashboard.php" class="navbar-btn navbar-btn-primary">Dashboard</a>
        <?php else: ?>
          <a href="login.php" class="navbar-btn navbar-btn-secondary">Login</a>
          <a href="register.php" class="navbar-btn navbar-btn-outline">Register</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav">
      <div class="mobile-nav-header">
        <a href="index.php" class="navbar-logo">
          <i class="fa-solid fa-graduation-cap"></i>
          <span><?php echo htmlspecialchars(($site ?? 'EduLearn')); ?></span>
        </a>
        <button class="mobile-nav-toggle" id="mobileToggle">
          <i class="fa-solid fa-bars" id="menuIcon"></i>
        </button>
      </div>

      <div class="mobile-nav-menu" id="mobileMenu">
        <a href="index.php#home" class="mobile-nav-link">Home</a>
        <a href="index.php#features" class="mobile-nav-link">Features</a>
        <a href="index.php#courses" class="mobile-nav-link">Courses</a>
        <a href="index.php#testimonials" class="mobile-nav-link">Testimonials</a>
        <a href="AboutUs.php" class="mobile-nav-link">About Us</a>
        <a href="index.php#contact" class="mobile-nav-link">Contact</a>
        <div class="mobile-nav-buttons">
          <?php if ($isLoggedIn): ?>
            <span class="mobile-nav-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
            <a href="logout.php" class="navbar-btn navbar-btn-secondary mobile-btn">Logout</a>
            <a href="dashboard.php" class="navbar-btn navbar-btn-primary mobile-btn">Dashboard</a>
          <?php else: ?>
            <a href="login.php" class="navbar-btn navbar-btn-secondary mobile-btn">Login</a>
            <a href="register.php" class="navbar-btn navbar-btn-primary mobile-btn">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
  </div>

