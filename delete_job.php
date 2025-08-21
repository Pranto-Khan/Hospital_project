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

if ($id > 0) {
    $conn->query("DELETE FROM jobs WHERE id = $id");
}

header("Location: career.php");
exit();
