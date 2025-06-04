<?php
session_start();
include_once('../db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Mark all unread notifications as read
$update_stmt = $dbh->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
$update_stmt->execute();

// Fetch all notifications (latest first)
$stmt = $dbh->prepare("SELECT * FROM notifications ORDER BY created_at DESC");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .main-content {
            position: absolute;
            top: 70px;
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 30px;
            background-color: #f8f9fa;
        }

        .notification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .notification-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .notification-title {
            color: #004153;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .notif-item {
            background: #fff;
            border: 1px solid #e0e0e0;
            margin-bottom: 15px;
            padding: 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .notif-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .notif-message {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .notif-details {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 14px;
        }

        .notif-details span {
            color: #004153;
            font-weight: 600;
        }

        .notif-time {
            color: #888;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .notif-time i {
            margin-right: 5px;
            font-size: 14px;
        }

        .no-notifications {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-size: 18px;
        }

        .no-notifications i {
            font-size: 48px;
            color: #004153;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Override header profile image size */
        header .profile-container img {
            width: 35px !important;
            height: 35px !important;
            border-width: 1px !important;
            margin-bottom: -1px;
            margin-left: 1px;
        }

        /* Align admin name with profile image */
        header .profile-container span {
            display: inline-block;
            vertical-align: middle;
            margin-top: 1px;
            margin-right: 17px;
            font-size: 16px;
        }

        header .profile-container {
            display: flex;
            align-items: center;
            height: 60px;
        }
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>
    <div class="main-content">
        <div class="notification-card">
            <div class="notification-header">
                <h2 class="notification-title">
                    <i class="fas fa-bell me-2"></i>Notifications
                </h2>
            </div>

            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notif): ?>
                    <div class="notif-item">
                        <div class="notif-message">
                            <?= htmlentities($notif['message']) ?>
                        </div>
                        <div class="notif-time">
                            <i class="far fa-clock"></i>
                            <?= date('F j, Y g:i A', strtotime($notif['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-notifications">
                    <i class="fas fa-bell-slash d-block"></i>
                    <p>No notifications yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
