<?php
session_start();

// শুধুমাত্র অ্যাডমিন
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$result = $conn->query("SELECT * FROM chairman_message WHERE id=$id");
if (!$result || $result->num_rows == 0) die("Chairman message not found.");

$chairman = $result->fetch_assoc();
$message = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $msg = $conn->real_escape_string($_POST['message']);
    $photo = $chairman['photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $targetDir = "images/";
        $photoName = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $photoName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo = $photoName;
        } else {
            $error = "Photo upload failed.";
        }
    }

    if (!$error) {
        $update = "UPDATE chairman_message SET name='$name', message='$msg', photo='$photo' WHERE id=$id";
        if ($conn->query($update)) {
            header("Location: chairman_message.php");
            exit();
        } else {
            $error = "Update failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Chairman Message</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background: #f2f7fb; font-family: 'Segoe UI', sans-serif; padding-top: 60px; }
    .container { max-width: 700px; background: #fff; padding: 30px 35px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    h2 { color: #003366; font-weight: 700; margin-bottom: 25px; text-align: center; }
    label { font-weight: 600; }
    .btn-update { background-color: #28a745; color: white; font-weight: 600; }
    .btn-update:hover { background-color: #218838; }
    .current-photo img { width: 150px; height: 150px; border-radius: 12px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .btn-cancel { margin-left: 10px; }
    .alert { margin-top: 15px; }
</style>
</head>
<body>

<div class="container">
    <h2>Edit Chairman Message</h2>

    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name">Chairman Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($chairman['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="message">Message:</label>
            <textarea id="message" name="message" class="form-control" rows="6" required><?= htmlspecialchars($chairman['message']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Current Photo:</label>
            <div class="current-photo mb-2">
                <img src="images/<?= htmlspecialchars($chairman['photo']) ?>" alt="Chairman Photo">
            </div>
            <label for="photo">Change Photo (optional):</label>
            <input type="file" id="photo" name="photo" class="form-control">
        </div>

        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-update">Update</button>
            <a href="chairman_message.php" class="btn btn-secondary btn-cancel">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
