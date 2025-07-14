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
    $dob = $_POST['dob'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $status = $_POST['status'] ?? 'Active';
    if (!$name || !$phone) {
        $error = 'Name and Phone are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO Customers (FullName, Email, PhoneNumber, DOB, Gender, Status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $name, $email, $phone, $dob, $gender, $status);
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
  <!-- Custom Admin Panel Styles -->
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="title">Add New Customer</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Name</label>
        <input name="name" required />
      </div>
      <div class="form-group">
        <label>Email</label>
        <input name="email" type="email" />
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input name="phone" required />
      </div>
      <div class="form-group">
        <label>Date of Birth</label>
        <input name="dob" type="date" />
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender">
          <option value="">Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
      <button class="btn" type="submit">Add Customer</button>
      <a href="/scheme_/admin/customers/index.php" class="back-btn">Back</a>
    </form>
  </div>
  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom Admin Panel Scripts -->
  <script src="../assets/script.js"></script>
</body>
</html> 