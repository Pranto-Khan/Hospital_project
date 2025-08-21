<?php
session_start();

// ইউজার লগইন ও রোল চেক
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php"); // লগইন পেজে পাঠাবে যদি লগইন না থাকে
    exit();
}

$role = $_SESSION['role'];  // 'admin' বা 'user'

// ডাটাবেস কানেকশন
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// নার্সদের ডেটা নাও
$sql = "SELECT * FROM nurses";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nurses List</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .nurse-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 15px;
            overflow: hidden;
        }
        .nurse-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .nurse-img {
            height: 250px;
            object-fit: cover;
        }
        header, footer {
            background-color: #2b76bc; /* Requested blue color */
            color: white;
            padding: 15px 0;
        }
        footer {
            margin-top: 50px;
            text-align: center;
        }
        /* হেডারের হোম বাটনের স্টাইল */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body class="bg-light">

<!-- Header -->
<header>
    <div class="container header-container">
        <div>
            <h1>SH Hospital</h1>
            <p class="lead mb-0">Providing Care with Compassion</p>
        </div>
        <div>
            <a href="home.php" class="btn btn-light btn-sm">Home</a>
        </div>
    </div>
</header>

<div class="container py-4">
    <!-- Back Button -->
    <div class="mb-3">
        <button onclick="window.history.back();" class="btn btn-secondary">&larr; Back</button>
    </div>

    <h2 class="text-center mb-4">Our Nurses</h2>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card nurse-card shadow-sm">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" 
                             class="card-img-top nurse-img" 
                             alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text"><strong>Specialty:</strong> <?php echo htmlspecialchars($row['specialty']); ?></p>
                            <p class="card-text"><strong>Availability:</strong> <?php echo htmlspecialchars($row['availability']); ?></p>

                            <?php if ($role === 'admin'): ?>
                                <a href="edit_nurse.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">No nurses found in the database.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <p>&copy; <?php echo date("Y"); ?> SH Hospital. All rights reserved.</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>

