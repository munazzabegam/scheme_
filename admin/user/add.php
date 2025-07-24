<?php
session_start();
include_once '../../config/database.php';
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $formRole = $_POST['role'] ?? 'Admin';
    $status = $_POST['status'] ?? 'Active';
    // Profile image upload logic can be added here
    if (!$name || !$email || !$password) {
        $error = 'Name, Email, and Password are required.';
    } else {
        // Check for duplicate email
        $checkStmt = $conn->prepare("SELECT AdminID FROM Admins WHERE Email = ? LIMIT 1");
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $error = 'Admin with this email already exists.';
            $checkStmt->close();
        } else {
            $checkStmt->close();
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO Admins (Name, Email, PasswordHash, Role, Status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $name, $email, $passwordHash, $formRole, $status);
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: /scheme_/admin/user/index.php?added=1');
                exit;
            } else {
                $error = 'Failed to add admin.';
            }
            $stmt->close();
        }
    }
}
$conn->close();
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Admin</title>
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
  <div class="form-title"><i class="fa fa-user-plus me-2"></i>Add New Admin</div>
  <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" required />
      </div>
      <div class="col-lg-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required />
      </div>
      <div class="col-lg-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required />
      </div>
      <div class="col-lg-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <option value="Verifier" selected>Verifier</option>
          <option value="SuperAdmin">SuperAdmin</option>
          <option value="Editor">Editor</option>
        </select>
      </div>
    </div>
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
      <div class="col-lg-3">
        <label class="form-label">Profile Image (Optional)</label>
        <input type="file" name="profile_image" class="form-control" accept="image/*" />
      </div>
    </div>
    <button class="btn btn-primary" type="submit"><i class="fa fa-plus me-2"></i>Add Admin</button>
    <a href="/scheme_/admin/user/index.php" class="btn btn-secondary ms-2">Back</a>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/script.js"></script>
</body>
</html> 