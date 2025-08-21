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

$message = "";

// Room Add করলে
if (isset($_POST['add'])) {
    $room_no = $_POST['room_no'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO rooms (room_no, room_type, price, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $room_no, $room_type, $price, $status);

    if ($stmt->execute()) {
        // ✅ Add হলে roombooking.php তে পাঠাবে
        header("Location: roombooking.php");
        exit();
    } else {
        $message = "❌ Failed to add room!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Room</title>
    <style>
        body { font-family: Arial; background:#f5f6fa; }
        .container { width:450px; margin:50px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,.2); }
        h2 { text-align:center; margin-bottom:20px; }
        input, select { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:5px; }
        button { width:100%; padding:10px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer; }
        button:hover { opacity:0.9; }
        .message { text-align:center; color:red; margin:10px 0; }
    </style>
</head>
<body>
<div class="container">
    <h2>Add New Room</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>
    <form method="post">
        <input type="text" name="room_no" placeholder="Room Number" required>
        <input type="text" name="room_type" placeholder="Room Type (e.g. Single, Double)" required>
        <input type="number" name="price" placeholder="Price" required>
        <select name="status">
            <option value="Available">Available</option>
            <option value="Booked">Booked</option>
        </select>
        <button type="submit" name="add">➕ Add Room</button>
    </form>
    <!-- ✅ Back button roombooking.php তে যাবে -->
    <p style="text-align:center;"><a href="roombooking.php">⬅ Back</a></p>
</div>
</body>
</html>
