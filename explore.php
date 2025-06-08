<?php  
include('header.php');  
include('db.php');  

// Fetch vehicle data  
$sql = "SELECT   
          vehicle_title,  
          brand_name,  
          price_per_day,  
          fuel_type,  
          model_year,  
          id,  
          seating_capacity,  
          vehicle_overview,  
          image1  
        FROM vehicles";  

$query = $dbh->prepare($sql);  
$query->execute();  
$results = $query->fetchAll(PDO::FETCH_OBJ);  
?>  

<!-- Explore Cars Section -->  
<section class="explore-section">  
  <div class="container">  
    
    <h1>Explore Our Cars</h1>  
    <p>Choose the perfect car for your needs from our wide selection!</p>  

    <div class="car-list">  
    <?php  
    if ($query->rowCount() > 0) {  
        foreach ($results as $result) {  
            // Build full server path to check file existence  
            $image_full_path = __DIR__ . '/admin/uploads/' . basename($result->image1);  

            // Build URL to use in img src  
            $imageUrl = !empty($result->image1) ? 'admin/uploads/' . basename($result->image1) : 'uploads/default-image.png';  

            // Check if image file exists on server, else fallback to default image  
            $imagePath = file_exists($image_full_path) ? $imageUrl : 'uploads/default-image.png';  
    ?>  
      <div class="car-card">  
        <a href="vehicle-details.php?vhid=<?php echo htmlentities($result->id); ?>">  
          <img src="<?php echo htmlentities($imagePath); ?>" alt="Car Image" class="car-img">  
        </a>  
        <div class="car-details">  
          <h6>  
            <a href="vehicle-details.php?vhid=<?php echo htmlentities($result->id); ?>">  
              <?php echo htmlentities($result->brand_name); ?>, <?php echo htmlentities($result->vehicle_title); ?>  
            </a>  
          </h6>  
          <p class="car-price">₱<?php echo number_format($result->price_per_day, 2); ?> /Day</p>  
          <div class="car-info-bar">  
            <span><i class="fa fa-car" aria-hidden="true"></i> <?php echo htmlentities($result->fuel_type); ?></span>  
            <span><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo htmlentities($result->model_year); ?> Model</span>  
            <span><i class="fa fa-user" aria-hidden="true"></i> <?php echo htmlentities($result->seating_capacity); ?> seats</span>  
          </div>  
          <p><?php echo htmlentities(substr($result->vehicle_overview, 0, 70)); ?>...</p>  
        </div>  
      </div>  
    <?php  
        }  
    }  
    ?>  
    </div>  
  </div>  
</section>  

<!-- FontAwesome -->  
<script src="https://kit.fontawesome.com/yourfontawesomekit.js" crossorigin="anonymous"></script>  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />  

<!-- Styles -->  
<style>  
body {  
    font-family: Arial, sans-serif;  
    background-color: WHITE;  
    margin: 0;  
    padding: 0;  
    min-height: 100vh;
}  
  .explore-section {  
    padding: 50px 0;  
    background: url('images/explore-bg.jpg') no-repeat center center;  
    background-size: cover;  
    color: #333;  
  }  

  .explore-section h1 {  
    text-align: center;  
    font-size: 40px;  
    color: #004153;  
  }  

  .explore-section p {  
    text-align: center;  
    font-size: 20px;  
  }  

  .car-list {  
    display: flex;  
    flex-wrap: wrap;  
    gap: 30px;  
    justify-content: center;  
  }  

  .car-card {
    width: 320px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #004153;
}  

.car-card:hover {
    background: #004153;
    color: #fff;
    transform: translateY(-10px) scale(1.03);
    box-shadow: 0 12px 36px rgba(0, 65, 83, 0.22);
}

.car-info-bar {
    background: rgba(0, 0, 0, 0.8);
    color: white;
    display: flex;
    justify-content: space-around;
    align-items: center;
    border-radius: 40px;
    padding: 8px;
    font-size: 0.9rem;
    margin: -28px -50px;
    width: calc(130% + -10px);
    transition: all 0.3s ease;
}

.car-info-bar span {
    display: flex;
    white-space: nowrap;
    padding: 0 4px;
    transition: all 0.3s ease;
}

.car-info-bar i {
    color: #ffe082;
    margin-right: 6px;
    transition: all 0.3s ease;
}

.car-card:hover .car-info-bar {
    background: #003040;
}

.car-card:hover .car-info-bar span {
    color: #ffe082;
}

.car-card:hover .car-info-bar i {
    color: #ffe082;
}

.car-details {
    text-align: center;
    padding: 18px;
    flex: 1;
    width: 100%;
    position: relative;
    background: #fff;
    border-radius: 18px;
    transition: all 0.3s ease;
}

.car-card:hover .car-details {
    background: #004153;
}

.car-card:hover .car-details h6 a,
.car-card:hover .car-details p,
.car-card:hover .car-price {
    color: #fff;
}

.car-img {
    width: 90%;
    height: 180px;
    object-fit: contain;
    display: block;
    margin: 24px auto 0 auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}  

  .car-details h6 {
    font-size: 1.6rem;
    font-weight: bold;
    margin: 10px 0 8px 0;
    color: #004153;
    transition: color 0.3s;
}  

  .car-price {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 8px;
    color: #004153;
    transition: color 0.3s;
}  

  .car-details p,
  .car-description {
    color: #004153;
    margin-bottom: 0;
    font-size: 1.05rem;
    min-height: 40px;
    transition: color 0.3s;
}
</style>  

<?php include('footer.php'); ?>