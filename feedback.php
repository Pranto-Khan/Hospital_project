<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $feedback = $conn->real_escape_string($_POST['feedback']);

    if (!empty($name) && !empty($feedback)) {
        $sql = "INSERT INTO patient_feedback (name, email, feedback)
                VALUES ('$name', '$email', '$feedback')";
        if ($conn->query($sql) === TRUE) {
            $success = "✅ Thank you for your feedback!";
        } else {
            $error = "❌ Error: " . $conn->error;
        }
    } else {
        $error = "❗ Name and Feedback are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Feedback - SHP Hospital</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<!-- Header -->
<header class="text-white py-3 mb-4" style="background-color: #2b76bc;">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
      <h1 class="m-0">SHP Hospital</h1>
      <p class="m-0">Patient Feedback Form</p>
    </div>
    <nav>
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link text-white" href="home.php">Home</a>
        </li>
      </ul>
    </nav>
  </div>
</header>

<!-- Form Section -->
<div class="container">
  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="form-group">
      <label>Full Name *</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="form-group">
      <label>Email (optional)</label>
      <input type="email" name="email" class="form-control">
    </div>

    <div class="form-group">
      <label>Your Feedback *</label>
      <textarea name="feedback" class="form-control" rows="4" required></textarea>
    </div>

    <button type="submit" class="btn btn-success">Submit Feedback</button>
    <a href="feedback_list.php" class="btn btn-info ml-2">See All Feedback</a>
  </form>
</div>

<!-- Footer -->
<footer class="text-white text-center py-3 mt-5" style="background-color: #2b76bc;">
  <p>&copy; 2025 SHP Hospital. All rights reserved.</p>
</footer>

</body>
</html>
