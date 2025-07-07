<?php
// Sidebar component for admin dashboard
?>
<style>
.sidebar {
  width: 220px;
  background: linear-gradient(135deg, #003399 0%, #898ac4 100%);
  min-height: 100vh;
  color: #fff;
  position: fixed;
  top: 0;
  left: 0;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  padding: 32px 0 0 0;
  box-shadow: 2px 0 16px 0 rgba(137,138,196,0.10);
  z-index: 100;
}
.sidebar .sidebar-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-left: 32px;
  margin-bottom: 36px;
  letter-spacing: 1px;
}
.sidebar nav {
  width: 100%;
}
.sidebar nav a {
  display: block;
  width: 92%;
  margin: 0 0 10px 0;
  padding: 14px 20px 14px 32px;
  color: #fff;
  text-decoration: none;
  font-size: 1.08rem;
  font-weight: 500;
  letter-spacing: 0.5px;
  border-left: 4px solid transparent;
  border-radius: 0 30px 30px 0;
  transition: background 0.2s, border-color 0.2s, color 0.2s;
  box-sizing: border-box;
}
.sidebar nav a:hover {
  background: #a2aadb;
  color: #003399;
  border-left: 4px solid #003399;
}
.sidebar nav a.active {
  background: #fff2e0;
  color: #003399;
  border-left: 4px solid #003399;
  border-radius: 0 30px 30px 0;
  font-weight: 700;
  box-shadow: 0 2px 8px 0 rgba(137,138,196,0.10);
  width: 100%;
  margin: 0 0 10px 0;
  padding: 14px 20px 14px 32px;
}
.sidebar .sidebar-footer {
  margin-top: auto;
  width: 100%;
  padding: 24px 0 16px 0;
  text-align: center;
  font-size: 0.95rem;
  color: #e0e0e0;
}
@media (max-width: 900px) {
  .sidebar {
    position: relative;
    width: 100%;
    min-height: unset;
    flex-direction: row;
    padding: 0;
    box-shadow: none;
  }
  .sidebar nav {
    display: flex;
    flex-direction: row;
    width: 100%;
    justify-content: space-around;
  }
  .sidebar nav a {
    padding: 12px 10px;
    font-size: 1rem;
    border-left: none;
    border-bottom: 2px solid transparent;
    border-radius: 20px;
    margin: 0 4px 0 4px;
    width: auto;
  }
  .sidebar nav a:hover, .sidebar nav a.active {
    background: #a2aadb;
    color: #003399;
    border-bottom: 2px solid #003399;
    border-left: none;
    border-radius: 20px;
    width: auto;
    margin: 0 4px 0 4px;
    padding: 12px 10px;
  }
  .sidebar .sidebar-title, .sidebar .sidebar-footer {
    display: none;
  }
}
</style>
<div class="sidebar">
  <div class="sidebar-title">Admin Panel</div>
  <nav>
    <a href="/admin/dashboard/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="/admin/customers.php" class="<?= basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : '' ?>">Customers</a>
    <a href="/admin/payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : '' ?>">Payments</a>
    <a href="/admin/schemes.php" class="<?= basename($_SERVER['PHP_SELF']) == 'schemes.php' ? 'active' : '' ?>">Schemes</a>
    <a href="/admin/installments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'installments.php' ? 'active' : '' ?>">Installments</a>
    <a href="/admin/logout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : '' ?>">Logout</a>
  </nav>
  <div class="sidebar-footer">&copy; <?php echo date('Y'); ?> Scheme Admin</div>
</div> 