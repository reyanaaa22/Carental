<?php
ob_start(); // Start output buffering
include('../db.php');
include('includes/header.php');
include('includes/sidebar.php');

session_start(); // Ensure session is started

// Handle confirm/cancel actions
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    $admin_id = $_SESSION['admin_id']; // Ensure admin_id is stored in the session

    if ($action === 'confirm') {
        $stmt = $dbh->prepare("UPDATE bookings SET Status = 1 WHERE id = ?");
        if ($stmt->execute([$id])) {
            // Log the action in the activity_log table
            $log_action = "Confirmed booking ID: $id";
            $log_stmt = $dbh->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
            $log_stmt->execute([$admin_id, $log_action]);
        }
    } elseif ($action === 'cancel') {
        $stmt = $dbh->prepare("UPDATE bookings SET Status = 0 WHERE id = ?");
        if ($stmt->execute([$id])) {
            // Log the action in the activity_log table
            $log_action = "Cancelled booking ID: $id";
            $log_stmt = $dbh->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
            $log_stmt->execute([$admin_id, $log_action]);
        }
    }

    header('Location: manage_bookings.php');
    exit();
}

// Fetch all bookings with user and vehicle info
$stmt = $dbh->prepare("
    SELECT b.*, u.FullName, v.vehicle_title, v.brand_name
    FROM bookings b
    LEFT JOIN tblusers u ON b.UserID = u.UserID
    LEFT JOIN vehicles v ON b.VehicleId = v.id
    ORDER BY b.PostingDate DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8" />  
    <title>Manage Bookings</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />  
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
            top: 70px; /* Matches the new header height */
            left: 0;
            width: 250px;
            height: calc(100vh - 70px); /* Adjusted for the new header height */
            background-color: #f1f1f1;
            padding: 15px;
            overflow-y: auto;
        }

        .main-content {
            position: absolute;
            top: 70px; /* Matches the new header height */
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 70px); /* Adjusted for the new header height */
            overflow-y: auto;
            padding: 20px;
            background-color: #ffffff;
        }

        table {
            margin-top: 20px; /* Optional: fine-tune the table's position */
        }
        h4 {
            margin-left: 0px; /* Same as sidebar width */
            margin-top: 30px;   /* Adjusted to leave space below the header */
            padding: 20px;
            font-size: 30px;
            overflow-y: auto;
        }
    </style>
</head>  
<body>  

<div class="main-content">  
    <h4>Manage Bookings</h4>  

    <div class="card mt-3">  
        <div class="card-header bg-light">  
            <strong>BOOKINGS INFO</strong>  
        </div>  
        <div class="card-body p-3">  
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">  
                <div>  
                    Show   
                    <select id="entries" class="form-select form-select-sm d-inline-block" style="width: auto;">  
                        <option selected>10</option>  
                        <option>25</option>  
                        <option>50</option>  
                        <option>100</option>  
                    </select>   
                    entries  
                </div>  
                <div>  
                    Search: <input type="search" class="form-control form-control-sm d-inline-block" style="width: auto;">  
                </div>  
            </div>  

            <table class="table table-bordered table-striped table-hover table-sm mb-0">  
                <thead class="table-light">  
                    <tr>  
                        <th style="width: 5%;">#</th>  
                        <th>Name</th>  
                        <th>Vehicle</th>  
                        <th>From Date</th>  
                        <th>To Date</th>  
                        <th>Message</th>  
                        <th>Status</th>  
                        <th>Posting date</th>  
                        <th>Action</th>  
                    </tr>  
                </thead>  
                <tbody>  
                    <?php if (empty($bookings)): ?>
                        <tr>  
                            <td colspan="9" class="text-center">No data available in table</td>  
                        </tr>  
                    <?php else: ?>
                        <?php $i = 1; foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlentities($booking['FullName'] ?? 'Unknown') ?></td>
                            <td><?= htmlentities(($booking['brand_name'] ?? '') . ' ' . ($booking['vehicle_title'] ?? '')) ?></td>
                            <td><?= htmlentities($booking['FromDate']) ?></td>
                            <td><?= htmlentities($booking['ToDate']) ?></td>
                            <td><?= htmlentities($booking['message']) ?></td>
                            <td>
                                <?php if ($booking['Status'] == 1): ?>
                                    <span class="badge bg-success">Confirmed</span>
                                <?php elseif ($booking['Status'] == 0): ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Not Confirmed</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlentities($booking['PostingDate']) ?></td>
                            <td>
                                <a href="manage_bookings.php?action=confirm&id=<?= $booking['id'] ?>" class="text-success">Confirm</a> /
                                <a href="manage_bookings.php?action=cancel&id=<?= $booking['id'] ?>" class="text-danger">Cancel</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>  
                <tfoot class="bg-light">  
                    <tr>  
                        <th>#</th>  
                        <th>Name</th>  
                        <th>Vehicle</th>  
                        <th>From Date</th>  
                        <th>To Date</th>  
                        <th>Message</th>  
                        <th>Status</th>  
                        <th>Posting date</th>  
                        <th>Action</th>  
                    </tr>  
                </tfoot>  
            </table>  

            <div class="mt-2 small text-muted">  
                Showing <?= count($bookings) ? '1 to ' . count($bookings) . ' of ' . count($bookings) : '0 to 0 of 0' ?> entries  
            </div>  

            <nav aria-label="Page navigation example">  
                <ul class="pagination pagination-sm justify-content-end mb-0">  
                    <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>  
                    <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>  
                </ul>  
            </nav>  
        </div>  
    </div>  
</div>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>

<?php
ob_end_flush(); // End output buffering
?>
