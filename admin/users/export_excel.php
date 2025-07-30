<?php
require 'vendor/autoload.php';
require 'includes/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$stmt = $pdo->query("SELECT fullname, email, phone, role FROM users");
$users = $stmt->fetchAll();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray(['Họ tên', 'Email', 'Phone', 'Quyền'], null, 'A1');

$row = 2;
foreach ($users as $u) {
    $sheet->setCellValue("A$row", $u['fullname']);
    $sheet->setCellValue("B$row", $u['email']);
    $sheet->setCellValue("C$row", $u['phone']);
    $sheet->setCellValue("D$row", $u['role']);
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="users.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
