<?php  
include('db.php'); // Database connection
session_start(); // Ensure session is started

// Handle delete action
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = intval($_GET['id']);
    $admin_id = $_SESSION['admin_id']; // Ensure admin_id is stored in the session

    // Fetch the vehicle title before deleting (for logging purposes)
    $stmt = $conn->prepare("SELECT vehicle_title FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $vehicle_title = $vehicle['vehicle_title'] ?? 'Unknown';

    // Delete the vehicle
    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Log the action in the activity_log table
        $action = "Deleted vehicle: $vehicle_title";
        $log_stmt = $conn->prepare("INSERT INTO activity_log (admin_id, action) VALUES (?, ?)");
        $log_stmt->bind_param("is", $admin_id, $action);
        $log_stmt->execute();

        header("Location: manage_vehicles.php?status=deleted");
        exit();
    } else {
        echo "Error deleting vehicle.";
    }
}

// Fetch all vehicles
$sql = "SELECT id, vehicle_title, brand_name, price_per_day, fuel_type, model_year,  
               image1, image2, image3, image4, image5   
        FROM vehicles";  
$result = $conn->query($sql);  
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8" />  
    <title>Manage Vehicles</title>  
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

        img.vehicle-image {  
            width: 80px;  
            height: 50px;  
            object-fit: cover;  
        }  
        h4{
            font-size: 30px;
            font-weight: 20px;
        }
    </style>  
</head>  
<body>  

<?php include('includes/header.php'); ?>  
<?php include('includes/sidebar.php'); ?>  

<div class="main-content">  
    <h4>Manage Vehicles</h4>  

    <?php if (isset($_GET['status'])): ?>  
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">  
            <?php  
                if ($_GET['status'] === 'deleted') echo "🗑️ Vehicle deleted successfully!";  
            ?>  
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>  
        </div>  
    <?php endif; ?>  

    <div class="card mt-3">  
        <div class="card-header bg-light">  
            <strong>VEHICLE DETAILS</strong>  
        </div>  
        <div class="card-body p-3">  
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">  
                <div>  
                    Show  
                    <select id="entries" class="form-select form-select-sm d-inline-block" style="width: auto;">  
                        <option selected>5</option>  
                        <option>10</option>  
                        <option>25</option>  
                        <option>50</option>  
                        <option>100</option>  
                    </select>  
                    entries  
                </div>  
                <div>  
                    Search:  
                    <input type="search" class="form-control form-control-sm d-inline-block" style="width: auto;" />  
                </div>  
            </div>  

            <table class="table table-bordered table-striped table-hover table-sm mb-0">  
                <thead class="table-light">  
                    <tr>  
                        <th style="width: 5%;">#</th>  
                        <th>Vehicle Title</th>  
                        <th>Brand Name</th>  
                        <th>Price Per Day</th>  
                        <th>Fuel Type</th>  
                        <th>Model Year</th>  
                        <th>Image</th>  
                        <th>Action</th>  
                    </tr>  
                </thead>  
                <tbody>  
                <?php  
                if ($result->num_rows > 0) {  
                    $count = 1;  
                    while($row = $result->fetch_assoc()) {  
                        echo "<tr>";  
                        echo "<td>" . $count++ . "</td>";  
                        echo "<td>" . htmlspecialchars($row['vehicle_title']) . "</td>";  
                        echo "<td>" . htmlspecialchars($row['brand_name']) . "</td>";  
                        echo "<td>₱" . number_format($row['price_per_day'], 2) . "</td>";  
                        echo "<td>" . htmlspecialchars($row['fuel_type']) . "</td>";  
                        echo "<td>" . htmlspecialchars($row['model_year']) . "</td>";  
                        
                        // Find first valid image  
                        $imagePath = '';  
                        for ($i = 1; $i <= 5; $i++) {  
                            $imgField = 'image' . $i;  
                            if (!empty($row[$imgField]) && file_exists($row[$imgField])) {  
                                $imagePath = $row[$imgField];  
                                break;  
                            }  
                        }  

                        if ($imagePath) {  
                            echo "<td><img src='" . htmlspecialchars($imagePath) . "' alt='Vehicle Image' class='vehicle-image'></td>";  
                        } else {  
                            echo "<td>No Image</td>";  
                        }  

                        
                        // Action buttons  
                        echo "<td>  
                                <a href='edit_vehicle.php?id=" . intval($row['id']) . "' class='btn btn-sm btn-primary'>Edit</a>  
                                <a href='manage_vehicles.php?action=delete&id=" . intval($row['id']) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this vehicle?\")'>Delete</a>  
                              </td>";  

                        echo "</tr>";  
                    }  
                } else {  
                    echo "<tr><td colspan='9' class='text-center'>No vehicles found</td></tr>";  
                }  
                ?>  
                </tbody>  
                <tfoot class="bg-light">  
                    <tr>  
                        <th>#</th>  
                        <th>Vehicle Title</th>  
                        <th>Brand Name</th>  
                        <th>Price Per Day</th>  
                        <th>Fuel Type</th>  
                        <th>Model Year</th>  
                        <th>Image</th>  
                        <th>Action</th>  
                    </tr>  
                </tfoot>  
            </table>  

            <div class="mt-2 small text-muted">  
                Showing 0 to 0 of 0 entries  
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
document.addEventListener('DOMContentLoaded', function() {
    const entriesSelect = document.getElementById('entries');
    const searchInput = document.querySelector('input[type="search"]');
    const tableBody = document.querySelector('table tbody');
    let allRows = Array.from(tableBody.querySelectorAll('tr'));

    // Store all rows that have actual data (exclude the "No vehicles found" row)
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
            tableBody.innerHTML = `<tr><td colspan="9" class="text-center">No vehicles matching your search</td></tr>`;
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

<?php  
$conn->close();  
?>
