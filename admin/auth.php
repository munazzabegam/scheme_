<?php
session_start();

// Authentication helper functions
function requireLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit();
    }
    
    // Verify admin exists in database
    try {
        require_once __DIR__ . '/../config/database.php';
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM Admins WHERE AdminID = ? AND Status = 'Active'");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if (!$admin) {
            // Admin not found or inactive, clear session and redirect
            session_destroy();
            header('Location: login.php?error=invalid_session');
            exit();
        }
        
        // Update session with current admin data
        $_SESSION['admin_name'] = $admin['Name'];
        $_SESSION['admin_email'] = $admin['Email'];
        $_SESSION['admin_role'] = $admin['Role'];
        
        return $admin;
        
    } catch (Exception $e) {
        // Database error, redirect to login
        session_destroy();
        header('Location: login.php?error=db_error');
        exit();
    }
}

function requireRole($requiredRole) {
    $admin = requireLogin();
    
    $roleHierarchy = [
        'SuperAdmin' => 3,
        'Editor' => 2,
        'Verifier' => 1
    ];
    
    $userLevel = $roleHierarchy[$admin['Role']] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
    
    if ($userLevel < $requiredLevel) {
        header('Location: login.php?error=unauthorized');
        exit();
    }
    
    return $admin;
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function getCurrentAdmin() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        require_once __DIR__ . '/../config/database.php';
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM Admins WHERE AdminID = ? AND Status = 'Active'");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

function logout() {
    // Log the logout activity
    if (isset($_SESSION['admin_id'])) {
        try {
            require_once __DIR__ . '/../config/database.php';
            logActivity($_SESSION['admin_id'], 'Logout');
        } catch (Exception $e) {
            // Ignore logging errors during logout
        }
    }
    
    // Clear all session data
    session_destroy();
    
    // Clear any remember me cookies
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // Redirect to login
    header('Location: login.php');
    exit();
}
?> 