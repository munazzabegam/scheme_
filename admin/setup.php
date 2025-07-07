<?php
require_once '../config/database.php';

try {
    $pdo = getDBConnection();
    
    // Check if admin already exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Admins");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        // Create default admin user
        $adminPassword = password_hash('admin@123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO Admins (Name, Email, PasswordHash, Role, Status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(['John Doe', 'admin@gmail.com', $adminPassword, 'SuperAdmin', 'Active']);
        
        echo "✅ Default admin user created successfully!<br>";
        echo "Email: admin@gmail.com<br>";
        echo "Password: admin@123<br><br>";
    } else {
        echo "ℹ️ Admin user already exists.<br><br>";
    }
    
    // Insert sample schemes
    $schemes = [
        ['Education Support', 'Financial assistance for students pursuing higher education', 'Must be enrolled in recognized institution', '2024-01-01', '2024-12-31'],
        ['Healthcare Aid', 'Medical expense support for low-income families', 'Income below poverty line', '2024-01-01', '2024-12-31'],
        ['Housing Scheme', 'Affordable housing for eligible citizens', 'First-time homebuyers only', '2024-01-01', '2024-12-31'],
        ['Business Startup', 'Financial support for new entrepreneurs', 'Must submit business plan', '2024-01-01', '2024-12-31']
    ];
    
    foreach ($schemes as $scheme) {
        $stmt = $pdo->prepare("
            INSERT INTO Schemes (SchemeName, Description, EligibilityCriteria, StartDate, EndDate, Status) 
            VALUES (?, ?, ?, ?, ?, 'Active')
        ");
        $stmt->execute($scheme);
    }
    
    echo "✅ Sample schemes created successfully!<br><br>";
    
    // Insert sample customers
    $customers = [
        ['Rahul Kumar', 'rahul@email.com', '9876543210', 'Mumbai, Maharashtra', '123456789012', '1990-05-15', 'Male'],
        ['Priya Sharma', 'priya@email.com', '9876543211', 'Delhi, Delhi', '123456789013', '1988-08-22', 'Female'],
        ['Amit Patel', 'amit@email.com', '9876543212', 'Bangalore, Karnataka', '123456789014', '1992-03-10', 'Male'],
        ['Neha Singh', 'neha@email.com', '9876543213', 'Chennai, Tamil Nadu', '123456789015', '1995-11-05', 'Female'],
        ['Vikram Malhotra', 'vikram@email.com', '9876543214', 'Pune, Maharashtra', '123456789016', '1985-12-18', 'Male']
    ];
    
    foreach ($customers as $customer) {
        $stmt = $pdo->prepare("
            INSERT INTO Customers (FullName, Email, PhoneNumber, Address, AadharNumber, DOB, Gender, Status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')
        ");
        $stmt->execute($customer);
    }
    
    echo "✅ Sample customers created successfully!<br><br>";
    
    // Insert sample payments
    $payments = [
        [1, 5000.00, 'UPI', 'Success', 'TXN123456'],
        [2, 7500.00, 'Card', 'Success', 'TXN123457'],
        [3, 3000.00, 'NetBanking', 'Success', 'TXN123458'],
        [4, 4500.00, 'UPI', 'Pending', 'TXN123459'],
        [5, 6000.00, 'Cash', 'Success', 'TXN123460']
    ];
    
    foreach ($payments as $payment) {
        $stmt = $pdo->prepare("
            INSERT INTO Payments (CustomerID, Amount, PaymentMethod, PaymentStatus, TransactionID) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute($payment);
    }
    
    echo "✅ Sample payments created successfully!<br><br>";
    
    // Insert sample winners
    $winners = [
        [1, 'Customer', 'Education Scholarship', '2024-01-15', 'Verified', 1, 1],
        [2, 'Customer', 'Healthcare Support', '2024-02-20', 'Delivered', 1, 2],
        [3, 'Customer', 'Business Grant', '2024-03-10', 'Pending', 1, 3]
    ];
    
    foreach ($winners as $winner) {
        $stmt = $pdo->prepare("
            INSERT INTO Winners (UserID, UserType, PrizeType, WinningDate, Status, AdminID, SchemeID) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute($winner);
    }
    
    echo "✅ Sample winners created successfully!<br><br>";
    
    echo "🎉 Setup completed successfully!<br>";
    echo "You can now login with:<br>";
    echo "Email: admin@gmail.com<br>";
    echo "Password: admin@123<br><br>";
    echo "<a href='login.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login</a>";
    
} catch (Exception $e) {
    echo "❌ Setup failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Scheme Manager</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: white;
        }
        
        .setup-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        h1 {
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        
        .setup-content {
            text-align: left;
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        a {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>🛠️ Database Setup</h1>
        <div class="setup-content">
            <!-- PHP output will be displayed here -->
        </div>
    </div>
</body>
</html> 