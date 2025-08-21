<?php
session_start();

// শুধু অ্যাডমিন এক্সেস করতে পারবে
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get chairman id
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch photo to delete
$result = $conn->query("SELECT photo FROM chairman_message WHERE id=$id");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $photoPath = "images/" . $row['photo'];
    if (file_exists($photoPath)) unlink($photoPath); // delete photo
}

// Delete row
$conn->query("DELETE FROM chairman_message WHERE id=$id");

// Redirect back
header("Location: chairman_message.php");
exit();
?>
