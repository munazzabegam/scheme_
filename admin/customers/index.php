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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/scheme_/admin/assets/style.css">
</head>
<body>
<?php include_once __DIR__ . '/../components/sidebar.php'; ?>
<div class="main-container">
  <div class="topbar-premium mb-4">
    <h2><i class="fa-solid fa-users"></i> Customers</h2>
    <div class="action-btns">
      <a href="/scheme_/admin/customers/add.php" class="btn btn-primary"><i class="fa fa-user-plus"></i> Add New Customer</a>
      <a href="../customers/export.php" class="btn btn-export"><i class="fa fa-file-excel"></i> Export to Excel</a>
    </div>
  </div>
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form class="row g-3 align-items-end mb-3" method="get">
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
      <option value="All"<?= $status_filter === 'All' ? ' selected' : '' ?>>All Status</option>
      <option value="Active"<?= $status_filter === 'Active' ? ' selected' : '' ?>>Active</option>
      <option value="Inactive"<?= $status_filter === 'Inactive' ? ' selected' : '' ?>>Inactive</option>
    </select>
        </div>
        <div class="col-md-5">
          <label class="form-label">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Search by name, email, contact or ID..." value="<?= htmlspecialchars($search) ?>" />
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-primary w-100" type="submit"><i class="fa fa-filter"></i> Apply Filters</button>
        </div>
  </form>
      <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">Customer added successfully!</div>
      <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Customer deleted successfully!</div>
      <?php endif; ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Contact</th>
          <th>Status</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['CustomerID']) ?></td>
            <td><?= htmlspecialchars($row['FullName']) ?></td>
            <td><?= htmlspecialchars($row['Email']) ?></td>
            <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
            <td>
              <?php if ($row['Status'] === 'Active'): ?>
                <span class="status-active">Active</span>
              <?php else: ?>
                <span class="status-inactive">Inactive</span>
              <?php endif; ?>
            </td>
            <td><?= date('M d, Y', strtotime($row['CreatedAt'])) ?></td>
                <td>
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