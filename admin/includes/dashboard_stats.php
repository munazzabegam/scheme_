<?php
require_once __DIR__ . '/../../config/database.php';

function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    try {
        // Total Customers
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Customers WHERE Status = 'Active'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_customers'] = $result['count'];
        
        // Active Schemes
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Schemes WHERE Status = 'Active'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['active_schemes'] = $result['count'];
        
        // Total Revenue (all successful payments)
        $stmt = $conn->prepare("SELECT COALESCE(SUM(Amount), 0) as total FROM Payments WHERE PaymentStatus = 'Success'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_revenue'] = $result['total'];
        
        // Current Month Revenue
        $stmt = $conn->prepare("SELECT COALESCE(SUM(Amount), 0) as total FROM Payments 
                               WHERE PaymentStatus = 'Success' 
                               AND MONTH(PaymentDate) = MONTH(CURRENT_DATE()) 
                               AND YEAR(PaymentDate) = YEAR(CURRENT_DATE())");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['current_month_revenue'] = $result['total'];
        
        // Previous Month Revenue
        $stmt = $conn->prepare("SELECT COALESCE(SUM(Amount), 0) as total FROM Payments 
                               WHERE PaymentStatus = 'Success' 
                               AND MONTH(PaymentDate) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) 
                               AND YEAR(PaymentDate) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['previous_month_revenue'] = $result['total'];
        
        // Total Winners
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Winners WHERE Status IN ('Verified', 'Delivered')");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_winners'] = $result['count'];
        
        // Pending Winners
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Winners WHERE Status = 'Pending'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['pending_winners'] = $result['count'];
        
        // Total Enrollments
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM CustomerSchemeEnrollments WHERE Status = 'Enrolled'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_enrollments'] = $result['count'];
        
        // Pending Installments
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Installments WHERE Status = 'Pending'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['pending_installments'] = $result['count'];
        
        // Overdue Installments
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Installments WHERE Status = 'Overdue'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['overdue_installments'] = $result['count'];
        
        // Calculate growth percentages
        $stats['revenue_growth'] = calculateGrowthPercentage($stats['current_month_revenue'], $stats['previous_month_revenue']);
        
        // Customer growth (this month vs last month)
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Customers 
                               WHERE Status = 'Active' 
                               AND MONTH(CreatedAt) = MONTH(CURRENT_DATE()) 
                               AND YEAR(CreatedAt) = YEAR(CURRENT_DATE())");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentMonthCustomers = $result['count'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Customers 
                               WHERE Status = 'Active' 
                               AND MONTH(CreatedAt) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) 
                               AND YEAR(CreatedAt) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $previousMonthCustomers = $result['count'];
        
        $stats['customer_growth'] = calculateGrowthPercentage($currentMonthCustomers, $previousMonthCustomers);
        
        // Scheme growth
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Schemes 
                               WHERE Status = 'Active' 
                               AND MONTH(CreatedAt) = MONTH(CURRENT_DATE()) 
                               AND YEAR(CreatedAt) = YEAR(CURRENT_DATE())");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentMonthSchemes = $result['count'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Schemes 
                               WHERE Status = 'Active' 
                               AND MONTH(CreatedAt) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) 
                               AND YEAR(CreatedAt) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $previousMonthSchemes = $result['count'];
        
        $stats['scheme_growth'] = $currentMonthSchemes - $previousMonthSchemes; // Absolute difference for schemes
        
        // Winner growth
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Winners 
                               WHERE Status IN ('Verified', 'Delivered') 
                               AND MONTH(WinningDate) = MONTH(CURRENT_DATE()) 
                               AND YEAR(WinningDate) = YEAR(CURRENT_DATE())");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentMonthWinners = $result['count'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Winners 
                               WHERE Status IN ('Verified', 'Delivered') 
                               AND MONTH(WinningDate) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) 
                               AND YEAR(WinningDate) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $previousMonthWinners = $result['count'];
        
        $stats['winner_growth'] = $currentMonthWinners - $previousMonthWinners; // Absolute difference for winners
        
    } catch (PDOException $e) {
        // Return default values if database error occurs
        $stats = [
            'total_customers' => 0,
            'active_schemes' => 0,
            'total_revenue' => 0,
            'current_month_revenue' => 0,
            'previous_month_revenue' => 0,
            'total_winners' => 0,
            'pending_winners' => 0,
            'total_enrollments' => 0,
            'pending_installments' => 0,
            'overdue_installments' => 0,
            'revenue_growth' => 0,
            'customer_growth' => 0,
            'scheme_growth' => 0,
            'winner_growth' => 0
        ];
    }
    
    return $stats;
}

function getMonthlyRevenueData() {
    global $conn;
    
    $monthlyData = [];
    
    try {
        // Get revenue for last 12 months
        $stmt = $conn->prepare("SELECT 
                                   DATE_FORMAT(PaymentDate, '%Y-%m') as month,
                                   COALESCE(SUM(Amount), 0) as revenue
                               FROM Payments 
                               WHERE PaymentStatus = 'Success' 
                               AND PaymentDate >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
                               GROUP BY DATE_FORMAT(PaymentDate, '%Y-%m')
                               ORDER BY month");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Initialize all months with 0
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthlyData[$month] = 0;
        }
        
        // Fill in actual data
        foreach ($results as $row) {
            $monthlyData[$row['month']] = (float)$row['revenue'];
        }
        
    } catch (PDOException $e) {
        // Return empty data if error
        $monthlyData = [];
    }
    
    return $monthlyData;
}

function getRecentActivities() {
    global $conn;
    
    $activities = [];
    
    try {
        // Get recent payments
        $stmt = $conn->prepare("SELECT 
                                   'payment' as type,
                                   CONCAT('Payment of ₹', Amount, ' received from Customer #', CustomerID) as message,
                                   PaymentDate as activity_date,
                                   'fas fa-credit-card' as icon,
                                   '#10b981' as color
                               FROM Payments 
                               WHERE PaymentStatus = 'Success'
                               ORDER BY PaymentDate DESC 
                               LIMIT 5");
        $stmt->execute();
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get recent enrollments
        $stmt = $conn->prepare("SELECT 
                                   'enrollment' as type,
                                   CONCAT('Customer #', c.CustomerID, ' enrolled in ', s.SchemeName) as message,
                                   cse.EnrolledOn as activity_date,
                                   'fas fa-user-plus' as icon,
                                   '#3b82f6' as color
                               FROM CustomerSchemeEnrollments cse
                               JOIN Customers c ON cse.CustomerID = c.CustomerID
                               JOIN Schemes s ON cse.SchemeID = s.SchemeID
                               ORDER BY cse.EnrolledOn DESC 
                               LIMIT 5");
        $stmt->execute();
        $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get recent winners
        $stmt = $conn->prepare("SELECT 
                                   'winner' as type,
                                   CONCAT('New winner selected for ', PrizeType) as message,
                                   WinningDate as activity_date,
                                   'fas fa-trophy' as icon,
                                   '#ef4444' as color
                               FROM Winners 
                               ORDER BY WinningDate DESC 
                               LIMIT 5");
        $stmt->execute();
        $winners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combine and sort by date
        $allActivities = array_merge($payments, $enrollments, $winners);
        usort($allActivities, function($a, $b) {
            return strtotime($b['activity_date']) - strtotime($a['activity_date']);
        });
        
        // Take top 10 and format time
        $activities = array_slice($allActivities, 0, 10);
        foreach ($activities as &$activity) {
            $activity['time'] = timeAgo($activity['activity_date']);
        }
        
    } catch (PDOException $e) {
        // Return mock data if error
        $activities = [
            ['type' => 'payment', 'message' => 'Payment received from Customer #1234', 'time' => '2 minutes ago', 'icon' => 'fas fa-credit-card', 'color' => '#10b981'],
            ['type' => 'enrollment', 'message' => 'Customer #5678 enrolled in Scheme A', 'time' => '15 minutes ago', 'icon' => 'fas fa-user-plus', 'color' => '#3b82f6'],
            ['type' => 'winner', 'message' => 'New winner selected for monthly draw', 'time' => '2 hours ago', 'icon' => 'fas fa-trophy', 'color' => '#ef4444']
        ];
    }
    
    return $activities;
}

function calculateGrowthPercentage($current, $previous) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    return round((($current - $previous) / $previous) * 100, 1);
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    }
}

function formatCurrency($amount) {
    return '₹' . number_format($amount, 0);
}
?> 