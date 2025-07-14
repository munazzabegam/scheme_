<?php
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div style="color:red;text-align:center;">Database connection failed!</div>');
}
$success = '';
$error = '';
$customer = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $dob = $_POST['dob'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $status = $_POST['status'] ?? 'Active';
        $stmt = $conn->prepare("UPDATE Customers SET FullName=?, Email=?, PhoneNumber=?, DOB=?, Gender=?, Status=? WHERE CustomerID=?");
        $stmt->bind_param('ssssssi', $name, $email, $phone, $dob, $gender, $status, $id);
        if ($stmt->execute()) {
            $success = 'Customer updated successfully!';
        } else {
            $error = 'Failed to update customer.';
        }
        $stmt->close();
    }
    $sql = "SELECT * FROM Customers WHERE CustomerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom Admin Panel Styles -->
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="title">Edit Customer</div>
    <?php if ($success): ?><div class="msg-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
    <?php if ($customer): ?>
    <form method="post">
      <div class="form-group">
        <label>Name</label>
        <input name="name" value="<?= htmlspecialchars($customer['FullName']) ?>" required />
      </div>
      <div class="form-group">
        <label>Email</label>
        <input name="email" value="<?= htmlspecialchars($customer['Email']) ?>" type="email" />
      </div>
      <div class="form-group">
        <label>Phone</label>
        <input name="phone" value="<?= htmlspecialchars($customer['PhoneNumber']) ?>" required />
      </div>
      <div class="form-group">
        <label>Date of Birth</label>
        <input name="dob" value="<?= htmlspecialchars($customer['DOB']) ?>" type="date" />
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender">
          <option value="">Select</option>
          <option value="Male" <?= $customer['Gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
          <option value="Female" <?= $customer['Gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
          <option value="Other" <?= $customer['Gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="Active" <?= $customer['Status'] == 'Active' ? 'selected' : '' ?>>Active</option>
          <option value="Inactive" <?= $customer['Status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
      <button class="btn" type="submit">Update</button>
      <a href="/scheme_/admin/customers/index.php" class="back-btn">Back</a>
    </form>
    <?php else: ?>
      <div class="msg-error">Customer not found.</div>
      <a href="/scheme_/admin/customers/index.php" class="back-btn">Back</a>
    <?php endif; ?>
  </div>
  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom Admin Panel Scripts -->
  <script src="../assets/script.js"></script>
</body>
</html> 