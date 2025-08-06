<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$payroll_date = $_SESSION['PAYROLL_DATE'] ?? '';
$payroll_from = $_SESSION['PAYROLL_FROM'] ?? '';
$payroll_to = $_SESSION['PAYROLL_TO'] ?? '';
$cluster = $_SESSION['PAYROLL_CLUSTER'] ?? '';
$branch = $_SESSION['PAYROLL_BRANCH'] ?? '';

$date = date("Y-m-d H:i:s");
$query="SELECT * FROM tbl_employees_leave WHERE payroll_date='$payroll_date' AND approved=1";
$result = mysqli_query($db, $query);  
if ( $result->num_rows > 0 ) 
{
	while ($ROWS = mysqli_fetch_array($result))
	{
		$idcode = $ROWS['idcode'];
		$trans_date = $ROWS['date_of_leave'];
		$day_type = $ROWS['leave_description'];
		$day_type_code = $ROWS['day_type_code'];		
		echo $functions->updateLeaves($payroll_date,$idcode,$trans_date,$day_type,$day_type_code,$db);
	}
} else {
	echo "No Records found";
}