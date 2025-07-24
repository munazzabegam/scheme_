<?php
// Database connection
include_once '../../config/database.php';

// Handle filters
$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$where = [];
$params = [];
$types = '';
if ($status_filter && $status_filter !== 'All') {
    $where[] = 'Status = ?';
    $params[] = $status_filter;
    $types .= 's';
}
if ($search) {
    $where[] = '(SchemeName LIKE ? OR Description LIKE ? OR SchemeID LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT * FROM Schemes $where_sql ORDER BY CreatedAt DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get session data for profile chip
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scheme Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/scheme_/admin/assets/style.css">
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
  </style>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<div class="main-container">
  <div class="topbar-premium d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Scheme Management</h2>
    <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Scheme</a>
  </div>
  <form class="row g-2 mb-4 align-items-center" method="get" action="">
    <div class="col-md-7 col-12 mb-2 mb-md-0">
      <input type="text" name="search" class="form-control" placeholder="Search schemes..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3 col-6 mb-2 mb-md-0">
      <select name="status" class="form-select">
        <option value="All"<?= $status_filter === 'All' ? ' selected' : '' ?>>All Statuses</option>
        <option value="Active"<?= $status_filter === 'Active' ? ' selected' : '' ?>>Active</option>
        <option value="Closed"<?= $status_filter === 'Closed' ? ' selected' : '' ?>>Closed</option>
        <option value="Upcoming"<?= $status_filter === 'Upcoming' ? ' selected' : '' ?>>Upcoming</option>
      </select>
    </div>
    <div class="col-md-2 col-6">
      <button type="submit" class="btn btn-secondary w-100"><i class="fa fa-search"></i> Search</button>
    </div>
  </form>
  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="card mb-4 p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
          <h4 class="mb-0"><?= htmlspecialchars($row['SchemeName']) ?></h4>
          <div class="action-btn-group">
            <a href="view.php?id=<?= $row['SchemeID'] ?>" class="btn btn-info action-btn"><i class="fa fa-eye"></i> View</a>
            <a href="edit.php?id=<?= $row['SchemeID'] ?>" class="btn btn-success action-btn"><i class="fa fa-edit"></i> Edit</a>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-md-3 col-6 mb-2 mb-md-0">
            <div class="label">Monthly Payment</div>
            <div class="value">₹<?= number_format($row['MonthlyPayment'], 2) ?></div>
          </div>
          <div class="col-md-3 col-6 mb-2 mb-md-0">
            <div class="label">Total Payments</div>
            <div class="value"><?= $row['TotalPayments'] ?></div>
          </div>
          <div class="col-md-3 col-12">
            <div class="label">Created Date</div>
            <div class="value"><?= date('M d, Y', strtotime($row['CreatedAt'])) ?></div>
          </div>
        </div>
        <div class="mb-2">
          <?= nl2br(htmlspecialchars($row['Description'])) ?>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2 align-items-center">
          <span class="status-active">Active</span>
          <span class="badge bg-primary">2123 Active Subscriptions</span>
          <span class="badge bg-secondary">18 Installments</span>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="alert alert-info text-center">No schemes found.</div>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/scheme_/admin/assets/script.js"></script>
</body>
</html>
