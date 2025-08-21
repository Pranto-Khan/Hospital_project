
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

// User form submission
if ($role === 'user' && isset($_POST['submit'])) {
    $patient_name = $_POST['patient_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $msg_text = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contact_messages (patient_name, email, phone, department, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $patient_name, $email, $phone, $department, $msg_text);

    if ($stmt->execute()) {
        $message = "✅ Your enquiry has been submitted successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
    $stmt->close();
}

// Admin delete message
if ($role === 'admin' && isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM contact_messages WHERE id=$delete_id");
    header("Location: contact.php");
    exit();
}

// Admin fetch messages
$all_messages = [];
if ($role === 'admin') {
    $result = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");
    while ($row = $result->fetch_assoc()) {
        $all_messages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact / Enquiry</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; margin:0; padding:0; }
    .container { width: 90%; max-width: 800px; margin: 50px auto; background:#fff; padding:30px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); }
    h2 { text-align:center; color:#333; margin-bottom:25px; font-size:28px; }
    form { display:flex; flex-direction:column; gap:15px; margin-bottom:20px; }
    input, select, textarea, button { padding:12px; border-radius:8px; border:1px solid #ccc; font-size:16px; }
    input:focus, select:focus, textarea:focus { border-color:#28a745; outline:none; }
    textarea { resize: vertical; min-height:120px; }
    button { background:#28a745; color:#fff; border:none; cursor:pointer; font-size:16px; }
    button:hover { opacity:0.9; }
    .message { text-align:center; font-weight:bold; margin-bottom:15px; color:green; }
    .home-btn { position:absolute; top:20px; right:30px; padding:10px 20px; background:#2b76bc; color:white; text-decoration:none; border-radius:8px; font-weight:500; }
    .home-btn:hover { opacity:0.9; }
    table { width:100%; border-collapse: collapse; }
    th, td { padding:12px; border:1px solid #ddd; text-align:center; }
    th { background:#007BFF; color:#fff; }
    tr:nth-child(even){background-color:#f9f9f9;}
    tr:hover {background-color:#f1f1f1;}
    .edit-btn { padding:5px 10px; background:#ffc107; color:#000; border-radius:5px; text-decoration:none; }
    .delete-btn { padding:5px 10px; background:#dc3545; color:#fff; border-radius:5px; text-decoration:none; }
</style>
</head>
<body>
<div class="container">
    <h2>Contact / Enquiry Form</h2>
    <a href="home.php" class="home-btn">Home</a>

    <?php if($message) echo "<p class='message'>$message</p>"; ?>

    <?php if($role === 'user'): ?>
        <!-- User Form -->
        <form method="post">
            <input type="text" name="patient_name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <select name="department" required>
                <option value="">Select Department</option>
                <option value="General">General</option>
                <option value="Cardiology">Cardiology</option>
                <option value="Neurology">Neurology</option>
                <option value="Pediatrics">Pediatrics</option>
                <option value="Orthopedics">Orthopedics</option>
            </select>
            <textarea name="message" placeholder="Your Message" required></textarea>
            <button type="submit" name="submit">Submit</button>
        </form>
    <?php elseif($role === 'admin'): ?>
        <!-- Admin View -->
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Message</th>
                <th>Action</th>
            </tr>
            <?php foreach($all_messages as $msg): ?>
                <tr>
                    <td><?= $msg['id'] ?></td>
                    <td><?= htmlspecialchars($msg['patient_name']) ?></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td><?= htmlspecialchars($msg['phone']) ?></td>
                    <td><?= htmlspecialchars($msg['department']) ?></td>
                    <td><?= htmlspecialchars($msg['message']) ?></td>
                    <td>
                        <a href="edit_message.php?id=<?= $msg['id'] ?>" class="edit-btn">Edit</a>
                        <a href="contact.php?delete_id=<?= $msg['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure to delete?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
