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

$system_date = date("Y-m-d H:i:s");
$system_user = $_SESSION['timekeeping_appnameuser'];

$schedule_column = "*";
$query="SELECT $schedule_column FROM tbl_dtr WHERE payroll_date='$payroll_date' GROUP BY idcode";
$result = mysqli_query($db, $query);  
if ( $result->num_rows > 0 ) 
{
	$i=0;$lates=0;$undertime=0;$overtime=0;
	while ($ROW = mysqli_fetch_array($result))
	{
		$i++;
		$rowid = $ROW['id'];
		$idcode = $ROW['idcode'];
		$acctname = $ROW['acctname'];
	    $branch = $ROW['branch'];
	    $cluster = $ROW['location'];
		$company = $ROW['company'];
		$day_type_code = $ROW['day_type_code'];
		$position = $functions->getEmployeeInfo($idcode,'position',$db);
		$trans_date = $ROW['trans_date'];
						
		$time_in = $functions->combineDateTime($ROW['date_in'], $ROW['time_in']);
		$time_out = $functions->combineDateTime($ROW['date_out'], $ROW['time_out']);
				
		/* -------------------------------- LEAVES -------------------- */
		$leaveQuery="
			SELECT 
			count(case when day_type_code = 6 AND leave_approved = 1 then day_type_code end) as leave_count,
			count(case when day_type_code = 7 AND leave_approved = 1 then day_type_code end) as sick_count,
			count(case when day_type_code = 9 AND leave_approved = 1 then day_type_code end) as wpay_count,
			count(case when day_type_code = 14 AND leave_approved = 1 then day_type_code end) as wpayPM_count,
			count(case when day_type_code = 5 AND leave_approved = 1 then day_type_code end) as paternity,
			count(case when day_type_code = 2 then day_type_code end) as legalholiday_count,
			count(case when day_type_code = 3 then day_type_code end) as specialholiday_count,
			count(case when day_type_code = 0 AND present = 1 then day_type_code end) as restday_duty,
			count(case when day_type_code = 0 then day_type_code end) as restday_count
			
			FROM tbl_dtr WHERE idcode='$idcode' AND absent != 1 AND payroll_date='$payroll_date'";
		$lresult = mysqli_query($db,$leaveQuery) or die('ERROR::: '.$db->error);
		while ($lrow = mysqli_fetch_array($lresult))
		{
			$restday_duty = $lrow['restday_duty'];
			
			$a = $lrow['leave_count'];
			$b = $lrow['sick_count'];
			$c = $lrow['wpay_count'] + $lrow['wpayPM_count'];
			$p = $lrow['paternity'];
			
			$aa = $a; $bb = $b; $cc = $c * (0.5);
			$total_leaves = $aa + $bb + $cc + $p;

			$d = $lrow['legalholiday_count'];
			$e = $lrow['specialholiday_count'];
			$dd = $d; $ee = $e;
			$total_holidays = $dd + $ee;
			$restday_count = $lrow['restday_count'];			
		}
		$total_hour=0;$total_late=0;
		$total_undertime=0;$total_overtime=0;
		$total_present=0;$total_absent=0;

		$kwiri = "idcode='$idcode' AND payroll_date='$payroll_date'";
		/* -------------------------------- LEAVES -------------------- */
		$working_days = $functions->getWorkingDays($payroll_from,$payroll_to);
		$days_worked = $functions->getColumnCount('COUNT','present',$kwiri,'0',$db);
		$worked_hours = $functions->getColumnCount('SUM','regular_hour',$kwiri,'0',$db);
		$late_minutes = $functions->getColumnCount('SUM','late_time',$kwiri,'0',$db);
		$under_time = $functions->getColumnCount('SUM','under_time',$kwiri,'0',$db);
		$over_time = $functions->getColumnCount('SUM','overtime_hour',$kwiri,'1',$db);

		
		$nightdiff_reg_hours = $functions->getColumnCount('SUM','nightdiff_reg_hours',$kwiri,'0',$db);
		$nightdiff_dr_hours = $functions->getColumnCount('SUM','nightdiff_dr_hours',$kwiri,'0',$db);
		$nightdiff_hol_hours = $functions->getColumnCount('SUM','nightdiff_hol_hours',$kwiri,'0',$db);
		
//		echo $acctname." -- RD ND -- ".$nightdiff_reg_hours."<br>";
		
		$update = "
            payroll_date = '$payroll_date',
            payroll_from = '$payroll_from',
            payroll_to = '$payroll_to',
            idcode = '$idcode',
            acctname ='$acctname',
            company ='$company',
            cluster='$cluster',
            branch='$branch',
            position='$position',
            working_days='$working_days',
            days_worked='$days_worked',
            leave_with_pay='$total_leaves',
            duty_rest='$restday_duty',
            over_time='$over_time',
            late_minutes='$late_minutes',
            under_time='$under_time',
            nightdiff_reg_hours='$nightdiff_reg_hours',
            nightdiff_dr_hours='$nightdiff_dr_hours',
            nightdiff_hol_hours='$nightdiff_hol_hours',
            date_generated='$system_date',
            generated_by='$system_user'
        ";
	                
        $insertColumn = "
            `payroll_date`,`payroll_from`,`payroll_to`,`idcode`,`acctname`,`company`,`cluster`,`branch`,`position`,`working_days`,`days_worked`,`leave_with_pay`,`duty_rest`,
            `over_time`,`late_minutes`,`under_time`,`nightdiff_reg_hours`,`nightdiff_dr_hours`,`nightdiff_hol_hours`,`date_generated`,`generated_by`
        ";	        
        $insertData = "
            '$payroll_date','$payroll_from','$payroll_to','$idcode','$acctname','$company','$cluster','$branch','$position','$working_days','$days_worked','$total_leaves','$restday_duty',
            '$over_time','$late_minutes','$under_time','$nightdiff_reg_hours','$nightdiff_dr_hours','$nightdiff_hol_hours','$system_date','$system_user'
        ";        				

		$allinsertData[] = "($insertData)";
	}
	pushJournalRecords($insertColumn,$allinsertData,$payroll_date,$db);
} else {
	echo "No Records found";
}

function pushJournalRecords($insertColumn, $allinsertData, $payroll_date, $db)
{
    $qDelete = "DELETE FROM tbl_journal WHERE payroll_date='$payroll_date'";
    if (!$db->query($qDelete)) {
        echo $db->error;
        return;
    }
    $query = "INSERT INTO tbl_journal ($insertColumn) VALUES " . implode(', ', $allinsertData);
    if ($db->query($query) === TRUE) {
    } else { echo "Error: " . $db->error; }
}


$show_table = 0;
if($show_table == 1)
{
$html ='';
$html .= '
	<table style="width: 100%" class="table table-bordered">
		<tr>
			<th>#</th>
			<th>EMPLOYEE</th>
			<th>COMPANY</th>
			<th>POSITION</th>
			<th>WRKNG DAYS</th>
			<th>DAYS WRKD</th>
			<th>LATE</th>
			<th>UNDTME</th>
			<th>OVRTME</th>
			<th>DTYRST</th>
			<th>NIGHTDIFF</th>
			<th>NIGHTDIFF DR</th>
			<th>NIGHTDIFF HOL</th>
			<th>LEAVE</th>
		</tr>
	';
		$pushUpdate = json_encode($journalData);
		$array = json_decode($pushUpdate, true);
		$j=0;	
		for ($i = 0; $i < count($array); $i++)
		{
			$j++;
			$employee = $array[$i]["employee"];
			$company = $array[$i]["company"];
			$position = $array[$i]["position"];
			$working_days = $array[$i]["working_days"];
			$days_worked = $array[$i]["days_worked"];
			$late_minutes = $array[$i]["late_minutes"];
			$under_time = $array[$i]["under_time"];
			$over_time = $array[$i]["over_time"];
			$dutyrest = $array[$i]["dutyrest"];
			$nightdiff_reg_hours = $array[$i]["nightdiff_reg_hours"];
			$nightdiff_dr_hours = $array[$i]["nightdiff_dr_hours"];
			$nightdiff_hol_hours = $array[$i]["nightdiff_hol_hours"];
			$leave = $array[$i]["leave"];
$html .= '
		<tr>
			<td>'.$j.'</td>
			<td>'.$employee.'</td>
			<td>'.$company.'</td>
			<td>'.$position.'</td>
			<td>'.$working_days.'</td>
			<td>'.$days_worked.'</td>
			<td>'.$late_minutes.'</td>
			<td>'.$under_time.'</td>
			<td>'.$over_time.'</td>
			<td>'.$dutyrest.'</td>
			<td>'.$nightdiff_reg_hours.'</td>
			<td>'.$nightdiff_dr_hours.'</td>
			<td>'.$nightdiff_hol_hours.'</td>
			<td>'.$leave.'</td>
		</tr>
	';
	}
$html .= '</table>';
echo $html;
}

