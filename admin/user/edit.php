<?php
include_once '../../config/database.php';
$error = '';
$success = '';
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: /scheme_/admin/user/index.php');
    exit;
}
// Fetch current admin data
$stmt = $conn->prepare('SELECT Name, Email, Role, Status FROM Admins WHERE AdminID = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header('Location: /scheme_/admin/user/index.php');
    exit;
}
$stmt->bind_result($name, $email, $role, $status);
$stmt->fetch();
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['name'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $new_role = $_POST['role'] ?? 'Admin';
    $new_status = $_POST['status'] ?? 'Active';
    if (!$new_name || !$new_email) {
        $error = 'Name and Email are required.';
    } else {
        if ($new_password) {
            $passwordHash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare('UPDATE Admins SET Name=?, Email=?, PasswordHash=?, Role=?, Status=? WHERE AdminID=?');
            $update->bind_param('sssssi', $new_name, $new_email, $passwordHash, $new_role, $new_status, $id);
        } else {
            $update = $conn->prepare('UPDATE Admins SET Name=?, Email=?, Role=?, Status=? WHERE AdminID=?');
            $update->bind_param('ssssi', $new_name, $new_email, $new_role, $new_status, $id);
        }
        if ($update->execute()) {
            $update->close();
            $conn->close();
            header('Location: /scheme_/admin/user/index.php?edited=1');
            exit;
        } else {
            $error = 'Failed to update admin.';
        }
        $update->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="title">Edit Admin</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Name</label>
        <input name="name" required value="<?= htmlspecialchars($name) ?>" />
      </div>
      <div class="form-group">
        <label>Email</label>
        <input name="email" type="email" required value="<?= htmlspecialchars($email) ?>" />
      </div>
      <div class="form-group">
        <label>Password (leave blank to keep current)</label>
        <input name="password" type="password" />
      </div>
      <div class="form-group">
        <label>Role</label>
        <select name="role">
          <option value="Verifier"<?= $role === 'Verifier' ? ' selected' : '' ?>>Verifier</option>
          <option value="SuperAdmin"<?= $role === 'SuperAdmin' ? ' selected' : '' ?>>SuperAdmin</option>
          <option value="Editor"<?= $role === 'Editor' ? ' selected' : '' ?>>Editor</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          <option value="Active"<?= $status === 'Active' ? ' selected' : '' ?>>Active</option>
          <option value="Inactive"<?= $status === 'Inactive' ? ' selected' : '' ?>>Inactive</option>
        </select>
      </div>
      <button class="btn" type="submit">Update Admin</button>
      <a href="/scheme_/admin/user/index.php" class="back-btn">Back</a>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/script.js"></script>
</body>
</html> 