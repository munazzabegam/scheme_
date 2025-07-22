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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .modern-card { max-width: 480px; margin: 48px auto; border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(2,0,36,0.10); background: #fff; padding: 40px 32px 32px 32px; position: relative; }
    .modern-title { font-size: 2rem; font-weight: 700; color: #e74c3c; margin-bottom: 24px; letter-spacing: 0.5px; text-align: center; }
    .modern-btn { width: 100%; padding: 12px; font-size: 1.1rem; border-radius: 10px; background: linear-gradient(90deg, #e74c3c 0%, #ff7675 100%); color: #fff; border: none; font-weight: 600; transition: background 0.2s; margin-top: 10px; }
    .modern-btn:hover { background: linear-gradient(90deg, #ff7675 0%, #e74c3c 100%); color: #fff; }
    .modern-back { display: block; text-align: center; margin-top: 18px; color: #003399; text-decoration: none; font-weight: 500; transition: color 0.2s; }
    .modern-back:hover { color: #6a82fb; }
    .msg-error { background: #fdeaea; color: #e74c3c; border-radius: 8px; padding: 10px 16px; margin-bottom: 18px; text-align: center; font-weight: 500; }
    .desc { color: #444; margin-bottom: 24px; text-align: center; font-size: 1.1rem; }
  </style>
</head>
<body>
  <div class="modern-card">
    <div class="modern-title"><i class="fa fa-trash me-2"></i>Delete Customer</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php else: ?>
    <div class="desc">Are you sure you want to delete <strong><?= htmlspecialchars($customer['FullName']) ?></strong>?</div>
      <form method="post">
      <button class="modern-btn" type="submit"><i class="fa fa-trash me-2"></i>Delete</button>
      <a href="/scheme_/admin/customers/index.php" class="modern-back"><i class="fa fa-arrow-left me-1"></i>Back to Customers</a>
      </form>
    <?php endif; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/script.js"></script>
</body>
</html> 