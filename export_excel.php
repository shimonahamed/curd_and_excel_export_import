<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Database connection
$conn = new mysqli('localhost', 'root', 'root', 'js_crud');
if ($conn->connect_error) die("DB connection failed");

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Row
$sheet->setCellValue('A1', 'Name');
$sheet->setCellValue('B1', 'Contact Number');
$sheet->setCellValue('C1', 'Email');

// Fetch data from DB
$sql = "SELECT name, contact_number, email FROM users";
$result = $conn->query($sql);

$row = 2;
while ($data = $result->fetch_assoc()) {
    $sheet->setCellValue("A{$row}", $data['name']);
    $sheet->setCellValue("B{$row}", $data['contact_number']);
    $sheet->setCellValue("C{$row}", $data['email']);
    $row++;
}

// File download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="users.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
