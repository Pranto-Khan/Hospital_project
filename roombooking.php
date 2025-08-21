
<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// সেশন থেকে role নেয়া
$role = $_SESSION['role'] ?? 'user'; 
$username = $_SESSION['username'] ?? 'Guest';

$message = "";

// Fetch available rooms
$rooms = $conn->query("SELECT * FROM rooms WHERE status='Available' ORDER BY room_type, room_no");

// শুধুমাত্র User বুক করতে পারবে
if ($role === 'user' && isset($_POST['book'])) {
    $patient_name = $_POST['patient_name'];
    $patient_email = $_POST['patient_email'];
    $room_id = $_POST['room_id'];

    $check = $conn->query("SELECT status FROM rooms WHERE id=$room_id")->fetch_assoc();
    if ($check['status'] == 'Available') {
        $stmt = $conn->prepare("INSERT INTO bookings (patient_name, patient_email, room_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $patient_name, $patient_email, $room_id);
        if ($stmt->execute()) {
            $conn->query("UPDATE rooms SET status='Booked' WHERE id=$room_id");
            $message = "✅ Room booked successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "❌ Sorry, this room is already booked!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Room Booking</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; margin: 0; padding: 0; }
        .container {
            width: 95%; max-width: 900px; margin: 50px auto;
            background: #fff; padding: 25px 30px; border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); position: relative;
        }
        h2, h3 { text-align: center; color: #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 15px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #007BFF; color: #fff; }
        tr:nth-child(even){ background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        form { margin-top: 20px; display: flex; flex-direction: column; gap: 12px; }
        input, select, button {
            padding: 12px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px;
        }
        button {
            background: #28a745; color: #fff; border: none; cursor: pointer; transition: 0.3s;
        }
        button:hover { opacity: 0.9; }
        .message { color: green; font-weight: bold; text-align: center; margin-top: 10px; }
        .home-btn, .add-btn {
            padding: 10px 18px; color: white; border-radius: 8px; text-decoration: none; transition: 0.3s;
        }
        .home-btn { position: absolute; top: 20px; right: 30px; background: #007BFF; }
        .home-btn:hover, .add-btn:hover { opacity: 0.8; }
        .add-btn { background: #17a2b8; display: inline-block; margin-bottom: 10px; }
        .alert { text-align:center; padding:10px; margin-bottom:20px; border-radius:8px; }
        .alert-success { background:#d4edda; color:#155724; }
        .alert-info { background:#cce5ff; color:#004085; }
        .edit-btn { padding:6px 10px; background:#ffc107; color:#000; border-radius:5px; text-decoration:none; }
        .delete-btn { padding:6px 10px; background:#dc3545; color:#fff; border-radius:5px; text-decoration:none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Patient Room Booking</h2>
    <a href="home.php" class="home-btn">Home</a>

    <!-- Role Message -->
    <?php if ($role === 'admin'): ?>
        <div class="alert alert-success">You are logged in as <b>Admin</b>. You can manage rooms but not book them.</div>
        <!-- Add Room Button -->
        <a href="add_room.php" class="add-btn">➕ Add New Room</a>
    <?php else: ?>
        <div class="alert alert-info">You are logged in as <b>User</b>. You can book available rooms.</div>
    <?php endif; ?>

    <?php if($message) echo "<p class='message'>$message</p>"; ?>

    <!-- Booking Form (Only for User) -->
    <?php if ($role === 'user'): ?>
        <form method="post">
            <input type="text" name="patient_name" placeholder="Patient Name" required>
            <input type="email" name="patient_email" placeholder="Patient Email" required>
            <select name="room_id" required>
                <option value="">Select Room Type & Number</option>
                <?php while($room = $rooms->fetch_assoc()): ?>
                    <option value="<?php echo $room['id']; ?>">
                        <?php echo $room['room_type'] . " - " . $room['room_no'] . " (Price: BDT " . $room['price'] . ")"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="book">Book Room</button>
        </form>
    <?php endif; ?>

    <!-- Available Rooms Table -->
    <h3>Available Rooms</h3>
    <table>
        <tr>
            <th>Room No</th>
            <th>Room Type</th>
            <th>Price (BDT)</th>
            <th>Status</th>
            <?php if ($role === 'admin'): ?>
                <th>Action</th>
            <?php endif; ?>
        </tr>
        <?php
        $all_rooms = $conn->query("SELECT * FROM rooms ORDER BY room_type, room_no");
        while($r = $all_rooms->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo $r['room_no']; ?></td>
            <td><?php echo $r['room_type']; ?></td>
            <td><?php echo $r['price']; ?></td>
            <td><?php echo $r['status']; ?></td>
            <?php if ($role === 'admin'): ?>
                <td>
                    <a href="edit_room.php?id=<?php echo $r['id']; ?>" class="edit-btn">Edit</a>
                    <a href="delete_room.php?id=<?php echo $r['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
