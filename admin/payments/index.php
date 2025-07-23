<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div class="alert alert-danger text-center">Database connection failed!</div>');
}
// Fetch payments with customer info
$sql = "SELECT Payments.PaymentID, Payments.Amount, Payments.PaymentDate, Payments.PaymentStatus, Payments.CustomerID, Customers.FullName, Customers.PhoneNumber FROM Payments LEFT JOIN Customers ON Payments.CustomerID = Customers.CustomerID ORDER BY Payments.PaymentID DESC";
$result = $conn->query($sql);
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
    @media (max-width: 900px) {
      .main-container { margin-left: 0; padding: 12px 2px; }
      .profile-chip { position: static; margin-bottom: 18px; }
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
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form class="row g-3 align-items-end mb-3 flex-nowrap" method="get" style="flex-wrap:nowrap;">
        <div class="col-md-3">
          <label class="form-label w-100 text-center">Status</label>
          <select name="status" class="form-select">
            <option value="All">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Verified">Verified</option>
            <option value="Rejected">Rejected</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label w-100 text-center">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Search by customer name, ID or contact...">
        </div>
        <div class="col-md-3">
          <label class="form-label w-100 text-center">Scheme</label>
          <select name="scheme" class="form-select">
            <option>All Schemes</option>
            <!-- Add more schemes dynamically -->
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label w-100 text-center">Date Range:</label>
          <div class="d-flex align-items-center">
            <input type="date" name="date_from" class="form-control me-2" style="max-width: 120px;">
            <span class="mx-1">to</span>
            <input type="date" name="date_to" class="form-control" style="max-width: 120px;">
        </div>
        </div>
      </form>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-center">ID</th>
              <th class="text-center">Name</th>
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
              <td class="text-center">₹<?= number_format($row['Amount'], 2) ?></td>
              <td class="text-center"><?= htmlspecialchars($row['PhoneNumber']) ?></td>
              <td class="text-center"><?= date('Y-m-d H:i', strtotime($row['PaymentDate'])) ?></td>
              <td class="text-center">
                <?php
                  $status = strtolower($row['PaymentStatus']);
                  if ($status === 'pending') echo '<span class="status-badge status-pending">Pending</span>';
                  elseif ($status === 'verified' || $status === 'success') echo '<span class="status-badge status-verified">Verified</span>';
                  elseif ($status === 'rejected' || $status === 'failed') echo '<span class="status-badge status-rejected">Rejected</span>';
                  else echo htmlspecialchars($row['PaymentStatus']);
                ?>
              </td>
              <td class="text-end d-flex justify-content-end align-items-center">
                <div class="action-btn-group w-100 justify-content-between d-flex">
                  <button class="btn btn-primary btn-sm flex-fill mx-1"><i class="fa fa-check"></i> Verify</button>
                  <button class="btn btn-danger btn-sm flex-fill mx-1"><i class="fa fa-times"></i> Reject</button>
                  <button class="btn btn-primary btn-sm flex-fill mx-1" onclick="scrollToScreenshot('screenshot<?= htmlspecialchars($row['PaymentID']) ?>')"><i class="fa fa-image"></i> View</button>
                </div>
              </td>
            </tr>
            <tr id="screenshot<?= htmlspecialchars($row['PaymentID']) ?>" class="screenshot-row">
              <td colspan="7" class="text-center">
                <img src="/path/to/screenshot.jpg" alt="Payment Screenshot" class="screenshot-img">
              </td>
            </tr>
<?php endwhile; ?>
<?php else: ?>
            <tr><td colspan="7" class="text-center">No payments found.</td></tr>
<?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php $conn->close(); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/scheme_/admin/assets/script.js"></script>
</body>
</html>
 