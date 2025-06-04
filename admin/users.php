<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8" />  
    <title>Registered Users</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />  
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
        .alert {
            padding: 12px;
            margin-top: 15px;
            border-radius: 4px;
            border: 1px solid transparent;
            font-size: 0.9rem;
        }

        .errorWrap {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .succWrap {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .alert strong {
            font-weight: 600;
        }
        </style>
</head>  
<body>  
<?php
include('includes/header.php');
include('includes/sidebar.php');
include('db.php');

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $dbh->prepare("DELETE FROM tblusers WHERE UserID = ?");
    if ($stmt->execute([$id])) {
        $success_message = "User deleted successfully";
    } else {
        $error_message = "Error deleting user";
    }
}

// Fetch users
$users = array(); // Initialize as empty array
try {
    $sql = "SELECT UserID, FullName, EmailId, ContactNumber, dob, address, DateRegistered FROM tblusers ORDER BY DateRegistered DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching users: " . $e->getMessage();
}
?>  

<div class="main-content">  
    <h4>Registered Users</h4>  

    <?php if (isset($error_message)) { ?>
        <div class="alert errorWrap">
            <strong>ERROR</strong>: <?php echo htmlentities($error_message); ?>
        </div>
    <?php } elseif (isset($success_message)) { ?>
        <div class="alert succWrap">
            <strong>SUCCESS</strong>: <?php echo htmlentities($success_message); ?>
        </div>
    <?php } ?>  
    <div class="card mt-3">  
        <div class="card-header bg-light">  
            <strong>REG USERS</strong>  
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
                        <th>Email</th>  
                        <th>Contact no</th>  
                        <th>DOB</th>  
                        <th>Address</th>  
                        <th>Reg Date</th>  
                        <th>Action</th>  
                    </tr>  
                </thead>  
                <tbody>  
                    <?php
                    $cnt = 1;
                    if ($users) {
                        foreach ($users as $row) {
                            echo "<tr>";
                            echo "<td>" . $cnt++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['FullName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['EmailId']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['ContactNumber']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['DateRegistered']) . "</td>";
                            echo '<td>';
                            echo '<a href="users.php?action=delete&id=' . $row['UserID'] . '" class="btn btn-sm btn-danger delete-user" title="Delete">Delete</a>';
                            echo '</td>';
                            echo "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="8" class="text-center">No users found.</td></tr>';
                    }
                    ?>
                </tbody>  
                <tfoot class="bg-light">  
                    <tr>  
                        <th>#</th>  
                        <th>Name</th>  
                        <th>Email</th>  
                        <th>Contact no</th>  
                        <th>DOB</th>  
                        <th>Address</th>  
                        <th>Reg Date</th>  
                    </tr>  
                </tfoot>  
            </table>  

            <div class="mt-2 small text-muted">  
                Showing <?php echo count($users); ?> of <?php echo count($users); ?> entries  
            </div>  

            <nav aria-label="Page navigation example">  
                <ul class="pagination pagination-sm justify-content-end mb-0">  
                    <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>  
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>  
                    <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>  
                </ul>  
            </nav>  
        </div>  
    </div>  
</div>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>