<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Doctors - SHP Hospital</title>
  <link rel="stylesheet" href="doctor.css">
  <style>
    body {
      background-color: #f4f8fb;
      font-family: Arial, sans-serif;
    }
    header {
      background-color: #2b76bc  ; /* Green */
      color: white;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 { font-size: 24px; }
    .home-btn {
      background-color: white;
      color: #00796b ;
      padding: 10px 18px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
    }
    .doctor-list {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      padding: 40px;
      justify-content: center;
    }
    .doctor-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      padding: 20px;
      width: 260px;
      text-align: center;
    }
    .doctor-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .edit-btn {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 12px;
      background-color: #ff9800;
      color: white;
      text-decoration: none;
      border-radius: 4px;
    }
    footer {
      background: #2b76bc; /* Green */
      color: white;
      text-align: center;
      padding: 15px;
    }
  </style>
</head>
<body>
<header>
  <h1>SHP Hospital - Our Doctors</h1>
  <a href="home.php" class="home-btn">🏠 Home</a>
</header>

<main class="doctor-list">
<?php if ($result && $result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="doctor-card">
      <img src="images/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
      <h3><?= htmlspecialchars($row['name']) ?></h3>
      <p><strong><?= htmlspecialchars($row['specialty']) ?></strong></p>
      <p><em><?= htmlspecialchars($row['availability']) ?></em></p>
      
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="edit_doctor.php?id=<?= $row['id'] ?>" class="edit-btn">✏ Edit</a>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p>No doctors found.</p>
<?php endif; ?>
</main>

<footer>
  &copy; 2025 SHP Hospital. All rights reserved.
</footer>
</body>
</html>
<?php $conn->close(); ?>
