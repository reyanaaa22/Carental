<?php  
// manage_brand.php  
include 'db.php';  
session_start(); // Ensure session is started

// Handle delete action
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = intval($_GET['id']);
    $admin_id = $_SESSION['admin_id']; // Ensure admin_id is stored in the session

    // Fetch the brand name before deleting (for logging purposes)
    $stmt = $conn->prepare("SELECT brand_name FROM brands WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $brand = $result->fetch_assoc();
    $brand_name = $brand['brand_name'] ?? 'Unknown';

    // Delete the brand
    $stmt = $conn->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Log the action in the activity_log table
        $action = "Deleted brand: $brand_name";
        $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
        $log_stmt->bind_param("is", $admin_id, $action);
        $log_stmt->execute();

        header("Location: manage_brand.php?status=deleted");
        exit();
    } else {
        echo "Error deleting brand.";
    }
}

// Fetch all brands  
$sql = "SELECT * FROM brands";  
$result = $conn->query($sql);  
?>  
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8" />  
    <title>Manage Brands</title>  
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
            height: 100px;
            z-index: 999;
            background-color: #f8f9fa;
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

        .ts-main-content {
            position: absolute;
            top: 100px;
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 100px);
            overflow-y: auto;
            padding: 20px;
            background-color: #ffffff;
        }

        .container-fluid {  
            padding: 20px;  
        }

        .card {  
            border-radius: 8px;  
        }

        .card-header {  
            background-color: #f8f9fa;  
            font-weight: bold;  
            color: #333;  
            text-align: center;  
        }

        .table {  
            width: 100%;  
            font-size: 14px;  
            margin: 0 auto;  
            border-radius: 8px;  
            border: 1px solid #ddd;  
            table-layout: fixed; /* This helps keep the columns aligned */
        }

        .table th, .table td {  
            padding: 5px 80px;  
        }

        .table th {  
            text-align: center;  
        }

        .table-hover tbody tr:hover {  
            background-color: #f1f1f1;  
        }

        .form-select, .form-control {  
            border-radius: 4px;  
            width: auto;  
        }

        .alert {  
            font-size: 14px;  
        }

        .btn {  
            font-size: 12px;  
            padding: 6px 12px;  
            border-radius: 4px;  
        }

        .btn-primary {  
            background-color: #004153;  
            border: 1px solid #004153;  
        }

        .btn-danger {  
            background-color: darkgreen;  
            border: 1px solid darkgreen;  
        }

        .btn-close {  
            font-size: 12px;  
        }

        .gap-2 {  
            gap: 10px;  
        }

        @media (max-width: 768px) {  
            .table th, .table td {  
                font-size: 12px;  
                padding: 8px;  
            }

            .form-select, .form-control {  
                width: 100%;  
            }
        }

        /* Fix sidebar */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px; /* Adjust sidebar width */
            height: 100vh; /* Full height */
            background-color: #f8f9fa;
            padding: 20px;
            overflow-y: auto; /* Prevent sidebar from scrolling */
        }

        /* Allow the table to scroll independently */
        .table-responsive {
            max-height: calc(100vh - 150px); /* Adjust based on your header size */
            overflow-y: auto;
        }
    </style>  
</head>  
<body>  
<?php include('includes/header.php');?>  

<div style="display: flex; min-height: 100vh;">  
    <?php include('includes/sidebar.php');?>  

    <div class="ts-main-content" style="flex: 1;">  
        <div class="container-fluid">  
            <h4>Manage Brands</h4>  

            <?php if (isset($_GET['status'])): ?>  
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">  
                    <?php  
                        if ($_GET['status'] === 'updated') echo "✅ Brand updated successfully!";  
                        if ($_GET['status'] === 'deleted') echo "🗑️ Brand deleted successfully!";  
                    ?>  
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>  
                </div>  
            <?php endif; ?>  

            <div class="card mt-3">  
                <div class="card-header bg-light">  
                    <strong>LISTED BRANDS</strong>  
                </div>  
                <div class="card-body p-3">  

                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">  
                        <div>  
                            Show   
                            <select id="entries" class="form-select form-select-sm d-inline-block" style="width: auto;">  
                                <option selected>5</option>  
                                <option>10</option>  
                                <option>50</option>  
                                <option>100</option>  
                            </select>   
                            entries  
                        </div>  
                        <div>  
                            Search: <input type="search" id="searchInput" class="form-control form-control-sm d-inline-block" style="width: auto;" />  
                        </div>  
                    </div>  

                    <div class="table-responsive">  
                        <table class="table table-bordered table-striped table-hover table-sm mb-0">  
                            <thead class="table-light">  
                                <tr>
                                <th style="width: 5%;">#</th>  
                                    <th>Brand Name</th>  
                                    <th>Creation Date</th>  
                                    <th>Updation Date</th>  
                                    <th>Action</th>  
                                </tr>  
                            </thead>  
                            <tbody>  
                                <?php if ($result->num_rows > 0): ?>  
                                    <?php $count = 1; while ($row = $result->fetch_assoc()): ?>  
                                        <tr>  
                                            <td><?php echo $count++; ?></td>  
                                            <td><?php echo htmlspecialchars($row['brand_name']); ?></td>  
                                            <td><?php echo $row['creation_date']; ?></td>  
                                            <td><?php echo $row['updation_date']; ?></td>  
                                            <td>  
                                                <a href="edit_brand.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>  
                                                <a href="manage_brand.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this brand?')">Delete</a>  
                                            </td>  
                                        </tr>  
                                    <?php endwhile; ?>  
                                <?php else: ?>  
                                    <tr>  
                                        <td colspan="5" class="text-center">No data available in table</td>  
                                    </tr>  
                                <?php endif; ?>  
                            </tbody>  
                            <tfoot class="bg-light">  
                                <tr>  
                                    <th>#</th>  
                                    <th>Brand Name</th>  
                                    <th>Creation Date</th>  
                                    <th>Updation Date</th>  
                                    <th>Action</th>  
                                </tr>  
                            </tfoot>  
                        </table>  
                    </div>  

                    <div class="mt-2 small text-muted">  
                        Showing 0 entries  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  
</div>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  

<script>  
document.addEventListener('DOMContentLoaded', function() {  
    const entriesSelect = document.getElementById('entries');  
    const searchInput = document.getElementById('searchInput');  
    const tableBody = document.querySelector('table tbody');  
    let allRows = Array.from(tableBody.querySelectorAll('tr'));  

    // Store all rows that have actual data (exclude the "No data available" row)  
    allRows = allRows.filter(r => !r.querySelector('td').classList.contains('text-center') || r.querySelectorAll('td').length > 1);  

    // Number of entries to show  
    let currentEntries = parseInt(entriesSelect.value);  

    function filterAndPaginate() {  
        const searchTerm = searchInput.value.toLowerCase();  

        // Filter rows by search term (search all columns except the action column)  
        let filteredRows = allRows.filter(row => {  
            const cells = row.querySelectorAll('td');  
            // Ignore 'Action' cell (last one)  
            for (let i = 1; i < cells.length -1; i++) {  
                if(cells[i].textContent.toLowerCase().includes(searchTerm)) {  
                    return true;  
                }  
            }  
            return false;  
        });  

        // Clear table body  
        tableBody.innerHTML = '';  

        if(filteredRows.length === 0) {  
            // No matching data found  
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center">No data matching your search</td></tr>`;  
            updateShowingText(0);  
            return;  
        }  

        // Display only the rows up to the current entries selected  
        const rowsToShow = filteredRows.slice(0, currentEntries);  

        rowsToShow.forEach(row => tableBody.appendChild(row));  

        updateShowingText(rowsToShow.length, filteredRows.length);  
    }  

    function updateShowingText(showing, totalFiltered = null) {  
        const showingText = document.querySelector('.mt-2.small.text-muted');  
        showingText.textContent = `Showing ${showing} entries` + (totalFiltered !== null ? ` of ${totalFiltered} total matching` : '');  
    }  

    // Event listeners  
    entriesSelect.addEventListener('change', function() {  
        currentEntries = parseInt(this.value);  
        filterAndPaginate();  
    });  

    searchInput.addEventListener('input', filterAndPaginate);  

    // Initial call to populate table with entries  
    filterAndPaginate();  
});  
</script>  

</body>  
</html>
