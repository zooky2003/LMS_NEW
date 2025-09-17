<?php 
require_once __DIR__ . '/session_helper.php';
safe_session_start();
$cfg = require __DIR__ . '/config.php'; 
$site = $cfg['site_name'] ?? 'NextOra'; 

// No login requirement - allow access to payment page

// Get class details if class_id is provided
$class_id = $_GET['class_id'] ?? null;
$selected_class = null;

if ($class_id) {
    try {
        $class_stmt = $pdo->prepare("SELECT * FROM classes WHERE id = :id AND status = 'Active'");
        $class_stmt->execute([':id' => $class_id]);
        $selected_class = $class_stmt->fetch();
        
        if (!$selected_class) {
            header('Location: classes.php');
            exit();
        }
        
        // Check if user already has a pending payment or is enrolled (only if logged in)
        if (is_logged_in()) {
            $user_id = $_SESSION['user_id'];
            
            try {
                $existing_payment_stmt = $pdo->prepare("SELECT id FROM payments WHERE user_id = :user_id AND class_id = :class_id AND status = 'Pending'");
                $existing_payment_stmt->execute([':user_id' => $user_id, ':class_id' => $class_id]);
                
                $enrollment_stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = :user_id AND class_id = :class_id");
                $enrollment_stmt->execute([':user_id' => $user_id, ':class_id' => $class_id]);
                $enrollment = $enrollment_stmt->fetch();
                
                // Check if user has active enrollment
                $has_active_enrollment = false;
                if ($enrollment) {
                    $enrollment_status = isset($enrollment['status']) ? $enrollment['status'] : 'Active';
                    $has_active_enrollment = ($enrollment_status === 'Active');
                }
                
                if ($existing_payment_stmt->fetch() || $has_active_enrollment) {
                    header('Location: my-classes.php');
                    exit();
                }
            } catch (PDOException $e) {
                // Handle database errors gracefully - allow access to payment page
            }
        }
        
    } catch (PDOException $e) {
        header('Location: classes.php');
        exit();
    }
} else {
    header('Location: classes.php');
    exit();
}

// Handle success/error messages
$success_message = $_SESSION['payment_success'] ?? null;
$error_message = $_SESSION['payment_error'] ?? null;
unset($_SESSION['payment_success'], $_SESSION['payment_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment Management — <?php echo htmlspecialchars($site); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Tailwind CDN (preflight disabled to avoid global resets) -->
  <script>
    window.tailwind = window.tailwind || {};
    tailwind.config = { corePlugins: { preflight: false } };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body data-nav-theme="dark">
  <!-- Liquid Glass Background -->
  <div id="liquidBg" class="liquid-bg" aria-hidden="true"></div>
  
  <!-- Dashboard Navbar -->
  <?php 
    $activeSlug = 'payments';
    include __DIR__ . '/components/dashboard-navbar.php'; 
  ?>
  
  <style>
    /* Dashboard-only: force navbar text to gray */
    #tw-nav-body a,
    #tw-nav-body .fas,
    #tw-nav-body .nav-text,
    #tw-nav-body span {
      color: #9ca3af !important; /* gray-400 */
    }
    #tw-nav-body a:hover { color: #d1d5db !important; } /* gray-300 */
    #tw-nav-body a.tw-active,
    #tw-nav-body.tw-light a.tw-active { color: #8b5cf6 !important; } /* violet-500 */
    /* When navbar switches to light background on scroll */
    #tw-nav-body.tw-light a,
    #tw-nav-body.tw-light .fas,
    #tw-nav-body.tw-light .nav-text,
    #tw-nav-body.tw-light span {
      color: #4b5563 !important; /* gray-600 for contrast on light bg */
    }
    /* Hide legacy emoji symbols inside item-head */
    .item-head .item-symbol { display: none !important; }
  </style>

  <main class="container main">
    <!-- Page Header -->
    <section class="welcome-banner" id="payment-header">
      <div class="welcome-text">
        <div class="eyebrow">SECURE PAYMENT</div>
        <h1>Payment for <?php echo htmlspecialchars($selected_class['title']); ?></h1>
        <p class="text-white/90 mt-2"><?php echo htmlspecialchars($selected_class['description'] ?: 'Complete your course payment securely and get instant access to all materials'); ?></p>
        <div class="flex flex-wrap gap-2 mt-3">
          <div class="flex items-center gap-2 bg-white/10 rounded-full px-3 py-1 border border-white/10">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-100/80">
              <i data-lucide="shield-check" class="w-4 h-4 text-green-700"></i>
            </span>
            <span class="text-sm">Secure Payment</span>
          </div>
          <div class="flex items-center gap-2 bg-white/10 rounded-full px-3 py-1 border border-white/10">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100/80">
              <i data-lucide="clock" class="w-4 h-4 text-blue-700"></i>
            </span>
            <span class="text-sm">Quick Processing</span>
          </div>
        </div>
      </div>
      <div class="metrics">
        <div class="metric">
          <span class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-white/25">
            <i data-lucide="credit-card" class="w-5 h-5 text-white"></i>
          </span>
          <div class="metric-body">
            <div class="label">Amount</div>
            <div class="value">Rs. <?php echo number_format($selected_class['price'], 0); ?></div>
          </div>
        </div>
        <div class="metric">
          <span class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-white/25">
            <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
          </span>
          <div class="metric-body">
            <div class="label">Level</div>
            <div class="value"><?php echo ucfirst($selected_class['difficulty']); ?></div>
          </div>
        </div>
      </div>
    </section>

    <?php if ($error_message): ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
      <div class="flex items-center gap-2 text-red-700">
        <i class="fas fa-exclamation-circle"></i>
        <span><?php echo htmlspecialchars($error_message); ?></span>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
      <div class="flex items-center gap-2 text-green-700">
        <i class="fas fa-check-circle"></i>
        <span><?php echo htmlspecialchars($success_message); ?></span>
      </div>
    </div>
    <?php endif; ?>

    <!-- Payment Content -->
    <section class="bento-grid" style="grid-template-columns: 1fr 1fr; margin-top: 24px;">
      <!-- Bank Details Card -->
      <article class="bento-item" style="min-height: auto;">
        <div class="item-head">
          <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-100">
            <i data-lucide="building-2" class="w-5 h-5 text-blue-700"></i>
          </span>
        </div>
        <h3 class="item-title">Bank Details</h3>
        <p class="item-desc mb-4">Transfer payment to the following account</p>
        
        <!-- Bank Info -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-lg mb-4">
          <div class="flex items-center gap-3 mb-3">
            <i class="fas fa-university text-2xl"></i>
            <h4 class="text-lg font-bold">COMMERCIAL BANK</h4>
          </div>
        </div>
        
        <div class="space-y-3">
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Account Number</span>
              <button onclick="copyToClipboard('8009418020')" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-copy"></i>
              </button>
            </div>
            <p class="font-bold text-lg text-gray-900">8009418020</p>
          </div>
          
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <span class="text-sm text-gray-600">Account Name</span>
            <p class="font-semibold text-gray-900">Pasindu Prageeth Athukorala</p>
          </div>
          
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
            <span class="text-sm text-gray-600">Branch</span>
            <p class="font-semibold text-gray-900">Puttalam</p>
          </div>
        </div>
        
        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
          <div class="flex items-start gap-2">
            <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
            <div class="text-sm text-amber-800">
              <p class="font-semibold">Important:</p>
              <p>Please keep your payment receipt for verification purposes.</p>
            </div>
          </div>
        </div>
      </article>

      <!-- Upload Payment Slip Card -->
      <article class="bento-item" style="min-height: auto;">
        <div class="item-head">
          <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-green-100">
            <i data-lucide="upload" class="w-5 h-5 text-green-700"></i>
          </span>
        </div>
        <h3 class="item-title">Upload Payment Slip</h3>
        <p class="item-desc mb-4">Upload your payment receipt for verification</p>
        
        <form action="process_payment.php" method="POST" enctype="multipart/form-data" class="space-y-4">
          <!-- Hidden fields -->
          <input type="hidden" name="class_id" value="<?php echo $selected_class['id']; ?>">
          <input type="hidden" name="amount" value="<?php echo $selected_class['price']; ?>">
          
          <!-- Amount Display -->
          <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-4 rounded-lg text-center">
            <div class="text-sm opacity-90">Course Fee</div>
            <div class="text-2xl font-bold">Rs. <?php echo number_format($selected_class['price'], 0); ?></div>
          </div>
          
          <!-- Reference Number -->
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2" for="reference">
              Reference Number <span class="text-red-500">*</span>
            </label>
            <input type="text" id="reference" name="reference" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="Enter transaction reference number">
            <p class="text-xs text-gray-600 mt-1">
              <i class="fas fa-info-circle mr-1"></i>
              Enter Transaction ID, Trace ID, Reference Number, or TRN number from your slip
            </p>
          </div>

          <!-- File Upload -->
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2" for="slip">
              Payment Slip <span class="text-red-500">*</span>
            </label>
            <div id="uploadArea" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer">
              <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                  <i class="fas fa-cloud-upload-alt text-blue-600 text-xl"></i>
                </div>
                <p class="text-gray-700 font-medium mb-1">Click to upload payment slip</p>
                <p class="text-xs text-gray-500">JPG, PNG, PDF (Max 5MB)</p>
              </div>
              <input type="file" name="slip" id="slip" class="hidden" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
            <div id="fileInfo" class="hidden mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
              <div class="flex items-center gap-2 text-green-700">
                <i class="fas fa-check-circle"></i>
                <span id="fileName"></span>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition-all transform hover:scale-105 shadow-lg">
            <i class="fas fa-paper-plane mr-2"></i>
            Submit Payment Details
          </button>
        </form>

        <!-- Note -->
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
          <div class="flex items-start gap-2">
            <i class="fas fa-clock text-blue-600 mt-0.5"></i>
            <div class="text-sm text-blue-800">
              <p class="font-semibold">Processing Time:</p>
              <p>Your payment will be verified by admin within 24 hours and your account will be activated automatically.</p>
            </div>
          </div>
        </div>
      </article>
    </section>

    <!-- Payment Instructions -->
    <section class="bento-grid" style="grid-template-columns: 1fr; margin-top: 16px;">
      <article class="bento-item" style="min-height: auto;">
        <div class="item-head">
          <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-purple-100">
            <i data-lucide="list-checks" class="w-5 h-5 text-purple-700"></i>
          </span>
        </div>
        <h3 class="item-title">Payment Instructions</h3>
        <p class="item-desc mb-4">Follow these steps to complete your payment</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="text-center p-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-blue-700 font-bold text-lg">1</span>
            </div>
            <h4 class="font-semibold text-gray-900 mb-2">Transfer Money</h4>
            <p class="text-sm text-gray-600">Transfer Rs. <?php echo number_format($selected_class['price'], 0); ?> to the provided bank account using online banking or visit the branch</p>
          </div>
          
          <div class="text-center p-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-green-700 font-bold text-lg">2</span>
            </div>
            <h4 class="font-semibold text-gray-900 mb-2">Upload Receipt</h4>
            <p class="text-sm text-gray-600">Take a clear photo of your payment slip and upload it using the form above</p>
          </div>
          
          <div class="text-center p-4">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
              <span class="text-purple-700 font-bold text-lg">3</span>
            </div>
            <h4 class="font-semibold text-gray-900 mb-2">Get Access</h4>
            <p class="text-sm text-gray-600">Once verified, you'll receive instant access to all course materials and resources</p>
          </div>
        </div>
      </article>
    </section>
  </main>

  <!-- Footer -->
  <?php include 'components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <script src="script.js"></script>
  <script src="assets/js/dashboard.js"></script>
  <script src="assets/js/navbar-theme.js"></script>
  
  <script>
    // Initialize Lucide icons
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons();
    } else {
      document.addEventListener('DOMContentLoaded', function () {
        try { window.lucide && window.lucide.createIcons && window.lucide.createIcons(); } catch (e) {}
      });
    }

    // File upload functionality
    document.addEventListener('DOMContentLoaded', function() {
      const fileInput = document.getElementById('slip');
      const uploadArea = document.getElementById('uploadArea');
      const fileInfo = document.getElementById('fileInfo');
      const fileName = document.getElementById('fileName');

      if (fileInput && uploadArea) {
        uploadArea.addEventListener('click', () => {
          fileInput.click();
        });

        uploadArea.addEventListener('dragover', (e) => {
          e.preventDefault();
          uploadArea.classList.add('border-blue-400', 'bg-blue-50');
        });

        uploadArea.addEventListener('dragleave', (e) => {
          e.preventDefault();
          uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
        });

        uploadArea.addEventListener('drop', (e) => {
          e.preventDefault();
          uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
          const files = e.dataTransfer.files;
          if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
          }
        });

        fileInput.addEventListener('change', (e) => {
          if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
          }
        });
      }

      function handleFileSelect(file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        fileName.textContent = `${file.name} (${fileSize}MB)`;
        fileInfo.classList.remove('hidden');
        uploadArea.classList.add('border-green-400', 'bg-green-50');
      }
    });

    // Copy to clipboard function
    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-green-600"></i>';
        setTimeout(() => {
          button.innerHTML = originalIcon;
        }, 2000);
      });
    }

    // Force light theme for dashboard page
    document.addEventListener('DOMContentLoaded', function() {
      if (window.navbarThemeController) {
        window.navbarThemeController.forceTheme('light');
      }
    });
  </script>
</body>
</html>