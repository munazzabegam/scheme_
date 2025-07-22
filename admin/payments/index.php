<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    .filter-bar { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px 0 rgba(2,0,36,0.06); padding: 18px 24px; margin-bottom: 32px; }
    .payment-card { background: #fff; border-radius: 18px; box-shadow: 0 8px 32px 0 rgba(2,0,36,0.10); padding: 32px 32px 24px 32px; margin-bottom: 32px; display: flex; flex-wrap: wrap; align-items: flex-start; gap: 32px; position: relative; }
    .payment-card .left { flex: 1 1 320px; min-width: 320px; }
    .payment-card .right { flex: 2 1 400px; min-width: 300px; display: flex; flex-direction: column; gap: 16px; }
    .payment-card .customer-name { font-size: 1.4rem; font-weight: 700; color: #2d3a4b; }
    .payment-card .customer-code { color: #888; font-size: 1.1rem; margin-bottom: 8px; }
    .payment-card .phone-badge { background: #f4f6fb; color: #007bff; border-radius: 20px; padding: 6px 18px; font-size: 1.1rem; display: inline-block; margin-bottom: 12px; }
    .payment-card .label { color: #888; font-size: 15px; min-width: 120px; display: inline-block; font-weight: 500; }
    .payment-card .value { font-size: 1.1rem; color: #222; font-weight: 500; word-break: break-all; }
    .payment-card .status-badge { color: #ffc107; background: #fffbe6; padding: 6px 22px; border-radius: 16px; font-size: 1.1rem; font-weight: 600; display: inline-block; }
    .payment-card .status-badge.success { color: #43b581; background: #eafaf1; }
    .payment-card .status-badge.failed { color: #e74c3c; background: #fdeaea; }
    .payment-card .status-badge.refunded { color: #007bff; background: #e0f0ff; }
    .payment-card .action-btns { display: flex; gap: 12px; margin-top: 12px; }
    .payment-card .action-btns .btn { font-size: 1rem; font-weight: 600; border-radius: 8px; padding: 8px 22px; }
    .payment-card .action-btns .btn-success { background: #43b581; border: none; }
    .payment-card .action-btns .btn-danger { background: #e74c3c; border: none; }
    .payment-card .action-btns .btn-primary { background: #007bff; border: none; }
    .payment-card .action-btns .btn-success:hover { background: #2e8b57; }
    .payment-card .action-btns .btn-danger:hover { background: #c0392b; }
    .payment-card .action-btns .btn-primary:hover { background: #0056b3; }
    .payment-card .transaction-card { background: #fafbfc; border-radius: 12px; box-shadow: 0 2px 8px 0 rgba(2,0,36,0.06); padding: 18px 20px; margin-top: 18px; font-size: 1rem; }
    .export-btn { float: right; margin-bottom: 18px; }
    @media (max-width: 900px) {
      .payment-card { flex-direction: column; gap: 18px; padding: 18px 8px; }
      .payment-card .left, .payment-card .right { min-width: 0; }
      .export-btn { float: none; display: block; width: 100%; margin-bottom: 18px; }
    }
    .main-container { margin-left: 250px; padding: 32px 24px; min-height: 100vh; }
    @media (max-width: 900px) { .main-container { margin-left: 0; padding: 12px 2px; } }
  </style>
</head>
<body>
  <?php require_once '../components/sidebar.php'; ?>
  <div class="main-container">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
      <h2 class="fw-bold">Payment Management</h2>
      <a href="add.php" class="btn btn-primary btn-lg"><i class="fa fa-plus me-2"></i>Add New Payment</a>
    </div>
    <div class="filter-bar mb-4">
      <form class="row g-3 align-items-center">
        <div class="col-md-3">
          <input type="text" class="form-control" placeholder="Search by customer name, ID or contact...">
        </div>
        <div class="col-md-2">
          <select class="form-select">
            <option>All Status</option>
            <option>Pending</option>
            <option>Success</option>
            <option>Failed</option>
            <option>Refunded</option>
          </select>
        </div>
        <div class="col-md-2">
          <select class="form-select">
            <option>All Schemes</option>
            <!-- Add more schemes dynamically -->
          </select>
        </div>
        <div class="col-md-2">
          <input type="text" class="form-control" placeholder="Search promoter...">
        </div>
        <div class="col-md-3 d-flex align-items-center">
          <label class="me-2 mb-0">Date Range:</label>
          <input type="date" class="form-control me-2" style="max-width: 140px;">
          <span class="mx-1">to</span>
          <input type="date" class="form-control" style="max-width: 140px;">
        </div>
      </form>
    </div>
    <a href="#" class="btn btn-success export-btn"><i class="fa fa-file-excel me-2"></i>Export to Excel</a>
    <!--
      Payment cards will be rendered here from real data in the future.
      Example:
      <div class="payment-card"> ... </div>
    -->
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
