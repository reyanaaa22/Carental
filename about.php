<?php include('header.php'); ?>

<!-- Main Content Section -->
<section class="about-section">
  <div class="container">
    <h1><i class="fas fa-car-side"></i> About Ormoc Car Rental Management System</h1>

    <p>Welcome to the <strong>Ormoc Car Rental Management System</strong>, your trusted car rental service located within Ormoc City. We offer a wide variety of vehicles to suit your needs at affordable prices, making it easy for you to rent a car for any occasion.</p>

    <p>Whether you're visiting for business, leisure, or need a vehicle for a special event, our user-friendly system ensures a seamless and hassle-free car rental experience. You can choose from a range of cars that best fit your preferences and budget.</p>

    <h2><i class="fas fa-star"></i> Why Choose Us?</h2>
    <ul>
      <li><i class="fas fa-tags"></i> <strong>Affordable Prices:</strong> Competitive rental rates that won't break the bank.</li>
      <li><i class="fas fa-car"></i> <strong>Wide Range of Cars:</strong> From compact cars to spacious SUVs.</li>
      <li><i class="fas fa-map-marker-alt"></i> <strong>Convenient Location:</strong> Located in the heart of Ormoc City.</li>
      <li><i class="fas fa-headset"></i> <strong>Customer Support:</strong> Friendly team ready to assist you anytime.</li>
      <li><i class="fas fa-mouse-pointer"></i> <strong>Easy Booking:</strong> Simple online booking and fast pickup!</li>
    </ul>

    <h2><i class="fas fa-caravan"></i> Our Fleet</h2>
    <p>Our diverse fleet includes various models of cars that cater to different preferences and requirements. Whether you need a fuel-efficient car for a solo trip, a family car, or a larger vehicle for a group, we've got you covered.</p>

    <h2><i class="fas fa-map"></i> Location</h2>
    <p>We are conveniently located in Ormoc City, making it easy for you to pick up your rental car and start your journey right away. Our office is easily accessible, and we are always ready to serve our customers with excellent car rental services.</p>

    <div class="location-map">
      <h3><i class="fas fa-map-marked-alt"></i> Visit Us</h3>
      <p>Location: Ormoc City, Leyte</p>
      <!-- Embed Google Map -->
      <iframe
        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBuZ9i6AXux4ggvMXUkVEKqWoIYu-thsWo&q=Ormoc+City,Leyte"
        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy">
      </iframe>
    </div>
  </div>
</section>

<!-- CSS Styling -->
<style>
body {  
    font-family: Arial, sans-serif;  
    background-color: WHITE;  
    margin: 0;  
    padding: 0;  
    min-height: 100vh;
}  
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  /* About Section */
  .about-section {
    padding: 60px 20px;
    background: linear-gradient(to right, #f9f9f9, #e3f2fd);
    font-family: 'Poppins', sans-serif;
  }

  .about-section .container {
    max-width: 1200px;
    margin: 0 auto;
    background: #ffffff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  .about-section h1, .about-section h2, .about-section h3 {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .about-section p {
    font-size: 18px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 20px;
  }

  .about-section ul {
    list-style: none;
    padding: 0;
    margin: 20px 0;
  }

  .about-section ul li {
    font-size: 18px;
    color: #444;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .about-section ul li i {
    color: #3498db;
    font-size: 20px;
  }

  .about-section .location-map {
    margin-top: 40px;
  }

  .about-section .location-map iframe {
    width: 100%;
    height: 400px;
    border: none;
    border-radius: 10px;
    margin-top: 10px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .about-section .container {
      padding: 20px;
    }

    .about-section h1, .about-section h2 {
      font-size: 28px;
    }

    .about-section p, .about-section ul li {
      font-size: 16px;
    }
  }
</style>

<!-- FontAwesome for Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<?php include('footer.php'); ?>
