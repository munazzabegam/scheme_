<?php
// Dashboard for Admin
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

include '../components/sidebar.php';

$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('Database connection failed!');
}

// Get counts
$admin_count = $conn->query('SELECT COUNT(*) FROM Admins')->fetch_row()[0];
$customer_count = $conn->query('SELECT COUNT(*) FROM Customers')->fetch_row()[0];
$payment_count = $conn->query('SELECT COUNT(*) FROM Payments')->fetch_row()[0];
$scheme_count = $conn->query('SELECT COUNT(*) FROM Schemes')->fetch_row()[0];
$installment_count = $conn->query('SELECT COUNT(*) FROM Installments')->fetch_row()[0];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <style>
    body {
      background: #f4f6fb;
      min-height: 100vh;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      color: #222;
    }
    .dashboard-container {
      max-width: 1100px;
      margin: 40px auto;
      padding: 32px 24px;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 32px 0 rgba(2,0,36,0.08);
      margin-left: 240px;
    }
    @media (max-width: 900px) {
      .dashboard-container {
        margin-left: 0;
        margin-top: 120px;
        padding: 18px 6px;
        max-width: 100vw;
      }
      .sidebar {
        position: relative !important;
        width: 100% !important;
        min-height: unset !important;
        flex-direction: row !important;
        padding: 0 !important;
        box-shadow: none !important;
      }
    }
    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
    }
    .dashboard-header h1 {
      font-size: 2.2rem;
      font-weight: 700;
      color: #003399;
      margin: 0;
    }
    .dashboard-header .admin-info {
      font-size: 1.1rem;
      color: #555;
    }
    .summary-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 28px;
      justify-content: space-between;
    }
    .card {
      flex: 1 1 180px;
      min-width: 180px;
      max-width: 220px;
      background: linear-gradient(135deg, #c0c9ee 0%, #a2aadb 100%);
      color: #222;
      border-radius: 16px;
      box-shadow: 0 4px 16px 0 rgba(137,138,196,0.10);
      padding: 32px 20px 24px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.2s;
    }
    .card:hover {
      transform: translateY(-6px) scale(1.04);
      box-shadow: 0 8px 32px 0 rgba(137,138,196,0.18);
    }
    .card-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 12px;
      color: #003399;
      letter-spacing: 0.5px;
    }
    .card-count {
      font-size: 2.6rem;
      font-weight: 700;
      color: #171717;
      margin-bottom: 6px;
    }
    .card-label {
      font-size: 1rem;
      color: #555;
      margin-top: 2px;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_email']); ?>!</h1>
      <div class="admin-info">SuperAdmin</div>
    </div>
    <div class="summary-cards">
      <div class="card">
        <div class="card-title">Admins</div>
        <div class="card-count"><?php echo $admin_count; ?></div>
        <div class="card-label">Total Admins</div>
      </div>
      <div class="card">
        <div class="card-title">Customers</div>
        <div class="card-count"><?php echo $customer_count; ?></div>
        <div class="card-label">Total Customers</div>
      </div>
      <div class="card">
        <div class="card-title">Payments</div>
        <div class="card-count"><?php echo $payment_count; ?></div>
        <div class="card-label">Total Payments</div>
      </div>
      <div class="card">
        <div class="card-title">Schemes</div>
        <div class="card-count"><?php echo $scheme_count; ?></div>
        <div class="card-label">Total Schemes</div>
      </div>
      <div class="card">
        <div class="card-title">Installments</div>
        <div class="card-count"><?php echo $installment_count; ?></div>
        <div class="card-label">Total Installments</div>
      </div>
    </div>
  </div>
</body>
</html> 