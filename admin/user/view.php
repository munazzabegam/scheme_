<?php
include_once '../../config/database.php';
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: /scheme_/admin/user/index.php');
    exit;
}
$stmt = $conn->prepare('SELECT Name, Email, Role, Status, CreatedAt FROM Admins WHERE AdminID = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header('Location: /scheme_/admin/user/index.php');
    exit;
}
$stmt->bind_result($name, $email, $role, $status, $created);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<div class="main-container">
  <div class="profile-chip">
    <i class="fa-regular fa-user-circle"></i>
  </div>
  <div class="form-title"><i class="fa fa-user"></i> Admin Details</div>
  <div class="card-form" style="background:#fff; border-radius:16px; box-shadow:0 8px 32px 0 rgba(2,0,36,0.08); padding:32px;">
    <div class="info-row"><span class="label">Name:</span> <span class="value"><?= htmlspecialchars($name) ?></span></div>
    <div class="info-row"><span class="label">Email:</span> <span class="value"><?= htmlspecialchars($email) ?></span></div>
    <div class="info-row"><span class="label">Role:</span> <span class="value"><?= htmlspecialchars($role) ?></span></div>
    <div class="info-row"><span class="label">Status:</span> <span class="value"><?= htmlspecialchars($status) ?></span></div>
    <div class="info-row"><span class="label">Created At:</span> <span class="value"><?= date('M d, Y', strtotime($created)) ?></span></div>
    <a href="/scheme_/admin/user/index.php" class="btn btn-secondary">Back</a>
  </div>
</div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/script.js"></script>
</body>
</html> 