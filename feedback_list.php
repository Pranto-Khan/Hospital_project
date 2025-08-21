<?php
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM patient_feedback ORDER BY submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Feedback - SHP Hospital</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<!-- Header -->
<header class="text-white text-center py-3 mb-4" style="background-color: #2b76bc;">
  <h2>All Patient Feedback</h2>
</header>


<div class="container">
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="border rounded p-3 mb-3 shadow-sm">
      <h5><?= htmlspecialchars($row['name']) ?></h5>
      <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?: 'N/A' ?></p>
      <p><?= nl2br(htmlspecialchars($row['feedback'])) ?></p>
      <small class="text-muted">Submitted on: <?= $row['submitted_at'] ?></small>
    </div>
  <?php endwhile; ?>

  <a href="feedback.php" class="btn btn-secondary mt-3">⬅️ Back to Feedback Form</a>
</div>

<!-- Footer -->
<footer class="text-white text-center py-3 mt-5" style="background-color: #2b76bc;">
  <p>&copy; 2025 SHP Hospital. All rights reserved.</p>
</footer>


</body>
</html>
