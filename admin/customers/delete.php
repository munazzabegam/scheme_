<?php
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div style="color:red;text-align:center;">Database connection failed!</div>');
}
$error = '';
$success = '';
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
    $stmt = $conn->prepare("DELETE FROM Customers WHERE CustomerID = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $stmt->close();
$conn->close();
    header('Location: /scheme_/admin/customers/index.php?deleted=1');
    exit;
    } else {
        $error = 'Failed to delete customer.';
    }
    $stmt->close();
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
  <title>Delete Customer</title>
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
    }
    @media (max-width: 900px) {
      .main-container { margin-left: 0; padding: 12px 2px; }
      .profile-chip { position: static; margin-bottom: 18px; }
    }
    .modern-card { max-width: 480px; margin: 48px auto; border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(2,0,36,0.10); background: #fff; padding: 40px 32px 32px 32px; position: relative; }
    .modern-title { font-size: 2rem; font-weight: 700; color: #e74c3c; margin-bottom: 24px; letter-spacing: 0.5px; text-align: center; }
    .modern-btn { width: 100%; padding: 12px; font-size: 1.1rem; border-radius: 10px; background: linear-gradient(90deg, #e74c3c 0%, #ff7675 100%); color: #fff; border: none; font-weight: 600; transition: background 0.2s; margin-top: 10px; }
    .modern-btn:hover { background: linear-gradient(90deg, #ff7675 0%, #e74c3c 100%); color: #fff; }
    .modern-back { display: block; text-align: center; margin-top: 18px; color: #003399; text-decoration: none; font-weight: 500; transition: color 0.2s; }
    .modern-back:hover { color: #6a82fb; }
    .msg-error { background: #fdeaea; color: #e74c3c; border-radius: 8px; padding: 10px 16px; margin-bottom: 18px; text-align: center; font-weight: 500; }
    .desc { color: #444; margin-bottom: 24px; text-align: center; font-size: 1.1rem; }
    .fullpage-form-container {
      width: 100%;
      max-width: 650px;
      margin: 0 auto;
      padding: 32px 24px 24px 24px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 16px 0 rgba(22,33,62,0.06);
      border: 1px solid #e5e7eb;
    }
    .fullpage-form-title {
      font-size: 2.2rem;
      font-weight: 700;
      color: #e74c3c;
      margin-bottom: 32px;
      letter-spacing: 0.5px;
      text-align: left;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .delete-customer-row {
      display: flex;
      flex-wrap: wrap;
      gap: 32px;
    }
    .delete-customer-left {
      flex: 0 0 180px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      border-right: 1px solid #f0f0f0;
      padding-right: 32px;
      min-height: 180px;
    }
    .delete-customer-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: #f4f6fb;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 18px;
      font-size: 3rem;
      color: #bfc9da;
      overflow: hidden;
    }
    .delete-customer-right {
      flex: 1 1 0;
      padding-left: 32px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    @media (max-width: 900px) {
      .delete-customer-row { flex-direction: column; gap: 0; }
      .delete-customer-left { border-right: none; border-bottom: 1px solid #f0f0f0; padding-right: 0; padding-bottom: 24px; min-height: unset; }
      .delete-customer-right { padding-left: 0; }
    }
    .desc { color: #444; margin-bottom: 24px; text-align: left; font-size: 1.1rem; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<div class="main-container">
  <div class="profile-chip">
    <i class="fa-regular fa-user-circle"></i> <?= htmlspecialchars($role) ?>
  </div>
  <div class="fullpage-form-container">
    <div class="fullpage-form-title"><i class="fa fa-trash me-2"></i>Delete Customer</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php else: ?>
      <div class="delete-customer-row">
        <div class="delete-customer-left">
          <div class="delete-customer-avatar">
            <i class="fa fa-user"></i>
          </div>
        </div>
        <div class="delete-customer-right">
          <div class="desc">Are you sure you want to delete <strong><?= htmlspecialchars($customer['FullName']) ?></strong>?</div>
          <form method="post">
            <button class="modern-btn" type="submit"><i class="fa fa-trash me-2"></i>Delete</button>
            <a href="/scheme_/admin/customers/index.php" class="modern-back"><i class="fa fa-arrow-left me-1"></i>Back to Customers</a>
          </form>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/script.js"></script>
</body>
</html> 