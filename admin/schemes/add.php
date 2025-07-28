<?php
// Database connection
include_once '../../config/database.php';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@gmail.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'SuperAdmin';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schemeName = trim($_POST['schemeName'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $monthlyPayment = floatval($_POST['monthlyPayment'] ?? 0);
    $totalPayments = intval($_POST['totalPayments'] ?? 0);
    $status = 'Active'; // Default status
    $schemeImageURL = null;

    // Handle scheme image upload
    if (isset($_FILES['schemeImage']) && $_FILES['schemeImage']['error'] === UPLOAD_ERR_OK) {
        $imgTmp = $_FILES['schemeImage']['tmp_name'];
        $imgName = basename($_FILES['schemeImage']['name']);
        $targetDir = __DIR__ . '/../uploads/schemes/';
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        $targetFileName = uniqid('scheme_') . '_' . $imgName;
        $targetPath = $targetDir . $targetFileName;
        if (move_uploaded_file($imgTmp, $targetPath)) {
            $schemeImageURL = '/admin/uploads/schemes/' . $targetFileName;
        }
    }

    // Insert scheme
    $stmt = $conn->prepare("INSERT INTO Schemes (SchemeName, SchemeImageURL, Description, MonthlyPayment, TotalPayments, Status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssdis', $schemeName, $schemeImageURL, $description, $monthlyPayment, $totalPayments, $status);
    if ($stmt->execute()) {
        $schemeID = $stmt->insert_id;
        $stmt->close();
        // Handle installments
        if (isset($_POST['installments']) && is_array($_POST['installments'])) {
            foreach ($_POST['installments'] as $i => $inst) {
                $instName = trim($inst['name'] ?? '');
                $instNumber = intval($inst['number'] ?? 0);
                $instAmount = floatval($inst['amount'] ?? 0);
                $instDrawDate = $inst['draw_date'] ?? null;
                $instBenefits = trim($inst['benefits'] ?? '');
                $isRepayable = isset($inst['is_repayable']) ? 1 : 0;
                $instImageURL = null;
                // Handle installment image upload
                if (isset($_FILES['installments']['name'][$i]['image']) && $_FILES['installments']['error'][$i]['image'] === UPLOAD_ERR_OK) {
                    $imgTmp = $_FILES['installments']['tmp_name'][$i]['image'];
                    $imgName = basename($_FILES['installments']['name'][$i]['image']);
                    $targetDir = __DIR__ . '/../uploads/installments/';
                    if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
                    $targetFileName = uniqid('inst_') . '_' . $imgName;
                    $targetPath = $targetDir . $targetFileName;
                    if (move_uploaded_file($imgTmp, $targetPath)) {
                        $instImageURL = '/admin/uploads/installments/' . $targetFileName;
                    }
                }
                $stmt2 = $conn->prepare("INSERT INTO Installments (SchemeID, InstallmentName, InstallmentNumber, Amount, DrawDate, Benefits, ImageURL, Status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
                $stmt2->bind_param('isidsss', $schemeID, $instName, $instNumber, $instAmount, $instDrawDate, $instBenefits, $instImageURL);
                $stmt2->execute();
                $stmt2->close();
            }
        }
        header('Location: index.php?added=1');
        exit;
    } else {
        $error = 'Failed to add scheme.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Scheme</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/style.css">
  <style>
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
    .section-title {
      font-size: 1.3rem;
      font-weight: 600;
      color: #003399;
      margin-bottom: 18px;
      margin-top: 32px;
    }
    .form-label { font-weight: 500; color: #003399; margin-bottom: 4px; }
    .form-control, .form-select {
      border-radius: 8px;
      font-size: 1.08rem;
      margin-bottom: 18px;
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
    .installment-card {
      background: #f7f8fa;
      border-radius: 12px;
      padding: 24px 18px 12px 18px;
      margin-bottom: 18px;
      position: relative;
      border: 1px solid #e0e0e0;
    }
    .remove-installment {
      position: absolute;
      top: 16px;
      right: 18px;
      color: #e74c3c;
      font-size: 1.3rem;
      cursor: pointer;
    }
    .installment-label {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 12px;
      color: #003399;
    }
    .btn-add { margin-bottom: 24px; }
    .form-section { margin-bottom: 32px; }
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
  <div class="form-title"><i class="fa fa-cubes me-2"></i>Add New Scheme</div>
  <form id="schemeForm" method="post" enctype="multipart/form-data" autocomplete="off">
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-6">
        <label for="schemeName" class="form-label">Scheme Name *</label>
        <input type="text" class="form-control" id="schemeName" name="schemeName" required>
      </div>
      <div class="col-lg-6">
        <label for="schemeImage" class="form-label">Scheme Image</label>
        <input type="file" class="form-control" id="schemeImage" name="schemeImage" accept="image/*">
      </div>
    </div>
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-12">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
      </div>
    </div>
    <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
      <div class="col-lg-6">
        <label for="monthlyPayment" class="form-label">Monthly Payment (₹) *</label>
        <input type="number" class="form-control" id="monthlyPayment" name="monthlyPayment" required min="0" step="0.01">
      </div>
      <div class="col-lg-6">
        <label for="totalPayments" class="form-label">Total Number of Payments *</label>
        <input type="number" class="form-control" id="totalPayments" name="totalPayments" required min="1" step="1">
      </div>
    </div>
    <div class="section-title">Installments</div>
    <div id="installmentsList"></div>
    <button type="button" class="btn btn-primary btn-add" id="addInstallmentBtn"><i class="fa fa-plus me-2"></i>Add Installment</button>
    <div class="d-flex gap-2 mt-4">
      <button type="submit" class="btn btn-primary"><i class="fa fa-save me-2"></i>Save Scheme</button>
      <a href="index.php" class="btn btn-secondary"><i class="fa fa-times me-2"></i>Cancel</a>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let installmentCount = 0;
function createInstallmentCard(num) {
  return `
    <div class="installment-card" data-index="${num}">
      <div class="installment-label">Installment #${num+1}
        <span class="remove-installment" title="Remove" onclick="removeInstallment(this)">&times;</span>
      </div>
      <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
        <div class="col-lg-3">
          <label class="form-label">Installment Name *</label>
          <input type="text" class="form-control" name="installments[${num}][name]" required>
        </div>
        <div class="col-lg-2">
          <label class="form-label">Installment Number *</label>
          <input type="number" class="form-control" name="installments[${num}][number]" required min="1">
        </div>
        <div class="col-lg-3">
          <label class="form-label">Amount (₹) *</label>
          <input type="number" class="form-control" name="installments[${num}][amount]" required min="0" step="0.01">
        </div>
        <div class="col-lg-4">
          <label class="form-label">Draw Date *</label>
          <input type="date" class="form-control" name="installments[${num}][draw_date]" required>
        </div>
      </div>
      <div class="row form-row-flex mb-3" style="display: flex; gap: 18px;">
        <div class="col-lg-6">
          <label class="form-label">Benefits</label>
          <textarea class="form-control" name="installments[${num}][benefits]" rows="2"></textarea>
        </div>
        <div class="col-lg-4">
          <label class="form-label">Image</label>
          <input type="file" class="form-control" name="installments[${num}][image]" accept="image/*">
        </div>
        <div class="col-lg-2 d-flex align-items-center">
          <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="installments[${num}][is_repayable]" value="1" id="isRepayable${num}">
            <label class="form-check-label" for="isRepayable${num}">Is Repayable</label>
          </div>
        </div>
      </div>
    </div>
  `;
}
function addInstallment() {
  const list = document.getElementById('installmentsList');
  list.insertAdjacentHTML('beforeend', createInstallmentCard(installmentCount));
  installmentCount++;
}
function removeInstallment(el) {
  el.closest('.installment-card').remove();
}
document.getElementById('addInstallmentBtn').addEventListener('click', addInstallment);
window.onload = function() { addInstallment(); };
</script>
</body>
</html>
