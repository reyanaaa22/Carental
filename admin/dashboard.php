<?php
include('db.php');

$bookings_count = 0;
$vehicles_count = 0;

// Fetch number of bookings
$result = $conn->query("SELECT COUNT(*) as count FROM bookings");
if ($result && $row = $result->fetch_assoc()) {
    $bookings_count = $row['count'];
}

// Fetch number of vehicles
$result = $conn->query("SELECT COUNT(*) as count FROM vehicles");
if ($result && $row = $result->fetch_assoc()) {
    $vehicles_count = $row['count'];
}

// Fetch booking status counts
$status_approved = 0;
$status_pending = 0;
$status_cancelled = 0;

// Adjust these values based on your actual status codes/values
$result = $conn->query("SELECT Status, COUNT(*) as count FROM bookings GROUP BY Status");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['Status'] == 1) { // Approved
            $status_approved = $row['count'];
        } elseif ($row['Status'] == 0) { // Pending
            $status_pending = $row['count'];
        } elseif ($row['Status'] == 2) { // Cancelled
            $status_cancelled = $row['count'];
        }
    }
}

// Calculate total bookings for Rent Status
$total = $status_approved + $status_pending + $status_cancelled;

// Helper function to calculate percentages
function percent($count, $total) {
    return $total > 0 ? round(($count / $total) * 100) : 0;
}

// Total Bookings
$result = $conn->query("SELECT COUNT(*) as count FROM bookings");
$total_bookings = ($result && $row = $result->fetch_assoc()) ? $row['count'] : 0;

// Registered Users
$result = $conn->query("SELECT COUNT(*) as count FROM tblusers");
$total_users = ($result && $row = $result->fetch_assoc()) ? $row['count'] : 0;

// Rented Cars (vehicles with at least one confirmed booking)
$result = $conn->query("SELECT COUNT(DISTINCT VehicleId) as count FROM bookings WHERE Status = 1");
$rented_cars = ($result && $row = $result->fetch_assoc()) ? $row['count'] : 0;

// Available Cars (vehicles not currently rented)
$result = $conn->query("SELECT COUNT(*) as count FROM vehicles WHERE id NOT IN (SELECT VehicleId FROM bookings WHERE Status = 1)");
$available_cars = ($result && $row = $result->fetch_assoc()) ? $row['count'] : 0;

// Bookings per month for the current year
$bookings_per_month = array_fill(1, 12, 0); // Initialize months 1-12 to 0

$result = $conn->query("
    SELECT MONTH(FromDate) as month, COUNT(*) as count
    FROM bookings
    WHERE YEAR(FromDate) = YEAR(CURDATE())
    GROUP BY MONTH(FromDate)
    ORDER BY month
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings_per_month[(int)$row['month']] = (int)$row['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .main-content, .ts-main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 40px 32px;
            background: #fff;
            min-height: calc(100vh - 60px);
            width: 100%;
            box-sizing: border-box;
            display: block;
            overflow-x: hidden;
        }
        .summary-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 8px #0001;
            padding: 24px;
            flex: 1;
            min-width: 0;
        }
        .card-title {
            font-size: 1rem;
            color: orange;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .card-value {
            font-size: 2rem;
            font-weight: bold;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-top: 24px;
        }
        .section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 8px #0001;
            padding: 24px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>

<?php include('includes/header.php');?>
<?php include('includes/sidebar.php');?>

<div class="main-content">
    <div class="summary-cards">
      <div class="card">
        <div class="card-title"><i class="fa fa-calendar-check"></i> Total Bookings</div>
        <div class="card-value"><?= $total_bookings ?></div>
      </div>
      <div class="card">
        <div class="card-title"><i class="fa fa-users"></i> Registered Users</div>
        <div class="card-value"><?= $total_users ?></div>
      </div>
      <div class="card">
        <div class="card-title"><i class="fa fa-car"></i> Rented Cars</div>
        <div class="card-value"><?= $rented_cars ?> Unit</div>
      </div>
      <div class="card">
        <div class="card-title"><i class="fa fa-car-side"></i> Available Cars</div>
        <div class="card-value"><?= $available_cars ?> Unit</div>
      </div>
    </div>

    <div class="dashboard-grid">
      <div>
        <div class="section">
          <div style="font-weight: bold; margin-bottom: 12px;">Bookings Overview</div>
          <canvas id="bookingsChart" height="60"></canvas>
        </div>
        <div class="section">
          <div style="font-weight: bold; margin-bottom: 25px; font-size: 25px; color: #004153;">Recent Activity</div>
          <ul style="list-style:none;padding:0;margin:0;">
          <?php
          $recent = $conn->query("SELECT b.*, u.FullName, v.vehicle_title, v.brand_name FROM bookings b LEFT JOIN tblusers u ON b.UserID = u.UserID LEFT JOIN vehicles v ON b.VehicleId = v.id ORDER BY b.PostingDate DESC LIMIT 5");
          if ($recent && $recent->num_rows > 0):
              while ($row = $recent->fetch_assoc()):
          ?>
            <li style="margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #eee;">
              <strong><?= htmlentities($row['FullName'] ?? 'Unknown') ?></strong> booked <strong><?= htmlentities(($row['brand_name'] ?? '') . ' ' . ($row['vehicle_title'] ?? '')) ?></strong><br>
              <span style="color:#888;font-size:0.95em;">on <?= htmlentities($row['PostingDate']) ?></span>
            </li>
          <?php endwhile; else: ?>
            <li>No recent activity.</li>
          <?php endif; ?>
          </ul>
        </div>
      </div>
      <div>
        <div class="section">
          <div style="font-weight: bold; margin-bottom: 12px;">Rent Status</div>
          <canvas id="statusChart" height="180"></canvas>
          <div style="margin-top: 16px;">
            <span style="color:#223; font-weight:500;">Approved</span> <?= percent($status_approved, $total) ?>%<br>
            <span style="color:#e53935; font-weight:500;">Pending</span> <?= percent($status_pending, $total) ?>%<br>
            <span style="color:#bbb; font-weight:500;">Cancelled</span> <?= percent($status_cancelled, $total) ?>%
          </div>
        </div>
      </div>
    </div>
</div>

<script>
const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
new Chart(bookingsCtx, {
  type: 'bar',
  data: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    datasets: [{
      label: 'Bookings',
      data: [
        <?= $bookings_per_month[1] ?>,
        <?= $bookings_per_month[2] ?>,
        <?= $bookings_per_month[3] ?>,
        <?= $bookings_per_month[4] ?>,
        <?= $bookings_per_month[5] ?>,
        <?= $bookings_per_month[6] ?>,
        <?= $bookings_per_month[7] ?>,
        <?= $bookings_per_month[8] ?>,
        <?= $bookings_per_month[9] ?>,
        <?= $bookings_per_month[10] ?>,
        <?= $bookings_per_month[11] ?>,
        <?= $bookings_per_month[12] ?>
      ],
      backgroundColor: '#1976d2'
    }]
  },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
  type: 'doughnut',
  data: {
    labels: ['Approved', 'Pending', 'Cancelled'],
    datasets: [{
      data: [
        <?= $status_approved ?>,
        <?= $status_pending ?>,
        <?= $status_cancelled ?>
      ],
      backgroundColor: ['#223', '#e53935', '#bbb']
    }]
  },
  options: { plugins: { legend: { display: false } } }
});
</script>

</body>
</html>
