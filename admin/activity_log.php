<?php
session_start();
include 'db.php'; // Ensure the correct path to db.php
include('includes/header.php');
include('includes/sidebar.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch activity logs
$sql = "SELECT activity_log.action, activity_log.timestamp, admin.first_name, admin.last_name 
        FROM activity_log 
        INNER JOIN admin ON activity_log.admin_id = admin.id 
        ORDER BY activity_log.timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
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
            height: 60px; /* Adjusted header height */
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
            top: 100px; /* height of the header */
            left: 0;
            width: 250px;
            height: calc(100vh - 100px);
            background-color: #f1f1f1;
            padding: 15px;
            overflow-y: auto;
        }

        .main-content {
            position: absolute;
            top: 100px;
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
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #004153;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="text-center">Admin Activity Log</h2>
    <table>
        <thead>
            <tr>
                <th>Admin Name</th>
                <th>Action</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['action']) ?></td>
                        <td><?= htmlspecialchars($row['timestamp']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">No activity logs found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>