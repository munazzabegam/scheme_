<?php
// Database connection
include_once '../../config/database.php';

$error = '';
$id = $_GET['id'] ?? null;
if (!$id) {
    $error = 'No scheme ID provided.';
} else {
    // Get scheme details
    $stmt = $conn->prepare("SELECT * FROM Schemes WHERE SchemeID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $scheme = $result->fetch_assoc();
    $stmt->close();
    
    if (!$scheme) {
        $error = 'Scheme not found.';
    } else {
        // Get installments for this scheme
        $stmt2 = $conn->prepare("SELECT * FROM Installments WHERE SchemeID = ? ORDER BY InstallmentNumber ASC");
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $installments_result = $stmt2->get_result();
        $installments = [];
        while ($row = $installments_result->fetch_assoc()) {
            $installments[] = $row;
        }
        $stmt2->close();
        
        // Get enrollment count
        $stmt3 = $conn->prepare("SELECT COUNT(*) as enrollment_count FROM CustomerSchemeEnrollments WHERE SchemeID = ?");
        $stmt3->bind_param('i', $id);
        $stmt3->execute();
        $enrollment_result = $stmt3->get_result();
        $enrollment_count = $enrollment_result->fetch_assoc()['enrollment_count'];
        $stmt3->close();
    }
}

// Get session data for profile chip
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Scheme</title>
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
    .scheme-header { 
      display: flex; 
      align-items: center; 
      gap: 32px; 
      margin-bottom: 24px; 
    }
    .scheme-avatar {
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
      overflow: hidden;
    }
    .scheme-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .scheme-avatar:hover {
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.08);
    }
    .scheme-header-info { flex: 1 1 0; }
    .scheme-header-title { 
      font-size: 2rem; 
      font-weight: 700; 
      margin-bottom: 4px; 
      display: flex; 
      align-items: center; 
      gap: 12px; 
      color: #2d3a4b; 
    }
    .scheme-status { 
      font-size: 1rem; 
      font-weight: 600; 
      padding: 4px 14px; 
      border-radius: 12px; 
      background: #eafaf1; 
      color: #43b581; 
      margin-left: 10px; 
    }
    .scheme-header-meta { 
      color: #888; 
      font-size: 1.05rem; 
      margin-bottom: 6px; 
    }
    .scheme-header-actions { 
      display: flex; 
      gap: 10px; 
      margin-top: 8px; 
      flex-wrap: wrap; 
    }
    .scheme-header-actions .btn {
      font-size: 1rem; 
      font-weight: 600; 
      border-radius: 12px; 
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
      transition: background 0.25s, color 0.25s, box-shadow 0.25s, border 0.25s;
    }
    .scheme-header-actions .btn-primary {
      background: #003399;
      border: none; 
      color: #fff;
    }
    .scheme-header-actions .btn-primary:hover {
      background: #002266;
      color: #fff; 
      box-shadow: 0 4px 16px 0 rgba(0,51,153,0.3);
    }
    .scheme-header-actions .btn-warning {
      background: #f6c23e;
      border: none; 
      color: #fff;
    }
    .scheme-header-actions .btn-warning:hover {
      background: #f39c12;
      color: #fff; 
      box-shadow: 0 4px 16px 0 rgba(246,194,62,0.3);
    }
    .scheme-header-actions .btn-outline-secondary {
      border: 1px solid #bfc9da; 
      color: #003399; 
      background: #fff;
    }
    .scheme-header-actions .btn-outline-secondary:hover {
      background: #f4f6fb; 
      color: #003399; 
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
    }
    .scheme-header-actions .btn-danger {
      background: #e74c3c;
      border: none; 
      color: #fff;
    }
    .scheme-header-actions .btn-danger:hover {
      background: #c0392b;
      color: #fff; 
      box-shadow: 0 4px 16px 0 rgba(231,76,60,0.3);
    }
    .scheme-content-row { 
      display: flex; 
      gap: 32px; 
    }
    .scheme-main { flex: 2 1 0; }
    .scheme-side { flex: 1 1 0; min-width: 320px; }
    .scheme-card, .side-widget {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
      border: 1px solid #e5e7eb;
      margin-bottom: 24px; 
      padding: 24px 28px;
      transition: box-shadow 0.3s;
    }
    .scheme-card:hover, .side-widget:hover {
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.1);
    }
    .scheme-card-title, .side-widget-title { 
      font-size: 1.2rem; 
      font-weight: 600; 
      margin-bottom: 18px; 
      color: #2d3a4b; 
    }
    .scheme-info-grid { 
      display: grid; 
      grid-template-columns: 1fr 1fr; 
      gap: 12px 32px; 
    }
    .scheme-info-label { 
      color: #003399; 
      font-size: 1rem; 
      font-weight: 500; 
      margin-bottom: 2px; 
    }
    .scheme-info-value { 
      color: #23272f; 
      font-size: 1.08rem; 
      font-weight: 600; 
    }
    .installment-card {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 16px;
      border: 1px solid #e9ecef;
      transition: box-shadow 0.3s;
    }
    .installment-card:hover {
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.1);
    }
    .installment-header {
      display: flex;
      justify-content: between;
      align-items: center;
      margin-bottom: 12px;
    }
    .installment-number {
      font-size: 1.1rem;
      font-weight: 600;
      color: #003399;
    }
    .installment-status {
      padding: 4px 12px;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 500;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-paid { background: #d4edda; color: #155724; }
    .status-overdue { background: #f8d7da; color: #721c24; }
    .status-drawn { background: #cce5ff; color: #004085; }
    .installment-details {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }
    .installment-image {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      object-fit: cover;
      border: 1px solid #e9ecef;
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
      .scheme-content-row { flex-direction: column; } 
      .scheme-side { min-width: unset; } 
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
  
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php else: ?>
    <!-- Header -->
    <div class="scheme-header">
      <div class="scheme-avatar">
        <?php if ($scheme['SchemeImageURL']): ?>
          <img src="<?= htmlspecialchars($scheme['SchemeImageURL']) ?>" alt="Scheme Image">
        <?php else: ?>
          <i class="fa fa-cubes"></i>
        <?php endif; ?>
      </div>
      <div class="scheme-header-info">
        <div class="scheme-header-title">
          <?= htmlspecialchars($scheme['SchemeName']) ?>
          <span class="scheme-status" style="background:<?= $scheme['Status'] === 'Active' ? '#eafaf1' : ($scheme['Status'] === 'Closed' ? '#fdeaea' : '#fff3cd'); ?>;color:<?= $scheme['Status'] === 'Active' ? '#43b581' : ($scheme['Status'] === 'Closed' ? '#e74c3c' : '#f39c12'); ?>;">
            <?= htmlspecialchars($scheme['Status']) ?>
          </span>
        </div>
        <div class="scheme-header-meta">
          <span><b>ID:</b> <?= htmlspecialchars($scheme['SchemeID']) ?></span> &nbsp;|
          <span><i class="fa fa-money-bill"></i> ₹<?= number_format($scheme['MonthlyPayment'], 2) ?> monthly</span> &nbsp;|
          <span><i class="fa fa-calendar"></i> Created: <?= date('M d, Y', strtotime($scheme['CreatedAt'])) ?></span>
        </div>
        <div class="scheme-header-actions">
          <a href="edit.php?id=<?= $scheme['SchemeID'] ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Edit Scheme</a>
          <button class="btn btn-warning"><i class="fa fa-ban"></i> Close Scheme</button>
          <button class="btn btn-outline-secondary"><i class="fa fa-plus"></i> Add Installment</button>
          <button class="btn btn-outline-secondary"><i class="fa fa-users"></i> View Enrollments</button>
          <a href="delete.php?id=<?= $scheme['SchemeID'] ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
        </div>
      </div>
    </div>
    
    <!-- Content Row -->
    <div class="scheme-content-row">
      <div class="scheme-main">
        <!-- Basic Information Card -->
        <div class="scheme-card">
          <div class="scheme-card-title">Scheme Information</div>
          <div class="scheme-info-grid">
            <div><div class="scheme-info-label">Scheme Name</div><div class="scheme-info-value"><?= htmlspecialchars($scheme['SchemeName']) ?></div></div>
            <div><div class="scheme-info-label">Scheme ID</div><div class="scheme-info-value"><?= htmlspecialchars($scheme['SchemeID']) ?></div></div>
            <div><div class="scheme-info-label">Monthly Payment</div><div class="scheme-info-value">₹<?= number_format($scheme['MonthlyPayment'], 2) ?></div></div>
            <div><div class="scheme-info-label">Total Payments</div><div class="scheme-info-value"><?= htmlspecialchars($scheme['TotalPayments']) ?></div></div>
            <div><div class="scheme-info-label">Status</div><div class="scheme-info-value"><?= htmlspecialchars($scheme['Status']) ?></div></div>
            <div><div class="scheme-info-label">Created Date</div><div class="scheme-info-value"><?= date('M d, Y', strtotime($scheme['CreatedAt'])) ?></div></div>
            <div><div class="scheme-info-label">Total Value</div><div class="scheme-info-value">₹<?= number_format($scheme['MonthlyPayment'] * $scheme['TotalPayments'], 2) ?></div></div>
            <div><div class="scheme-info-label">Start Date</div><div class="scheme-info-value"><?= $scheme['StartDate'] ? date('M d, Y', strtotime($scheme['StartDate'])) : 'Not set' ?></div></div>
          </div>
          <?php if ($scheme['Description']): ?>
            <div style="margin-top: 20px;">
              <div class="scheme-info-label">Description</div>
              <div class="scheme-info-value" style="font-weight: normal; line-height: 1.6;"><?= nl2br(htmlspecialchars($scheme['Description'])) ?></div>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Installments Card -->
        <div class="scheme-card">
          <div class="scheme-card-title">Installments (<?= count($installments) ?>)</div>
          <?php if (empty($installments)): ?>
            <div class="empty-state">
              <i class="fa fa-list-alt"></i><br />No installments found for this scheme.<br />
              <button class="btn btn-primary mt-3"><i class="fa fa-plus"></i> Add New Installment</button>
            </div>
          <?php else: ?>
            <?php foreach ($installments as $installment): ?>
              <div class="installment-card">
                <div class="installment-header">
                  <div class="installment-number"><?= htmlspecialchars($installment['InstallmentName'] ?: 'Installment #' . $installment['InstallmentNumber']) ?></div>
                  <span class="installment-status status-<?= strtolower($installment['Status']) ?>"><?= htmlspecialchars($installment['Status']) ?></span>
                </div>
                <div class="installment-details">
                  <div>
                    <div class="scheme-info-label">Amount</div>
                    <div class="scheme-info-value">₹<?= number_format($installment['Amount'], 2) ?></div>
                  </div>
                  <div>
                    <div class="scheme-info-label">Draw Date</div>
                    <div class="scheme-info-value"><?= $installment['DrawDate'] ? date('M d, Y', strtotime($installment['DrawDate'])) : 'Not set' ?></div>
                  </div>
                  <?php if ($installment['ImageURL']): ?>
                    <div style="grid-column: 1 / -1; margin-top: 12px;">
                      <div class="scheme-info-label">Image</div>
                      <img src="<?= htmlspecialchars($installment['ImageURL']) ?>" alt="Installment Image" class="installment-image">
                    </div>
                  <?php endif; ?>
                  <?php if ($installment['Benefits']): ?>
                    <div style="grid-column: 1 / -1; margin-top: 12px;">
                      <div class="scheme-info-label">Benefits</div>
                      <div class="scheme-info-value" style="font-weight: normal; line-height: 1.6;"><?= nl2br(htmlspecialchars($installment['Benefits'])) ?></div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="scheme-side">
        <div class="side-widget">
          <div class="side-widget-title">Total Enrollments</div>
          <div style="font-size:2.2rem;font-weight:700;text-align:center;background:#f6c23e;color:#fff;border-radius:8px;padding:18px 0;margin-bottom:18px;"><?= $enrollment_count ?></div>
        </div>
        
        <div class="side-widget">
          <div class="side-widget-title">Scheme Statistics</div>
          <div style="margin-bottom: 16px;">
            <div class="scheme-info-label">Total Value</div>
            <div class="scheme-info-value">₹<?= number_format($scheme['MonthlyPayment'] * $scheme['TotalPayments'], 2) ?></div>
          </div>
          <div style="margin-bottom: 16px;">
            <div class="scheme-info-label">Installments</div>
            <div class="scheme-info-value"><?= count($installments) ?></div>
          </div>
          <div style="margin-bottom: 16px;">
            <div class="scheme-info-label">Duration</div>
            <div class="scheme-info-value"><?= $scheme['TotalPayments'] ?> months</div>
          </div>
        </div>
        
        <div class="side-widget">
          <div class="side-widget-title">Quick Actions</div>
          <button class="btn btn-primary"><i class="fa fa-plus"></i> Add Installment</button>
          <button class="btn btn-outline-secondary"><i class="fa fa-users"></i> View Enrollments</button>
          <button class="btn btn-outline-secondary"><i class="fa fa-chart-bar"></i> View Reports</button>
          <button class="btn btn-outline-secondary"><i class="fa fa-bell"></i> Send Notification</button>
          <button class="btn btn-outline-secondary"><i class="fa fa-download"></i> Export Data</button>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/script.js"></script>
</body>
</html>
