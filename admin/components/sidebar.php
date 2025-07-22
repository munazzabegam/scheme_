<?php
// Sidebar component for admin dashboard
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/style.css">
<script src="/scheme_/admin/assets/script.js" defer></script>
<div class="sidebar-premium">
  <div class="sidebar-title"><i class="fa-solid fa-gem"></i> Admin Panel</div>
  <nav>
    <a href="/scheme_/admin/dashboard/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'dashboard' ? 'active' : '' ?>">
      <i class="fa-solid fa-chart-line"></i> Dashboard
    </a>
    <a href="/scheme_/admin/user/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'user' ? 'active' : '' ?>">
      <i class="fa-solid fa-user"></i> Users
    </a>
    <a href="/scheme_/admin/customers/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'customers' ? 'active' : '' ?>">
      <i class="fa-solid fa-users"></i> Customers
    </a>
    <a href="/scheme_/admin/payments/index.php" class="<?= basename(dirname($_SERVER['PHP_SELF'])) == 'payments' ? 'active' : '' ?>">
      <i class="fa-solid fa-credit-card"></i> Payments
    </a>
    <a href="/scheme_/admin/schemes/index.php" class="<?= basename(dirname($_SERVER['PHP_SELF'])) == 'schemes' ? 'active' : '' ?>">
      <i class="fa-solid fa-gift"></i> Schemes
    </a>
    <a href="/scheme_/admin/installments/index.php" class="<?= basename(dirname($_SERVER['PHP_SELF'])) == 'installments' ? 'active' : '' ?>">
      <i class="fa-solid fa-coins"></i> Installments
    </a>
    <a href="/scheme_/admin/logout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </nav>
  <div class="sidebar-footer">&copy; <?php echo date('Y'); ?> Scheme Admin</div>
</div> 