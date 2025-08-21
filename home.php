<?php
session_start();

// সেশন থেকে ইউজার রোল নেবে
// যদি কেউ লগইন না করে home.php খোলে, তাহলে redirect করবে login পেজে
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");  // তোমার লগইন পেজ যেটা হবে সেটা এখানে দিবে
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];  // 'admin' অথবা 'user'
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hospital Home</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
 <style>
    .carousel-item img { height: 500px; object-fit: cover; }
    .feature-icon { font-size: 2rem; color: #1954ff87; }
    .navbar-green { background-color: #2b76bc;  !important; } /* Header color changed */
    .footer-green { background-color: #2b76bc; color: white; } /* Footer color changed */
    .nav-link { color: white !important; font-weight: 500; }
    .nav-link.active { text-decoration: underline; }
</style>

</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-green sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold text-white" href="#">SHP Hospital</a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="doctors.php">Doctors</a></li>
          <li class="nav-item"><a class="nav-link" href="nurses.php">Nurses</a></li>
          <li class="nav-item"><a class="nav-link" href="department.php">Departments</a></li>
          <li class="nav-item"><a class="nav-link" href="appointment.php">Appointments</a></li>
          <li class="nav-item"><a class="nav-link" href="career.php">Career</a></li>
          <li class="nav-item"><a class="nav-link" href="roombooking.php">Room Rent</a></li>
          <li class="nav-item"><a class="nav-link" href="chairman_message.php">Message</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-3">
    <h4>Welcome, <?= htmlspecialchars($username) ?>!</h4>
    <?php if ($role === 'admin'): ?>
      <div class="alert alert-success">
        You are logged in as <strong>Admin</strong>. You have editing permissions.
      </div>
    <?php else: ?>
      <div class="alert alert-info">
        You are logged in as <strong>User</strong>. You have view-only permissions.
      </div>
    <?php endif; ?>
  </div>

  <!-- Carousel -->
<div class="container text-center">
  <img src="images/hospital_front.jpg" 
       alt="Hospital Front View" 
       class="header-img rounded shadow img-fluid"
       style="width: 1000 px; height: auto; max-width: 100%;">
</div>

  <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="images/under.jpg" class="d-block w-100" alt="Slide 1">
      </div>
      <div class="carousel-item">
        <img src="images/under1.jpg" class="d-block w-100" alt="Slide 2">
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>

  <!-- Hospital Introduction -->
  <section class="py-5 bg-white">
    <div class="container">
      <h2 class="text-center text-success mb-4">Welcome to SHP Hospital</h2>
      <p class="text-center fs-5">SHP Hospital is a leading healthcare institution in Bangladesh, committed to delivering high-quality, patient-centered medical services. Equipped with modern technologies and skilled professionals, we provide comprehensive healthcare across various specialties, ensuring compassion, excellence, and innovation in every service we offer.</p>
    </div>
  </section>

  <!-- Centers of Excellence -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-center mb-4 text-success">Centers of Excellence</h2>
      <div class="row gy-4">
        <div class="col-md-4"><div class="d-flex align-items-start">
          <i class="bi bi-heart-fill feature-icon me-3"></i>
          <div><h5>Gastro & Liver Center</h5><p>Advanced hepatobiliary & pancreatic surgery services.</p>
          <?php if ($role === 'admin'): ?>
            <a href="edit_center.php?id=1" class="btn btn-sm btn-warning mt-2">Edit</a>
          <?php endif; ?>
          </div>
        </div></div>
        <div class="col-md-4"><div class="d-flex align-items-start">
          <i class="bi bi-person-badge-fill feature-icon me-3"></i>
          <div><h5>Mother & Child Care</h5><p>Specialist NICU, obstetrics & pediatric services.</p>
          <?php if ($role === 'admin'): ?>
            <a href="edit_center.php?id=2" class="btn btn-sm btn-warning mt-2">Edit</a>
          <?php endif; ?>
          </div>
        </div></div>
        <div class="col-md-4"><div class="d-flex align-items-start">
          <i class="bi bi-brain feature-icon me-3"></i>
          <div><h5>Neuro & Spine Center</h5><p>Expert neurosurgery & spine care team.</p>
          <?php if ($role === 'admin'): ?>
            <a href="edit_center.php?id=3" class="btn btn-sm btn-warning mt-2">Edit</a>
          <?php endif; ?>
          </div>
        </div></div>
        <!-- Add more centers with edit buttons for admin similarly -->
      </div>
    </div>
  </section>

  <!-- Why Choose Us -->
  <section class="bg-light py-5">
    <div class="container">
      <h2 class="text-center mb-4 text-success">Why Choose Us</h2>
      <div class="row text-center gy-4">
        <div class="col-md-4"><i class="bi bi-clock-history feature-icon mb-2"></i><h5>24/7 Emergency & ICU</h5></div>
        <div class="col-md-4"><i class="bi bi-flask feature-icon mb-2"></i><h5>Advanced Lab Facilities</h5></div>
        <div class="col-md-4"><i class="bi bi-camera-reels feature-icon mb-2"></i><h5>Radiology & Imaging</h5></div>
      </div>
    </div>
  </section>

  <!-- Emergency Contact Section with Image -->
  <section class="py-5 bg-white">
    <div class="container">
      <div class="row align-items-center">

        <!-- Text Content -->
        <div class="col-md-6 mb-4 mb-md-0 text-center text-md-start">
          <h2 class="text-success mb-3">Emergency Services</h2>
          <p class="lead">Our dedicated emergency team is available 24/7 for all critical care needs.</p>
          <p><strong>Emergency Hotline:</strong> <span class="text-danger fs-4">+880 1711 000000</span></p>
          <p><strong>Ambulance:</strong> Rapid response within Dhaka city. Just call us!</p>
          <p><strong>Email:</strong> <a href="mailto:emergency@hospital.com">emergency@hospital.com</a></p>
        </div>

        <!-- Image -->
        <div class="col-md-6 text-center">
          <img src="images/emer.jpg" alt="Emergency Services" class="img-fluid rounded shadow">
        </div>

      </div>
    </div>
  </section>

  <!-- Patient Feedback Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center text-success mb-4">Patient Feedback</h2>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="p-4 rounded shadow text-center" style="background-color: #054478; color: white;">
            <p class="mb-3">We value your opinion. Please share your experience with us to help improve our service quality.</p>
            <a href="feedback.php" class="btn btn-light fw-bold">Feedback</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact -->
  <section class="py-5">
    <div class="container text-center">
      <h2 class="mb-3 text-success">Contact Us</h2>
      <p><strong>Care Line:</strong> 10647</p>
      <p><strong>Address:</strong> West Panthapath, Dhaka 1215</p>
      <p><strong>Email:</strong> info@hospital.com</p>
    </div>
  </section>

  <!-- Footer -->
  <!-- Footer -->
  <footer class="footer-green text-center py-3">
    <p class="mb-0">© 2025 SHP Hospital — All Rights Reserved</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

