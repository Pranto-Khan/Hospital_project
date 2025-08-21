<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM appointments WHERE id = $id");

if ($result->num_rows === 0) {
    die("Appointment not found.");
}

$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name = $conn->real_escape_string($_POST['patient_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $department = $conn->real_escape_string($_POST['department']);
    $doctor_name = $conn->real_escape_string($_POST['doctor_name']);
    $appointment_date = $conn->real_escape_string($_POST['appointment_date']);
    $appointment_time = $conn->real_escape_string($_POST['appointment_time']);
    $reason = $conn->real_escape_string($_POST['reason']);

    $sql = "UPDATE appointments SET 
            patient_name='$patient_name',
            phone='$phone',
            department='$department',
            doctor_name='$doctor_name',
            appointment_date='$appointment_date',
            appointment_time='$appointment_time',
            reason='$reason'
            WHERE id = $id";

    if ($conn->query($sql)) {
        header("Location: appointment.php?msg=updated");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating appointment: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }
        h3 {
            font-weight: 600;
            color: #2b76bc ;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 w-50">
        <h3 class="mb-4">✏️ Edit Appointment</h3>
        <form method="POST">
            <div class="form-group">
                <label>Patient Name</label>
                <input type="text" name="patient_name" value="<?= htmlspecialchars($row['patient_name']) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" value="<?= htmlspecialchars($row['department']) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Doctor Name</label>
                <input type="text" name="doctor_name" value="<?= htmlspecialchars($row['doctor_name']) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Appointment Date</label>
                <input type="date" name="appointment_date" value="<?= htmlspecialchars($row['appointment_date']) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Appointment Time</label>
                <input type="time" name="appointment_time" value="<?= htmlspecialchars($row['appointment_time']) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Reason</label>
                <textarea name="reason" class="form-control" rows="3" required><?= htmlspecialchars($row['reason']) ?></textarea>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success px-4">💾 Update</button>
                <a href="appointment.php" class="btn btn-secondary px-4">❌ Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
