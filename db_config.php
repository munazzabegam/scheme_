<?php
// Database Configuration Helper
// Update these values to connect to your real database

$db_config = [
    'host' => 'localhost',      // Your database host (usually localhost)
    'database' => 'scheme',     // Your database name
    'username' => 'root',       // Your database username
    'password' => '',           // Your database password
    'port' => 3306              // Your database port (usually 3306 for MySQL)
];

// Test the connection
echo "<h2>🔧 Database Configuration Test</h2>";

try {
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "✅ <strong>Database connection successful!</strong><br>";
    echo "Host: {$db_config['host']}<br>";
    echo "Database: {$db_config['database']}<br>";
    echo "Username: {$db_config['username']}<br>";
    echo "Port: {$db_config['port']}<br><br>";
    
    // Test if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "⚠️ <strong>No tables found in database.</strong><br>";
        echo "You need to import the schema first.<br>";
        echo "<a href='config/requirememts/schema.sql' target='_blank'>View Schema File</a><br><br>";
    } else {
        echo "✅ <strong>Found " . count($tables) . " tables:</strong><br>";
        foreach ($tables as $table) {
            echo "- $table<br>";
        }
        echo "<br>";
    }
    
    echo "<a href='admin/setup.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Run Setup</a> ";
    echo "<a href='admin/login.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Go to Login</a>";
    
} catch (PDOException $e) {
    echo "❌ <strong>Database connection failed!</strong><br>";
    echo "Error: " . $e->getMessage() . "<br><br>";
    
    echo "<h3>🔧 How to fix:</h3>";
    echo "1. <strong>Update the configuration above</strong> with your real database details<br>";
    echo "2. <strong>Make sure your database server is running</strong><br>";
    echo "3. <strong>Verify the database name exists</strong><br>";
    echo "4. <strong>Check username and password</strong><br><br>";
    
    echo "<h3>📝 Common configurations:</h3>";
    echo "<strong>XAMPP (default):</strong><br>";
    echo "- Host: localhost<br>";
    echo "- Username: root<br>";
    echo "- Password: (empty)<br>";
    echo "- Port: 3306<br><br>";
    
    echo "<strong>WAMP (default):</strong><br>";
    echo "- Host: localhost<br>";
    echo "- Username: root<br>";
    echo "- Password: (empty)<br>";
    echo "- Port: 3306<br><br>";
    
    echo "<strong>MAMP (default):</strong><br>";
    echo "- Host: localhost<br>";
    echo "- Username: root<br>";
    echo "- Password: root<br>";
    echo "- Port: 8889<br><br>";
    
    echo "<strong>Remote MySQL:</strong><br>";
    echo "- Host: your-server-ip<br>";
    echo "- Username: your-username<br>";
    echo "- Password: your-password<br>";
    echo "- Port: 3306<br><br>";
}
?>

<style>
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 2rem;
    line-height: 1.6;
}

h2, h3 {
    color: #1e293b;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

a {
    display: inline-block;
    margin: 5px;
    transition: all 0.3s ease;
}

a:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.config-box {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    margin: 2rem 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.config-item {
    margin: 1rem 0;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}
</style>

<div class="config-box">
    <h3>🔧 Current Configuration</h3>
    <div class="config-item">
        <strong>Host:</strong> <?php echo $db_config['host']; ?><br>
        <strong>Database:</strong> <?php echo $db_config['database']; ?><br>
        <strong>Username:</strong> <?php echo $db_config['username']; ?><br>
        <strong>Password:</strong> <?php echo $db_config['password'] ? '***' : '(empty)'; ?><br>
        <strong>Port:</strong> <?php echo $db_config['port']; ?>
    </div>
    
    <p><strong>To update:</strong> Edit the <code>$db_config</code> array at the top of this file.</p>
</div> 