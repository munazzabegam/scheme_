<?php
include_once '../../config/database.php';

// Fetch live stats
$adminCount = $conn->query('SELECT COUNT(*) FROM Admins')->fetch_row()[0];
$customerCount = $conn->query('SELECT COUNT(*) FROM Customers')->fetch_row()[0];
$paymentCount = $conn->query('SELECT COUNT(*) FROM Payments')->fetch_row()[0];
$schemeCount = $conn->query('SELECT COUNT(*) FROM Schemes')->fetch_row()[0];
$notificationCount = $conn->query('SELECT COUNT(*) FROM Notifications')->fetch_row()[0];
$winnerCount = $conn->query('SELECT COUNT(*) FROM Winners')->fetch_row()[0];
$backupCount = $conn->query('SELECT COUNT(*) FROM backup')->fetch_row()[0];
$paymentQRCount = $conn->query('SELECT COUNT(*) FROM PaymentQR')->fetch_row()[0];

$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
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
    .welcome {
      font-size: 1.1rem;
      color: #7b7f87;
      margin-bottom: 18px;
      font-weight: 500;
    }
    .dashboard-cards {
      display: flex; gap: 32px; margin-top: 18px;
    }
    .dashboard-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.04);
      padding: 32px 36px;
      min-width: 180px;
      flex: 1 1 0;
      display: flex; flex-direction: column; align-items: center;
      transition: box-shadow 0.18s;
    }
    .dashboard-card:hover {
      box-shadow: 0 8px 32px rgba(0,0,0,0.07);
    }
    .dashboard-card .card-title {
      font-size: 1.1rem;
      color: #4f8cff;
      font-weight: 500;
      margin-bottom: 10px;
      letter-spacing: 0.01em;
    }
    .dashboard-card .card-number {
      font-size: 2.8rem;
      font-weight: 700;
      color: #4f8cff;
      margin-bottom: 6px;
      letter-spacing: 0.01em;
    }
    .dashboard-card .card-desc {
      font-size: 1rem;
      color: #7b7f87;
      font-weight: 400;
      margin-top: 2px;
    }
    .sidebar-footer {
      margin-top: auto;
      margin-left: 32px;
      color: #bfc9da;
      font-size: 0.95rem;
    }
    .topbar-premium {
      background: #16213e;
      color: #fff;
      padding: 20px 32px;
      border-radius: 12px;
      margin-bottom: 32px;
      display: flex;
      align-items: center;
      gap: 15px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .topbar-premium h2 {
      margin-bottom: 0;
      font-size: 1.8rem;
      font-weight: 700;
      color: #fff;
    }
    .topbar-premium i {
      font-size: 1.8rem;
      color: #4f8cff;
    }
    @media (max-width: 900px) {
      .main-container { margin-left: 0; padding: 12px 2px; }
      .dashboard-cards { flex-direction: column; gap: 18px; }
      .profile-chip { position: static; margin-bottom: 18px; }
      .topbar-premium {
        flex-direction: column;
        align-items: flex-start;
        padding: 15px 20px;
      }
      .topbar-premium h2 {
        font-size: 1.5rem;
      }
      .topbar-premium i {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<div class="main-container">
  <div class="profile-chip">
    <i class="fa-regular fa-user-circle"></i> <?= htmlspecialchars($role) ?>
  </div>
  <div class="topbar-premium mb-4">
    <h2><i class="fa-solid fa-gauge-high"></i> Dashboard</h2>
  </div>
  <div class="row g-4 mb-4">
    <div class="col-md-3 col-6">
      <div class="dashboard-card text-center">
        <div class="card-title"><i class="fa-solid fa-user-shield"></i> Admins</div>
        <div class="card-number"><?= $adminCount ?></div>
        <div class="card-desc">Total Admins</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="dashboard-card text-center">
        <div class="card-title"><i class="fa-solid fa-users"></i> Customers</div>
        <div class="card-number"><?= $customerCount ?></div>
        <div class="card-desc">Total Customers</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="dashboard-card text-center">
        <div class="card-title"><i class="fa-solid fa-credit-card"></i> Payments</div>
        <div class="card-number"><?= $paymentCount ?></div>
        <div class="card-desc">Total Payments</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="dashboard-card text-center">
        <div class="card-title"><i class="fa-solid fa-layer-group"></i> Schemes</div>
        <div class="card-number"><?= $schemeCount ?></div>
        <div class="card-desc">Total Schemes</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="dashboard-card text-center">
        <div class="card-title"><i class="fa-solid fa-bell"></i> Notifications</div>
        <div class="card-number"><?= $notificationCount ?></div>
        <div class="card-desc">Total Notifications</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="dashboard-card text-center">
        <div class="card-title"><i class="fa-solid fa-trophy"></i> Winners</div>
        <div class="card-number"><?= $winnerCount ?></div>
        <div class="card-desc">Total Winners</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="dashboard-card text-center">
        <div class="card-title"><i class="fa-solid fa-database"></i> Backups</div>
        <div class="card-number"><?= $backupCount ?></div>
        <div class="card-desc">Total Backups</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="dashboard-card text-center">
        <div class="card-title"><i class="fa-solid fa-qrcode"></i> Payment QR</div>
        <div class="card-number"><?= $paymentQRCount ?></div>
        <div class="card-desc">Payment QR Codes</div>
      </div>
    </div>
  </div>
  <!-- You can add more dashboard widgets, recent activity, or summary tables here -->
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 