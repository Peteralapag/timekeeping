<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$payroll_date = $_SESSION['PAYROLL_DATE'] ?? '';
$payroll_from = $_SESSION['PAYROLL_FROM'] ?? '';
$payroll_to = $_SESSION['PAYROLL_TO'] ?? '';
$cluster = $_SESSION['PAYROLL_CLUSTER'] ?? '';
$branch = $_SESSION['PAYROLL_BRANCH'] ?? '';

	$dtr_column = "payroll_date,idcode,employment_status,company,department,trans_date,time_in,day_type,day_type_code,present,absent,acctname,rate";
	$query = "SELECT *
		FROM tbl_dtr dtr
		INNER JOIN tbl_employees employees
		ON dtr.idcode=employees.idcode
		WHERE dtr.payroll_date='$payroll_date' GROUP BY dtr.idcode
	";	
	$result = mysqli_query($db, $query);    
    $rowcnt = $result->num_rows;
    if ( $result->num_rows > 0 ) 
    {
    
	    $sqlCnt="SELECT idcode FROM tbl_dtr WHERE payroll_date='$payroll_date'";
		if ($cntResult=mysqli_query($db,$sqlCnt))
		{
			  $rowcount=mysqli_num_rows($cntResult);
			  mysqli_free_result($cntResult);
		} else {
			$rowcount = 0;
		}	
		$days_cnt = round($rowcount / $rowcnt);
		$x=0;
		while($ROWS = mysqli_fetch_array($result))  
		{
			$x++;
			$idcode = $ROWS['idcode'];
			$acctname = utf8_encode($ROWS['acctname']);
			$employment_status = $ROWS['employment_status'];
			$company = $ROWS['company'];
			$department = $ROWS['department'];
			$salary_type = $ROWS['salary_type'];
			if($salary_type == 'MONTHLY')
			{
				$rate = $ROWS['salary_monthly'];
			}
			if($salary_type == 'DAILY')
			{
				$rate = $ROWS['salary_daily'];
			}			
			update_dtr($idcode,$acctname,$payroll_date,$employment_status,$company,$department,$rate,$x,$rowcnt,$db);
		}
	} else {
		echo "Payroll has not been set";
	}
function update_dtr($idcode,$acctname,$payroll_date,$employment_status,$company,$department,$rate,$x,$rowcnt,$db)
{
	$update = "employment_status='$employment_status',company='$company',department='$department',rate='$rate'";
	$query = "UPDATE tbl_dtr SET $update WHERE payroll_date='$payroll_date' AND idcode='$idcode'";		
	if ($db->query($query) === TRUE)
	{
		if($x ==  $rowcnt)
		{
			echo "Employee`s DTR has been successfuly Updated";
		}
	} else { 
		if($x ==  $rowcnt)
		{
			echo $db->error;			
		}
	}


}