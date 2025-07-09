<?php

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;


// Database connection
$conn = new mysqli('localhost', 'root', 'root', 'js_crud');
if ($conn->connect_error) die("Connection failed");

// File upload check
if (isset($_FILES['excel_file']['tmp_name'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    // Load spreadsheet
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $inserted = 0;
    $skipped_empty = 0;
    $skipped_duplicate = 0;

    // Loop through all rows, skipping the header
    for ($i = 1; $i < count($rows); $i++) {
        $name  = trim($rows[$i][0]);
        $number = trim($rows[$i][1]);
        $email = trim($rows[$i][2]);

    
        if (empty($email)) {
            $skipped_empty++;
            continue;
        }
    
        // ✅ Check if email format is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $skipped_invalid++;
            continue;
        }
    
        // ✅ Check duplicate
        $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $skipped_duplicate++;
            continue;
        }
    
        $safe_name = $conn->real_escape_string($name);
        $safe_number = $conn->real_escape_string($number);
        $safe_email = $conn->real_escape_string($email);
        $sql = "INSERT INTO users (name, contact_number, email) VALUES ('$safe_name', '$safe_number', '$safe_email')";

        if ($conn->query($sql)) {
            $inserted++;
        }
        
    }
    
    header("Location: table.php");
    exit;
    // ✅ রিপোর্ট দেখাও
    // echo "✅ Inserted: $inserted rows<br>";
    // echo "⛔ Skipped (empty email): $skipped_empty<br>";
    // echo "🚫 Skipped (duplicate email): $skipped_duplicate";
}
?>
