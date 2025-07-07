<?php
require_once '../auth.php';
require_once '../../config/database.php';
require_once '../includes/dashboard_stats.php';

// Require login and get admin data
$admin = requireLogin();

// Get real database statistics
$stats = getDashboardStats();
$monthlyRevenue = getMonthlyRevenueData();
$recentActivities = getRecentActivities();

// Calculate current month revenue
$currentRevenue = $stats['current_month_revenue'];
$previousRevenue = $stats['previous_month_revenue'];
$growthPercentage = $stats['revenue_growth'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Scheme Manager</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed + .main-content {
            margin-left: 70px;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Mobile sidebar toggle */
        .mobile-sidebar-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .mobile-sidebar-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        /* Mobile responsive */
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.collapsed + .main-content {
                margin-left: 0;
            }
            
            .mobile-sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #fff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .welcome-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .welcome-stat {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .trend-up {
            color: #10b981;
        }

        .trend-down {
            color: #ef4444;
        }

        .stat-number-large {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .stat-label-large {
            color: #64748b;
            font-size: 1rem;
            font-weight: 500;
        }

        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e293b;
        }

        .chart-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chart-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            background: #f1f5f9;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .chart-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .chart-btn.active {
            background: #667eea;
            color: white;
        }

        .activities-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item:hover {
            background: #f8fafc;
            border-radius: 10px;
            padding-left: 1rem;
            padding-right: 1rem;
            margin: 0 -1rem;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        .activity-content {
            flex: 1;
        }

        .activity-message {
            color: #1e293b;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            color: #64748b;
            font-size: 0.9rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #1e293b;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .action-text {
            font-weight: 500;
        }

        @media (max-width: 1024px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .welcome-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../components/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Mobile sidebar toggle -->
        <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Sidebar overlay for mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome back, <?php echo $_SESSION['admin_name']; ?>!</h1>
                <p class="welcome-subtitle">Here's what's happening with your scheme management system today.</p>
                
                <div class="welcome-stats">
                    <div class="welcome-stat">
                        <div class="stat-number"><?php echo number_format($stats['total_customers']); ?></div>
                        <div class="stat-label">Total Customers</div>
                    </div>
                    <div class="welcome-stat">
                        <div class="stat-number"><?php echo number_format($stats['active_schemes']); ?></div>
                        <div class="stat-label">Active Schemes</div>
                    </div>
                    <div class="welcome-stat">
                        <div class="stat-number"><?php echo formatCurrency($currentRevenue); ?></div>
                        <div class="stat-label">This Month's Revenue</div>
                    </div>
                    <div class="welcome-stat">
                        <div class="stat-number"><?php echo number_format($stats['total_winners']); ?></div>
                        <div class="stat-label">Total Winners</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-trend <?php echo $stats['customer_growth'] >= 0 ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fas fa-arrow-<?php echo $stats['customer_growth'] >= 0 ? 'up' : 'down'; ?>"></i>
                        <?php echo abs($stats['customer_growth']); ?>%
                    </div>
                </div>
                <div class="stat-number-large"><?php echo number_format($stats['total_customers']); ?></div>
                <div class="stat-label-large">Total Customers</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="stat-trend <?php echo $stats['scheme_growth'] >= 0 ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fas fa-arrow-<?php echo $stats['scheme_growth'] >= 0 ? 'up' : 'down'; ?>"></i>
                        <?php echo $stats['scheme_growth'] >= 0 ? '+' : ''; ?><?php echo $stats['scheme_growth']; ?>
                    </div>
                </div>
                <div class="stat-number-large"><?php echo number_format($stats['active_schemes']); ?></div>
                <div class="stat-label-large">Active Schemes</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stat-trend <?php echo $growthPercentage >= 0 ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fas fa-arrow-<?php echo $growthPercentage >= 0 ? 'up' : 'down'; ?>"></i>
                        <?php echo abs(round($growthPercentage, 1)); ?>%
                    </div>
                </div>
                <div class="stat-number-large"><?php echo formatCurrency($currentRevenue); ?></div>
                <div class="stat-label-large">Monthly Revenue</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-trend <?php echo $stats['winner_growth'] >= 0 ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fas fa-arrow-<?php echo $stats['winner_growth'] >= 0 ? 'up' : 'down'; ?>"></i>
                        <?php echo $stats['winner_growth'] >= 0 ? '+' : ''; ?><?php echo $stats['winner_growth']; ?>
                    </div>
                </div>
                <div class="stat-number-large"><?php echo number_format($stats['total_winners']); ?></div>
                <div class="stat-label-large">Total Winners</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Revenue Overview</h3>
                    <div class="chart-actions">
                        <button class="chart-btn active">Monthly</button>
                        <button class="chart-btn">Quarterly</button>
                        <button class="chart-btn">Yearly</button>
                    </div>
                </div>
                <canvas id="revenueChart" height="300"></canvas>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Recent Activities</h3>
                </div>
                <div class="activities-section" style="box-shadow: none; padding: 0;">
                    <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: <?php echo $activity['color']; ?>">
                            <i class="<?php echo $activity['icon']; ?>"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-message"><?php echo $activity['message']; ?></div>
                            <div class="activity-time"><?php echo $activity['time']; ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="../customers/" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="action-text">Add New Customer</div>
            </a>
            
            <a href="../schemes/" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="action-text">Create New Scheme</div>
            </a>
            
            <a href="../payments/" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="action-text">Process Payment</div>
            </a>
            
            <a href="../reports/" class="action-btn">
                <div class="action-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="action-text">Generate Report</div>
            </a>
        </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($monthlyRevenue)); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode(array_values($monthlyRevenue)); ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + (value / 1000) + 'K';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Chart button functionality
        document.querySelectorAll('.chart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Auto-refresh dashboard data every 30 seconds
        setInterval(() => {
            // You can add AJAX calls here to refresh data
            console.log('Refreshing dashboard data...');
        }, 30000);

        // Mobile sidebar functionality
        const mobileToggle = document.getElementById('mobileSidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
                document.body.classList.toggle('sidebar-open');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            });
        }
    </script>
</body>
</html> 