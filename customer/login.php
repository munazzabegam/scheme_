<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['email_phone'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $conn = new mysqli('localhost', 'root', '', 'scheme');
    if ($conn->connect_error) {
        $error = 'Database connection failed!';
    } else {
        $stmt = $conn->prepare("SELECT * FROM Customers WHERE (Email = ? OR PhoneNumber = ?) AND Status = 'Active' LIMIT 1");
        $stmt->bind_param('ss', $input, $input);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        if ($customer && password_verify($password, $customer['PasswordHash'] ?? '')) {
            $_SESSION['customer_id'] = $customer['CustomerID'];
            $_SESSION['customer_name'] = $customer['FullName'];
            $_SESSION['customer_email'] = $customer['Email'];
            header('Location: /scheme_/customer/dashboard/');
            exit;
        } else {
            $error = 'Invalid credentials or inactive account.';
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #f7f8fa;
      font-family: 'Poppins', 'Inter', Arial, sans-serif;
      color: #23272f;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-container {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 32px 0 rgba(31,38,135,0.10), 0 1.5px 8px 0 #4f8cff22;
      padding: 40px 32px 32px 32px;
      max-width: 400px;
      width: 100%;
      margin: 32px auto;
    }
    .login-title {
      font-size: 2rem;
      font-weight: 700;
      color: #003399;
      margin-bottom: 18px;
      text-align: center;
      letter-spacing: 0.5px;
    }
    .form-label {
      font-weight: 500;
      color: #003399;
      margin-bottom: 4px;
    }
    .form-control {
      border-radius: 8px;
      font-size: 1.08rem;
      margin-bottom: 18px;
      border: 1px solid #e5e7eb;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus {
      border-color: #003399;
      box-shadow: 0 0 0 0.2rem rgba(0,51,153,0.10);
    }
    .btn-primary {
      background: #003399;
      border: none;
      font-weight: 600;
      border-radius: 10px;
      padding: 12px 32px;
      font-size: 1.1rem;
      width: 100%;
      margin-top: 10px;
      transition: background 0.25s, box-shadow 0.25s;
    }
    .btn-primary:hover {
      background: #002266;
      box-shadow: 0 4px 16px 0 rgba(0,51,153,0.15);
    }
    .login-footer {
      text-align: center;
      margin-top: 18px;
      color: #888;
      font-size: 1rem;
    }
    .login-footer a {
      color: #003399;
      text-decoration: underline;
      font-weight: 500;
    }
    .alert {
      border-radius: 8px;
      margin-bottom: 18px;
      font-size: 1rem;
      text-align: center;
    }
    @media (max-width: 600px) {
      .login-container {
        padding: 24px 8px 18px 8px;
        max-width: 98vw;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-title"><i class="fa fa-user-circle me-2"></i>Customer Login</div>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
      <label for="email_phone" class="form-label">Email or Phone Number</label>
      <input type="text" class="form-control" id="email_phone" name="email_phone" required autofocus placeholder="Enter your email or phone">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
      <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in-alt me-2"></i>Login</button>
    </form>
    <div class="login-footer">
      Don't have an account? <a href="register.php">Register</a>
    </div>
  </div>
</body>
</html> 