<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shp_hospital");

$id = $_GET['id'];
$conn->query("DELETE FROM appointments WHERE id=$id");

header("Location: appointment.php");
exit();
?>

