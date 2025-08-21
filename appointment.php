<?php
session_start();

// লগইন চেক
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // 'admin' অথবা 'user'

// ডাটাবেস কানেকশন
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// ফর্ম সাবমিট হলে (শুধু user এর জন্য)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $role === 'user') {
    $patient_name = $_POST['patient_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $department = $_POST['department'];
    $doctor_name = $_POST['doctor_name'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO appointments 
            (patient_name, email, phone, gender, date_of_birth, department, doctor_name, appointment_date, appointment_time, reason) 
            VALUES 
            ('$patient_name', '$email', '$phone', '$gender', '$date_of_birth', '$department', '$doctor_name', '$appointment_date', '$appointment_time', '$reason')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>✅ Appointment booked successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Doctor Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        header {
            background-color: #2b76bc; color: white;
            padding: 10px 20px; display: flex;
            justify-content: space-between; align-items: center;
        }
        .home-btn { background: white; color: #2b76bc; padding: 8px 15px; border-radius: 5px; font-weight: bold; }
        .appointment-wrapper { display: flex; gap: 40px; align-items: center; justify-content: center; max-width: 1000px; margin: auto; }
        .appointment-image { flex: 0 0 70%; margin-right: 110px; }
        .appointment-image img { width: 100%; border-radius: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.15); height: 300px; object-fit: cover; }
        .appointment-form { flex: 0 0 50%; background: white; border-radius: 15px; padding: 30px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #2b76bc; border: none; width: 100%; padding: 12px; font-size: 16px; }
        .btn-primary:hover { background-color: #1f5a91; }
    </style>
</head>
<body>

<header>
    <h2>🏥 Book Doctor Appointment</h2>
    <a href="home.php" class="home-btn">Home</a>
</header>

<div class="container mt-4">
    <h4>Welcome, <?= htmlspecialchars($username) ?>! (Role: <?= htmlspecialchars($role) ?>)</h4>
    
    <?php if ($role === 'user'): ?>
        <!-- User এর জন্য Appointment Booking Form -->
        <div class="appointment-wrapper">
            <div class="appointment-image">
                <img src="images/appo.jpg" alt="Appointment Image">
            </div>
            <div class="appointment-form">
                <?= $message; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Patient Name</label>
                        <input type="text" name="patient_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email (optional)</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" class="form-control" required>
                            <option value="">-- Select Department --</option>
                            <option>Cardiology</option>
                            <option>Neurology</option>
                            <option>Orthopedics</option>
                            <option>Pediatrics</option>
                            <option>Dermatology</option>
                            <option>Oncology</option>
                            <option>Gynecology</option>
                            <option>Anesthesiology</option>
                            <option>Radiology</option>
                            <option>General Surgery</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Doctor Name</label>
                        <input type="text" name="doctor_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Appointment Date</label>
                        <input type="date" name="appointment_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Appointment Time</label>
                        <input type="time" name="appointment_time" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Reason for Appointment</label>
                        <textarea name="reason" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </form>
            </div>
        </div>

    <?php elseif ($role === 'admin'): ?>
        <!-- Admin এর জন্য Appointment List with Edit/Delete -->
        <h3 class="mt-4 mb-3">All Appointments</h3>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Patient Name</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM appointments ORDER BY appointment_date DESC");
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                    <td><?= $row['appointment_date'] ?></td>
                    <td><?= $row['appointment_time'] ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td>
                        <a href="edit_appointment.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_appoinment.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
