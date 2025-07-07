<?php
require_once '../config/database.php';

echo "<h2>Database Tables Check</h2>";

try {
    // List of required tables
    $requiredTables = [
        'Admins',
        'Customers', 
        'Schemes',
        'Payments',
        'Winners',
        'CustomerSchemeEnrollments',
        'Installments',
        'Notifications'
    ];
    
    echo "<h3>Checking Required Tables:</h3>";
    
    foreach ($requiredTables as $table) {
        $stmt = $conn->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' missing<br>";
        }
    }
    
    echo "<h3>Table Structure Check:</h3>";
    
    // Check if tables have data
    $tablesWithData = [
        'Admins' => 'SELECT COUNT(*) as count FROM Admins',
        'Customers' => 'SELECT COUNT(*) as count FROM Customers',
        'Schemes' => 'SELECT COUNT(*) as count FROM Schemes',
        'Payments' => 'SELECT COUNT(*) as count FROM Payments',
        'Winners' => 'SELECT COUNT(*) as count FROM Winners'
    ];
    
    foreach ($tablesWithData as $table => $query) {
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "📊 Table '$table' has {$result['count']} records<br>";
        } catch (Exception $e) {
            echo "❌ Error checking '$table': " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "❌ " . $e->getMessage();
}
?> 