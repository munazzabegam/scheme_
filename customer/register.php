<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $gender = $_POST['gender'] ?? null;

    // Basic validation
    if (!$fullName || !$phone || !$password || !$confirm) {
        $error = 'Please fill all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $error = 'Invalid phone number.';
    } elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        $conn = new mysqli('localhost', 'root', '', 'scheme');
        if ($conn->connect_error) {
            $error = 'Database connection failed!';
        } else {
            // Check for existing email or phone
            $stmt = $conn->prepare("SELECT CustomerID FROM Customers WHERE Email = ? OR PhoneNumber = ? LIMIT 1");
            $stmt->bind_param('ss', $email, $phone);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = 'Email or phone already registered.';
            } else {
                $stmt->close();
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO Customers (FullName, Email, PhoneNumber, Address, DOB, Gender, Status, CreatedAt, UpdatedAt, PasswordHash) VALUES (?, ?, ?, ?, ?, ?, 'Active', NOW(), NOW(), ?)");
                $stmt->bind_param('sssssss', $fullName, $email, $phone, $address, $dob, $gender, $hash);
                if ($stmt->execute()) {
                    $success = 'Registration successful! You can now <a href=\'login.php\'>login</a>.';
                } else {
                    $error = 'Registration failed. Please try again.';
                }
                $stmt->close();
            }
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Registration</title>
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
    .register-container {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 32px 0 rgba(31,38,135,0.10), 0 1.5px 8px 0 #4f8cff22;
      padding: 36px 28px 28px 28px;
      max-width: 480px;
      width: 100%;
      margin: 32px auto;
    }
    .register-title {
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
    .form-control, .form-select {
      border-radius: 8px;
      font-size: 1.08rem;
      margin-bottom: 18px;
      border: 1px solid #e5e7eb;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus, .form-select:focus {
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
    .register-footer {
      text-align: center;
      margin-top: 18px;
      color: #888;
      font-size: 1rem;
    }
    .register-footer a {
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
      .register-container {
        padding: 18px 4px 12px 4px;
        max-width: 98vw;
      }
    }
  </style>
</head>
<body>
  <div class="register-container">
    <div class="register-title"><i class="fa fa-user-plus me-2"></i>Customer Registration</div>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
      <label for="full_name" class="form-label">Full Name *</label>
      <input type="text" class="form-control" id="full_name" name="full_name" required>
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="Optional">
      <label for="phone" class="form-label">Phone Number *</label>
      <input type="text" class="form-control" id="phone" name="phone" required pattern="[0-9]{10,15}">
      <label for="password" class="form-label">Password *</label>
      <input type="password" class="form-control" id="password" name="password" required>
      <label for="confirm_password" class="form-label">Confirm Password *</label>
      <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
      <label for="address" class="form-label">Address</label>
      <textarea class="form-control" id="address" name="address" rows="2"></textarea>
      <label for="dob" class="form-label">Date of Birth</label>
      <input type="date" class="form-control" id="dob" name="dob">
      <label for="gender" class="form-label">Gender</label>
      <select class="form-select" id="gender" name="gender">
        <option value="">Select</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>
      <button type="submit" class="btn btn-primary"><i class="fa fa-user-plus me-2"></i>Register</button>
    </form>
    <div class="register-footer">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>
</body>
</html> 