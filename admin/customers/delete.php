<?php
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div style="color:red;text-align:center;">Database connection failed!</div>');
}
$deleted = false;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $stmt = $conn->prepare("DELETE FROM Customers WHERE CustomerID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        $deleted = true;
    } else {
        $sql = "SELECT * FROM Customers WHERE CustomerID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        $stmt->close();
    }
}
$conn->close();
if ($deleted) {
    header('Location: /scheme_/admin/customers/index.php?deleted=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom Admin Panel Styles -->
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="title">Delete Customer</div>
    <?php if (isset($customer)): ?>
      <div class="desc">Are you sure you want to delete <b><?= htmlspecialchars($customer['FullName']) ?></b>?</div>
      <form method="post">
        <button class="btn btn-danger" name="confirm" value="yes" type="submit">Yes, Delete</button>
        <a href="/scheme_/admin/customers/index.php" class="btn btn-secondary">Cancel</a>
      </form>
    <?php else: ?>
      <div class="desc">Customer not found or already deleted.</div>
      <a href="/scheme_/admin/customers/index.php" class="btn btn-secondary">Back</a>
    <?php endif; ?>
  </div>
  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom Admin Panel Scripts -->
  <script src="../assets/script.js"></script>
</body>
</html> 