<?php
session_start();

// শুধু admin ঢুকতে পারবে
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: nurses.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM nurses WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Nurse not found.";
    exit();
}

$nurse = $result->fetch_assoc();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $specialty = $conn->real_escape_string($_POST['specialty']);
    $availability = $conn->real_escape_string($_POST['availability']);

    // আগের ছবি ডিফল্ট
    $image_url = $nurse['image_url'];

    // ছবি আপলোড চেক
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/images/';  // তোমার images ফোল্ডার পাথ
        $tmp_name = $_FILES['image_file']['tmp_name'];
        $original_name = basename($_FILES['image_file']['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        // শুধুমাত্র jpg, png, jpeg, gif ফাইল গ্রহণযোগ্য
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed_ext)) {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            // ইউনিক ফাইল নাম
            $new_filename = uniqid('nurse_') . '.' . $ext;
            $target_file = $upload_dir . $new_filename;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_url = 'images/' . $new_filename;
            } else {
                $error = "Image upload failed.";
            }
        }
    }

    if (!$error) {
        $image_url_db = $conn->real_escape_string($image_url);

        $update_sql = "UPDATE nurses SET 
                       name='$name', 
                       specialty='$specialty', 
                       availability='$availability', 
                       image_url='$image_url_db'
                       WHERE id=$id";

        if ($conn->query($update_sql)) {
            header("Location: nurses.php");
            exit();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Nurse - SHP Hospital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background-color: #f4f8fb;">

<nav class="navbar navbar-dark" style="background-color: #2b76bc;">
    <a class="navbar-brand" href="nurses.php">← Back to Nurses</a>
    <span class="navbar-text text-white">
        Logged in as: <?= htmlspecialchars($_SESSION['username']) ?> (Admin)
    </span>
</nav>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header" style="background-color: #2b76bc; color: white;">
            <h4>Edit Nurse Information</h4>
        </div>
        <div class="card-body">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label><strong>Name</strong></label>
                    <input type="text" name="name" class="form-control" 
                           value="<?= htmlspecialchars($nurse['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label><strong>Specialty</strong></label>
                    <input type="text" name="specialty" class="form-control" 
                           value="<?= htmlspecialchars($nurse['specialty']) ?>" required>
                </div>

                <div class="form-group">
                    <label><strong>Availability</strong></label>
                    <input type="text" name="availability" class="form-control" 
                           value="<?= htmlspecialchars($nurse['availability']) ?>" required>
                </div>

                <div class="form-group">
                    <label><strong>Current Image</strong></label><br>
                    <img src="<?= htmlspecialchars($nurse['image_url']) ?>" alt="Current Nurse Image" style="max-width: 200px; border-radius: 8px; box-shadow: 0 0 8px #ccc;">
                </div>

                <div class="form-group">
                    <label><strong>Upload New Image (optional):</strong></label>
                    <input type="file" name="image_file" class="form-control-file" accept="image/*">
                    <small class="form-text text-muted">Leave empty if you don't want to change the image.</small>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-4">💾 Update</button>
                    <a href="nurses.php" class="btn btn-secondary px-4">Cancel</a>
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

<?php $conn->close(); ?>



