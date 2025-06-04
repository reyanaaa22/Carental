<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8" />  
    <title>Manage Contact Us Queries</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />  
    <style>
    .container {
        margin-left: 250px;
        margin-top: 150px;
        padding: 32px 24px;
        min-height: calc(100vh - 60px);
        background: #f5f5f5;
        box-sizing: border-box;
    }
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

        .alert {
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            background-color: #ffffff;
        }

        .errorWrap {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .succWrap {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
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
    $stmt = $dbh->prepare("DELETE FROM contact_messages WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success_message = "Message deleted successfully";
    } else {
        $error_message = "Error deleting message";
    }
}

// Fetch contact messages
$sql = "SELECT id, name, email, message, posting_date FROM contact_messages ORDER BY posting_date DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<<<<<<< HEAD
<div class="main-content">  
=======
<div class="container mt-10">  
>>>>>>> 57a14d4ef1856b1b796bd0ff4e37f94dbc2c91b4
    <h4>Manage Contact Us Queries</h4>  

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
            <strong>USER QUERIES</strong>  
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
                        <th>#</th>  
                        <th>Name</th>  
                        <th>Email</th>  
                        <th>Message</th>  
                        <th>Posting date</th>  
                        <th>Action</th>  
                    </tr>  
                </thead>  
                <tbody>  
                    <?php
                    if ($messages) {
                        $cnt = 1;
                        foreach ($messages as $row) {
                            echo "<tr>";
                            echo "<td>" . $cnt++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . nl2br(htmlspecialchars($row['message'])) . "</td>";
                            echo "<td>" . htmlspecialchars($row['posting_date']) . "</td>";
                            echo '<td>';
                            echo '<a href="contact.php?action=delete&id=' . $row['id'] . '" class="btn btn-sm btn-danger delete-message" title="Delete">Delete</a>';
                            echo '</td>';
                            echo "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="6" class="text-center">No data available in table</td></tr>';
                    }
                    ?>
                </tbody>  
                <tfoot class="bg-light">  
                    <tr>  
                        <th>#</th>  
                        <th>Name</th>  
                        <th>Email</th>  
                        <th>Message</th>  
                        <th>Posting date</th>  
                        <th>Action</th>  
                    </tr>  
                </tfoot>  
            </table>  

            <div class="mt-2 small text-muted">  
                Showing <?php echo count($messages); ?> of <?php echo count($messages); ?> entries  
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
    <script>
        // Add confirmation dialog for delete action
        document.querySelectorAll('.delete-message').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this message?')) {
                    e.preventDefault();
                }
            });
        });
    </script>

</body>  
</html>