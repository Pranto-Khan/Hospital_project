<?php
session_start();

// শুধুমাত্র Admin প্রবেশ করতে পারবে
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: roombooking.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// রুম ডিলিট করা
if ($id > 0) {
    $conn->query("DELETE FROM rooms WHERE id=$id");
}

// আবার roombooking.php তে ফেরত পাঠানো
header("Location: roombooking.php");
exit();
?>
