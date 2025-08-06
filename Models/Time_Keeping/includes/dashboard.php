<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$payroll_date = $_SESSION['PAYROLL_DATE'] ?? '';
?>

