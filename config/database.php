<?php
// Database configuration
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'scheme';

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    // You can customize this error handling as needed
    die('Database connection failed: ' . htmlspecialchars($conn->connect_error));
}
// Usage: include this file and use $conn for queries
