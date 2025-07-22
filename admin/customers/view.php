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
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .modern-card { max-width: 480px; margin: 48px auto; border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(2,0,36,0.10); background: #fff; padding: 40px 32px 32px 32px; position: relative; }
    .modern-title { font-size: 2rem; font-weight: 700; color: #003399; margin-bottom: 24px; letter-spacing: 0.5px; text-align: center; }
    .info-row { margin-bottom: 18px; display: flex; align-items: center; gap: 12px; }
    .label { color: #888; font-size: 15px; min-width: 120px; display: inline-block; font-weight: 500; }
    .value { font-size: 18px; color: #222; font-weight: 500; word-break: break-all; }
    .modern-back { display: block; text-align: center; margin-top: 18px; color: #003399; text-decoration: none; font-weight: 500; transition: color 0.2s; }
    .modern-back:hover { color: #6a82fb; }
    .msg-error { background: #fdeaea; color: #e74c3c; border-radius: 8px; padding: 10px 16px; margin-bottom: 18px; text-align: center; font-weight: 500; }
  </style>
</head>
<body>
  <div class="modern-card">
    <div class="modern-title"><i class="fa fa-user me-2"></i>Customer Details</div>
    <?php if ($error): ?><div class="msg-error"><?= $error ?></div><?php else: ?>
      <div class="info-row"><span class="label">Name:</span> <span class="value"><?= htmlspecialchars($customer['FullName']) ?></span></div>
      <div class="info-row"><span class="label">Email:</span> <span class="value"><?= htmlspecialchars($customer['Email']) ?></span></div>
      <div class="info-row"><span class="label">Phone:</span> <span class="value"><?= htmlspecialchars($customer['PhoneNumber']) ?></span></div>
      <div class="info-row"><span class="label">Address:</span> <span class="value"><?= htmlspecialchars($customer['Address']) ?></span></div>
      <div class="info-row"><span class="label">Date of Birth:</span> <span class="value"><?= htmlspecialchars($customer['DOB']) ?></span></div>
      <div class="info-row"><span class="label">Gender:</span> <span class="value"><?= htmlspecialchars($customer['Gender']) ?></span></div>
      <div class="info-row"><span class="label">Status:</span> <span class="value"><?= htmlspecialchars($customer['Status']) ?></span></div>
      <div class="info-row"><span class="label">Created At:</span> <span class="value"><?= htmlspecialchars($customer['CreatedAt']) ?></span></div>
      <div class="info-row"><span class="label">Updated At:</span> <span class="value"><?= htmlspecialchars($customer['UpdatedAt']) ?></span></div>
      <a href="/scheme_/admin/customers/index.php" class="modern-back"><i class="fa fa-arrow-left me-1"></i>Back to Customers</a>
    <?php endif; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/script.js"></script>
</body>
</html> 