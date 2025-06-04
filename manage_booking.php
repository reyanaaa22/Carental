<?php
ob_start(); // Start output buffering

include('header.php'); // Ensure header.php includes session_start()
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['login']) || empty($_SESSION['login']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle booking cancellation
if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['id'])) {
    $bookingId = intval($_GET['id']);
    $userId = $_SESSION['user_id'];
    
    // Verify booking belongs to the user
    $stmtVerify = $dbh->prepare("SELECT id FROM bookings WHERE id = ? AND UserID = ? AND Status = 0");
    $stmtVerify->execute([$bookingId, $userId]);
    
    if ($stmtVerify->rowCount() > 0) {
        // Update status to 2 (Cancelled)
        $stmtCancel = $dbh->prepare("UPDATE bookings SET Status = 2 WHERE id = ? AND UserID = ?");
        $stmtCancel->execute([$bookingId, $userId]);
        
        // Redirect back to manage_booking.php
        header('Location: manage_booking.php?cancelled=1');
        exit;
    } else {
        header('Location: manage_booking.php?error=1');
        exit;
    }
}

// Get logged-in user info
$userId = $_SESSION['user_id'];
$fullName = $_SESSION['fname'] ?? '';

// Fetch user info (optional, for profile image, etc.)
$stmtUser = $dbh->prepare("SELECT * FROM tblusers WHERE UserID = :uid LIMIT 1");
$stmtUser->bindParam(':uid', $userId, PDO::PARAM_INT);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Fetch bookings for this user
$stmt = $dbh->prepare("
    SELECT b.*, v.vehicle_title, v.brand_name, v.image1
    FROM bookings b
    JOIN vehicles v ON b.VehicleId = v.id
    WHERE b.UserID = :uid
    ORDER BY b.PostingDate DESC
");
$stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {  
    font-family: Arial, sans-serif;  
    background-color: WHITE;  
    margin: 0;  
    padding: 0;  
    min-height: 100vh;
}  
        /* Styling for the profile and bookings section */
        .profile-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        .profile-header img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
        }
        .profile-header .profile-name {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .bookings-section h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        .booking-card {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 15px 0;
            border-bottom: 1px solid #ddd;
        }
        .booking-card img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .booking-info {
            flex: 1;
        }
        .booking-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .booking-dates, .booking-message {
            font-size: 1rem;
            margin-bottom: 5px;
        }
        .booking-status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }
        .status-confirmed {
            color: #15803d;
            background: #e6f4ea;
        }
        .status-pending {
            color: #b45309;
            background: #fef3c7;
        }
        .status-cancelled {
            color: #dc2626;
            background: #fee2e2;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <img src="<?= !empty($user['profile_image']) ? htmlentities($user['profile_image']) : 'default-profile.png' ?>" alt="Profile">
            <div>
                <div class="profile-name"><?= htmlentities($user['FullName'] ?? $fullName) ?></div>
            </div>
        </div>

        <!-- Bookings Section -->
        <div class="bookings-section">
            <h2>My Bookings</h2>
            <?php if (empty($bookings)): ?>
                <p>No bookings found.</p>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <?php
                        $image_full_path = __DIR__ . '/admin/uploads/' . basename($booking['image1']);
                        $imageUrl = !empty($booking['image1']) ? 'admin/uploads/' . basename($booking['image1']) : 'uploads/default-image.png';
                        $imagePath = file_exists($image_full_path) ? $imageUrl : 'uploads/default-image.png';
                    ?>
                    <div class="booking-card">
                        <img src="<?= htmlentities($imagePath) ?>" alt="Vehicle">
                        <div class="booking-info">
                            <div class="booking-title">
                                <?= htmlentities($booking['brand_name'] . ' , ' . $booking['vehicle_title']) ?>
                                <?php 
                                if ($booking['Status'] == 1) {
                                    echo '<span class="booking-status status-confirmed">Confirmed</span>';
                                } elseif ($booking['Status'] == 0) {
                                    echo '<span class="booking-status status-pending">Pending</span>';
                                    echo '<a href="manage_booking.php?action=cancel&id=' . $booking['id'] . '" class="text-danger ms-2" onclick="return confirm(\'Are you sure you want to cancel this booking?\')">Cancel</a>';
                                } else {
                                    echo '<span class="booking-status status-cancelled">Cancelled</span>';
                                }
                                ?>
                            </div>
                            <div class="booking-dates">
                                From: <?= htmlentities($booking['FromDate']) ?><br>
                                To: <?= htmlentities($booking['ToDate']) ?>
                            </div>
                            <div class="booking-message">
                                Message: <?= htmlentities($booking['message']) ?>
                            </div>
                            <div class="booking-actions mt-2">
                                <?php if ($booking['Status'] == 0): ?>
                                    <a href="admin/manage_bookings.php?action=approve&id=<?= $booking['id'] ?>" class="btn btn-success btn-sm me-2" onclick="return confirm('Are you sure you want to approve this booking?')">Approve</a>
                                    <a href="admin/manage_bookings.php?action=cancel&id=<?= $booking['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php include('footer.php'); ?>
