<?php
// Test file to verify dashboard functions
require_once '../config/database.php';
require_once 'includes/dashboard_stats.php';

echo "<h2>Testing Dashboard Functions</h2>";

try {
    echo "<h3>Database Connection Test:</h3>";
    if ($conn) {
        echo "✅ Database connection successful<br>";
    } else {
        echo "❌ Database connection failed<br>";
    }
    
    echo "<h3>Dashboard Stats Test:</h3>";
    $stats = getDashboardStats();
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
    
    echo "<h3>Monthly Revenue Test:</h3>";
    $monthlyData = getMonthlyRevenueData();
    echo "<pre>";
    print_r($monthlyData);
    echo "</pre>";
    
    echo "<h3>Recent Activities Test:</h3>";
    $activities = getRecentActivities();
    echo "<pre>";
    print_r($activities);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "❌ " . $e->getMessage();
}
?> 