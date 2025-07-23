<?php
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div style="color:red;text-align:center;">Database connection failed!</div>');
}
$error = '';
$id = $_GET['id'] ?? null;
if (!$id) {
    $error = 'No customer ID provided.';
} else {
    $stmt = $conn->prepare("SELECT * FROM Customers WHERE CustomerID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $stmt->close();
    if (!$customer) {
        $error = 'Customer not found.';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
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
        $stmt = $conn->prepare("UPDATE Customers SET FullName=?, Email=?, PhoneNumber=?, Address=?, DOB=?, Gender=?, Status=? WHERE CustomerID=?");
        $stmt->bind_param('sssssssi', $name, $email, $phone, $address, $dob, $gender, $status, $id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: /scheme_/admin/customers/index.php?updated=1');
            exit;
        } else {
            $error = 'Failed to update customer.';
        }
        $stmt->close();
    }
}
$conn->close();
?>
<?php
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Customer</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    body {
      background: #f7f8fa;
      font-family: 'Poppins', 'Inter', Arial, sans-serif;
      color: #23272f;
    }
    .sidebar-premium {
      width: 250px;
      background: #16213e;
      min-height: 100vh;
      color: #fff;
      position: fixed;
      top: 0; left: 0;
      display: flex; flex-direction: column;
      padding: 32px 0 0 0;
      box-shadow: 2px 0 16px 0 rgba(22,33,62,0.04);
    }
    .sidebar-premium nav a {
      display: flex; align-items: center; gap: 16px;
      padding: 18px 24px 18px 32px;
      color: #bfc9da;
      text-decoration: none;
      font-size: 1.08rem;
      font-weight: 500;
      border-left: 4px solid transparent;
      transition: background 0.2s, border-color 0.2s, color 0.2s;
      border-radius: 0 16px 16px 0;
      margin-bottom: 4px;
    }
    .sidebar-premium nav a.active {
      color: #fff;
      border-left: 4px solid #4f8cff;
      background: rgba(79,140,255,0.08);
    }
    .sidebar-premium nav a:hover {
      background: rgba(79,140,255,0.04);
      color: #fff;
    }
    .sidebar-premium .sidebar-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-left: 32px;
      margin-bottom: 36px;
      letter-spacing: 1px;
      display: flex; align-items: center; gap: 10px;
    }
    .profile-chip {
      position: absolute; top: 32px; right: 48px;
      display: flex; align-items: center; gap: 10px;
      background: #fff;
      color: #23272f;
      border-radius: 24px;
      padding: 6px 18px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
      font-size: 1rem;
      font-weight: 500;
      z-index: 10;
    }
    .main-container {
      margin-left: 250px;
      padding: 48px 32px 32px 32px;
      min-height: 100vh;
      max-width: 100vw;
    }
    .form-title {
      font-size: 2rem;
      font-weight: 700;
      color: #003399;
      margin-bottom: 32px;
      letter-spacing: 0.5px;
      text-align: left;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .form-label { font-weight: 500; color: #003399; margin-bottom: 4px; }
    .form-control, .form-select {
      border-radius: 8px;
      font-size: 1.08rem;
      margin-bottom: 18px;
    }
    .btn-primary {
      background: linear-gradient(90deg, #003399 0%, #6a82fb 100%);
      border: none;
      font-weight: 600;
      border-radius: 10px;
      padding: 12px 32px;
      font-size: 1.1rem;
      margin-top: 10px;
    }
    .btn-primary:hover {
      background: linear-gradient(90deg, #6a82fb 0%, #003399 100%);
    }
    .msg-error {
      background: #fdeaea;
      color: #e74c3c;
      border-radius: 8px;
      padding: 10px 16px;
      margin-bottom: 18px;
      text-align: center;
      font-weight: 500;
    }
    @media (max-width: 991.98px) {
      .form-row-flex { flex-direction: column !important; }
      .form-row-flex > div { width: 100% !important; margin-bottom: 18px; }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<div class="main-container">
  <div class="profile-chip">
    <i class="fa-regular fa-user-circle"></i> <?= htmlspecialchars($role) ?>
  </div>
  <div class="form-title"><i class="fa fa-user-edit me-2"></i>Edit Customer</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
    <?php if (!$error): ?>
    <form method="post" autocomplete="off" enctype="multipart/form-data">
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-6">
              <label for="name" class="form-label">Name *</label>
                <input name="name" id="name" class="form-control" placeholder="Enter name" value="<?= htmlspecialchars($customer['FullName']) ?>" required />
              </div>
      <div class="col-lg-6">
              <label for="email" class="form-label">Email</label>
                <input name="email" id="email" type="email" class="form-control" placeholder="Enter email" value="<?= htmlspecialchars($customer['Email']) ?>" />
              </div>
            </div>
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-12">
              <label for="phone" class="form-label">Phone *</label>
                <input name="phone" id="phone" class="form-control" placeholder="Enter phone number" value="<?= htmlspecialchars($customer['PhoneNumber']) ?>" required />
              </div>
            </div>
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-12">
              <label for="address" class="form-label">Address</label>
        <textarea name="address" id="address" class="form-control" placeholder="Enter address" rows="2"><?= htmlspecialchars($customer['Address']) ?></textarea>
              </div>
            </div>
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-6">
              <label for="dob" class="form-label">Date of Birth</label>
                <input name="dob" id="dob" type="date" class="form-control" placeholder="Date of Birth" value="<?= htmlspecialchars($customer['DOB']) ?>" />
              </div>
      <div class="col-lg-6">
              <label for="gender" class="form-label">Gender</label>
              <select name="gender" id="gender" class="form-select" aria-label="Gender">
                <option value="" <?= $customer['Gender'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="Male" <?= $customer['Gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $customer['Gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $customer['Gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
              </select>
            </div>
          </div>
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-12">
              <label for="status" class="form-label">Status</label>
              <select name="status" id="status" class="form-select" aria-label="Status">
                <option value="Active" <?= $customer['Status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $customer['Status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
              </select>
            </div>
          </div>
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-6">
        <button class="btn btn-primary w-100" type="submit"><i class="fa fa-save me-2"></i>Update Customer</button>
        </div>
      <div class="col-lg-6">
        <a href="/scheme_/admin/customers/index.php" class="btn btn-secondary w-100">Back to Customers</a>
      </div>
    </div>
    </form>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/script.js"></script>
</body>
</html> 