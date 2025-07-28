<?php
// You can set the current page for active highlighting, e.g. $current = 'dashboard';
if (!isset($current)) $current = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="sidebar-premium">
  <div class="sidebar-title">
    <i class="fa fa-user-circle"></i> Customer Panel
  </div>
  <nav>
    <a href="/scheme_/customer/dashboard/" class="<?= $current === 'dashboard' ? 'active' : '' ?>">
      <i class="fa fa-home"></i> Dashboard
    </a>
    <a href="/scheme_/customer/profile.php" class="<?= $current === 'profile' ? 'active' : '' ?>">
      <i class="fa fa-user"></i> Profile
    </a>
    <a href="/scheme_/customer/schemes.php" class="<?= $current === 'schemes' ? 'active' : '' ?>">
      <i class="fa fa-cubes"></i> Schemes
    </a>
    <a href="/scheme_/customer/enrollments.php" class="<?= $current === 'enrollments' ? 'active' : '' ?>">
      <i class="fa fa-list"></i> My Enrollments
    </a>
    <a href="/scheme_/customer/payments.php" class="<?= $current === 'payments' ? 'active' : '' ?>">
      <i class="fa fa-credit-card"></i> Payments
    </a>
    <a href="/scheme_/customer/installments.php" class="<?= $current === 'installments' ? 'active' : '' ?>">
      <i class="fa fa-calendar-check"></i> Installments
    </a>
    <a href="/scheme_/customer/notifications.php" class="<?= $current === 'notifications' ? 'active' : '' ?>">
      <i class="fa fa-bell"></i> Notifications
    </a>
    <a href="/scheme_/customer/logout.php" class="<?= $current === 'logout' ? 'active' : '' ?>">
      <i class="fa fa-sign-out-alt"></i> Logout
    </a>
  </nav>
  <div class="sidebar-footer">
    &copy; <?= date('Y') ?> Scheme Customer Panel
  </div>
</div>
<style>
.sidebar-premium {
  width: 250px;
  background: #16213e;
  min-height: 100vh;
  color: #fff;
  position: fixed;
  top: 0;
  left: 0;
  display: flex;
  flex-direction: column;
  padding: 32px 0 0 0;
  box-shadow: 2px 0 16px 0 rgba(22,33,62,0.04);
  z-index: 100;
}
.sidebar-premium .sidebar-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-left: 32px;
  margin-bottom: 36px;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.sidebar-premium nav {
  width: 100%;
}
.sidebar-premium nav a {
  display: flex;
  align-items: center;
  gap: 14px;
  width: 92%;
  margin: 0 0 10px 0;
  padding: 14px 20px 14px 32px;
  color: #bfc9da;
  text-decoration: none;
  font-size: 1.08rem;
  font-weight: 500;
  border-left: 4px solid transparent;
  border-radius: 0 30px 30px 0;
  transition: background 0.2s, border-color 0.2s, color 0.2s;
  box-sizing: border-box;
}
.sidebar-premium nav a.active {
  color: #fff;
  border-left: 4px solid #4f8cff;
  background: #233055;
  font-weight: 700;
  border-radius: 0 30px 30px 0;
  box-shadow: 0 2px 8px 0 rgba(137,138,196,0.10);
  width: 100%;
  margin: 0 0 10px 0;
  padding: 14px 20px 14px 32px;
}
.sidebar-premium nav a:hover {
  background: #233055;
  color: #fff;
}
.sidebar-premium .sidebar-footer {
  margin-top: auto;
  width: 100%;
  padding: 24px 0 16px 0;
  text-align: center;
  font-size: 0.95rem;
  color: #e0e0e0;
}
@media (max-width: 900px) {
  .sidebar-premium {
    position: relative;
    width: 100%;
    min-height: unset;
    flex-direction: row;
    padding: 0;
    box-shadow: none;
  }
  .sidebar-premium nav {
    display: flex;
    flex-direction: row;
    width: 100%;
    justify-content: space-around;
  }
  .sidebar-premium nav a {
    padding: 12px 10px;
    font-size: 1rem;
    border-left: none;
    border-bottom: 2px solid transparent;
    border-radius: 20px;
    margin: 0 4px 0 4px;
    width: auto;
  }
  .sidebar-premium nav a:hover, .sidebar-premium nav a.active {
    background: #a2aadb;
    color: #003399;
    border-bottom: 2px solid #003399;
  }
  .sidebar-premium .sidebar-title, .sidebar-premium .sidebar-footer {
    display: none;
  }
}
</style> 