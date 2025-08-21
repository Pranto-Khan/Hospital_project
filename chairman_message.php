<?php
session_start();

// রোল চেক
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // 'admin' অথবা 'user'

// Database connection
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch chairman message (assuming one row)
$sql = "SELECT * FROM chairman_message LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $chairman = $result->fetch_assoc();
} else {
    $chairman = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Chairman Message</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #eef3fb; margin: 0; padding: 60px 20px 40px; }
    .home-button { position: fixed; top: 20px; right: 20px; background: #007bff; color: #fff; padding: 10px 18px; border-radius: 8px; font-weight: 600; z-index: 1000; text-decoration: none; }
    .chairman-container { display: flex; flex-wrap: wrap; align-items: center; max-width: 720px; width: 100%; padding: 35px 30px; background: linear-gradient(135deg, #ffffff, #d6e0fb); border-radius: 18px; box-shadow: 0 10px 28px rgba(0,123,255,0.25); gap: 32px; margin: auto; }
    .chairman-photo { flex: 0 0 180px; height: 180px; border-radius: 50%; overflow: hidden; border: 5px solid #0056b3; }
    .chairman-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .chairman-message { flex: 1 1 400px; }
    .chairman-name { font-weight: 800; font-size: 28px; color: #003366; margin-bottom: 16px; }
    .chairman-text { font-size: 18px; line-height: 1.7; white-space: pre-line; color: #222; }
    .action-buttons { margin-top: 20px; }
    .action-buttons a { margin-right: 10px; }
    @media (max-width: 540px) {
        .chairman-container { flex-direction: column; padding: 28px 22px; }
        .chairman-photo { margin: 0 auto 24px; }
        .chairman-message { text-align: center; }
        .chairman-name { font-size: 24px; }
        .chairman-text { font-size: 16px; }
    }
</style>
</head>
<body>

<a href="home.php" class="home-button">Home</a>

<?php if ($chairman): ?>
    <section class="chairman-container">
        <div class="chairman-photo">
            <img src="images/<?php echo htmlspecialchars($chairman['photo']); ?>" alt="Photo of Chairman <?php echo htmlspecialchars($chairman['name']); ?>" />
        </div>
        <div class="chairman-message">
            <h1 class="chairman-name"><?php echo htmlspecialchars($chairman['name']); ?></h1>
            <p class="chairman-text"><?php echo nl2br(htmlspecialchars($chairman['message'])); ?></p>

            <?php if ($role === 'admin'): ?>
                <div class="action-buttons">
                    <a href="edit_chairman.php?id=<?php echo $chairman['id']; ?>" class="btn btn-warning">Edit</a>
                    <a href="delete_chairman.php?id=<?php echo $chairman['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php else: ?>
    <p style="text-align:center; font-size:18px; color:#555; margin-top:80px;">Chairman message not found.</p>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

