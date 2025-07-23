<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('<div class="alert alert-danger text-center">Database connection failed!</div>');
}
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Payment</title>
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
      min-height: 100vh;
      max-width: 100vw;
    }
    .form-title {
      font-size: 2rem;
      font-weight: 700;
      color: #003399;
      margin-bottom: 32px;
      letter-spacing: 0.5px;
      text-align: left;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .form-label { font-weight: 500; color: #003399; margin-bottom: 4px; }
    .form-check-input:checked {
      background-color: #003399;
      border-color: #003399;
    }
    .form-check-label {
      color: #23272f;
      font-weight: 500;
    }
    .form-control, .form-select {
      border-radius: 8px;
      font-size: 1.08rem;
      margin-bottom: 18px;
    }
    .form-check {
      margin-right: 18px;
    }
    .form-check-group {
      display: flex;
      gap: 18px;
      margin-bottom: 0;
    }
    .btn-primary {
      background: linear-gradient(90deg, #003399 0%, #6a82fb 100%);
      border: none;
      font-weight: 600;
      border-radius: 10px;
      padding: 12px 32px;
      font-size: 1.1rem;
      margin-top: 10px;
    }
    .btn-primary:hover {
      background: linear-gradient(90deg, #6a82fb 0%, #003399 100%);
    }
    .form-text {
      color: #888;
      font-size: 0.98rem;
      margin-bottom: 12px;
    }
    @media (max-width: 991.98px) {
      .form-row-flex { flex-direction: column !important; }
      .form-row-flex > div { width: 100% !important; margin-bottom: 18px; }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../components/sidebar.php'; ?>
<div class="main-container">
  <div class="profile-chip">
    <i class="fa-regular fa-user-circle"></i> <?= htmlspecialchars($role) ?>
  </div>
  <div class="form-title"><i class="fa fa-plus me-2"></i>Add New Payment</div>
  <form method="post" enctype="multipart/form-data" autocomplete="off">
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-4" style="min-width:180px;">
        <label class="form-label">Select Customer</label>
        <select class="form-select" name="customer_id" required>
          <option value="">Search customer by name or ID...</option>
          <!-- Populate dynamically -->
        </select>
      </div>
      <div class="col-lg-4" style="min-width:180px;">
        <label class="form-label">Select Scheme</label>
        <select class="form-select" name="scheme_id">
          <option value="">Select Scheme</option>
          <!-- Populate dynamically -->
        </select>
      </div>
      <div class="col-lg-4" style="min-width:180px;">
        <label class="form-label">Select Installment</label>
        <select class="form-select" name="installment_id">
          <option value="">Select Installment</option>
          <!-- Populate dynamically -->
        </select>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-lg-6 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="cash_payment" id="cashPayment">
          <label class="form-check-label" for="cashPayment">Cash Payment</label>
        </div>
      </div>
      <div class="col-lg-6 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="extra_payment" id="extraPayment">
          <label class="form-check-label" for="extraPayment">Extra Payment (Skip Scheme & Installment)</label>
        </div>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Amount</label>
      <input type="number" step="0.01" min="0" class="form-control" name="amount" required />
    </div>
    <div class="mb-3">
      <label class="form-label">Payment Screenshot</label>
      <input type="file" class="form-control" name="payment_screenshot" accept="image/jpeg,image/png,image/jpg" />
      <div class="form-text">Max file size: 5MB. Allowed formats: JPG, JPEG, PNG</div>
    </div>
    <div class="mb-3">
      <label class="form-label">Remarks</label>
      <textarea class="form-control" name="remarks" rows="3" placeholder="Enter any remarks about the payment (optional)"></textarea>
    </div>
    <button class="btn btn-primary" type="submit"><i class="fa fa-plus me-2"></i>Add Payment</button>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/scheme_/admin/assets/script.js"></script>
</body>
</html>
