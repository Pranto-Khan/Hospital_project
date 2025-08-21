<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role']; // 'admin' or 'user'

// Database Connection
$conn = new mysqli("localhost", "root", "", "shp_hospital");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch jobs
$jobs_result = $conn->query("SELECT * FROM jobs ORDER BY posted_date DESC");

$top_jobs = [];
$other_jobs = [];

while ($job = $jobs_result->fetch_assoc()) {
    $title_normalized = strtolower(str_replace(' ', '_', $job['title']));
    if ($title_normalized === 'staff_nurse' || $title_normalized === 'medical_officer') {
        $top_jobs[] = $job;
    } else {
        $other_jobs[] = $job;
    }
}

// Handle application (Only for users)
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job'])) {
    if ($role !== 'user') {
        $message = "<div class='alert alert-danger'>❌ Only users can apply for jobs.</div>";
    } else {
        $job_id = $_POST['job_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $cover_letter = $_POST['cover_letter'];

        $resume_name = $_FILES['resume']['name'];
        $resume_tmp = $_FILES['resume']['tmp_name'];
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . time() . "_" . basename($resume_name);
        move_uploaded_file($resume_tmp, $target_file);

        $stmt = $conn->prepare("INSERT INTO job_applications (job_id, name, email, phone, resume, cover_letter) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $job_id, $name, $email, $phone, $target_file, $cover_letter);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Application submitted successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Failed to submit application.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Hospital Career</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .career-banner {
            position: relative;
            text-align: center;
            margin-bottom: 150px;
        }
        .career-banner img {
            width: 80%;        /* ছবির width adjustable */
            max-width: 1300px;  /* max width */
            height: auto;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            display: block;
            margin: 0 auto;
        }
        .job-card {
            transition: transform 0.3s;
        }
        .job-card:hover {
            transform: translateY(-10px);
        }
        /* Floating top jobs over banner */
        .career-banner .top-jobs {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
        }
    </style>
</head>
<body>

<a href="home.php" class="btn btn-outline-primary" style="position: fixed; top: 15px; right: 15px; z-index: 1050;">
    🏠 Home
</a>

<div class="container py-5">
    <h2 class="text-center mb-4" style="color: #2b76bc;">🏥 Hospital Career Opportunities</h2>

    <?= $message ?>

    <!-- User Career Banner + Floating Top Jobs -->
    <?php if ($role === 'user'): ?>
        <div class="career-banner">
            <img src="images/career.jpg" alt="Career" class="img-fluid rounded shadow">
            <div class="row top-jobs justify-content-center">
                <?php foreach ($top_jobs as $job): ?>
                    <div class="col-md-5 mb-4 job-card">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5><?= htmlspecialchars($job['title']) ?></h5>
                                <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                                <small><?= htmlspecialchars($job['posted_date']) ?></small>
                                <hr />
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#applyModal" onclick="setJobId(<?= (int)$job['id'] ?>)">Apply Now</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Admin: Add Job Button -->
    <?php if ($role === 'admin'): ?>
        <div class="mb-4 text-end">
            <a href="add_job.php" class="btn btn-success">➕ Add New Job</a>
        </div>
    <?php endif; ?>

    <!-- Other jobs (normal list) -->
    <div class="row mt-4 <?php if($role==='user') echo 'justify-content-center'; ?>">
        <?php foreach ($other_jobs as $job): ?>
            <div class="col-md-6 mb-4 job-card">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5><?= htmlspecialchars($job['title']) ?></h5>
                        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                        <small><?= htmlspecialchars($job['posted_date']) ?></small>
                        <hr />
                        <?php if ($role === 'admin'): ?>
                            <a href="edit_job.php?id=<?= $job['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_job.php?id=<?= $job['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        <?php else: ?>
                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#applyModal" onclick="setJobId(<?= (int)$job['id'] ?>)">Apply Now</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Apply Modal (Users Only) -->
<?php if ($role === 'user'): ?>
<div class="modal fade" id="applyModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="apply_job" value="1">
        <div class="modal-header">
          <h5 class="modal-title">Apply for Job</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="job_id" id="job_id" />
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required />
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required />
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" required />
            </div>
            <div class="form-group">
                <label>Resume (PDF/DOC)</label>
                <input type="file" name="resume" class="form-control-file" accept=".pdf,.doc,.docx" required />
            </div>
            <div class="form-group">
                <label>Cover Letter</label>
                <textarea name="cover_letter" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit Application</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
function setJobId(id) {
    document.getElementById('job_id').value = id;
}
</script>

</body>
</html>
