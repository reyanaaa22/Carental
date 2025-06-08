<?php
session_start();
include('db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch last 20 activities for this user
$stmt = $dbh->prepare("SELECT activity, log_time FROM users_activity_log WHERE user_id = :user_id ORDER BY log_time DESC LIMIT 20");
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Activity Log</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f9f9f9; 
            margin-top: -50px; 
            padding: 0; 
            padding-top: 80px; /* Add padding to account for the fixed header */
        }
        .container { 
            max-width: 700px; 
            margin: 40px auto; 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); 
            padding: 40px 32px 32px 32px; 
            position: relative; /* Add this */
            z-index: 1; /* Add this */
        }
        h2 { 
            margin-bottom: 24px; 
            color: #004153; 
            letter-spacing: 1px;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        h2 i {
            color: #004153;
            font-size: 24px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
            background: #fff;
        }
        th, td { 
            padding: 14px 18px; 
            border-bottom: 1px solid #eee; 
            text-align: left; 
            font-size: 1.05rem; 
        }
        th { 
            background: #004153; 
            color: #fff; 
            font-size: 1.1rem;
            font-weight: 600;
        }
        tr:last-child td { 
            border-bottom: none; 
        }
        tr:hover td { 
            background: #f4f8fb; 
            transition: background 0.2s; 
        }
        .back-btn {
            display: inline-block;
            margin-top: 24px;
            color: #fff;
            background: #004153;
            padding: 10px 22px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background: #022c43;
            color: #f4c542;
            transform: translateY(-2px);
        }
        /* Add styles for no activities message */
        .no-activities {
            text-align: center;
            padding: 30px;
            color: #fff;
            font-size: 1.1rem;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fa fa-list"></i> My Activity Log</h2>
    <?php if (empty($logs)): ?>
        <div class="no-activities">No activity found.</div>
    <?php else: ?>
        <table>
            <tr><th>Activity</th><th>Date & Time</th></tr>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlentities($log['activity']) ?></td>
                    <td><?= htmlentities($log['log_time']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <a href="index.php" class="back-btn">&larr; Back to Homepage</a>
</div>
</body>
</html>
