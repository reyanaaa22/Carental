<?php
if (!isset($_SESSION)) session_start();
include_once('../db.php');

// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'] ?? null;
$adminName = 'Admin'; // Default admin name
$adminPic = 'uploads/default-profile.png'; // Default profile picture

if ($admin_id) {
    $stmt = $dbh->prepare("SELECT first_name, profile_image FROM admin WHERE id = ?");
    $stmt->bindParam(1, $admin_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin) {
            $adminName = $admin['first_name'] ?? 'Admin';
            $adminPic = $admin['profile_image'] ?: 'uploads/default-profile.png';
        }
    } else {
        error_log("Error fetching admin details: " . implode(", ", $stmt->errorInfo()));
    }
}

// Notification count for unseen bookings
$notifCount = 0;
$stmt = $dbh->prepare("SELECT COUNT(*) FROM bookings WHERE is_seen_by_admin = 0");
if ($stmt->execute()) {
    $notifCount = $stmt->fetchColumn() ?: 0;
} else {
    error_log("Error fetching notification count: " . implode(", ", $stmt->errorInfo()));
}
?>
<header>
  <div class="header-left">
    <div class="hamburger" onclick="toggleSidebar()">☰</div>
  </div>
  <div class="header-right">
    <a href="notification.php" class="notification-icon" title="Notifications">&#128276;
      <?php if ($notifCount > 0): ?>
        <span class="notif-badge"><?= $notifCount ?></span>
      <?php endif; ?>
    </a>
    <div class="profile-container" onclick="toggleDropdown()">
      <img src="<?php echo htmlspecialchars($adminPic); ?>" alt="Admin Profile" />
      <span><?php echo htmlspecialchars($adminName); ?></span>
      <div class="dropdown-menu" id="profileDropdown">
        <a href="profile.php">Profile Settings</a>
        <a href="activity_log.php">Activity Log</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</header>

<script>
    // Toggle dropdown
    function toggleDropdown() {
      const dropdown = document.getElementById("profileDropdown");
      dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    window.addEventListener("click", function(e) {
      const profile = document.querySelector(".profile-container");
      const dropdown = document.getElementById("profileDropdown");
      if (!profile.contains(e.target)) {
        dropdown.style.display = "none";
      }
    });

    // Toggle sidebar collapse
    function toggleSidebar() {
      document.body.classList.toggle('collapsed');
    }
</script>

<style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      display: flex;
    }

    header {
      height: 60px;
      background-color: #004153;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 10px;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      padding-left: 250px;
      transition: left 0.3s;
      z-index: 1000;
    }

    .collapsed header {
      left: 70px;
      padding-left: 70px;
    }

    .header-left .hamburger {
      font-size: 24px;
      cursor: pointer;
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .notification-icon {
      font-size: 20px;
      cursor: pointer;
      position: relative;
    }
    .notif-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #e53935;
      color: #fff;
      border-radius: 50%;
      padding: 2px 7px;
      font-size: 0.8em;
      font-weight: bold;
    }

    .profile-container {
      display: flex;
      align-items: center;
      cursor: pointer;
      position: relative;
    }

    .profile-container img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      top: 50px;
      right: 0;
      background-color: white;
      color: black;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 180px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
      z-index: 1001;
    }

    .dropdown-menu a {
      display: block;
      padding: 10px;
      text-decoration: none;
      color: #004153;
    }

    .dropdown-menu a:hover {
      background-color: #f0f0f0;
    }
</style>
