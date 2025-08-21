<?php
session_start();

// শুধুমাত্র admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// জব ডেটা ফেচ
$result = $conn->query("SELECT * FROM jobs WHERE id = $id");
$job = $result->fetch_assoc();

if (!$job) {
    die("Job not found!");
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE jobs SET title=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $description, $id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Job updated successfully!</div>";
        // আবার রিফ্রেশ করে নতুন ডেটা দেখানো
        $result = $conn->query("SELECT * FROM jobs WHERE id = $id");
        $job = $result->fetch_assoc();
    } else {
        $message = "<div class='alert alert-danger'>❌ Failed to update job.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Job</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container py-5">
    <h2>Edit Job</h2>
    <?= $message ?>
    <form method="POST">
        <div class="form-group">
            <label>Job Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($job['title']) ?>" required>
        </div>
        <div class="form-group">
            <label>Job Description</label>
            <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($job['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Update Job</button>
        <a href="career.php" class="btn btn-secondary">Back</a>
    </form>
</body>
</html>
