<?php
// This file is included by the parent page (e.g., index.php, profile.php),
// so it has access to the `$page` variable to set the active state.
?>
<aside class="w-64 flex-shrink-0 flex-col bg-gray-800 text-gray-400 hidden md:flex">
    <div class="h-16 flex items-center px-4 border-b border-gray-700">
        <a href="index.php" class="flex items-center gap-3 text-white font-semibold">
            <i class="fas fa-graduation-cap text-xl text-blue-500"></i>
            <span>LMS Admin</span>
        </a>
    </div>

    <nav class="flex-grow p-4">
        <ul class="space-y-2">
            <li class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                <a href="index.php?page=dashboard" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors hover:bg-gray-700 hover:text-white">
                    <i class="fas fa-tachometer-alt fa-fw"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo ($page === 'students') ? 'active' : ''; ?>">
                <a href="index.php?page=students" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors hover:bg-gray-700 hover:text-white">
                    <i class="fas fa-users fa-fw"></i>
                    <span>Students</span>
                </a>
            </li>
            <li class="<?php echo ($page === 'classes') ? 'active' : ''; ?>">
                <a href="index.php?page=classes" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors hover:bg-gray-700 hover:text-white">
                    <i class="fas fa-chalkboard-teacher fa-fw"></i>
                    <span>Classes</span>
                </a>
            </li>
            <li class="<?php echo ($page === 'recordings') ? 'active' : ''; ?>">
                <a href="index.php?page=recordings" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors hover:bg-gray-700 hover:text-white">
                    <i class="fas fa-video fa-fw"></i>
                    <span>Recordings</span>
                </a>
            </li>
            <li class="<?php echo ($page === 'payments') ? 'active' : ''; ?>">
                <a href="index.php?page=payments" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors hover:bg-gray-700 hover:text-white">
                    <i class="fas fa-dollar-sign fa-fw"></i>
                    <span>Payments</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="p-4 border-t border-gray-700">
        <a href="../logout.php" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors hover:bg-gray-700 hover:text-white">
             <i class="fas fa-sign-out-alt fa-fw"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>