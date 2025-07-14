<?php
$conn = new mysqli('localhost', 'root', '', 'scheme');
if ($conn->connect_error) {
    die('Database connection failed!');
}

// Output UTF-8 BOM for Excel compatibility
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=customers_export_' . date('Ymd_His') . '.csv');
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');
// Output column headings
fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'DOB', 'Gender', 'Status', 'Created At']);

$sql = "SELECT CustomerID, FullName, Email, PhoneNumber, DOB, Gender, Status, CreatedAt FROM Customers ORDER BY CustomerID ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format date fields for Excel
        $dob = $row['DOB'] ? date('Y-m-d', strtotime($row['DOB'])) : '';
        $created = $row['CreatedAt'] ? date('Y-m-d H:i:s', strtotime($row['CreatedAt'])) : '';
        fputcsv($output, [
            $row['CustomerID'],
            $row['FullName'],
            $row['Email'],
            "'" . $row['PhoneNumber'], // Force Excel to treat as text
            $dob,
            $row['Gender'],
            $row['Status'],
            $created,
        ]);
    }
}
fclose($output);
$conn->close();
exit; 

