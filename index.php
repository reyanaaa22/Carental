<?php  
session_start();
include('db.php');  

// Initialize login status (adjust according to your session logic)  
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);  

// Fetch vehicles for best-car section  
$sql = "SELECT id, vehicle_title, brand_name, price_per_day, fuel_type, model_year, seating_capacity, vehicle_overview, image1 FROM vehicles ORDER BY created_at DESC LIMIT 8";  
$query = $dbh->prepare($sql);  
$query->execute();  
$results = $query->fetchAll(PDO::FETCH_OBJ);  
?>

<!DOCTYPE html>  
<html lang="en">  
<head>  
  <meta charset="UTF-8" />  
  <title>Ormoc Car Rental</title>  
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />  

  <style>
    .car-card {
      transition: all 0.3s ease;
      box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
    }
    .car-card:hover {
      background: #004153 !important;
      color: #fff !important;
      box-shadow: 0 0 20px rgba(0, 123, 255, 0.5);
    }
    .car-card:hover h6 a,
    .car-card:hover .car-price,
    .car-card:hover .car-description {
      color: #fff !important;
    }
    .car-card:hover .car-info-bar {
      background: #003040 !important;
    }
    .hero {
      position: relative;
      overflow: hidden;
    }

    .hero-bubbles {
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      pointer-events: none;
      z-index: 1;
    }

    .bubble {
      position: absolute;
      width: 80px;
      height: 80px;
      background: rgba(0, 255, 204, 0.18);
      border-radius: 50%;
      filter: blur(2px);
      animation: floatBubble 8s infinite ease-in-out alternate;
    }

    .bubble:nth-child(2) { width: 120px; height: 120px; background: rgba(0, 153, 255, 0.15); animation-delay: 2s;}
    .bubble:nth-child(3) { width: 60px; height: 60px; background: rgba(255, 255, 255, 0.12); animation-delay: 4s;}
    .bubble:nth-child(4) { width: 100px; height: 100px; background: rgba(0, 255, 153, 0.13); animation-delay: 1s;}
    .bubble:nth-child(5) { width: 90px; height: 90px; background: rgba(0, 255, 204, 0.10); animation-delay: 3s;}

    @keyframes floatBubble {
      0% { transform: translateY(0) scale(1);}
      100% { transform: translateY(-40px) scale(1.1);}
    }

    .hero-text, .hero-slider {
      position: relative;
      z-index: 2;
    }
  </style>

  <script>  
    function toggleMenu() {  
      const menu = document.getElementById('mobileMenu');  
      menu.classList.toggle('show');  
    }  
    window.addEventListener('click', function(e) {  
      const sidebar = document.getElementById('mobileMenu');  
      const hamburger = document.querySelector('.hamburger-menu');  
      if (sidebar && sidebar.classList.contains('show') &&  
            !sidebar.contains(e.target) && !hamburger.contains(e.target)) {  
        sidebar.classList.remove('show');  
      }  
    });  
  </script>  
</head>  
<body>  

<?php include('header.php'); ?>  
<section class="hero" id="home">  
  <div class="hero-bubbles">
    <span class="bubble" style="top: 20%; left: 10%;"></span>
    <span class="bubble" style="top: 60%; left: 20%;"></span>
    <span class="bubble" style="top: 30%; left: 80%;"></span>
    <span class="bubble" style="top: 75%; left: 60%;"></span>
    <span class="bubble" style="top: 10%; left: 50%;"></span>
  </div>
  <div class="hero-text">  
    <h1 data-translate="Ormoc Car Rental Service" style="font-size: 3rem; margin-bottom: 5px;">Ormoc Car Rental Service</h1>  
    <p class="tagline" data-translate="Your journey starts here — fast, safe, and affordable car rentals in Ormoc." style="font-size: 1.2rem; color: #f0f0f0; margin-top: 0; margin-bottom: 15px; font-style: italic;">Your journey starts here — fast, safe, and affordable car rentals in Ormoc.</p>  
    <div class="hero-buttons">  
      <button class="book" data-translate="Book Now" onclick="location.href=''">Book Now</button>  
      <button class="explore" data-translate="Explore Us" onclick="location.href='explore.php'">Explore Us</button>  
    </div>  
  </div>  

  <!-- Hero Slider -->
  <div class="hero-slider" id="hero-slider" style="display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;">
    <div class="car-blob-bg" style="position:absolute;left:50%;top:55%;transform:translate(-50%,-50%);z-index:1;width:700px;height:700px;pointer-events:none;">
      <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;">
        <path fill="#D0E2FF" d="M40.9,-58.8C52.4,-47.9,60.8,-35.2,68.8,-20.1C76.7,-5,84.3,12.4,77.6,22.8C70.8,33.1,49.8,36.3,34.5,39.3C19.2,42.2,9.6,44.7,-4.9,51.4C-19.3,58.1,-38.7,69,-54.4,66.2C-70.2,63.5,-82.4,47.1,-82.1,30.8C-81.8,14.4,-68.9,-1.9,-60.2,-16.8C-51.5,-31.6,-47,-45.1,-37.7,-56.6C-28.3,-68.2,-14.2,-78,0.3,-78.4C14.7,-78.7,29.3,-69.6,40.9,-58.8Z" transform="translate(100 100)" />
      </svg>
    </div>
    <img id="slider-img" src="images/blue.png" alt="Car" style="display:block;margin:0 auto;width:100%;max-width:600px;border-radius:10px;object-fit:cover;min-height:200px;z-index:2;position:relative;">
    <div id="slider-caption" style="margin-top:10px;text-align:center;color:#222;z-index:3;position:relative;"></div>
  </div>

  <div class="wave">  
    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">  
      <path d="M0,0 C300,100 900,0 1200,100 L1200,120 L0,120 Z"></path>  
    </svg>  
  </div>  
</section>  

<!-- SVG Wave Divider for Best Car Section -->

<section class="best-car" id="best-car">  
  <div class="container">  
    <h2 data-translate="Find the Best Car for You" style="color: black; font-size: 40px;">
      Find the <span style="color: #004153;">Best Car</span> for You
    </h2>  
    <p data-translate="Select from our wide range of well-maintained vehicles perfect for every journey.">
      Select from our wide range of well-maintained vehicles perfect for every journey.
    </p>  

    <div class="car-list">  
      <?php if ($query->rowCount() > 0 && !empty($results)) : ?>  
        <?php foreach ($results as $result): ?>  
          <div class="car-card">  
            <div class="car-image-wrapper">  
              <a href="vehicle-details.php?vhid=<?php echo htmlentities($result->id); ?>">  
                <img src="<?php echo htmlentities(file_exists(__DIR__ . '/admin/uploads/' . basename($result->image1)) ? 'admin/uploads/' . basename($result->image1) : 'uploads/default-image.png'); ?>" alt="<?php echo htmlentities($result->vehicle_title); ?>" class="car-img" />  
              </a>  
              <div class="car-info-bar">  
                <span><i class="fa fa-car" style="color: yellow;"></i> <?php echo htmlentities($result->fuel_type); ?></span>  
                <span><i class="fa fa-calendar" style="color: yellow;"></i> <?php echo htmlentities($result->model_year); ?> Model</span>  
                <span><i class="fa fa-user" style="color: yellow;"></i> <?php echo htmlentities($result->seating_capacity); ?> seats</span>  
              </div>  
            </div>  
            <div class="car-details">  
              <h6><a href="vehicle-details.php?vhid=<?php echo htmlentities($result->id); ?>"><?php echo htmlentities($result->brand_name); ?> , <?php echo htmlentities($result->vehicle_title); ?></a></h6>  
              <p class="car-price">₱<?php echo htmlentities($result->price_per_day); ?> /Day</p>  
              <p class="car-description"><?php echo htmlentities(substr($result->vehicle_overview, 0, 70)); ?></p>  
            </div>  
          </div>  
        <?php endforeach; ?>  
      <?php else: ?>  
        <p data-translate="No vehicles available at the moment.">No vehicles available at the moment.</p>  
      <?php endif; ?>  
    </div>  

    <!-- View More Button -->
    <div class="view-more">
      <button onclick="location.href='explore.php'" style="margin-top: 20px; padding: 10px 20px; font-size: 16px; background-color: #004153; color: white; border: none; border-radius: 5px; cursor: pointer;">
        View More
      </button>
    </div>
  </div>  
</section>

<section class="car-clips" id="car-clips" style="padding: 100px 90px; background-color: #f9f9f9;">
  <div class="car-clips-container" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 40px; max-width: 1400px; margin: 0 auto;">
    <div class="car-text" style="flex: 1; min-width: 300px; text-align: left;">
      <h2 data-translate="Experience Quality Car Rentals" style="font-size: 3rem; margin-bottom: 20px;">Experience Quality Car Rentals</h2>
      <p data-translate="We offer reliable, clean, and affordable cars perfect for any trip around Ormoc City and beyond. Watch some clips of our fleet in action and see why customers love our service!" style="font-size: 1.4rem; line-height: 2; margin-bottom: 20px;">
        We offer reliable, clean, and affordable cars perfect for any trip around Ormoc City and beyond. Watch some clips of our fleet in action and see why customers love our service!
      </p>
      <ul style="font-size: 1.4rem; line-height: 2; padding-left: 20px; list-style-position: inside;">
        <li data-translate="Easy Booking Process">✅ Easy Booking Process</li>
        <li data-translate="Clean and Well-Maintained Cars">✅ Clean and Well-Maintained Cars</li>
        <li data-translate="Affordable Daily Rates">✅ Affordable Daily Rates</li>
        <li data-translate="Convenient Pickup Locations">✅ Convenient Pickup Locations</li>
      </ul>
    </div>

    <video src="images/CAR.mp4" controls style="flex: 1; min-width: 700px; max-width: 300px; max-height: 500px; border-radius: 10px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3); object-fit: cover;"></video>
  </div>
</section>

<section class="about" style="position:relative; overflow:hidden;">

  <div class="about-content" style="position:relative; z-index:1; padding: 60px 0;">
    <h2 style="text-align: center;" data-translate="Promos & Offers">Promos & Offers</h2>  
    <p data-translate="Check out our latest promos and exclusive offers! Enjoy huge discounts, free upgrades, and more from Ormoc Car Rental.">
      Check out our latest promos and exclusive offers! Enjoy huge discounts, free upgrades, and more from Ormoc Car Rental.
    </p>

    <!-- Promo/Offers Container -->
    <div class="promo-offers-row" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 32px; margin: 40px auto 24px auto;">
      <div class="promo-offer" style="flex:1 1 220px; min-width:200px; max-width:320px; min-height:460px; background: url('images/promo.webp') center/cover no-repeat; position: relative; color: #fff; border-radius: 18px; box-shadow: 0 4px 24px #00415322; padding: 32px 24px; text-align: center; font-size: 2rem; font-weight: bold; letter-spacing: 1px; overflow: hidden;">
        <div style="position:absolute;top:0;left:0;width:150%;height:150%;background:rgba(0,65,83,0.7);z-index:0;"></div>
        <span style="position:relative;z-index:1;font-size: 2.5rem; color: #f4c542;">50% OFF</span><br>
        <span style="position:relative;z-index:1;font-size: 1.2rem; font-weight: 400; color: #fff;">on your first car rental this month!</span>
      </div>
      <div class="promo-offer" style="flex:1 1 220px; min-width:200px; max-width:320px; min-height:260px; background: url('images/free.webp') center/cover no-repeat; position: relative; color: #fff; border-radius: 18px; box-shadow: 0 4px 24px #00415322; padding: 32px 24px; text-align: center; font-size: 2rem; font-weight: bold; letter-spacing: 1px; overflow: hidden;">
        <div style="position:absolute;top:0;left:0;width:150%;height:150%;background:rgba(0,65,83,0.7);z-index:0;"></div>
        <span style="position:relative;z-index:1;font-size: 2.5rem; color: #f4c542;">FREE UPGRADE</span><br>
        <span style="position:relative;z-index:1;font-size: 1.2rem; font-weight: 400; color: #fff;">Get a free car upgrade on select models!</span>
      </div>
      <div class="promo-offer" style="flex:1 1 220px; min-width:200px; max-width:320px; min-height:260px; background: url('images/Team.jpg') center/cover no-repeat; position: relative; color: #fff; border-radius: 18px; box-shadow: 0 4px 24px #00415322; padding: 32px 24px; text-align: center; font-size: 2rem; font-weight: bold; letter-spacing: 1px; overflow: hidden;">
        <div style="position:absolute;top:0;left:0;width:150%;height:150%;background:rgba(0,65,83,0.7);z-index:0;"></div>
        <span style="position:relative;z-index:1;font-size: 2.5rem; color: #f4c542;">UNLIMITED MILEAGE</span><br>
        <span style="position:relative;z-index:1;font-size: 1.2rem; font-weight: 400; color: #fff;">Drive as much as you want, no extra fees!</span>
      </div>
    </div>

    <!-- Car Brand Logos Row -->
    <div class="brand-logos" style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap:30px; margin: 200px 0 0 0;">
      <img src="images/TOYOTA.png" alt="Toyota" style="height:80px;filter:grayscale(0.2);">
      <img src="images/HONDA.png" alt="Honda" style="height:80px;filter:grayscale(0.2);">
      <img src="images/FORD.png" alt="Ford" style="height:80px;filter:grayscale(0.2);">
      <img src="images/BMW.webp" alt="BMW" style="height:80px;filter:grayscale(0.2);">
      <img src="images/MERCEDES.png" alt="Mercedes" style="height:80px;filter:grayscale(0.2);">
      <img src="images/NISSAN.png" alt="Nissan" style="height:80px;filter:grayscale(0.2);">
      <img src="images/SUZUKI.png" alt="Suzuki" style="height:80px;filter:grayscale(0.2);">
      <img src="images/AUDI.png" alt="Audi" style="height:80px;filter:grayscale(0.2);">
      <img src="images/ISUZU.png" alt=" Isuzu" style="height:80px;filter:grayscale(0.2);">
      <img src="images/HYUNDAI.png" alt="Hyundai" style="height:80px;filter:grayscale(0.2);">
      <img src="images/BUGATTI.png" alt="Bugatti" style="height:80px;filter:grayscale(0.2);">

      <!-- Add more logos as needed -->
    </div>
  </div>  
</section>  


<?php include('footer.php'); ?>  

<!-- Back to Top Button -->
<button id="backToTopBtn" title="Back to Top" style="display:none;position:fixed;bottom:40px;right:40px;z-index:9999;background:#004153;color:white;border:none;border-radius:50%;width:50px;height:50px;box-shadow:0 2px 8px #0003;cursor:pointer;font-size:24px;align-items:center;justify-content:center;">
  <i class="fa fa-arrow-up"></i>
</button>

<!-- Optional Script for Rent button login check -->  
<script>  
function handleRent(carId) {  
  const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;  
  if (isLoggedIn) {  
    window.location.href = 'book.php?id=' + carId;  
  } else {  
    alert("Please login to rent a car.");   
    // Or show login modal if implemented  
  }  
}

// Back to Top Button Logic
const backToTopBtn = document.getElementById('backToTopBtn');
window.addEventListener('scroll', function() {
  // Show button if scrolled near the bottom (footer area)
  if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 200)) {
    backToTopBtn.style.display = 'flex';
  } else {
    backToTopBtn.style.display = 'none';
  }
});
backToTopBtn.addEventListener('click', function() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>  

<!-- Hero Slider Script -->
<script>
const apiKey = 'HI7PYsS8hy+gH+GJD/yT+A==pmInSFKQ7YKHGCVa';
const pexelsApiKey = 'UgMEbt4LvPNWVWTNjNxjPmXarAkqkiAihLRKw3dz4OojTqpkfFEeWS54';
const carMakes = [
  'audi', 'toyota', 'honda', 'ford', 'bmw',
  'mercedes', 'chevrolet', 'nissan', 'mazda', 'hyundai',
  'kia', 'volkswagen', 'subaru', 'jeep', 'lexus',
  'mitsubishi', 'volvo', 'porsche', 'jaguar', 'tesla'
];
const carYears = [2024, 2023, 2022, 2021, 2020, 2019, 2018, 2017, 2016, 2015];
let cars = [];
let current = 0;

function fetchPexelsImage(make, model) {
  const query = encodeURIComponent(`${make} ${model} car`);
  return fetch(`https://api.pexels.com/v1/search?query=${query}&per_page=1`, {
    headers: { 'Authorization': pexelsApiKey }
  })
    .then(res => res.json())
    .then(data => (data.photos && data.photos[0] && data.photos[0].src && data.photos[0].src.medium) ? data.photos[0].src.medium : 'images/blue.png')
    .catch(() => 'images/blue.png');
}

function showCar(index) {
  if (!cars.length) return;
  const car = cars[index];
  sliderImg.onerror = function() {
    this.onerror = null;
    this.src = 'images/blue.png'; // fallback image
  };
  sliderImg.src = car.image;
  sliderCaption.innerHTML = `<h2 style='margin:0;font-size:2rem;'>${car.make} ${car.model}</h2><p style='margin:0.5em 0 0 0;'>${car.year} • ${car.fuel_type} • ${car.class}</p>`;
}

function nextCar() {
  current = (current + 1) % cars.length;
  showCar(current);
}

function fetchLatestCarWithImage(make) {
  let yearIndex = 0;
  function tryYear() {
    if (yearIndex >= carYears.length) return Promise.resolve(null);
    const year = carYears[yearIndex++];
    return fetch(`https://api.api-ninjas.com/v1/cars?make=${make}&year=${year}`, {
      headers: { 'X-Api-Key': apiKey }
    })
    .then(res => res.json())
    .then(data => {
      if (data && data[0]) {
        const car = data[0];
        return fetchPexelsImage(car.make, car.model).then(img => {
          car.image = img;
          return car;
        });
      } else {
        return tryYear();
      }
    })
    .catch(() => tryYear());
  }
  return tryYear();
}

Promise.all(
  carMakes.map(make => fetchLatestCarWithImage(make))
).then(results => {
  cars = results.filter(Boolean); // remove any nulls
  if (cars.length) {
    showCar(0);
    setInterval(nextCar, 4000);
  } else {
    sliderImg.src = 'images/blue.png';
    sliderCaption.innerHTML = '<p>Could not load car data.</p>';
  }
});
</script>

</body>  
</html>