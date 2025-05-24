<?php
include('header.php');
include('db.php');

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'], $_POST['message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($name && $email && $message) {
        $stmt = $dbh->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            echo "<script>alert('Your message has been sent!'); window.location.href='contact.php';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to send message. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
}
?>

<!-- Contact Section -->
<section class="contact-section">
  <div class="container">
    <h1>Contact Us</h1>
    <p>Have questions or need help? Feel free to reach out to us anytime!</p>

    <div class="contact-content">
      
      <!-- Contact Form -->
      <form action="" method="POST" class="contact-form">
        <div class="form-group">
          <label for="name"><i class="fas fa-user"></i> Your Name</label>
          <input type="text" id="name" name="name" required placeholder="Enter your full name">
        </div>

        <div class="form-group">
          <label for="email"><i class="fas fa-envelope"></i> Your Email</label>
          <input type="email" id="email" name="email" required placeholder="Enter your email address">
        </div>

        <div class="form-group">
          <label for="message"><i class="fas fa-comment-dots"></i> Your Message</label>
          <textarea id="message" name="message" rows="5" required placeholder="Type your message here"></textarea>
        </div>

        <button type="submit" class="btn-send">Send Message</button>
      </form>

      <!-- Google Map -->
      <div class="map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3853.0381433836435!2d124.60419711520407!3d11.024482993482045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33b1c5d2c8c9e345%3A0x40ab5c74c815bb9a!2sOrmoc%20City!5e0!3m2!1sen!2sph!4v1627642817789!5m2!1sen!2sph"
          width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>

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
  /* Contact Section */
  .contact-section {
    padding: 50px 0;
    background: #f2f2f2;
  }

  .contact-section h1 {
    text-align: center;
    font-size: 40px;
    margin-bottom: 10px;
    color: #333;
  }

  .contact-section p {
    text-align: center;
    font-size: 18px;
    color: #666;
    margin-bottom: 40px;
  }

  .contact-content {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
    align-items: flex-start;
  }

  .contact-form {
    flex: 1;
    min-width: 300px;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    font-size: 16px;
    color: #333;
    margin-bottom: 8px;
  }

  .form-group input,
  .form-group textarea {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
  }

  .form-group input:focus,
  .form-group textarea:focus {
    border-color: #3498db;
    outline: none;
  }

  .btn-send {
    background: #3498db;
    color: #fff;
    padding: 12px 25px;
    font-size: 18px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.3s;
  }

  .btn-send:hover {
    background: #2980b9;
  }

  .map {
    flex: 1;
    min-width: 300px;
  }

  /* Icons */
  i {
    margin-right: 5px;
    color: #3498db;
  }
</style>

<!-- FontAwesome Icons -->
<script src="https://kit.fontawesome.com/yourfontawesomekit.js" crossorigin="anonymous"></script>

<?php include('footer.php'); ?>
