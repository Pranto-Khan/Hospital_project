<?php
session_start();

// শুধুমাত্র Admin প্রবেশ করতে পারবে
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: contact.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// Message fetch
$result = $conn->query("SELECT * FROM contact_messages WHERE id=$id");
if ($result->num_rows === 0) {
    die("Message not found!");
}
$msg = $result->fetch_assoc();
$message = "";

// Update submission
if (isset($_POST['update'])) {
    $patient_name = $_POST['patient_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $msg_text = $_POST['message'];

    $stmt = $conn->prepare("UPDATE contact_messages SET patient_name=?, email=?, phone=?, department=?, message=? WHERE id=?");
    $stmt->bind_param("sssssi", $patient_name, $email, $phone, $department, $msg_text, $id);

    if ($stmt->execute()) {
        $message = "✅ Message updated successfully!";
    } else {
        $message = "❌ Update failed: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Contact Message</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { font-family: 'Segoe UI', sans-serif; background: #eef2f7; }
    .container { max-width: 700px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    h2 { text-align:center; margin-bottom: 25px; color: #333; }
    form { display:flex; flex-direction:column; gap:15px; }
    input, select, textarea, button { padding:12px; border-radius:8px; border:1px solid #ccc; font-size:16px; }
    input:focus, select:focus, textarea:focus { border-color:#28a745; outline:none; }
    textarea { resize: vertical; min-height:120px; }
    button { background:#28a745; color:#fff; border:none; cursor:pointer; font-size:16px; }
    button:hover { opacity:0.9; }
    .message { text-align:center; font-weight:bold; margin-bottom:15px; color:green; }
    .back-btn { text-decoration:none; color:white; background:#007BFF; padding:10px 15px; border-radius:8px; display:inline-block; margin-bottom:15px; }
    .back-btn:hover { opacity:0.9; }
</style>
</head>
<body>
<div class="container">
    <h2>Edit Contact Message</h2>
    <a href="contact.php" class="back-btn">⬅ Back</a>

    <?php if($message) echo "<p class='message'>$message</p>"; ?>

    <form method="post">
        <input type="text" name="patient_name" value="<?= htmlspecialchars($msg['patient_name']) ?>" placeholder="Patient Name" required>
        <input type="email" name="email" value="<?= htmlspecialchars($msg['email']) ?>" placeholder="Email" required>
        <input type="text" name="phone" value="<?= htmlspecialchars($msg['phone']) ?>" placeholder="Phone" required>
        <select name="department" required>
            <option value="">Select Department</option>
            <option value="General" <?= $msg['department']=='General'?'selected':'' ?>>General</option>
            <option value="Cardiology" <?= $msg['department']=='Cardiology'?'selected':'' ?>>Cardiology</option>
            <option value="Neurology" <?= $msg['department']=='Neurology'?'selected':'' ?>>Neurology</option>
            <option value="Pediatrics" <?= $msg['department']=='Pediatrics'?'selected':'' ?>>Pediatrics</option>
            <option value="Orthopedics" <?= $msg['department']=='Orthopedics'?'selected':'' ?>>Orthopedics</option>
        </select>
        <textarea name="message" placeholder="Message" required><?= htmlspecialchars($msg['message']) ?></textarea>
        <button type="submit" name="update">Update Message</button>
    </form>
</div>
</body>
</html>
