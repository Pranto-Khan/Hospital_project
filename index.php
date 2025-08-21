<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ডাটাবেজ কানেকশন
    $conn = new mysqli("localhost", "root", "", "shp_hospital");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ইনপুট ফিল্টারিং
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username == '' || $password == '') {
        $error = "Please enter username and password.";
    } else {
        // SQL Injection থেকে বাঁচতে Prepared Statement ব্যবহার করছি
        $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();
            // md5 দিয়ে হ্যাশ করা আছে ধরে নিচ্ছি
            if (md5($password) === $user['password']) {
                // সেশন সেট করা
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // লগইন সফল হলে home.php এ রিডাইরেক্ট
                header("Location: home.php");
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SHP Hospital Login</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0; padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      height: 100vh;
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                  url('https://images.unsplash.com/photo-1588776814546-ec7e5d6fafe1') no-repeat center center/cover;
      display: flex; align-items: center; justify-content: center;
      position: relative; color: white;
    }
    .background-title {
      position: absolute; top: 10%; width: 100%; text-align: center;
      font-size: 4rem; font-weight: 800; opacity: 0.1; letter-spacing: 10px;
      z-index: 0; pointer-events: none;
    }
    .login-container {
      background-color: rgba(255, 255, 255, 0.1);
      padding: 40px; border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(10px);
      z-index: 1; width: 300px;
    }
    .login-container h2 {
      text-align: center; margin-bottom: 20px; color: #fff;
    }
    .input-group { margin-bottom: 20px; }
    .input-group label {
      display: block; margin-bottom: 5px; color: #ddd;
    }
    .input-group input {
      width: 100%; padding: 10px; border: none; border-radius: 6px;
      outline: none; background-color: rgba(255,255,255,0.2);
      color: white;
    }
    .login-btn {
      width: 100%; padding: 10px;
      background-color: #00bcd4; border: none; border-radius: 6px;
      color: white; font-weight: bold; cursor: pointer;
      transition: background-color 0.3s;
    }
    .login-btn:hover { background-color: #0097a7; }
    .footer-text {
      text-align: center; margin-top: 15px; font-size: 12px; color: #bbb;
    }
    .error-msg {
      background-color: #ff4d4d; padding: 10px; border-radius: 6px;
      margin-bottom: 15px; text-align: center; font-weight: bold; color: white;
    }
  </style>
</head>
<body>

  <div class="background-title">SHP HOSPITAL</div>
  <div class="login-container">
    <h2>Login</h2>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="username" />
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password" />
      </div>
      <button class="login-btn" type="submit">Login</button>
    </form>

    <div class="footer-text">&copy; 2025 SHP Hospital. All rights reserved.</div>
  </div>

</body>
</html>
