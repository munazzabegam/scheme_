<?php
// Database Configuration Updater
// This script helps you update the main database configuration

$new_config = [
    'host' => 'localhost',      // Update this to your database host
    'database' => 'scheme',     // Update this to your database name
    'username' => 'root',       // Update this to your database username
    'password' => '',           // Update this to your database password
    'port' => 3306              // Update this to your database port
];

// Update the main database configuration file
$config_content = '<?php
// Database configuration
define(\'DB_HOST\', \'' . $new_config['host'] . '\');
define(\'DB_NAME\', \'' . $new_config['database'] . '\');
define(\'DB_USER\', \'' . $new_config['username'] . '\');
define(\'DB_PASS\', \'' . $new_config['password'] . '\');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection failed. Please check your configuration.");
    }
}

// Test database connection
function testDBConnection() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Get database statistics
function getDBStats() {
    try {
        $pdo = getDBConnection();
        $stats = [];
        
        // Get table counts
        $tables = [\'Admins\', \'Customers\', \'Schemes\', \'Payments\', \'Installments\', \'Winners\'];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            $stats[$table] = $result[\'count\'];
        }
        
        return $stats;
    } catch (Exception $e) {
        error_log("Error getting DB stats: " . $e->getMessage());
        return [];
    }
}

// Sanitize input
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map(\'sanitizeInput\', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, \'UTF-8\');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generate random string
function generateRandomString($length = 10) {
    $characters = \'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ\';
    $randomString = \'\';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Format currency
function formatCurrency($amount) {
    return \'₹\' . number_format($amount, 2);
}

// Format date
function formatDate($date, $format = \'d M Y\') {
    return date($format, strtotime($date));
}

// Get current timestamp
function getCurrentTimestamp() {
    return date(\'Y-m-d H:i:s\');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION[\'admin_id\']);
}

// Check user role
function hasRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userRole = $_SESSION[\'admin_role\'] ?? \'\';
    $roleHierarchy = [
        \'SuperAdmin\' => 3,
        \'Editor\' => 2,
        \'Verifier\' => 1
    ];
    
    $userLevel = $roleHierarchy[$userRole] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
    
    return $userLevel >= $requiredLevel;
}

// Log activity
function logActivity($adminId, $action, $details = \'\') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO AdminLoginActivity (AdminID, LoginTime, IPAddress, UserAgent, LoginStatus) 
            VALUES (?, NOW(), ?, ?, ?)
        ");
        $stmt->execute([
            $adminId,
            $_SERVER[\'REMOTE_ADDR\'] ?? \'\',
            $_SERVER[\'HTTP_USER_AGENT\'] ?? \'\',
            $action
        ]);
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}

// Get system info
function getSystemInfo() {
    return [
        \'php_version\' => PHP_VERSION,
        \'server_software\' => $_SERVER[\'SERVER_SOFTWARE\'] ?? \'Unknown\',
        \'database_version\' => \'MySQL 8.0+\',
        \'max_upload_size\' => ini_get(\'upload_max_filesize\'),
        \'memory_limit\' => ini_get(\'memory_limit\'),
        \'timezone\' => date_default_timezone_get()
    ];
}
?>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Configuration Updater</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 2rem;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        h1, h2, h3 {
            color: #1e293b;
            margin-bottom: 1rem;
        }
        
        .config-form {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #374151;
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        
        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .current-config {
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Configuration Updater</h1>
        
        <div class="current-config">
            <h3>Current Configuration:</h3>
            <strong>Host:</strong> <?php echo $new_config['host']; ?><br>
            <strong>Database:</strong> <?php echo $new_config['database']; ?><br>
            <strong>Username:</strong> <?php echo $new_config['username']; ?><br>
            <strong>Password:</strong> <?php echo $new_config['password'] ? '***' : '(empty)'; ?><br>
            <strong>Port:</strong> <?php echo $new_config['port']; ?>
        </div>
        
        <form method="POST" class="config-form">
            <h3>Update Database Configuration:</h3>
            
            <div class="form-group">
                <label for="host">Database Host:</label>
                <input type="text" id="host" name="host" value="<?php echo $new_config['host']; ?>" placeholder="localhost">
            </div>
            
            <div class="form-group">
                <label for="database">Database Name:</label>
                <input type="text" id="database" name="database" value="<?php echo $new_config['database']; ?>" placeholder="scheme">
            </div>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $new_config['username']; ?>" placeholder="root">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo $new_config['password']; ?>" placeholder="Leave empty for no password">
            </div>
            
            <div class="form-group">
                <label for="port">Port:</label>
                <input type="text" id="port" name="port" value="<?php echo $new_config['port']; ?>" placeholder="3306">
            </div>
            
            <button type="submit" class="btn btn-success">Update Configuration</button>
        </form>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_config = [
                'host' => $_POST['host'] ?? 'localhost',
                'database' => $_POST['database'] ?? 'scheme',
                'username' => $_POST['username'] ?? 'root',
                'password' => $_POST['password'] ?? '',
                'port' => $_POST['port'] ?? 3306
            ];
            
            try {
                // Test the new connection first
                $dsn = "mysql:host={$new_config['host']};port={$new_config['port']};dbname={$new_config['database']};charset=utf8mb4";
                $pdo = new PDO($dsn, $new_config['username'], $new_config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                
                // Update the configuration file
                $config_content = '<?php
// Database configuration
define(\'DB_HOST\', \'' . $new_config['host'] . '\');
define(\'DB_NAME\', \'' . $new_config['database'] . '\');
define(\'DB_USER\', \'' . $new_config['username'] . '\');
define(\'DB_PASS\', \'' . $new_config['password'] . '\');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection failed. Please check your configuration.");
    }
}

// Test database connection
function testDBConnection() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Get database statistics
function getDBStats() {
    try {
        $pdo = getDBConnection();
        $stats = [];
        
        // Get table counts
        $tables = [\'Admins\', \'Customers\', \'Schemes\', \'Payments\', \'Installments\', \'Winners\'];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            $stats[$table] = $result[\'count\'];
        }
        
        return $stats;
    } catch (Exception $e) {
        error_log("Error getting DB stats: " . $e->getMessage());
        return [];
    }
}

// Sanitize input
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map(\'sanitizeInput\', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, \'UTF-8\');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generate random string
function generateRandomString($length = 10) {
    $characters = \'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ\';
    $randomString = \'\';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Format currency
function formatCurrency($amount) {
    return \'₹\' . number_format($amount, 2);
}

// Format date
function formatDate($date, $format = \'d M Y\') {
    return date($format, strtotime($date));
}

// Get current timestamp
function getCurrentTimestamp() {
    return date(\'Y-m-d H:i:s\');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION[\'admin_id\']);
}

// Check user role
function hasRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userRole = $_SESSION[\'admin_role\'] ?? \'\';
    $roleHierarchy = [
        \'SuperAdmin\' => 3,
        \'Editor\' => 2,
        \'Verifier\' => 1
    ];
    
    $userLevel = $roleHierarchy[$userRole] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
    
    return $userLevel >= $requiredLevel;
}

// Log activity
function logActivity($adminId, $action, $details = \'\') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO AdminLoginActivity (AdminID, LoginTime, IPAddress, UserAgent, LoginStatus) 
            VALUES (?, NOW(), ?, ?, ?)
        ");
        $stmt->execute([
            $adminId,
            $_SERVER[\'REMOTE_ADDR\'] ?? \'\',
            $_SERVER[\'HTTP_USER_AGENT\'] ?? \'\',
            $action
        ]);
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}

// Get system info
function getSystemInfo() {
    return [
        \'php_version\' => PHP_VERSION,
        \'server_software\' => $_SERVER[\'SERVER_SOFTWARE\'] ?? \'Unknown\',
        \'database_version\' => \'MySQL 8.0+\',
        \'max_upload_size\' => ini_get(\'upload_max_filesize\'),
        \'memory_limit\' => ini_get(\'memory_limit\'),
        \'timezone\' => date_default_timezone_get()
    ];
}
?>';
                
                file_put_contents('config/database.php', $config_content);
                
                echo '<div class="alert alert-success">';
                echo '✅ <strong>Configuration updated successfully!</strong><br>';
                echo 'Database connection test passed.<br>';
                echo 'The main configuration file has been updated.';
                echo '</div>';
                
                echo '<a href="admin/setup.php" class="btn btn-success">🚀 Run Setup</a> ';
                echo '<a href="admin/login.php" class="btn">🔐 Go to Login</a>';
                
            } catch (PDOException $e) {
                echo '<div class="alert alert-error">';
                echo '❌ <strong>Connection failed!</strong><br>';
                echo 'Error: ' . $e->getMessage() . '<br>';
                echo 'Please check your database credentials and try again.';
                echo '</div>';
            }
        }
        ?>
        
        <div style="margin-top: 2rem;">
            <h3>📝 Common Database Configurations:</h3>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px;">
                <strong>XAMPP (default):</strong><br>
                Host: localhost | Username: root | Password: (empty) | Port: 3306<br><br>
                
                <strong>WAMP (default):</strong><br>
                Host: localhost | Username: root | Password: (empty) | Port: 3306<br><br>
                
                <strong>MAMP (default):</strong><br>
                Host: localhost | Username: root | Password: root | Port: 8889<br><br>
                
                <strong>Remote MySQL:</strong><br>
                Host: your-server-ip | Username: your-username | Password: your-password | Port: 3306
            </div>
        </div>
    </div>
</body>
</html> 