<?php
// Fetch customer details by ID
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div style="color:red;text-align:center;">Database connection failed!</div>');
}
$customer = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
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
  <title>View Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom Admin Panel Styles -->
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="title">Customer Details</div>
    <?php if ($customer): ?>
      <div class="info-row"><span class="label">Name</span><span class="value"><?= htmlspecialchars($customer['FullName']) ?></span></div>
      <div class="info-row"><span class="label">Email</span><span class="value"><?= htmlspecialchars($customer['Email']) ?></span></div>
      <div class="info-row"><span class="label">Phone</span><span class="value"><?= htmlspecialchars($customer['PhoneNumber']) ?></span></div>
      <div class="info-row"><span class="label">DOB</span><span class="value"><?= htmlspecialchars($customer['DOB']) ?></span></div>
      <div class="info-row"><span class="label">Gender</span><span class="value"><?= htmlspecialchars($customer['Gender']) ?></span></div>
      <div class="info-row"><span class="label">Status</span><span class="value"><?= htmlspecialchars($customer['Status']) ?></span></div>
      <div class="info-row"><span class="label">Created At</span><span class="value"><?= htmlspecialchars($customer['CreatedAt']) ?></span></div>
      <a href="index.php" class="back-btn">Back to Customers</a>
    <?php else: ?>
      <div style="color:red;">Customer not found.</div>
      <a href="index.php" class="back-btn">Back to Customers</a>
    <?php endif; ?>
  </div>
  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom Admin Panel Scripts -->
  <script src="../assets/script.js"></script>
</body>
</html> 