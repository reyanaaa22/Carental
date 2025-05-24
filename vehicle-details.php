<?php  
session_start();  
include('db.php');  

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fallback: If UserID is not set but login is, fetch UserID from DB and set it
if (!isset($_SESSION['UserID']) && isset($_SESSION['login'])) {
    $email = $_SESSION['login'];
    $stmt = $dbh->prepare('SELECT UserID FROM tblusers WHERE EmailId = :email LIMIT 1');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['UserID'])) {
        $_SESSION['UserID'] = $row['UserID'];
    }
}

if (!isset($_GET['vhid']) || empty($_GET['vhid'])) {  
    echo "Invalid vehicle ID.";  
    exit;  
}  

$vhid = intval($_GET['vhid']);  
$sql = "SELECT * FROM vehicles WHERE id = :vhid";  
$query = $dbh->prepare($sql);  
$query->bindParam(':vhid', $vhid, PDO::PARAM_INT);  
$query->execute();  
$result = $query->fetch(PDO::FETCH_OBJ);  

if (!$result) {  
    echo "Vehicle not found.";  
    exit;  
}  

$accessoriesList = [  
    "Air Conditioner",  
    "Power Door Locks",  
    "AntiLock Braking System",  
    "Brake Assist",  
    "Power Steering",  
    "Driver Airbag",  
    "Passenger Airbag",  
    "Power Windows",  
    "CD Player",  
    "Central Locking",  
    "Crash Sensor",  
    "Leather Seats"  
];  

 
$vehicleAccessories = !empty($result->accessories) ? array_map('trim', explode(',', $result->accessories)) : [];  

$images = [];  
for ($i = 1; $i <= 5; $i++) {  
    $imageField = 'image' . $i;  
    $imgPath = !empty($result->$imageField) ? 'admin/uploads/' . basename($result->$imageField) : '';  
    $fullPath = __DIR__ . '/' . $imgPath;  
    if (!empty($imgPath) && file_exists($fullPath)) {  
        $images[] = $imgPath;  
    }  
}  

// Show success or error message if redirected after booking
if (isset($_GET['booked']) && $_GET['booked'] == 1) {
    echo '<div style="color:green;font-weight:bold;">Booking successful!</div>';
}
if (isset($_GET['error'])) {
    $errorMsg = 'Booking failed. Please try again.';
    if ($_GET['error'] == 1) $errorMsg = 'Booking failed: All fields are required.';
    if ($_GET['error'] == 2) $errorMsg = 'Booking failed: Database error.';
    if ($_GET['error'] == 3) $errorMsg = 'Booking failed: Invalid session or method.';
    echo '<div style="color:red;font-weight:bold;">' . $errorMsg . '</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['login'])) {
    $fromdate = isset($_POST['fromdate']) ? $_POST['fromdate'] : '';
    $todate = isset($_POST['todate']) ? $_POST['todate'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    $useremail = $_SESSION['login'];
    $status = 0;
    $vhid = isset($_GET['vhid']) ? $_GET['vhid'] : (isset($_POST['vhid']) ? $_POST['vhid'] : null);

    if ($vhid) {
        $sql = "INSERT INTO bookings (UserID, VehicleId, FromDate, ToDate, message, Status) 
                VALUES (:UserID, :VehicleId, :FromDate, :ToDate, :message, :Status)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':UserID', $_SESSION['UserID'], PDO::PARAM_INT);
        $query->bindParam(':VehicleId', $vhid, PDO::PARAM_INT);
        $query->bindParam(':FromDate', $fromdate, PDO::PARAM_STR);
        $query->bindParam(':ToDate', $todate, PDO::PARAM_STR);
        $query->bindParam(':message', $message, PDO::PARAM_STR);
        $query->bindParam(':Status', $status, PDO::PARAM_INT);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            echo "<script>alert('Booking successful.');</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again');</script>";
        }
    } else {
        echo "<script>alert('User not found or invalid vehicle. Please log in again or try from a valid vehicle page.');</script>";
    }
}

?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8" />  
<title><?php echo htmlentities($result->brand_name . ' ' . $result->vehicle_title); ?></title>  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />  
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<style>  
body {  
    font-family: Arial, sans-serif;  
    background-color: WHITE;  
    margin: 0;  
    padding: 0;  
    min-height: 100vh;
}  

.main-white-container {
    background: #fff;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    border-radius: 0;
    box-shadow: none;
    padding: 40px 40px 40px 40px;
    box-sizing: border-box;
}

.slider-container {
    position: relative;
    width: 100%;
    max-width: 900px;
    height: 400px;
    margin: 0 auto 20px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.vehicle-details-wrapper {
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 30px;
    justify-content: space-between;
    background: none;
    box-shadow: none;
    align-items: flex-start;
}

.vehicle-details-container {
    flex: 1 1 65%;
    background: none;
    padding: 0 40px 0 0;
    box-shadow: none;
}

.sidebar-booking {
    flex: 1 1 30%;
    background: none;
    padding: 0;
    box-shadow: none;
    display: flex;
    flex-direction: column;
    gap: 25px;
    align-items: flex-end;
}

/* Header and price on top */  
.header-top {  
    display: flex;  
    justify-content: space-between;  
    align-items: flex-start;  
    margin-bottom: 25px;  
}  

.header-top h1 {  
    font-weight: 900;  
    font-size: 40px;  
    text-transform: lowercase;  
    margin: 0;
    line-height: 1.2;  
}  

.price-box-top {
    color: #f97316;
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 10px 0;
    text-align: right;
    white-space: nowrap;
}
.price-box-top small {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    display: block;
    font-weight: 400;
    margin-top: 2px;
}

/* Info boxes under header */  
.info-boxes {  
    display: flex;  
    gap: 20px;  
    margin-bottom: 25px;  
}  

.info-box {  
    background: white;  
    border: 1px solid #ddd;  
    border-radius: 4px;  
    padding: 10px 10px;  
    flex: 1;  
    text-align: center;  
    font-weight: 600;  
    color: #004153;  
    position: relative;  
    font-size: 15px;  
}  

.info-box i {  
    font-size: 30px;  
    color: orange;  
    display: block;  
    margin-bottom: 6px;  
}  

.info-box span {  
    display: block;  
    font-weight: 700;  
    font-size: 18px;  
    color: #222;  
    margin-top: 2px;  
}  


/* Tabs */  
.tab-buttons {  
    display: flex;  
    justify-content: flex-start;  
    gap: 5px;  
    margin-top: 30px;  
    border-bottom: 2px solid #ddd;  
}  

.tab-button {  
    background-color: transparent;  
    color: #555;  
    border: none;  
    padding: 12px 20px;  
    cursor: pointer;  
    font-weight: 600;  
    font-size: 16px;  
    border-bottom: 4px solid transparent;  
    transition: all 0.3s ease;  
    border-radius: 0;  
}  

.tab-button.active {  
    border-bottom: 4px solid #f97316; /* orange */  
    color: #f97316;  
    font-weight: 700;  
}  

.tab-content {  
    margin-top: 20px;  
    display: none;  
    font-size: 16px;  
    line-height: 1.5;  
}  

.tab-content.active {  
    display: block;  
}  

/* Accessories table */  
.accessories-table {  
    width: 100%;  
    border-collapse: collapse;  
    margin-top: 10px;  
}  

.accessories-table thead tr {  
    background-color: #f7f7f7;  
    border-bottom: 1px solid #ddd;  
}  

.accessories-table, th, td {  
    border: 1px solid #ddd;  
}  

th, td {  
    padding: 14px 20px;  
    text-align: left;  
    font-weight: 600;  
    font-size: 16px;  
}  

th {  
    font-weight: 700;  
    background-color: #f4f4f4;  
}  

.checkmark {  
    color: #22c55e; /* green */  
    font-size: 22px;  
    font-weight: 700;  
    display: inline-block;  
    vertical-align: middle;  
}  

.crossmark {  
    color: #ef4444; /* red */  
    font-size: 22px;  
    font-weight: 700;  
    display: inline-block;  
    vertical-align: middle;  
}  


/* Slider */  
.slider-image {  
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 8px;
    display: none;
}  

.slider-image.active {  
    display: block;  
}  

.slider-btn {  
    position: absolute;  
    top: 50%;  
    transform: translateY(-50%);  
    background: rgba(0, 0, 0, 0.5);  
    color: white;  
    border: none;  
    padding: 10px;  
    cursor: pointer;  
    border-radius: 50%;  
}  

.slider-btn.left {  
    left: 10px;  
}  

.slider-btn.right {  
    right: 10px;  
}  


/* Sidebar: Share block */  
.share-block {  
    background: #111;  
    color: #eee;  
    font-size: 14px;  
    padding: 10px 15px;  
    border-radius: 3px;  
    font-weight: 600;  
    margin-bottom: 20px;
}  

.share-block span {  
    margin-right: 9px;  
}  

.share-block a {  
    color: #eee;  
    margin-right: 10px;  
    font-size: 16px;  
    text-decoration: none;  
    display: inline-block;  
    vertical-align: middle;  
    transition: color 0.3s ease;  
}  

.share-block a:hover {  
    color: #f97316;  
}  

.booking-form h4 {  
    margin-top: 0;  
    font-weight: 700;  
    font-size: 25px;  
    color: #f97316;  
    display: flex;  
    align-items: center;  
    gap: 7px;  
}  

.booking-form form {  
    display: flex;  
    flex-direction: column;  
    gap: 15px;  
}  

.booking-form input[type="text"],
.booking-form textarea,
.booking-form input[type="submit"] {
    box-sizing: border-box;
}

.booking-form textarea {  
    height: 90px;  
}  

.booking-form input[type="submit"] {  
    background-color: #f97316;  
    color: white;  
    border: none;  
    padding: 10px 15px;  
    font-weight: 700;  
    cursor: pointer;  
    text-transform: uppercase;  
    font-size: 14px;  
    border-radius: 3px;  
    transition: background-color 0.3s ease;  
}  

.booking-form input[type="submit"]:hover {  
    background-color: #d46b0c;  
}  

/* Responsive */  
@media (max-width: 960px) {  
  .vehicle-details-wrapper {  
    flex-direction: column;  
    padding: 20px;  
  }  
  .vehicle-details-container, .sidebar-booking {  
    flex: none;  
    width: 100%;  
  }  
}  
</style>  
</head>  
<body>  

<?php include('header.php'); ?>  

<div class="main-white-container">
    <!-- Image Slider -->  
    <div class="slider-container">  
        <?php foreach (
            $images as $index => $img): ?>  
            <img src="<?php echo htmlentities($img); ?>" class="slider-image <?php echo $index === 0 ? 'active' : ''; ?>" alt="Vehicle Image <?php echo $index + 1; ?>">  
        <?php endforeach; ?>  
        <?php if (count($images) > 1): ?>  
            <button class="slider-btn left" onclick="changeSlide(-1)">❮</button>  
            <button class="slider-btn right" onclick="changeSlide(1)">❯</button>  
        <?php endif; ?>  
    </div>  

    <div class="vehicle-details-wrapper">  
        <!-- Left side: Main details -->  
        <div class="vehicle-details-container">  
            <!-- Header -->  
            <div class="header-top">  
                <h1><?php echo htmlentities(strtolower($result->brand_name . ' , ' . $result->vehicle_title)); ?></h1>  
            </div>  

            <!-- Info Boxes -->  
            <div class="info-boxes">  
                <div class="info-box">  
                    <i class="fa fa-calendar"></i>  
                    Reg.Year  
                    <span><?php echo htmlentities($result->model_year); ?></span>  
                </div>  
                <div class="info-box">  
                    <i class="fa fa-cogs"></i>  
                    Fuel Type  
                    <span><?php echo htmlentities($result->fuel_type); ?></span>  
                </div>  
                <div class="info-box">  
                    <i class="fa fa-user-plus"></i>  
                    Seats  
                    <span><?php echo htmlentities($result->seating_capacity); ?></span>  
                </div>  
            </div>  

            <!-- Tabs -->  
            <div class="tab-buttons">  
                <button class="tab-button active" onclick="showTab('overview')">Vehicle Overview</button>  
                <button class="tab-button" onclick="showTab('accessories')">Accessories</button>  
            </div>  

            <div id="overview" class="tab-content active">  
                <h3>Overview</h3>  
                <p><?php echo nl2br(htmlentities($result->vehicle_overview)); ?></p>  
            </div>  

            <div id="accessories" class="tab-content">  
                <h3>Accessories</h3>  
                <table class="accessories-table">  
                    <thead>  
                        <tr>  
                            <th>ACCESSORIES</th>  
                            <th>Status</th>  
                        </tr>  
                    </thead>  
                    <tbody>  
                    <?php foreach ($accessoriesList as $accessory): ?>  
                        <tr>  
                            <td><?php echo htmlentities($accessory); ?></td>  
                            <td>  
                                <?php  
                                $isAvailable = in_array($accessory, $vehicleAccessories);  
                                echo $isAvailable  
                                    ? '<span class="checkmark">&#10003;</span>'  
                                    : '<span class="crossmark">&#10007;</span>';  
                                ?>  
                            </td>  
                        </tr>  
                    <?php endforeach; ?>  
                    </tbody>  
                </table>  
            </div>  
        </div> <!-- End vehicle-details-container -->

        <!-- Right sidebar -->
        <aside class="sidebar-booking">  
            <!-- Price box at the top -->
            <div class="price-box-top" style="margin-bottom: 20px;">
                ₱<?php echo htmlentities($result->price_per_day); ?>  
                <small>Per Day</small>  
            </div>
            <!-- Share block -->  
            <div class="share-block">  
                <span>Share:</span>  
                <a href="#" title="Facebook" aria-label="Share on Facebook"><i class="fab fa-facebook-f"></i></a>  
                <a href="#" title="Twitter" aria-label="Share on Twitter"><i class="fab fa-twitter"></i></a>  
                <a href="#" title="LinkedIn" aria-label="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>  
                <a href="#" title="Google Plus" aria-label="Share on Google Plus"><i class="fab fa-google-plus-g"></i></a>  
            </div>  
            <!-- Booking form -->  
            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px;">
                <h4 style="margin-top: 0; font-weight: 700; font-size: 25px; color: #f97316; display: flex; align-items: center; gap: 7px;">
                    <i class="fa fa-envelope"></i> Book Now
                </h4>
                <form method="post" action="">
                    <input type="hidden" name="vhid" value="<?php echo $vhid; ?>" />
                    <input type="text" name="fromdate" id="fromdate" placeholder="From Date (yyyy-mm-dd)" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; color: #555; font-family: inherit;" />
                    <input type="text" name="todate" id="todate" placeholder="To Date (yyyy-mm-dd)" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; color: #555; font-family: inherit;" />
                    <textarea name="message" placeholder="Message" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; color: #555; font-family: inherit; resize: none; height: 90px;"></textarea>
                    <input type="submit" value="Book Now" style="background-color: #f97316; color: white; border: none; padding: 10px 15px; font-weight: 700; cursor: pointer; text-transform: uppercase; font-size: 14px; border-radius: 3px; transition: background-color 0.3s ease;" />
                </form>
            </div>
        </aside>
    </div> <!-- End vehicle-details-wrapper -->
</div> <!-- End main-white-container -->

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('get_booked_dates.php?vhid=<?php echo $vhid; ?>')
        .then(response => response.json())
        .then(data => {
            let disabledDates = [];
            data.forEach(range => {
                let start = new Date(range.FromDate);
                let end = new Date(range.ToDate);
                for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                    disabledDates.push(d.toISOString().split('T')[0]);
                }
            });
            flatpickr("input[name='fromdate']", {
                dateFormat: "Y-m-d",
                disable: disabledDates,
                minDate: "today"
            });
            flatpickr("input[name='todate']", {
                dateFormat: "Y-m-d",
                disable: disabledDates,
                minDate: "today"
            });
        });
});
</script>

<script>  
// Image slider  
let currentSlide = 0;  
const slides = document.querySelectorAll('.slider-image');  

function changeSlide(direction) {  
    slides[currentSlide].classList.remove('active');  
    currentSlide = (currentSlide + direction + slides.length) % slides.length;  
    slides[currentSlide].classList.add('active');  
}  

// Tab switcher  
function showTab(tabId) {  
    const tabs = document.querySelectorAll('.tab-content');  
    const buttons = document.querySelectorAll('.tab-button');  
    tabs.forEach(tab => tab.classList.remove('active'));  
    buttons.forEach(btn => btn.classList.remove('active'));  

    document.getElementById(tabId).classList.add('active');  
    Array.from(buttons).find(b => b.textContent.trim().toLowerCase().includes(tabId)).classList.add('active');  
}  
</script>  

<!-- FullCalendar Booking Availability -->
<div id="booking-calendar" style="max-width: 700px; margin: 0 auto 30px auto;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('booking-calendar');
  var vhid = <?php echo json_encode($vhid); ?>;

  fetch('get_booked_dates.php?vhid=' + vhid)
    .then(response => response.json())
    .then(data => {
      // Convert booked ranges to FullCalendar events
      let events = [];
      data.forEach(range => {
        events.push({
          title: 'Booked',
          start: range.FromDate,
          end: (new Date(new Date(range.ToDate).setDate(new Date(range.ToDate).getDate() + 1))).toISOString().split('T')[0], // FullCalendar end is exclusive
          color: '#e74c3c'
        });
      });

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 450,
        events: events,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: ''
        }
      });
      calendar.render();
    });
});
</script>

<?php include('footer.php'); ?>  

</body>  
</html>