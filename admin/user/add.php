<?php
include_once '../../config/database.php';
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'Admin';
    $status = $_POST['status'] ?? 'Active';
    if (!$name || !$email || !$password) {
        $error = 'Name, Email, and Password are required.';
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Admins (Name, Email, PasswordHash, Role, Status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $name, $email, $passwordHash, $role, $status);
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
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="title">Add New Admin</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Name</label>
        <input name="name" required />
      </div>
      <div class="form-group">
        <label>Email</label>
        <input name="email" type="email" required />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input name="password" type="password" required />
      </div>
      <div class="form-group">
        <label>Role</label>
        <select name="role">
          <option value="Verifier" selected>Verifier</option>
          <option value="SuperAdmin">SuperAdmin</option>
          <option value="Editor">Editor</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
      <button class="btn" type="submit">Add Admin</button>
      <a href="/scheme_/admin/user/index.php" class="back-btn">Back</a>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/script.js"></script>
</body>
</html> 