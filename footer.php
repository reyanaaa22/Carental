<?php
// Include database connection
include_once('db.php');

// Handle form submission
$alert_message = null; // Initialize alert message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Validate email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Insert email into the database
        $stmt = $dbh->prepare("INSERT INTO subscribers (email) VALUES (:email)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $alert_message = "Thank you for subscribing!";
        } else {
            $alert_message = "Failed to subscribe. Please try again.";
        }
    } else {
        $alert_message = "Invalid email address.";
    }
}
?>

<!-- SVG WAVE ABOVE FOOTER -->
<div class="footer-wave">
  <svg viewBox="0 0 1440 120" width="100%" height="120" preserveAspectRatio="none" style="display:block;">
    <path d="M0,32 C360,120 1080,0 1440,80 L1440,120 L0,120 Z" fill="#222"/>
    <path d="M0,80 C480,0 960,160 1440,40 L1440,120 L0,120 Z" fill="url(#footerGradient)" fill-opacity="0.5"/>
    <defs>
      <linearGradient id="footerGradient" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" stop-color="#ff9800"/>
        <stop offset="100%" stop-color="#222"/>
      </linearGradient>
    </defs>
  </svg>
</div>

<footer style="background: #222; color: #fff; padding: 30px 20px; text-align: center;">
  <div style="max-width: 1200px; margin: auto; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;">
    <!-- Left Section -->
    <div style="flex: 1; min-width: 300px; text-align: left;">
      <h3>Ormoc Car Rental Service</h3>
      <p>&copy; 2025 Ormoc Car Rental. All rights reserved.</p>

      <div style="margin: 15px 0;">
        <p><strong>Phone:</strong> +63 9567833665</p>
        <p><strong>Email:</strong> contact@ormoccarrental.com</p>
        <p><strong>Location:</strong> Brgy. Cogon, Ormoc City, Leyte</p>
      </div>

      <div style="margin-top: 20px;">
        <a href="#"><img src="images/FB.jpg" alt="Facebook" style="height: 30px; margin: 0 10px;"></a>
        <a href="#"><img src="images/google.png" alt="Google" style="height: 30px; margin: 0 10px;"></a>
        <a href="#"><img src="images/linkedin-icon.png" alt="LinkedIn" style="height: 30px; margin: 0 10px;"></a>
      </div>
    </div>

    <!-- Newsletter Section -->
    <div style="flex: 1; min-width: 300px; text-align: left;">
      <h2 style="font-size: 1.9rem; color:orange; margin-bottom: 10px;">Subscribe to Our Newsletter</h2>
      <p style="margin-bottom: 15px;">Get the latest updates, special promos, and exclusive car rental deals straight to your inbox.</p>
      <form class="subscribe-form" action="" method="post" style="display: flex; flex-direction: column; gap: 10px;">
        <input type="email" name="email" placeholder="Enter your email address" required style="padding: 10px; border: none; border-radius: 5px; width: 100%; max-width: 300px;">
        <button type="submit" style="padding: 10px; background: orange; color: #fff; border: none; border-radius: 5px; cursor: pointer; max-width: 150px;">Subscribe</button>
      </form>
    </div>
  </div>
</footer>

<?php if ($alert_message): ?>
  <div style="background: #ffe0e0; color: #b30000; border: 1px solid #ffb3b3; padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; text-align:center;">
    <?= htmlspecialchars($alert_message) ?>
  </div>
<?php endif; ?>

<style>
/* ================= Footer ================= */
footer {
  background-color: #222;
  color: white;
  text-align: center;
  padding: 20px;
  font-size: 14px;
}
footer h3, footer h2 {
  color: #fff;
}
footer a img {
  transition: transform 0.3s ease;
}
footer a img:hover {
  transform: scale(1.1);
}

.footer-wave {
  line-height: 0;
  position: relative;
  top: 2px;
  background: transparent;
}
.footer-wave svg {
  display: block;
  width: 100%;
  height: 80px;
  margin-bottom: -5px;
}
@media (max-width: 700px) {
  .footer-wave svg { height: 40px; }
}
</style>