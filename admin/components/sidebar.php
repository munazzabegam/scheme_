<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Include database stats helper
require_once __DIR__ . '/../includes/db_stats.php';
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-logo">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="brand-text">
                <h3>Scheme Manager</h3>
                <span>Admin Panel</span>
            </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
        </div>
        <div class="user-info">
            <div class="user-name"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></div>
            <div class="user-role"><?php echo $_SESSION['admin_role'] ?? 'Administrator'; ?></div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <h4 class="nav-title">Main</h4>
            <ul class="nav-list">
                <li class="nav-item <?php echo ($current_dir == 'dashboard') ? 'active' : ''; ?>">
                    <a href="../dashboard/" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'customers') ? 'active' : ''; ?>">
                    <a href="../customers/" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                        <span class="badge"><?php echo getDBStats()['Customers'] ?? 0; ?></span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'schemes') ? 'active' : ''; ?>">
                    <a href="../schemes/" class="nav-link">
                        <i class="fas fa-gift"></i>
                        <span>Schemes</span>
                        <span class="badge"><?php echo getDBStats()['Schemes'] ?? 0; ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h4 class="nav-title">Financial</h4>
            <ul class="nav-list">
                <li class="nav-item <?php echo ($current_dir == 'payments') ? 'active' : ''; ?>">
                    <a href="../payments/" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span>Payments</span>
                        <span class="badge"><?php echo getDBStats()['Payments'] ?? 0; ?></span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'installments') ? 'active' : ''; ?>">
                    <a href="../installments/" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Installments</span>
                        <span class="badge"><?php echo getDBStats()['Installments'] ?? 0; ?></span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'receipts') ? 'active' : ''; ?>">
                    <a href="../receipts/" class="nav-link">
                        <i class="fas fa-receipt"></i>
                        <span>Receipts</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h4 class="nav-title">Management</h4>
            <ul class="nav-list">
                <li class="nav-item <?php echo ($current_dir == 'winners') ? 'active' : ''; ?>">
                    <a href="../winners/" class="nav-link">
                        <i class="fas fa-trophy"></i>
                        <span>Winners</span>
                        <span class="badge"><?php echo getDBStats()['Winners'] ?? 0; ?></span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'enrollments') ? 'active' : ''; ?>">
                    <a href="../enrollments/" class="nav-link">
                        <i class="fas fa-user-plus"></i>
                        <span>Enrollments</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'notifications') ? 'active' : ''; ?>">
                    <a href="../notifications/" class="nav-link">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                        <span class="badge notification-badge"><?php echo getNotificationCount(); ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h4 class="nav-title">Reports</h4>
            <ul class="nav-list">
                <li class="nav-item <?php echo ($current_dir == 'reports') ? 'active' : ''; ?>">
                    <a href="../reports/" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'exports') ? 'active' : ''; ?>">
                    <a href="../exports/" class="nav-link">
                        <i class="fas fa-download"></i>
                        <span>Exports</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'backups') ? 'active' : ''; ?>">
                    <a href="../backups/" class="nav-link">
                        <i class="fas fa-database"></i>
                        <span>Backups</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h4 class="nav-title">Settings</h4>
            <ul class="nav-list">
                <li class="nav-item <?php echo ($current_dir == 'profile') ? 'active' : ''; ?>">
                    <a href="../profile/" class="nav-link">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'settings') ? 'active' : ''; ?>">
                    <a href="../settings/" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="nav-item <?php echo ($current_dir == 'activity') ? 'active' : ''; ?>">
                    <a href="../activity/" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span>Activity Log</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link logout-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<style>
.sidebar {
    width: 280px;
    height: 100vh;
    background: linear-gradient(180deg, #1e293b 0%, #334155 100%);
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    overflow-y: auto;
    transition: all 0.3s ease;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
}

.sidebar.collapsed {
    width: 70px;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.brand-logo {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    flex-shrink: 0;
}

.brand-text h3 {
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    line-height: 1.2;
}

.brand-text span {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
}

.sidebar-toggle {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.sidebar-toggle:hover {
    background: rgba(255, 255, 255, 0.2);
}

.sidebar.collapsed .brand-text,
.sidebar.collapsed .sidebar-toggle {
    display: none;
}

.sidebar-user {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #ff6b6b, #feca57);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 16px;
    flex-shrink: 0;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    color: white;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-role {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar.collapsed .user-info {
    display: none;
}

.sidebar-nav {
    padding: 1rem 0;
}

.nav-section {
    margin-bottom: 2rem;
}

.nav-title {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0 1.5rem 0.5rem;
    margin: 0;
}

.sidebar.collapsed .nav-title {
    display: none;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin: 2px 0;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.75rem 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    border-radius: 0 25px 25px 0;
    margin-right: 1rem;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(5px);
}

.nav-item.active .nav-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.nav-link i {
    width: 20px;
    text-align: center;
    font-size: 16px;
    flex-shrink: 0;
}

.nav-link span {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}

.notification-badge {
    background: #ef4444;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.logout-link {
    color: #ef4444 !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 1rem;
    padding-top: 1rem;
}

.logout-link:hover {
    background: rgba(239, 68, 68, 0.1) !important;
    color: #ef4444 !important;
}

.sidebar.collapsed .nav-link span,
.sidebar.collapsed .badge {
    display: none;
}

.sidebar.collapsed .nav-link {
    justify-content: center;
    padding: 0.75rem;
    margin-right: 0;
    border-radius: 0;
}

.sidebar.collapsed .nav-link:hover {
    transform: none;
}

/* Responsive */
@media (max-width: 1024px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.mobile-open {
        transform: translateX(0);
    }
    
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }
    
    .sidebar-overlay.active {
        display: block;
    }
}

/* Scrollbar styling */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    // Toggle sidebar
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        
        // Save state to localStorage
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    // Restore sidebar state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
    }
    
    // Mobile sidebar toggle
    const mobileToggle = document.querySelector('.mobile-sidebar-toggle');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            document.body.classList.toggle('sidebar-open');
        });
    }
    
    // Close sidebar when clicking overlay
    const overlay = document.querySelector('.sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            document.body.classList.remove('sidebar-open');
        });
    }
    
    // Auto-hide sidebar on mobile when clicking a link
    const navLinks = sidebar.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) {
                sidebar.classList.remove('mobile-open');
                document.body.classList.remove('sidebar-open');
            }
        });
    });
});
</script> 