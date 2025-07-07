<?php
require_once __DIR__ . '/../../config/database.php';

function getDBStats() {
    global $conn;
    
    $stats = [];
    
    try {
        // Get customer count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Customers WHERE Status = 'Active'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['Customers'] = $result['count'];
        
        // Get scheme count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Schemes WHERE Status = 'Active'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['Schemes'] = $result['count'];
        
        // Get payment count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Payments WHERE PaymentStatus = 'Success'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['Payments'] = $result['count'];
        
        // Get installment count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Installments WHERE Status = 'Pending'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['Installments'] = $result['count'];
        
        // Get winner count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Winners WHERE Status = 'Pending'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['Winners'] = $result['count'];
        
    } catch (PDOException $e) {
        // Return default values if database error occurs
        $stats = [
            'Customers' => 0,
            'Schemes' => 0,
            'Payments' => 0,
            'Installments' => 0,
            'Winners' => 0
        ];
    }
    
    return $stats;
}

function getNotificationCount() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Notifications WHERE ReceiverType = 'Admin' AND IsRead = 0");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}
?> 