<?php
// Database connection
include_once '../../config/database.php';

$error = '';
$success = '';
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
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $schemeName = trim($_POST['schemeName'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $monthlyPayment = floatval($_POST['monthlyPayment'] ?? 0);
    $totalPayments = intval($_POST['totalPayments'] ?? 0);
    $status = $_POST['status'] ?? 'Active';
    $startDate = $_POST['startDate'] ?? null;
    
    // Update scheme
    $stmt = $conn->prepare("UPDATE Schemes SET SchemeName = ?, Description = ?, MonthlyPayment = ?, TotalPayments = ?, Status = ?, StartDate = ? WHERE SchemeID = ?");
    $stmt->bind_param('ssdiisi', $schemeName, $description, $monthlyPayment, $totalPayments, $status, $startDate, $id);
    
    if ($stmt->execute()) {
        $success = 'Scheme updated successfully!';
        // Refresh scheme data
        $stmt = $conn->prepare("SELECT * FROM Schemes WHERE SchemeID = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $scheme = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = 'Failed to update scheme.';
    }
    $stmt->close();
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
  <title>Edit Scheme</title>
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
    .form-title {
      font-size: 2rem;
      font-weight: 700;
      color: #2d3a4b;
      margin-bottom: 32px;
      letter-spacing: 0.5px;
      text-align: left;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .section-title {
      font-size: 1.3rem;
      font-weight: 600;
      color: #2d3a4b;
      margin-bottom: 18px;
      margin-top: 32px;
    }
    .form-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
      border: 1px solid #e5e7eb;
      padding: 24px 28px;
      margin-bottom: 24px;
      transition: box-shadow 0.3s;
    }
    .form-card:hover {
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.1);
    }
    .form-label { 
      font-weight: 500; 
      color: #003399; 
      margin-bottom: 4px; 
    }
    .form-control, .form-select {
      border-radius: 8px;
      font-size: 1.08rem;
      margin-bottom: 18px;
      border: 1px solid #e5e7eb;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus, .form-select:focus {
      border-color: #003399;
      box-shadow: 0 0 0 0.2rem rgba(0,51,153,0.25);
    }
    .btn-primary {
      background: #003399;
      border: none;
      font-weight: 600;
      border-radius: 10px;
      padding: 12px 32px;
      font-size: 1.1rem;
      margin-top: 10px;
      transition: background 0.25s, box-shadow 0.25s;
    }
    .btn-primary:hover {
      background: #002266;
      box-shadow: 0 4px 16px 0 rgba(0,51,153,0.3);
    }
    .btn-secondary {
      background: #6c757d;
      border: none;
      font-weight: 600;
      border-radius: 10px;
      padding: 12px 32px;
      font-size: 1.1rem;
      margin-top: 10px;
      transition: background 0.25s, box-shadow 0.25s;
    }
    .btn-secondary:hover {
      background: #5a6268;
      box-shadow: 0 4px 16px 0 rgba(108,117,125,0.3);
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
      justify-content: space-between;
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
    .btn-add { 
      margin-bottom: 24px; 
    }
    .form-section { 
      margin-bottom: 32px; 
    }
    .alert {
      border-radius: 8px;
      margin-bottom: 20px;
    }
    @media (max-width: 991.98px) {
      .form-row-flex { flex-direction: column !important; }
      .form-row-flex > div { width: 100% !important; margin-bottom: 18px; }
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
  <?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  
  <?php if (!$error): ?>
    <div class="form-title"><i class="fa fa-edit me-2"></i>Edit Scheme</div>
    
    <form method="post" enctype="multipart/form-data" autocomplete="off">
      <div class="form-card">
        <div class="section-title">Basic Information</div>
        <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
          <div class="col-lg-6">
            <label for="schemeName" class="form-label">Scheme Name *</label>
            <input type="text" class="form-control" id="schemeName" name="schemeName" value="<?= htmlspecialchars($scheme['SchemeName']) ?>" required>
          </div>
          <div class="col-lg-6">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="Active" <?= $scheme['Status'] === 'Active' ? 'selected' : '' ?>>Active</option>
              <option value="Closed" <?= $scheme['Status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
              <option value="Upcoming" <?= $scheme['Status'] === 'Upcoming' ? 'selected' : '' ?>>Upcoming</option>
            </select>
          </div>
        </div>
        
        <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
          <div class="col-lg-12">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($scheme['Description'] ?? '') ?></textarea>
          </div>
        </div>
        
        <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
          <div class="col-lg-4">
            <label for="monthlyPayment" class="form-label">Monthly Payment (₹) *</label>
            <input type="number" class="form-control" id="monthlyPayment" name="monthlyPayment" value="<?= htmlspecialchars($scheme['MonthlyPayment']) ?>" required min="0" step="0.01">
          </div>
          <div class="col-lg-4">
            <label for="totalPayments" class="form-label">Total Number of Payments *</label>
            <input type="number" class="form-control" id="totalPayments" name="totalPayments" value="<?= htmlspecialchars($scheme['TotalPayments']) ?>" required min="1" step="1">
          </div>
          <div class="col-lg-4">
            <label for="startDate" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="startDate" name="startDate" value="<?= $scheme['StartDate'] ?? '' ?>">
          </div>
        </div>
      </div>
      
      <div class="form-card">
        <div class="section-title">Current Installments (<?= count($installments) ?>)</div>
        <?php if (empty($installments)): ?>
          <div class="text-center text-muted py-4">
            <i class="fa fa-list-alt fa-3x mb-3"></i>
            <p>No installments found for this scheme.</p>
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
                  <div class="form-label">Amount</div>
                  <div class="form-control-plaintext">₹<?= number_format($installment['Amount'], 2) ?></div>
                </div>
                <div>
                  <div class="form-label">Draw Date</div>
                  <div class="form-control-plaintext"><?= $installment['DrawDate'] ? date('M d, Y', strtotime($installment['DrawDate'])) : 'Not set' ?></div>
                </div>
                <?php if ($installment['ImageURL']): ?>
                  <div style="grid-column: 1 / -1; margin-top: 12px;">
                    <div class="form-label">Image</div>
                    <img src="<?= htmlspecialchars($installment['ImageURL']) ?>" alt="Installment Image" class="installment-image">
                  </div>
                <?php endif; ?>
                <?php if ($installment['Benefits']): ?>
                  <div style="grid-column: 1 / -1; margin-top: 12px;">
                    <div class="form-label">Benefits</div>
                    <div class="form-control-plaintext"><?= nl2br(htmlspecialchars($installment['Benefits'])) ?></div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      
      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Update Scheme</button>
        <a href="view.php?id=<?= $scheme['SchemeID'] ?>" class="btn btn-secondary"><i class="fa fa-eye me-2"></i>View Scheme</a>
        <a href="index.php" class="btn btn-secondary"><i class="fa fa-times me-2"></i>Cancel</a>
      </div>
    </form>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/script.js"></script>
</body>
</html>
