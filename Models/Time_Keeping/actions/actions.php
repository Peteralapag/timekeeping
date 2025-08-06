<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if(isset($_POST['mode']))
{
	$mode = $_POST['mode'];
} else {
	print_r('
		<script>
			app_alert("Warning"," The Mode you are trying to pass does not exist","warning","Ok","","no");
		</script>
	');
	exit();
}
if(isset($_SESSION['timekeeping_username']))
{
	$app_user = strtolower($_SESSION['timekeeping_appnameuser']);
	$app_user = ucwords($app_user);
	$the_date = date("Y-m-d H:i:s");
}
if($mode == 'deletecutoff')
{
	$rowid = $_POST['rowid'];
	$queryUpdate = "DELETE FROM dtrv_datelock_checker WHERE id='$rowid'";	
	if ($db->query($queryUpdate) === TRUE)
	{
		echo '
			<script>
				swal("Delete!", "The cutoff has been deleted.", "success");
				loadPage(1);;
			</script>
		';
	} else {
		echo '
			<script>
				swal("Delete Failed", "'.$db->error.'","error");
			</script>
		';
	}
}
if($mode == 'setpayrolldate')
{
	$rowid = $_POST['rowid'];
	$query = "SELECT * FROM dtrv_datelock_checker WHERE id='$rowid'";
    $results = $db->query($query);			
    if($results->num_rows > 0)
    {
        while($ROW = mysqli_fetch_array($results))  
        {
        	$payrollDate = $ROW['payroll_date'];
        	$payrollper = date('d', strtotime($payrollDate));
        	
        	$payrollperiod = $payrollper == 15? 'A': 'B';
        	
        	echo $functions->setPayrollDate($payrollDate,$ROW['payroll_from'],$ROW['payroll_to'],$payrollperiod,$db);
        }
    }
}
if($mode == 'savetimekeepingjournal')
{
	$_SESSION['PAYROLL_DATE'] = $_POST['cutoff_date'];
	$_SESSION['PAYROLL_FROM'] = $_POST['cutoff_from'];
	$_SESSION['PAYROLL_TO'] = $_POST['cutoff_to'];
	unset($_SESSION['PAYROLL_CLUSTER']);
	unset($_SESSION['PAYROLL_BRANCH']);
	echo '
		<script>			
			$("#formmodal").fadeOut();
			window.location.reload();  
		</script>
	';
}
if($mode == 'addcutoff')
{
	$payroll_date = $_POST['cutoff_date'];
	$payroll_from = $_POST['cutoff_from'];
	$payroll_to = $_POST['cutoff_to'];	
	$queryInsert = "
		INSERT INTO dtrv_datelock_checker (`payroll_date`,`payroll_from`,`payroll_to`,`created_date`,`created_by`)
		VALUES ('$payroll_date','$payroll_from','$payroll_to','$the_date','$app_user')
	";
	if ($db->query($queryInsert) === TRUE)
	{
		echo '
			<script>
				swal("Success","Successfuly added the cutoff date","success");
				$("#formmodal").hide();
				loadPage(1);
			</script>
		';
	} else {
		echo '
			<script>
				swal("Entry Failed", "'.$db->error.'","error");
			</script>
		';
	}
}
if($mode == 'lockcutoff')
{
	$rowid = $_POST['rowid'];
	$totalPages = $_POST['totalPages'];
	$queryDataUpdate = "UPDATE dtrv_datelock_checker SET lock_status='1',updated_by='$app_user',updated_date='$the_date'  WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		echo '
			<script>
				swal("Success","Successfuly locked the cutoff date","success");
				reloadPage('.$totalPages.');
			</script>
		';
	} else {
		echo $db->error;
	}
}