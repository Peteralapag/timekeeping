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
$schedule_column = "payroll_date,idcode";

$query="SELECT $schedule_column FROM tbl_dtr WHERE payroll_date='$payroll_date' GROUP by idcode";
$result = mysqli_query($db, $query);  
if ( $result->num_rows > 0 ) 
{
	
}

$query="SELECT $schedule_column FROM tbl_dtr WHERE payroll_date='$payroll_date' GROUP by idcode";
$result = mysqli_query($db, $query);  
if ( $result->num_rows > 0 ) 
{
	while ($ROW = mysqli_fetch_array($result))
	{
		$idcode = $ROW['idcode'];
		$queryLogs="SELECT idcode,acctname,time_in,time_out,trans_date,date_in,date_out FROM tbl_dtr_logs WHERE idcode='$idcode' AND trans_date BETWEEN '".$payroll_from."' AND '".$payroll_to."' ORDER BY idcode";
		$logsResult = mysqli_query($db, $queryLogs);
		$ccnt = mysqli_num_rows($logsResult);
		while ($ROWS = mysqli_fetch_array($logsResult))
		{
			$acctname = $ROWS['acctname'];
			$time_in = $ROWS['time_in'];
			$date_in = $ROWS['date_in'];
			$time_out = $ROWS['time_out'];
			$date_out = $ROWS['date_out'];
			$trans_date = $ROWS['trans_date'];
			$dtr_logs = $time_in." -- ".$time_out;
			
			echo $functions->updateDTRLogs($payroll_date,$idcode,$time_in,$date_in,$time_out,$date_out,$dtr_logs,$trans_date,$db);
		}				
	}
} else {
	echo "No Records found";
}