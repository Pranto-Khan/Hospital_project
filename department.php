


<?php
session_start();

// যদি কেউ লগইন না করে পেজ খুলে
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // 'admin' বা 'user'

// Department list
$departments = [
    ["name"=>"Anesthesia", "desc"=>"Safe and effective pain management during surgical procedures.", "link"=>"anas.html"],
    ["name"=>"Cardiology", "desc"=>"Advanced diagnosis and treatment of heart diseases and disorders.", "link"=>"cardio.html"],
    ["name"=>"Dental", "desc"=>"Complete dental services including cleaning, surgery, and orthodontics.", "link"=>"dental.html"],
    ["name"=>"Emergency & ICU", "desc"=>"Round-the-clock critical and emergency care for patients in need.", "link"=>"emergency.html"],
    ["name"=>"Burn & Plastic Surgery", "desc"=>"Reconstructive and cosmetic surgery services for burn victims and others.", "link"=>"burn.html"],
    ["name"=>"Orthopedics", "desc"=>"Specialized care for bones, joints, muscles, and physical injuries.", "link"=>""],
    ["name"=>"Urology", "desc"=>"Expert care for urinary tract and male reproductive system disorders.", "link"=>""],
    ["name"=>"Dermatology", "desc"=>"Treatment for skin, hair, nail diseases, and cosmetic dermatology.", "link"=>""],
    ["name"=>"Radiology", "desc"=>"Advanced imaging services including X-ray, MRI, and CT scans.", "link"=>""]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hospital Departments</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
header, footer { background-color: #2b76bc !important; color: white; }
.section-title { color: #a8c0ff; font-weight: 600; margin-bottom: 2rem; }
.card-title { color: #174ea6; font-weight: 600; }
.card-text { color: #333; }
footer a { color: #a8c0ff; text-decoration: underline; }
.admin-btn { margin-top: 10px; }
</style>
</head>
<body>

<header class="text-white py-3 mb-4">
  <div class="container d-flex justify-content-between align-items-center">
    <h1 class="mb-0">SHP Hospital</h1>
    <div>
      <span>Welcome, <?= htmlspecialchars($username) ?>!</span>
      <a href="home.php" class="text-white font-weight-bold ml-3" style="text-decoration: none;">Home</a>
      
    </div>
  </div>
</header>

<section class="py-5 bg-light">
  <div class="container">
    <h2 class="section-title">Our Departments</h2>
    <div class="row">

      <?php foreach($departments as $index => $dept): ?>
        <div class="col-md-4 mb-4">
          <?php if(!empty($dept['link'])): ?>
            <a href="<?= $dept['link'] ?>" style="text-decoration: none; color: inherit;">
          <?php endif ?>
          <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
              <h5 class="card-title"><?= $dept['name'] ?></h5>
              <p class="card-text"><?= $dept['desc'] ?></p>

              <?php if($role === 'admin'): ?>
                <a href="edit_department.php?id=<?= $index ?>" class="btn btn-sm btn-warning admin-btn">Edit</a>
                <a href="delete_department.php?id=<?= $index ?>" class="btn btn-sm btn-danger admin-btn" onclick="return confirm('Are you sure you want to delete this department?')">Delete</a>
              <?php endif ?>

            </div>
          </div>
          <?php if(!empty($dept['link'])): ?>
            </a>
          <?php endif ?>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<footer style="color: white; padding: 1.5rem 0; text-align: center;">
  <div class="container">
    <p class="mb-1">&copy; 2025 SHP Hospital. All rights reserved.</p>
    <p class="mb-0">
      Developed by SHP Web Team |
      <a href="mailto:info@shphospital.com">info@shphospital.com</a>
    </p>
  </div>
</footer>

</body>
</html>
