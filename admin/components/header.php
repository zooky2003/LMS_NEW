<?php
// This file has access to variables from the parent page like $page_title
// and session variables like $_SESSION['user_name'].
?>
<header class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="px-6 py-3 flex justify-between items-center">
        <h1 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-2 text-sm font-medium text-green-600">
                <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                System Online
            </div>

            <div class="relative" id="user-dropdown-container">
                <button id="user-menu-button" class="flex items-center gap-2 text-gray-700 hover:text-gray-900 rounded-full hover:bg-gray-100 p-1 pr-2 transition-colors">
                    <span class="h-8 w-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-sm">
                        <?php echo htmlspecialchars(strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1))); ?>
                    </span>
                    <span class="hidden sm:inline font-medium"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                    <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                </button>
                
                <div id="user-dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 hidden animate-fade-in-down">
                    <div class="px-4 py-2 border-b">
                        <p class="text-sm text-gray-500">Signed in as</p>
                        <p class="font-medium text-gray-800 truncate"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
                    </div>
                    <div class="py-1">
                        <a href="profile.php" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-circle fa-fw text-gray-400"></i> Profile
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog fa-fw text-gray-400"></i> Settings
                        </a>
                    </div>
                    <div class="py-1 border-t">
                        <a href="../logout.php" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt fa-fw"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
/* Simple animation for the dropdown */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-down {
    animation: fadeInDown 0.2s ease-out;
}
</style>

<script>
// Self-contained JavaScript to toggle the user dropdown menu
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('user-dropdown-container');
    if (!container) return;
    
    const menuButton = container.querySelector('#user-menu-button');
    const dropdownMenu = container.querySelector('#user-dropdown-menu');

    if (menuButton && dropdownMenu) {
        menuButton.addEventListener('click', function(event) {
            event.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
        });

        // Close the dropdown if the user clicks anywhere else on the page
        document.addEventListener('click', function(event) {
            if (!container.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }
});
</script>