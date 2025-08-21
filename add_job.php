<?php
session_start();

// শুধুমাত্র admin অ্যাক্সেস পাবে
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $posted_date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO jobs (title, description, posted_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $posted_date);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Job added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Failed to add job.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Job</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container py-5">
    <h2>Add New Job</h2>
    <?= $message ?>
    <form method="POST">
        <div class="form-group">
            <label>Job Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Job Description</label>
            <textarea name="description" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Add Job</button>
        <a href="career.php" class="btn btn-secondary">Back</a>
    </form>
</body>
</html>
