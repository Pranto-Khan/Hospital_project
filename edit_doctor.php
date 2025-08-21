<?php
session_start();

// Only admin can access
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: doctors.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM doctors WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Doctor not found.";
    exit();
}

$doctor = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $availability = $_POST['availability'];
    $image_url = $_POST['image_url'];

    $update_sql = "UPDATE doctors SET 
                   name='$name', 
                   specialty='$specialty', 
                   availability='$availability', 
                   image_url='$image_url'
                   WHERE id=$id";

    if ($conn->query($update_sql)) {
        header("Location: doctors.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor - SHP Hospital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color: #f4f8fb;">

<nav class="navbar navbar-dark" style="background-color: #2b76bc;">
    <a class="navbar-brand" href="doctors.php">← Back to Doctors</a>
    <span class="navbar-text text-white">
        Logged in as: <?= htmlspecialchars($_SESSION['username']) ?> (Admin)
    </span>
</nav>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background-color: #2b76bc; color: white;">
            <h4>Edit Doctor Information</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label><strong>Name</strong></label>
                    <input type="text" name="name" class="form-control" 
                           value="<?= htmlspecialchars($doctor['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label><strong>Specialty</strong></label>
                    <input type="text" name="specialty" class="form-control" 
                           value="<?= htmlspecialchars($doctor['specialty']) ?>" required>
                </div>

                <div class="form-group">
                    <label><strong>Availability</strong></label>
                    <input type="text" name="availability" class="form-control" 
                           value="<?= htmlspecialchars($doctor['availability']) ?>" required>
                </div>

                <div class="form-group">
                    <label><strong>Image URL</strong></label>
                    <input type="text" name="image_url" class="form-control" 
                           value="<?= htmlspecialchars($doctor['image_url']) ?>" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">💾 Update</button>
                    <a href="doctors.php" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="text-white text-center py-3 mt-5" style="background-color: #2b76bc;">
    &copy; 2025 SHP Hospital. All rights reserved.
</footer>

</body>
</html>

