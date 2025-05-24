<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8" />  
    <title>Manage Contact Us Queries</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />  
    <style>
    .container {
        margin-left: 250px;
        margin-top: 60px;
        padding: 32px 24px;
        min-height: calc(100vh - 60px);
        background: #f5f5f5;
        box-sizing: border-box;
    }
    </style>
</head>  
<body>  

<?php
include('includes/header.php');
include('includes/sidebar.php');
include('db.php');

// Fetch contact messages
$sql = "SELECT id, name, email, message, posting_date FROM contact_messages ORDER BY posting_date DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">  
    <h4>Manage Contact Us Queries</h4>  

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
                            echo '<td><button class="btn btn-sm btn-danger" disabled>Delete</button></td>';
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
</body>  
</html>