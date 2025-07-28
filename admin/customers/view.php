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
<?php
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Customer</title>
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
    .main-container { 
      margin-left: 250px; 
      padding: 48px 32px 32px 32px; 
      min-height: 100vh; 
    }
    .profile-chip {
      position: absolute; 
      top: 32px; 
      right: 48px;
      display: flex; 
      align-items: center; 
      gap: 10px;
      background: #fff;
      color: #23272f;
      border-radius: 24px;
      padding: 6px 18px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
      font-size: 1rem;
      font-weight: 500;
      z-index: 10;
      transition: box-shadow 0.3s;
    }
    .profile-chip:hover {
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.08);
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
    .sidebar-footer {
      margin-top: auto;
      margin-left: 32px;
      color: #bfc9da;
      font-size: 0.95rem;
    }
    .customer-header { 
      display: flex; 
      align-items: center; 
      gap: 32px; 
      margin-bottom: 24px; 
    }
    .customer-avatar {
      width: 90px; 
      height: 90px; 
      border-radius: 24px;
      background: #fff;
      display: flex; 
      align-items: center; 
      justify-content: center;
      font-size: 2.8rem; 
      color: #003399; 
      font-weight: 700;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
      border: 1px solid #e5e7eb;
      transition: box-shadow 0.3s;
    }
    .customer-avatar:hover {
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.08);
    }
    .customer-header-info { flex: 1 1 0; }
    .customer-header-title { 
      font-size: 2rem; 
      font-weight: 700; 
      margin-bottom: 4px; 
      display: flex; 
      align-items: center; 
      gap: 12px; 
      color: #2d3a4b; 
    }
    .customer-status { 
      font-size: 1rem; 
      font-weight: 600; 
      padding: 4px 14px; 
      border-radius: 12px; 
      background: #eafaf1; 
      color: #43b581; 
      margin-left: 10px; 
    }
    .customer-header-meta { 
      color: #888; 
      font-size: 1.05rem; 
      margin-bottom: 6px; 
    }
    .customer-header-actions { 
      display: flex; 
      gap: 10px; 
      margin-top: 8px; 
      flex-wrap: wrap; 
    }
    .customer-header-actions .btn {
      font-size: 1rem; 
      font-weight: 600; 
      border-radius: 12px; 
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
      transition: background 0.25s, color 0.25s, box-shadow 0.25s, border 0.25s;
    }
    .customer-header-actions .btn-primary {
      background: #003399;
      border: none; 
      color: #fff;
    }
    .customer-header-actions .btn-primary:hover {
      background: #002266;
      color: #fff; 
      box-shadow: 0 4px 16px 0 rgba(0,51,153,0.3);
    }
    .customer-header-actions .btn-warning {
      background: #f6c23e;
      border: none; 
      color: #fff;
    }
    .customer-header-actions .btn-warning:hover {
      background: #f39c12;
      color: #fff; 
      box-shadow: 0 4px 16px 0 rgba(246,194,62,0.3);
    }
    .customer-header-actions .btn-outline-secondary {
      border: 1px solid #bfc9da; 
      color: #003399; 
      background: #fff;
    }
    .customer-header-actions .btn-outline-secondary:hover {
      background: #f4f6fb; 
      color: #003399; 
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
    }
    .customer-header-actions .btn-danger {
      background: #e74c3c;
      border: none; 
      color: #fff;
    }
    .customer-header-actions .btn-danger:hover {
      background: #c0392b;
      color: #fff; 
      box-shadow: 0 4px 16px 0 rgba(231,76,60,0.3);
    }
    .customer-content-row { 
      display: flex; 
      gap: 32px; 
    }
    .customer-main { flex: 2 1 0; }
    .customer-side { flex: 1 1 0; min-width: 320px; }
    .customer-card, .side-widget {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
      border: 1px solid #e5e7eb;
      margin-bottom: 24px; 
      padding: 24px 28px;
      transition: box-shadow 0.3s;
    }
    .customer-card:hover, .side-widget:hover {
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.1);
    }
    .customer-card-title, .side-widget-title { 
      font-size: 1.2rem; 
      font-weight: 600; 
      margin-bottom: 18px; 
      color: #2d3a4b; 
    }
    .customer-info-grid { 
      display: grid; 
      grid-template-columns: 1fr 1fr; 
      gap: 12px 32px; 
    }
    .customer-info-label { 
      color: #003399; 
      font-size: 1rem; 
      font-weight: 500; 
      margin-bottom: 2px; 
    }
    .customer-info-value { 
      color: #23272f; 
      font-size: 1.08rem; 
      font-weight: 600; 
    }
    .nav-tabs { 
      border-bottom: 1px solid #e5e7eb; 
    }
    .nav-tabs .nav-link.active {
      background: rgba(79,140,255,0.08);
      color: #003399;
      font-weight: 600;
      border-bottom: 2px solid #4f8cff;
      border-radius: 8px 8px 0 0;
      transition: background 0.25s, color 0.25s, border-bottom 0.25s;
    }
    .nav-tabs .nav-link {
      color: #003399;
      font-weight: 500;
      border-radius: 8px 8px 0 0;
      transition: background 0.25s, color 0.25s;
    }
    .nav-tabs .nav-link:hover {
      background: rgba(79,140,255,0.04);
      color: #003399;
    }
    .tab-pane { 
      padding: 24px 0 0 0; 
    }
    .empty-state { 
      text-align: center; 
      color: #888; 
      padding: 32px 0; 
    }
    .empty-state i { 
      font-size: 2.5rem; 
      margin-bottom: 10px; 
      color: #bfc9da; 
    }
    .side-widget .btn {
      width: 100%; 
      margin-bottom: 10px;
      background: #003399;
      color: #fff; 
      border: none; 
      font-weight: 600; 
      border-radius: 12px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
      transition: background 0.25s, color 0.25s, box-shadow 0.25s;
    }
    .side-widget .btn:hover {
      background: #002266;
      color: #fff; 
      box-shadow: 0 4px 16px 0 rgba(0,51,153,0.3);
    }
    .side-widget .btn-outline-secondary {
      background: #fff; 
      color: #003399; 
      border: 1px solid #bfc9da;
    }
    .side-widget .btn-outline-secondary:hover {
      background: #f4f6fb; 
      color: #003399; 
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
    }
    @media (max-width: 1100px) { 
      .customer-content-row { flex-direction: column; } 
      .customer-side { min-width: unset; } 
    }
    @media (max-width: 900px) { 
      .main-container { margin-left: 0; padding: 12px 2px; } 
      .profile-chip { position: static; margin-bottom: 18px; } 
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<div class="main-container">
  <div class="profile-chip">
    <i class="fa-regular fa-user-circle"></i> <?= htmlspecialchars($role) ?>
  </div>
  <!-- Header -->
  <div class="customer-header">
    <div class="customer-avatar">
      <i class="fa fa-user"></i>
    </div>
    <div class="customer-header-info">
      <div class="customer-header-title">
        <?= htmlspecialchars($customer['FullName']) ?>
        <?php if ($customer['Status'] === 'Active'): ?>
          <span class="customer-status">Active</span>
        <?php else: ?>
          <span class="customer-status" style="background:#fdeaea;color:#e74c3c;">Inactive</span>
        <?php endif; ?>
      </div>
      <div class="customer-header-meta">
        <span><b>ID:</b> <?= htmlspecialchars($customer['CustomerID']) ?></span> &nbsp;|
        <span><i class="fa fa-phone"></i> <?= htmlspecialchars($customer['PhoneNumber']) ?></span> &nbsp;|
        <span><i class="fa fa-calendar"></i> Joined: <?= date('M d, Y', strtotime($customer['CreatedAt'])) ?></span>
      </div>
      <div class="customer-header-actions">
        <a href="edit.php?id=<?= $customer['CustomerID'] ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Edit Customer</a>
        <button class="btn btn-warning"><i class="fa fa-ban"></i> Deactivate</button>
        <button class="btn btn-outline-secondary"><i class="fa fa-plus"></i> Add Subscription</button>
        <button class="btn btn-outline-secondary"><i class="fa fa-credit-card"></i> Record Payment</button>
        <a href="delete.php?id=<?= $customer['CustomerID'] ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
      </div>
    </div>
  </div>
  <!-- Content Row -->
  <div class="customer-content-row">
    <div class="customer-main">
      <!-- Basic Information Card -->
      <div class="customer-card">
        <div class="customer-card-title">Basic Information</div>
        <div class="customer-info-grid">
          <div><div class="customer-info-label">Full Name</div><div class="customer-info-value"><?= htmlspecialchars($customer['FullName']) ?></div></div>
          <div><div class="customer-info-label">Unique ID</div><div class="customer-info-value"><?= htmlspecialchars($customer['CustomerID']) ?></div></div>
          <div><div class="customer-info-label">Contact Number</div><div class="customer-info-value"><?= htmlspecialchars($customer['PhoneNumber']) ?></div></div>
          <div><div class="customer-info-label">Email Address</div><div class="customer-info-value"><?= htmlspecialchars($customer['Email']) ?></div></div>
          <div><div class="customer-info-label">Status</div><div class="customer-info-value"><?= htmlspecialchars($customer['Status']) ?></div></div>
          <div><div class="customer-info-label">Registration Date</div><div class="customer-info-value"><?= date('M d, Y', strtotime($customer['CreatedAt'])) ?></div></div>
          <div><div class="customer-info-label">Referral Code</div><div class="customer-info-value">-</div></div>
          <div><div class="customer-info-label">Address</div><div class="customer-info-value"><?= htmlspecialchars($customer['Address']) ?></div></div>
        </div>
      </div>
      <!-- Customer Information Tabs -->
      <div class="customer-card">
        <div class="customer-card-title">Customer Information</div>
        <ul class="nav nav-tabs" id="customerTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions" type="button" role="tab">Subscriptions</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">Payments</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="withdrawals-tab" data-bs-toggle="tab" data-bs-target="#withdrawals" type="button" role="tab">Withdrawals</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="prizes-tab" data-bs-toggle="tab" data-bs-target="#prizes" type="button" role="tab">Prizes & Winnings</button>
          </li>
        </ul>
        <div class="tab-content" id="customerTabsContent">
          <div class="tab-pane fade show active" id="subscriptions" role="tabpanel">
            <div class="empty-state">
              <i class="fa fa-file-alt"></i><br />No subscriptions found for this customer.<br />
              <button class="btn btn-primary mt-3"><i class="fa fa-plus"></i> Add New Subscription</button>
            </div>
          </div>
          <div class="tab-pane fade" id="payments" role="tabpanel">
            <div class="empty-state">
              <i class="fa fa-credit-card"></i><br />No payments found for this customer.
            </div>
          </div>
          <div class="tab-pane fade" id="withdrawals" role="tabpanel">
            <div class="empty-state">
              <i class="fa fa-money-bill-wave"></i><br />No withdrawals found for this customer.
            </div>
          </div>
          <div class="tab-pane fade" id="prizes" role="tabpanel">
            <div class="empty-state">
              <i class="fa fa-trophy"></i><br />No prizes or winnings found for this customer.
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="customer-side">
      <div class="side-widget">
        <div class="side-widget-title">Total Subscriptions</div>
        <div style="font-size:2.2rem;font-weight:700;text-align:center;background:#f6c23e;color:#fff;border-radius:8px;padding:18px 0;margin-bottom:18px;">0</div>
      </div>
      <div class="side-widget">
        <div class="side-widget-title">Activity Log</div>
        <div class="empty-state" style="padding:12px 0;">No activity logs found.</div>
      </div>
      <div class="side-widget">
        <div class="side-widget-title">Quick Actions</div>
        <button class="btn btn-primary"><i class="fa fa-plus"></i> Add Subscription</button>
        <button class="btn btn-outline-secondary"><i class="fa fa-credit-card"></i> Record Payment</button>
        <button class="btn btn-outline-secondary"><i class="fa fa-key"></i> Reset Password</button>
        <button class="btn btn-outline-secondary"><i class="fa fa-bell"></i> Send Notification</button>
        <button class="btn btn-outline-secondary"><i class="fa fa-eye"></i> View Password</button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/script.js"></script>
</body>
</html> 