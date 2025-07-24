<?php
// DEBUG MARKER: SIDEBAR COMPONENT LOADED - v1.0
// Sidebar component for admin dashboard
?>
<!-- DEBUG MARKER: SIDEBAR COMPONENT LOADED - v1.0 -->
<div class="sidebar-premium">
  <div class="sidebar-title"><i class="fa-solid fa-gem"></i> Admin Panel</div>
  <nav>
    <a href="/scheme_/admin/dashboard/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'dashboard' ? 'active' : '' ?>">
      <i class="fa-solid fa-chart-line"></i> Dashboard
    </a>
    <a href="/scheme_/admin/user/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'user' ? 'active' : '' ?>">
      <i class="fa-solid fa-user-shield"></i> Users
    </a>
    <a href="/scheme_/admin/customers/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'customers' ? 'active' : '' ?>">
      <i class="fa-solid fa-users"></i> Customers
    </a>
    <a href="/scheme_/admin/payments/index.php" class="<?= basename(dirname($_SERVER['PHP_SELF'])) == 'payments' ? 'active' : '' ?>">
      <i class="fa-solid fa-credit-card"></i> Payments
    </a>
    <a href="/scheme_/admin/schemes/index.php" class="<?= basename(dirname($_SERVER['PHP_SELF'])) == 'schemes' ? 'active' : '' ?>">
      <i class="fa-solid fa-layer-group"></i> Schemes
    </a>
    <a href="/scheme_/admin/logout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
    </a>
  </nav>
  <div class="sidebar-footer">&copy; <?php echo date('Y'); ?> Scheme Admin</div>
</div> 