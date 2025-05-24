<?php
session_start();
include_once('../db.php');

// Mark all notifications as read
$dbh->query("UPDATE notifications SET is_read = 1 WHERE is_read = 0");

// Fetch all notifications (latest first)
$stmt = $dbh->query("SELECT * FROM notifications ORDER BY created_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .notif-list { max-width: 600px; margin: 40px auto; }
        .notif-item { background: #f9f9f9; border: 1px solid #ddd; margin-bottom: 10px; padding: 16px; border-radius: 6px; }
        .notif-time { color: #888; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="notif-list">
        <h2>Notifications</h2>
        <?php foreach ($notifications as $notif): ?>
            <div class="notif-item">
                <?= htmlentities($notif['message']) ?>
                <div class="notif-time"><?= htmlentities($notif['created_at']) ?></div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($notifications)): ?>
            <p>No notifications yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
