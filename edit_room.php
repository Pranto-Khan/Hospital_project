<?php
session_start();

// শুধুমাত্র Admin প্রবেশ করতে পারবে
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: rooms.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// Room তথ্য বের করা
$result = $conn->query("SELECT * FROM rooms WHERE id=$id");
if ($result->num_rows === 0) {
    die("Room not found!");
}
$room = $result->fetch_assoc();
$message = "";

// Update করলে
if (isset($_POST['update'])) {
    $room_no = $_POST['room_no'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE rooms SET room_no=?, room_type=?, price=?, status=? WHERE id=?");
    $stmt->bind_param("ssisi", $room_no, $room_type, $price, $status, $id);

    if ($stmt->execute()) {
        // ✅ Update হলে roombooking.php তে পাঠাবে
        header("Location: roombooking.php");
        exit();
    } else {
        $message = "❌ Update failed!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Room</title>
    <style>
        body { font-family: Arial; background:#f5f6fa; }
        .container { width:450px; margin:50px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,.2); }
        h2 { text-align:center; margin-bottom:20px; }
        input, select { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:5px; }
        button { width:100%; padding:10px; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer; }
        button:hover { opacity:0.9; }
        .message { text-align:center; color:green; margin:10px 0; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Room</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>
    <form method="post">
        <input type="text" name="room_no" value="<?php echo $room['room_no']; ?>" required>
        <input type="text" name="room_type" value="<?php echo $room['room_type']; ?>" required>
        <input type="number" name="price" value="<?php echo $room['price']; ?>" required>
        <select name="status">
            <option value="Available" <?php if($room['status']=="Available") echo "selected"; ?>>Available</option>
            <option value="Booked" <?php if($room['status']=="Booked") echo "selected"; ?>>Booked</option>
        </select>
        <button type="submit" name="update">Update Room</button>
    </form>
    <!-- ✅ Back button roombooking.php তে যাবে -->
    <p style="text-align:center;"><a href="roombooking.php">⬅ Back</a></p>
</div>
</body>
</html>
