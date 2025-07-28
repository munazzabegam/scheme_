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
    .scheme-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
      border: 1px solid #e5e7eb;
      padding: 24px;
      margin-bottom: 24px;
      transition: box-shadow 0.3s;
    }
    .scheme-card:hover {
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.1);
    }
    .scheme-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 16px;
      flex-wrap: wrap;
      gap: 16px;
    }
    .scheme-title {
      font-size: 1.4rem;
      font-weight: 600;
      color: #2d3a4b;
      margin: 0;
    }
    .scheme-image {
      max-width: 180px;
      max-height: 120px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 16px;
    }
    .scheme-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 16px;
    }
    .info-item {
      display: flex;
      flex-direction: column;
    }
    .info-label {
      font-size: 0.9rem;
      color: #003399;
      font-weight: 500;
      margin-bottom: 4px;
    }
    .info-value {
      font-size: 1.1rem;
      font-weight: 600;
      color: #23272f;
    }
    .scheme-description {
      color: #666;
      line-height: 1.6;
      margin-bottom: 16px;
    }
    .scheme-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
    }
    .scheme-status {
      padding: 4px 14px;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 500;
      display: inline-block;
    }
    .status-active { 
      background: #eafaf1; 
      color: #43b581; 
    }
    .status-closed { 
      background: #fdeaea; 
      color: #e74c3c; 
    }
    .status-upcoming { 
      background: #fff3cd; 
      color: #f39c12; 
    }
    .scheme-badges {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
    .badge {
      padding: 4px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 500;
    }
    .badge-primary {
      background: #003399;
      color: #fff;
    }
    .badge-secondary {
      background: #6c757d;
      color: #fff;
    }
    @media (max-width: 900px) {
      .main-container { margin-left: 0; padding: 12px 2px; }
      .profile-chip { position: static; margin-bottom: 18px; }
      .scheme-header { flex-direction: column; align-items: stretch; }
      .scheme-footer { flex-direction: column; align-items: stretch; }
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
    <h2><i class="fa-solid fa-cubes"></i> Schemes</h2>
    <div class="action-btns">
      <a href="add.php" class="btn btn-primary"><i class="fa fa-plus"></i> Add New Scheme</a>
    </div>
  </div>
  
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form class="row g-3 align-items-end mb-3" method="get">
        <div class="col-md-7">
          <label class="form-label">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Search by name, description or ID..." value="<?= htmlspecialchars($search) ?>" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
      <select name="status" class="form-select">
            <option value="All"<?= $status_filter === 'All' ? ' selected' : '' ?>>All Status</option>
        <option value="Active"<?= $status_filter === 'Active' ? ' selected' : '' ?>>Active</option>
        <option value="Closed"<?= $status_filter === 'Closed' ? ' selected' : '' ?>>Closed</option>
        <option value="Upcoming"<?= $status_filter === 'Upcoming' ? ' selected' : '' ?>>Upcoming</option>
      </select>
    </div>
        <div class="col-md-2">
          <button class="btn btn-outline-primary w-100" type="submit"><i class="fa fa-filter"></i> Apply Filters</button>
        </div>
      </form>
      <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">Scheme added successfully!</div>
      <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Scheme updated successfully!</div>
      <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Scheme deleted successfully!</div>
      <?php endif; ?>
    </div>
  </div>
  
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="scheme-card">
        <?php if (!empty($row['SchemeImageURL'])): ?>
          <div class="text-center">
            <img src="<?= htmlspecialchars($row['SchemeImageURL']) ?>" alt="Scheme Image" class="scheme-image">
          </div>
        <?php endif; ?>
        
        <div class="scheme-header">
          <h4 class="scheme-title"><?= htmlspecialchars($row['SchemeName']) ?></h4>
          <div class="action-btn-group">
            <a href="view.php?id=<?= $row['SchemeID'] ?>" class="action-btn" title="View"><i class="fa fa-eye"></i></a>
            <a href="edit.php?id=<?= $row['SchemeID'] ?>" class="action-btn edit" title="Edit"><i class="fa fa-edit"></i></a>
            <a href="delete.php?id=<?= $row['SchemeID'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this scheme?');"><i class="fa fa-trash"></i></a>
          </div>
        </div>
        
        <div class="scheme-info">
          <div class="info-item">
            <div class="info-label">Monthly Payment</div>
            <div class="info-value">₹<?= number_format($row['MonthlyPayment'], 2) ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">Total Payments</div>
            <div class="info-value"><?= $row['TotalPayments'] ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">Total Value</div>
            <div class="info-value">₹<?= number_format($row['MonthlyPayment'] * $row['TotalPayments'], 2) ?></div>
          </div>
          <div class="info-item">
            <div class="info-label">Created Date</div>
            <div class="info-value"><?= date('M d, Y', strtotime($row['CreatedAt'])) ?></div>
          </div>
        </div>
        
        <?php if (!empty($row['Description'])): ?>
          <div class="scheme-description">
          <?= nl2br(htmlspecialchars($row['Description'])) ?>
        </div>
        <?php endif; ?>
        
        <div class="scheme-footer">
          <span class="scheme-status status-<?= strtolower($row['Status']) ?>"><?= htmlspecialchars($row['Status']) ?></span>
          <div class="scheme-badges">
            <span class="badge badge-primary">0 Active Subscriptions</span>
            <span class="badge badge-secondary">0 Installments</span>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="alert alert-info text-center">
      <i class="fa fa-info-circle fa-2x mb-3"></i>
      <p>No schemes found matching your criteria.</p>
      <a href="add.php" class="btn btn-primary mt-2"><i class="fa fa-plus"></i> Add Your First Scheme</a>
    </div>
  <?php endif; ?>
</div>
<?php $stmt->close(); $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/scheme_/admin/assets/script.js"></script>
</body>
</html>

