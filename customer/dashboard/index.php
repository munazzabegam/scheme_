<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: /scheme_/customer/login.php');
    exit;
}
$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'] ?? '';

$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div class="alert alert-danger text-center">Database connection failed!</div>');
}

// Get active enrollments
$enrollments = 0;
$enrollments_sql = "SELECT COUNT(*) as cnt FROM CustomerSchemeEnrollments WHERE CustomerID = ? AND Status = 'Enrolled'";
$stmt = $conn->prepare($enrollments_sql);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$stmt->bind_result($enrollments);
$stmt->fetch();
$stmt->close();

// Get total payments
$total_payments = 0;
$payments_sql = "SELECT SUM(Amount) as total FROM Payments WHERE CustomerID = ? AND PaymentStatus = 'Success'";
$stmt = $conn->prepare($payments_sql);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$stmt->bind_result($total_payments);
$stmt->fetch();
$stmt->close();

// Get next installment
$next_inst = null;
$next_sql = "SELECT i.InstallmentName, i.Amount, i.DrawDate, s.SchemeName FROM Installments i
    INNER JOIN CustomerSchemeEnrollments e ON i.SchemeID = e.SchemeID
    INNER JOIN Schemes s ON s.SchemeID = i.SchemeID
    WHERE e.CustomerID = ? AND i.DrawDate >= CURDATE() AND e.Status = 'Enrolled'
    ORDER BY i.DrawDate ASC LIMIT 1";
$stmt = $conn->prepare($next_sql);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$stmt->bind_result($inst_name, $inst_amount, $inst_date, $scheme_name);
if ($stmt->fetch()) {
    $next_inst = [
        'name' => $inst_name,
        'amount' => $inst_amount,
        'date' => $inst_date,
        'scheme' => $scheme_name
    ];
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #f7f8fa;
      font-family: 'Poppins', 'Inter', Arial, sans-serif;
      color: #23272f;
      min-height: 100vh;
    }
    .main-container {
      max-width: 900px;
      margin: 40px auto 0 auto;
      padding: 32px 16px 16px 16px;
      margin-left: 250px;
    }
    @media (max-width: 900px) {
      .main-container { margin-left: 0; padding: 12px 2vw; }
    }
    .dashboard-title {
      font-size: 2rem;
      font-weight: 700;
      color: #003399;
      margin-bottom: 18px;
      letter-spacing: 0.5px;
      text-align: left;
    }
    .summary-cards {
      display: flex;
      gap: 24px;
      flex-wrap: wrap;
      margin-bottom: 32px;
    }
    .summary-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
      border: 1px solid #e5e7eb;
      flex: 1 1 220px;
      min-width: 220px;
      padding: 28px 20px 20px 20px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      transition: box-shadow 0.3s;
    }
    .summary-card:hover {
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.10);
    }
    .summary-icon {
      font-size: 2.2rem;
      margin-bottom: 10px;
      color: #003399;
      opacity: 0.85;
    }
    .summary-label {
      font-size: 1.1rem;
      color: #888;
      margin-bottom: 2px;
      font-weight: 500;
    }
    .summary-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: #23272f;
      margin-bottom: 2px;
    }
    .quick-links {
      display: flex;
      gap: 18px;
      flex-wrap: wrap;
      margin-top: 18px;
    }
    .quick-link {
      background: #003399;
      color: #fff;
      border-radius: 10px;
      padding: 16px 28px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 2px 8px 0 rgba(0,51,153,0.08);
      transition: background 0.2s, box-shadow 0.2s, color 0.2s;
    }
    .quick-link:hover {
      background: #002266;
      color: #fff;
      box-shadow: 0 4px 16px 0 rgba(0,51,153,0.15);
      text-decoration: none;
    }
    @media (max-width: 700px) {
      .summary-cards { flex-direction: column; gap: 16px; }
      .quick-links { flex-direction: column; gap: 10px; }
    }
  </style>
</head>
<body>
<?php $current = 'dashboard'; include '../components/sidebar.php'; ?>
  <div class="main-container">
    <div class="dashboard-title">Welcome, <?= htmlspecialchars($customer_name) ?>!</div>
    <div class="summary-cards">
      <div class="summary-card">
        <div class="summary-icon"><i class="fa fa-cubes"></i></div>
        <div class="summary-label">Active Schemes</div>
        <div class="summary-value"><?= $enrollments ?></div>
      </div>
      <div class="summary-card">
        <div class="summary-icon"><i class="fa fa-rupee-sign"></i></div>
        <div class="summary-label">Total Paid</div>
        <div class="summary-value">₹<?= number_format($total_payments, 2) ?></div>
      </div>
      <div class="summary-card">
        <div class="summary-icon"><i class="fa fa-calendar-check"></i></div>
        <div class="summary-label">Next Installment</div>
        <div class="summary-value">
          <?php if ($next_inst): ?>
            <?= htmlspecialchars($next_inst['name']) ?> <br>
            <span style="font-size:1rem;font-weight:400;">₹<?= number_format($next_inst['amount'],2) ?> on <?= date('M d, Y', strtotime($next_inst['date'])) ?><br>Scheme: <?= htmlspecialchars($next_inst['scheme']) ?></span>
          <?php else: ?>
            <span style="font-size:1rem;font-weight:400;">No upcoming installment</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="quick-links">
      <a href="/scheme_/customer/profile.php" class="quick-link"><i class="fa fa-user"></i> Profile</a>
      <a href="/scheme_/customer/schemes.php" class="quick-link"><i class="fa fa-cubes"></i> Schemes</a>
      <a href="/scheme_/customer/enrollments.php" class="quick-link"><i class="fa fa-list"></i> My Enrollments</a>
      <a href="/scheme_/customer/payments.php" class="quick-link"><i class="fa fa-credit-card"></i> Payments</a>
      <a href="/scheme_/customer/installments.php" class="quick-link"><i class="fa fa-calendar-check"></i> Installments</a>
      <a href="/scheme_/customer/notifications.php" class="quick-link"><i class="fa fa-bell"></i> Notifications</a>
      <a href="/scheme_/customer/logout.php" class="quick-link" style="background:#e74c3c;"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
</body>
</html> 