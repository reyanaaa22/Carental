<?php
// filepath: c:\xampp\htdocs\ormoc_carental\admin\subscriber.php
session_start();
include('includes/header.php');
include('../db.php');

// Fetch subscriber data
$stmt = $dbh->prepare("SELECT id, email, subscription_date FROM subscribers ORDER BY subscription_date DESC");
$stmt->execute();
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscribers</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <style>
        html, body {  
            height: 100%;  
            margin: 0;  
            overflow: hidden;  
        }  

        header {  
            position: fixed;  
            top: 0;  
            left: 0;  
            width: 100%;  
            height: 100px;  
            background-color: #f8f9fa;  
            z-index: 1000;  
        }  

        .sidebar {  
            position: fixed;  
            top: 100px;  
            left: 0;  
            width: 250px;  
            height: calc(100vh - 100px);  
            background-color: #f1f1f1;  
            overflow-y: auto;  
            padding: 15px;  
            z-index: 999;  
        }  

        .main-content {  
            position: absolute;  
            top: 100px;  
            left: 250px;  
            width: calc(100% - 250px);  
            height: calc(100vh - 100px);  
            padding: 20px;  
            overflow-y: auto;  
        }   
        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .table-container h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f4f4f4;
        }
        .action-btn {
            padding: 5px 10px;
            background-color: #e53935;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .action-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>

    <div class="main-content">
        <div class="table-container">
            <h2>Manage Subscribers</h2>
            <table id="subscribersTable" class="display">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email Id</th>
                        <th>Subscription Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($subscribers)): ?>
                        <?php foreach ($subscribers as $index => $subscriber): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($subscriber['email']) ?></td>
                                <td><?= htmlspecialchars($subscriber['subscription_date']) ?></td>
                                <td>
                                    <form method="post" action="delete_subscriber.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $subscriber['id'] ?>">
                                        <button type="submit" class="action-btn" onclick="return confirm('Are you sure you want to delete this subscriber?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No data available in table</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#subscribersTable').DataTable();
        });
    </script>
</body>
</html>