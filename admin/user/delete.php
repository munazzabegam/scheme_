<?php
include_once '../../config/database.php';
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: /scheme_/admin/user/index.php');
    exit;
}
$error = '';
$deleted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM Admins WHERE AdminID = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $deleted = true;
        $stmt->close();
        $conn->close();
        header('Location: /scheme_/admin/user/index.php?deleted=1');
        exit;
    } else {
        $error = 'Failed to delete admin.';
        $stmt->close();
    }
}
$stmt = $conn->prepare('SELECT Name, Email FROM Admins WHERE AdminID = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header('Location: /scheme_/admin/user/index.php');
    exit;
}
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="title" style="color:#e74c3c;">Delete Admin</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php endif; ?>
    <div class="desc">Are you sure you want to delete the following admin?</div>
    <div class="info-row"><span class="label">Name:</span> <span class="value"><?= htmlspecialchars($name) ?></span></div>
    <div class="info-row"><span class="label">Email:</span> <span class="value"><?= htmlspecialchars($email) ?></span></div>
    <form method="post" style="margin-top:24px;">
      <button class="btn btn-danger" type="submit">Delete</button>
      <a href="/scheme_/admin/user/index.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/script.js"></script>
</body>
</html> 