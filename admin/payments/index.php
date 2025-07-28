<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div class="alert alert-danger text-center">Database connection failed!</div>');
}

// Handle filters
$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$scheme_filter = $_GET['scheme'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build WHERE clause
$where = [];
$params = [];
$types = '';

if ($status_filter && $status_filter !== 'All') {
    $where[] = 'Payments.PaymentStatus = ?';
    $params[] = $status_filter;
    $types .= 's';
}

if ($search) {
    $where[] = '(Customers.FullName LIKE ? OR Customers.PhoneNumber LIKE ? OR Payments.PaymentID LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

if ($scheme_filter && $scheme_filter !== 'All') {
    $where[] = 'CustomerSchemeEnrollments.SchemeID = ?';
    $params[] = $scheme_filter;
    $types .= 'i';
}

if ($date_from) {
    $where[] = 'DATE(Payments.PaymentDate) >= ?';
    $params[] = $date_from;
    $types .= 's';
}

if ($date_to) {
    $where[] = 'DATE(Payments.PaymentDate) <= ?';
    $params[] = $date_to;
    $types .= 's';
}

// Build SQL query
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT Payments.PaymentID, Payments.Amount, Payments.PaymentDate, Payments.PaymentStatus, 
               Payments.CustomerID, Customers.FullName, Customers.PhoneNumber, 
               Schemes.SchemeName
        FROM Payments 
        LEFT JOIN Customers ON Payments.CustomerID = Customers.CustomerID 
        LEFT JOIN CustomerSchemeEnrollments ON Customers.CustomerID = CustomerSchemeEnrollments.CustomerID
        LEFT JOIN Schemes ON CustomerSchemeEnrollments.SchemeID = Schemes.SchemeID
        $where_sql 
        ORDER BY Payments.PaymentID DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get schemes for filter dropdown
$schemes_sql = "SELECT DISTINCT Schemes.SchemeID, Schemes.SchemeName 
                FROM Schemes 
                INNER JOIN CustomerSchemeEnrollments ON Schemes.SchemeID = CustomerSchemeEnrollments.SchemeID
                INNER JOIN Payments ON CustomerSchemeEnrollments.CustomerID = Payments.CustomerID
                ORDER BY Schemes.SchemeName";
$schemes_result = $conn->query($schemes_sql);

// Get session data for profile chip
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Management</title>
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
    .screenshot-row {
      background: #fff;
    }
    .screenshot-img {
      max-width: 350px;
      max-height: 350px;
      margin: 18px auto 18px auto;
      display: block;
      border-radius: 12px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.08);
    }
    .status-badge {
      font-size: 0.98rem;
      font-weight: 600;
      border-radius: 8px;
      padding: 4px 16px;
      display: inline-block;
    }
    .status-pending {
      background: #fffbe6;
      color: #ffc107;
    }
    .status-verified {
      background: #eafaf1;
      color: #43b581;
    }
    .status-rejected {
      background: #fdeaea;
      color: #e74c3c;
    }
    .filter-section {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
      border: 1px solid #e5e7eb;
      padding: 24px;
      margin-bottom: 24px;
    }
    .filter-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #2d3a4b;
      margin-bottom: 16px;
    }
    .date-range-container {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .date-range-container input[type="date"] {
      max-width: 140px;
    }
    .filter-actions {
      display: flex;
      gap: 12px;
      align-items: center;
      margin-top: 16px;
    }
    .btn-apply-filters {
      background: #003399;
      color: #fff;
      border: none;
      font-weight: 600;
      border-radius: 8px;
      padding: 10px 20px;
      transition: background 0.25s, box-shadow 0.25s;
    }
    .btn-apply-filters:hover {
      background: #002266;
      color: #fff;
      box-shadow: 0 4px 16px 0 rgba(0,51,153,0.3);
    }
    .btn-clear-filters {
      background: #6c757d;
      color: #fff;
      border: none;
      font-weight: 600;
      border-radius: 8px;
      padding: 10px 20px;
      transition: background 0.25s, box-shadow 0.25s;
    }
    .btn-clear-filters:hover {
      background: #5a6268;
      color: #fff;
      box-shadow: 0 4px 16px 0 rgba(108,117,125,0.3);
    }
    .action-btn-group {
      display: flex;
      gap: 8px;
      justify-content: center;
      align-items: center;
      flex-wrap: nowrap;
      width: 100%;
    }
    .action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      padding: 6px 10px;
      border: none;
      border-radius: 6px;
      font-weight: 500;
      transition: all 0.2s;
      text-decoration: none;
      min-width: 70px;
      white-space: nowrap;
      flex-shrink: 0;
    }
    .action-btn.verify {
      background: #28a745;
      color: #fff;
    }
    .action-btn.verify:hover {
      background: #218838;
      color: #fff;
      transform: translateY(-1px);
    }
    .action-btn.reject {
      background: #dc3545;
      color: #fff;
    }
    .action-btn.reject:hover {
      background: #c82333;
      color: #fff;
      transform: translateY(-1px);
    }
    .action-btn.view {
      background: #17a2b8;
      color: #fff;
    }
    .action-btn.view:hover {
      background: #138496;
      color: #fff;
      transform: translateY(-1px);
    }
    .action-btn i {
      margin-right: 3px;
      font-size: 11px;
    }
    @media (max-width: 900px) {
      .main-container { margin-left: 0; padding: 12px 2px; }
      .profile-chip { position: static; margin-bottom: 18px; }
      .date-range-container { flex-direction: column; align-items: stretch; }
      .date-range-container input[type="date"] { max-width: 100%; }
      .action-btn-group { 
        flex-direction: row; 
        gap: 4px; 
        justify-content: center;
        flex-wrap: nowrap;
      }
      .action-btn { 
        min-width: 60px;
        font-size: 11px;
        padding: 4px 8px;
      }
      .action-btn i {
        font-size: 10px;
        margin-right: 2px;
      }
    }
  </style>
  <script>
    function scrollToScreenshot(id) {
      var row = document.getElementById(id);
      if(row) {
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        row.classList.add('table-active');
        setTimeout(function(){ row.classList.remove('table-active'); }, 1500);
      }
    }
  </script>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
  <div class="main-container">
  <div class="profile-chip">
    <i class="fa-regular fa-user-circle"></i> <?= htmlspecialchars($role) ?>
  </div>
  
  <div class="topbar-premium mb-4">
    <h2><i class="fa-solid fa-credit-card"></i> Payments</h2>
    <div class="action-btns">
      <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Payment</a>
      <a href="#" class="btn btn-export"><i class="fa fa-file-excel"></i> Export to Excel</a>
    </div>
  </div>
  
  <div class="filter-section">
    <div class="filter-title"><i class="fa fa-filter me-2"></i>Filter Payments</div>
    <form method="get" action="">
      <div class="row g-3">
        <div class="col-md-2">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="All"<?= $status_filter === 'All' ? ' selected' : '' ?>>All Status</option>
            <option value="Pending"<?= $status_filter === 'Pending' ? ' selected' : '' ?>>Pending</option>
            <option value="Success"<?= $status_filter === 'Success' ? ' selected' : '' ?>>Success</option>
            <option value="Failed"<?= $status_filter === 'Failed' ? ' selected' : '' ?>>Failed</option>
            <option value="Refunded"<?= $status_filter === 'Refunded' ? ' selected' : '' ?>>Refunded</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Search by customer name, ID or contact..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Scheme</label>
          <select name="scheme" class="form-select">
            <option value="All"<?= $scheme_filter === 'All' ? ' selected' : '' ?>>All Schemes</option>
            <?php while ($scheme = $schemes_result->fetch_assoc()): ?>
              <option value="<?= $scheme['SchemeID'] ?>"<?= $scheme_filter == $scheme['SchemeID'] ? ' selected' : '' ?>>
                <?= htmlspecialchars($scheme['SchemeName']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Date Range</label>
          <div class="date-range-container">
            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>">
            <span class="text-muted">to</span>
            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>">
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label">&nbsp;</label>
          <button type="submit" class="btn btn-apply-filters w-100">
            <i class="fa fa-filter me-1"></i> Apply Filters
          </button>
        </div>
      </div>
      <div class="filter-actions">
        <a href="index.php" class="btn btn-clear-filters">
          <i class="fa fa-times me-1"></i> Clear Filters
        </a>
        </div>
      </form>
  </div>
  
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <?php if (isset($_GET['status']) || isset($_GET['search']) || isset($_GET['scheme']) || isset($_GET['date_from']) || isset($_GET['date_to'])): ?>
        <div class="alert alert-info mb-3">
          <i class="fa fa-info-circle me-2"></i>
          Showing filtered results 
          <?php if ($result->num_rows > 0): ?>
            (<?= $result->num_rows ?> payment<?= $result->num_rows !== 1 ? 's' : '' ?> found)
          <?php endif; ?>
        </div>
      <?php endif; ?>
      
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-center">ID</th>
              <th class="text-center">Customer Name</th>
              <th class="text-center">Scheme</th>
              <th class="text-center">Amount</th>
              <th class="text-center">Phone Number</th>
              <th class="text-center">Date & Time</th>
              <th class="text-center">Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
<?php if ($result && $result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td class="text-center"><?= htmlspecialchars($row['PaymentID']) ?></td>
              <td class="text-center"><?= htmlspecialchars($row['FullName']) ?></td>
              <td class="text-center"><?= htmlspecialchars($row['SchemeName'] ?? 'N/A') ?></td>
              <td class="text-center">₹<?= number_format($row['Amount'], 2) ?></td>
              <td class="text-center"><?= htmlspecialchars($row['PhoneNumber']) ?></td>
              <td class="text-center"><?= date('M d, Y H:i', strtotime($row['PaymentDate'])) ?></td>
              <td class="text-center">
                <?php
                  $status = strtolower($row['PaymentStatus']);
                  if ($status === 'pending') echo '<span class="status-badge status-pending">Pending</span>';
                  elseif ($status === 'success' || $status === 'verified') echo '<span class="status-badge status-verified">Success</span>';
                  elseif ($status === 'failed' || $status === 'rejected') echo '<span class="status-badge status-rejected">Failed</span>';
                  else echo '<span class="status-badge status-pending">' . htmlspecialchars($row['PaymentStatus']) . '</span>';
                ?>
              </td>
              <td class="text-center">
                <div class="action-btn-group">
                  <button class="action-btn verify" title="Verify Payment">
                    <i class="fa fa-check"></i> Verify
                  </button>
                  <button class="action-btn reject" title="Reject Payment">
                    <i class="fa fa-times"></i> Reject
                  </button>
                  <button class="action-btn view" onclick="scrollToScreenshot('screenshot<?= htmlspecialchars($row['PaymentID']) ?>')" title="View Screenshot">
                    <i class="fa fa-image"></i> View
                  </button>
                </div>
              </td>
            </tr>
            <tr id="screenshot<?= htmlspecialchars($row['PaymentID']) ?>" class="screenshot-row">
              <td colspan="8" class="text-center">
                <img src="/path/to/screenshot.jpg" alt="Payment Screenshot" class="screenshot-img">
              </td>
            </tr>
<?php endwhile; ?>
<?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-4">
                <i class="fa fa-info-circle fa-2x text-muted mb-3"></i>
                <p class="text-muted">No payments found matching your criteria.</p>
                <?php if (isset($_GET['status']) || isset($_GET['search']) || isset($_GET['scheme']) || isset($_GET['date_from']) || isset($_GET['date_to'])): ?>
                  <a href="index.php" class="btn btn-primary mt-2">
                    <i class="fa fa-times me-1"></i> Clear Filters
                  </a>
                <?php endif; ?>
              </td>
            </tr>
<?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php $stmt->close(); $conn->close(); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/scheme_/admin/assets/script.js"></script>
</body>
</html>
 