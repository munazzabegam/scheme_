<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Signup</title>
  <style>
    body {
      background: #171717;
      min-height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .wrapper {
      width: 420px;
      background: linear-gradient(90deg, rgba(2,0,36,1) 9%, rgba(9,9,121,1) 68%, rgba(0,91,255,1) 97%);
      backdrop-filter: blur(9px);
      color: #fff;
      border-radius: 16px;
      padding: 40px 40px 30px 40px;
      box-sizing: border-box;
      box-shadow: 0 8px 32px 0 rgba(2,0,36,0.2);
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .form-signup {
      font-size: 32px;
      text-align: center;
      margin-bottom: 32px;
      font-weight: 500;
    }
    .wrapper form {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .wrapper .input-box {
      position: relative;
      width: 100%;
      height: 50px;
      margin-bottom: 24px;
      margin-top: 0;
      display: flex;
      align-items: center;
    }
    .input-box input {
      width: 100%;
      height: 100%;
      background: transparent;
      border: none;
      outline: none;
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 40px;
      font-size: 16px;
      color: #fff;
      padding: 0 20px;
      box-sizing: border-box;
      transition: border 0.2s;
    }
    .input-box input:focus {
      border: 2px solid #fff;
    }
    .input-box input::placeholder {
      color: #fff;
      opacity: 0.8;
    }
    .wrapper .btn {
      width: 160px;
      height: 45px;
      background: #fff;
      border: none;
      outline: none;
      border-radius: 40px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      font-size: 18px;
      color: #333;
      font-weight: 600;
      margin: 18px 0 0 0;
      align-self: center;
      transition: background 0.2s, color 0.2s;
    }
    .wrapper .btn:hover {
      background: #e0e0e0;
      color: #222;
    }
  </style>
</head>
<body>
<?php
/*
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } elseif (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required!';
    } else {
        $conn = new mysqli('localhost', 'root', '', 'scheme');
        if ($conn->connect_error) {
            $error = 'Database connection failed!';
        } else {
            $stmt = $conn->prepare('SELECT AdminID FROM Admins WHERE Email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = 'An admin with this email already exists!';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $role = 'SuperAdmin';
                $status = 'Active';
                $stmt2 = $conn->prepare('INSERT INTO Admins (Name, Email, PasswordHash, Role, Status) VALUES (?, ?, ?, ?, ?)');
                $stmt2->bind_param('sssss', $name, $email, $hash, $role, $status);
                if ($stmt2->execute()) {
                    $success = 'Admin registered successfully!';
                } else {
                    $error = 'Failed to register admin.';
                }
                $stmt2->close();
            }
            $stmt->close();
            $conn->close();
        }
    }
}
?>
*/
?>
  <div class="wrapper">
    <form action="" method="post" autocomplete="off">
      <p class="form-signup">Admin Signup</p>
      <?php if ($success): ?>
        <div style="color: #b3ffb3; text-align:center; margin-bottom: 18px; font-size: 15px;">
          <?= htmlspecialchars($success) ?> Please <a href="login.php" style="color:#fff; text-decoration:underline;">login now</a>.
        </div>
      <?php elseif ($error): ?>
        <div style="color: #ffb3b3; text-align:center; margin-bottom: 18px; font-size: 15px;"> <?= htmlspecialchars($error) ?> </div>
      <?php endif; ?>
      <div class="input-box">
        <input required name="name" placeholder="Name" type="text" autocomplete="off" />
      </div>
      <div class="input-box">
        <input required name="email" placeholder="Email" type="email" autocomplete="off" />
      </div>
      <div class="input-box">
        <input required name="password" placeholder="Password" type="password" autocomplete="off" />
      </div>
      <div class="input-box">
        <input required name="confirm_password" placeholder="Confirm Password" type="password" autocomplete="off" />
      </div>
      <button class="btn" type="submit">Sign Up</button>
    </form>
  </div>
</body>
</html> 