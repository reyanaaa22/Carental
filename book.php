<?php
include('db.php');
session_start();

// Check login status
$isLoggedIn = isset($_SESSION['login']);

// Redirect if no vehicle selected
if (!isset($_GET['id'])) {
  echo "Invalid request: No vehicle ID provided.";
  exit;
}

$vehicleId = intval($_GET['id']);

// Fetch vehicle info
$stmt = $conn->prepare("SELECT vehicle_title, brand_name, price_per_day FROM vehicles WHERE id = ?");
$stmt->bind_param("i", $vehicleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Vehicle not found.";
  exit;
}

$vehicle = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Book a Vehicle</title>
  <style>
    body { font-family: Arial; padding: 40px; background: #f4f4f4; }
    .booking-form { background: #fff; padding: 30px; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.2); }
    .booking-form h2 { margin-bottom: 20px; }
    .booking-form input, .booking-form button { width: 100%; padding: 10px; margin-bottom: 15px; font-size: 16px; }
    .booking-form button { background: #28a745; color: white; border: none; cursor: pointer; }
    .booking-form button:hover { background: #218838; }
  </style>
</head>
<body>

<?php include('login.php'); ?>

<?php if (!$isLoggedIn): ?>
  <script>
    window.onload = function() {
      openModal(); // auto open login modal
    };
  </script>
<?php else: ?>
  <div class="booking-form">
    <h2>Book: <?= htmlspecialchars($vehicle['brand_name'] . ' - ' . $vehicle['vehicle_title']) ?></h2>
    <p><strong>Price per Day:</strong> ₱<?= number_format($vehicle['price_per_day'], 2) ?></p>

    <form action="confirm-booking.php" method="post">
      <input type="hidden" name="vehicle_id" value="<?= $vehicleId ?>">
      <label>Pickup Date</label>
      <input type="date" name="pickup_date" required>
      <label>Return Date</label>
      <input type="date" name="return_date" required>
      <button type="submit">Confirm Booking</button>
    </form>
  </div>
<?php endif; ?>

</body>
</html>
