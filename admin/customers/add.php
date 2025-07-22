<?php
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div style="color:red;text-align:center;">Database connection failed!</div>');
}
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $status = $_POST['status'] ?? 'Active';
    if (!$name || !$phone) {
        $error = 'Name and Phone are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO Customers (FullName, Email, PhoneNumber, Address, DOB, Gender, Status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $name, $email, $phone, $address, $dob, $gender, $status);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: /scheme_/admin/customers/index.php?added=1');
            exit;
        } else {
            $error = 'Failed to add customer.';
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .modern-card { max-width: 480px; margin: 48px auto; border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(2,0,36,0.10); background: #fff; padding: 40px 32px 32px 32px; position: relative; }
    .modern-title { font-size: 2rem; font-weight: 700; color: #003399; margin-bottom: 24px; letter-spacing: 0.5px; text-align: center; }
    .input-group-text { background: #f4f6fb; border: none; color: #003399; }
    .modern-btn { width: 100%; padding: 12px; font-size: 1.1rem; border-radius: 10px; background: linear-gradient(90deg, #003399 0%, #6a82fb 100%); color: #fff; border: none; font-weight: 600; transition: background 0.2s; margin-top: 10px; }
    .modern-btn:hover { background: linear-gradient(90deg, #6a82fb 0%, #003399 100%); color: #fff; }
    .modern-back { display: block; text-align: center; margin-top: 18px; color: #003399; text-decoration: none; font-weight: 500; transition: color 0.2s; }
    .modern-back:hover { color: #6a82fb; }
    .msg-error { background: #fdeaea; color: #e74c3c; border-radius: 8px; padding: 10px 16px; margin-bottom: 18px; text-align: center; font-weight: 500; }
    .form-label { font-weight: 500; color: #003399; margin-bottom: 4px; }
  </style>
</head>
<body>
  <div class="modern-card">
    <div class="modern-title"><i class="fa fa-user-plus me-2"></i>Add New Customer</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <div class="mb-3">
        <label for="name" class="form-label">Name *</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-user"></i></span>
          <input name="name" id="name" class="form-control" placeholder="Enter name" required />
        </div>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-envelope"></i></span>
          <input name="email" id="email" type="email" class="form-control" placeholder="Enter email" />
        </div>
      </div>
      <div class="mb-3">
        <label for="phone" class="form-label">Phone *</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-phone"></i></span>
          <input name="phone" id="phone" class="form-control" placeholder="Enter phone number" required />
        </div>
      </div>
      <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
          <input name="address" id="address" class="form-control" placeholder="Enter address" />
        </div>
      </div>
      <div class="mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
          <input name="dob" id="dob" type="date" class="form-control" placeholder="Date of Birth" />
        </div>
      </div>
      <div class="form-floating mb-3">
        <select name="gender" id="gender" class="form-select" aria-label="Gender">
          <option value="" selected>Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
        <label for="gender">Gender</label>
      </div>
      <div class="form-floating mb-3">
        <select name="status" id="status" class="form-select" aria-label="Status">
          <option value="Active" selected>Active</option>
          <option value="Inactive">Inactive</option>
        </select>
        <label for="status">Status</label>
      </div>
      <button class="modern-btn" type="submit"><i class="fa fa-plus me-2"></i>Add Customer</button>
      <a href="/scheme_/admin/customers/index.php" class="modern-back"><i class="fa fa-arrow-left me-1"></i>Back to Customers</a>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/script.js"></script>
</body>
</html> 