<?php
session_start();
include '../db.php'; // Ensure the correct path to db.php
include('includes/header.php');
include('includes/sidebar.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch only the current admin's activity logs
$sql = "SELECT activity_log.action, activity_log.timestamp, admin.first_name, admin.last_name 
        FROM activity_log 
        INNER JOIN admin ON activity_log.admin_id = admin.id 
        WHERE activity_log.admin_id = ?
        ORDER BY activity_log.timestamp DESC";

$stmt = $dbh->prepare($sql);
$stmt->execute([$admin_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get admin name for the title
$admin_sql = "SELECT first_name, last_name FROM admin WHERE id = ?";
$admin_stmt = $dbh->prepare($admin_sql);
$admin_stmt->execute([$admin_id]);
$admin = $admin_stmt->fetch(PDO::FETCH_ASSOC);
$admin_name = $admin ? htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Activity Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            z-index: 999;
            background-color: #004153;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            position: fixed;
            top: 100px;
            left: 0;
            width: 250px;
            height: calc(100vh - 100px);
            background-color: #f1f1f1;
            padding: 15px;
            overflow-y: auto;
        }

        .main-content {
            position: absolute;
            top: 80px;
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 100px);
            overflow-y: auto;
            padding: 20px;
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px 16px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #004153;
            color: #fff;
            font-weight: 600;
        }

        table tr:hover td {
            background-color: #f5f5f5;
        }

        .page-title {
            color: #004153;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i {
            font-size: 24px;
        }

        .no-activities {
            text-align: center;
            padding: 30px;
            color: #666;
            font-size: 16px;
            background: #f9f9f9;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="page-title">
        <i class="fas fa-history"></i>
        Activity Log - <?php echo $admin_name; ?>
    </h2>
    
    <?php if (empty($logs)): ?>
        <div class="no-activities">
            <i class="fas fa-info-circle"></i> No activities found.
        </div>
    <?php else: ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/yourfontawesomekit.js" crossorigin="anonymous"></script>
</body>
</html>