<?php
  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  require_once __DIR__ . '/../config.php';
  $recipient = $config['contact_email'] ?? 'you@example.com';
?>
    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-graduation-cap"></i>
                        <span>NextOra</span>
                    </div>
                    <p>Empowering learners worldwide with quality education and innovative learning experiences.</p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/share/15j1FTxBxW/"><i class="fab fa-facebook"></i></a>
                        <a href="https://x.com/hemza_gg?s=09"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/hemal-pramuditha-66248830b?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app"><i class="fab fa-linkedin"></i></a>
                        <a href="https://www.instagram.com/hemal_pramudith?igsh=OWt3M3Z4Zzh5OGE1"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php#home">Home</a></li>
                        <li><a href="index.php#courses">Courses</a></li>
                        <li><a href="index.php#features">Features</a></li>
                        <li><a href="AboutUs.php">About</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul>
                        <li><a href="help.php">Help Center</a></li>
                        <li><a href="form.php">Contact Us</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="help.php">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Newsletter</h3>
                    <p>Subscribe to get updates on new courses and features.</p>
                    <form class="newsletter-form" method="post" action="send_newsletter.php" onsubmit="return validateNewsletter(this)">
                        <input type="email" name="email" placeholder="Enter your email" required>
                        <button class="btn btn-primary" type="submit">Subscribe</button>
                    </form>
                    <small id="newsletterMsg" class="muted"></small>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> NextOra - By NSBM Team AG. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
    function validateNewsletter(form){
      var email = form.email.value.trim();
      var ok = /.+@.+\..+/.test(email);
      var el = document.getElementById('newsletterMsg');
      if(!ok){ el.textContent = 'Please enter a valid email.'; el.style.color = '#ef4444'; return false; }
      el.textContent = '';
      return true;
    }
    </script>


