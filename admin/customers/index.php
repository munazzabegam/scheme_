<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div class="alert alert-danger text-center">Database connection failed!</div>');
}

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
    $where[] = '(FullName LIKE ? OR Email LIKE ? OR PhoneNumber LIKE ? OR CustomerID LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ssss';
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT CustomerID, FullName, Email, PhoneNumber, Status, CreatedAt FROM Customers $where_sql ORDER BY CustomerID ASC";
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
  <title>Customer Management</title>
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
      margin-bottom: 10px;
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
  <!-- Main content: Customer Table, Filters, Actions -->
  <div class="topbar-premium mb-4">
    <h2><i class="fa-solid fa-users"></i> Customers</h2>
    <div class="action-btns">
      <a href="/scheme_/admin/customers/add.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add New Customer</a>
      <a href="../customers/export.php" class="btn btn-export"><i class="fa fa-file-excel"></i> Export to Excel</a>
    </div>
  </div>
  <div class="filter-section">
    <div class="filter-title"><i class="fa fa-filter me-2"></i>Filter Customers</div>
    <form method="get" action="">
      <div class="row g-3 align-items-end">
        <div class="col-md-2">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="All"<?= $status_filter === 'All' ? ' selected' : '' ?>>All Status</option>
            <option value="Active"<?= $status_filter === 'Active' ? ' selected' : '' ?>>Active</option>
            <option value="Inactive"<?= $status_filter === 'Inactive' ? ' selected' : '' ?>>Inactive</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Search by name, email, contact or ID..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Date From</label>
          <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Date To</label>
          <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-apply-filters flex-fill">
              <i class="fa fa-filter me-1"></i> Apply
            </button>
            <a href="index.php" class="btn btn-clear-filters">
              <i class="fa fa-times me-1"></i> Clear
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
      <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">Customer added successfully!</div>
      <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Customer deleted successfully!</div>
      <?php endif; ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-center">ID</th>
              <th class="text-center">Name</th>
              <th class="text-center">Contact</th>
              <th class="text-center">Email</th>
              <th class="text-center">Status</th>
              <th class="text-center">Date Joined</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="text-center"><?= htmlspecialchars($row['CustomerID']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['FullName']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['PhoneNumber']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['Email']) ?></td>
            <td class="text-center">
              <?php if ($row['Status'] === 'Active'): ?>
                <span class="status-active">Active</span>
              <?php else: ?>
                <span class="status-inactive">Inactive</span>
              <?php endif; ?>
            </td>
            <td class="text-center"><?= date('M d, Y', strtotime($row['CreatedAt'])) ?></td>
            <td class="text-end">
              <div class="action-btn-group">
                <a href="view.php?id=<?= $row['CustomerID'] ?>" class="action-btn" title="View"><i class="fa fa-eye"></i></a>
                <a href="edit.php?id=<?= $row['CustomerID'] ?>" class="action-btn edit" title="Edit"><i class="fa fa-edit"></i></a>
                <a href="delete.php?id=<?= $row['CustomerID'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this customer?');"><i class="fa fa-trash"></i></a>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
            <tr><td colspan="7" class="text-center">No customers found.</td></tr>
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
