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
/* ################################################################ */
/* # CALCULATE DTR 												  # */
/* ################################################################ */

/* ######################## LATE POLICY ####################### */

$late_grace_period = 15;
$late_30_minutes = 60;
$late_60_minutes = 240;

// $idcode='20191073';
$idcode = $_POST['idcode'] ?? null;
$q = isset($idcode) ? "AND idcode='$idcode'" : 'ORDER BY idcode ASC';
$attendance = isset($idcode) ? 1 : 0;
/* ############################# LOAD ALL DATA ################################### */
$select_columns = '
	id,trans_date,time_in,time_out,date_in,date_out,approved_overtime,overtime_hour,under_time,day_type,day_type_code,approved_present,
	approved_overtime,present,absent,regular_hour,late_time,shifting_code,shifting,shift_timein,shift_timeout
';
$QUERY_A="SELECT $select_columns FROM tbl_dtr WHERE payroll_date='$payroll_date' $q";
$RESULTS_A= mysqli_query($db,$QUERY_A) or die('SUB ERROR::: '.$db->error);
$dtrdata = [];
while ($ROWS = mysqli_fetch_assoc($RESULTS_A))
{
/* -----------------------------## START HERE ##------------------------------------------------------- */
    $rowid = $ROWS['id'];
    $date_trans = $ROWS['trans_date'];
    $time_in = $ROWS['time_in'];
    $time_out = $ROWS['time_out'];
	$date_in = $ROWS['date_in'];
    $date_out = $ROWS['date_out'];    
    $overtime = $ROWS['approved_overtime'];
    $othour = $ROWS['overtime_hour'];
    $undertime = $ROWS['under_time'];
    $daytype = $ROWS['day_type'];
    $day_type_code = $ROWS['day_type_code'];
    $approvedp_resent = $ROWS['approved_present'];
    $approved_overtime = $ROWS['approved_overtime'];
    $present = $ROWS['present'];
    $absent = $ROWS['absent'];
    $regular_hour = $ROWS['regular_hour'];
    $latetime = $ROWS['late_time'];
    $shifting_code = $ROWS['shifting_code'];
    $shifting = $ROWS['shifting'];
    $shift_timein = $ROWS['shift_timein'];
    $shift_timeout = $ROWS['shift_timeout'];
         
    $dtr_timein='';$dtr_timeout='';$sched_timein='';$sched_timeout=''; 
    if (!empty($time_in) && !empty($time_out))
    {
        $dtr_timein = $functions->combineDateTime($date_in, $time_in);
        $dtr_timeout = $functions->combineDateTime($date_out, $time_out);
        $sched_timein = $functions->combineDateTime($date_trans, $shift_timein);
        $sched_timeout = $functions->combineDateTime($date_trans, $shift_timeout);
    }
        
    /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ REST DAY @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 0)
    {	
        $late_minutes=0;$under_time = 0;$over_time = 0;$work_hours = 0;$present=0.00;$absent=0.00;

        if ($time_in != '' && $time_out !='')
        {
            $late_minutes = $functions->checkLateTimeIn($dtr_timein, $sched_timein);
            $under_time = $functions->getUndertime($sched_timeout,$dtr_timeout);
            $over_time = $functions->getOverTime($sched_timeout,$dtr_timeout);
            $work_hours = 8;
            $present=1.00;
            $absent=0.00;            
        }
        $late_minutes = $late_minutes;
        $under_time = $under_time;
        $over_time = $functions->getOverTimeHour($over_time);  
        $nightdiff = 0; 
        $nightdiff_hol = 0; 
        $nightdiff_dr = $functions->calculateNightDifferential($dtr_timein, $dtr_timeout); 
        
    }
    /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ REGULAR DAY @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 1)
    {
    	$late_minutes=0;$under_time = 0;$over_time = 0;$work_hours = 0;$present=0.00;$absent=0.00;
    			
        if (!empty($time_in) && !empty($time_out))
        {
            $late_minutes = $functions->checkLateTimeIn($dtr_timein, $sched_timein);
            $under_time = $functions->getUndertime($sched_timeout,$dtr_timeout);
            $over_time = $functions->getOverTime($sched_timeout,$dtr_timeout);
            $work_hours = 8;
            $present=1.00;
            $absent=0.00;
        }              
        $late_minutes = $late_minutes;
        $under_time = $under_time;
        $over_time = $functions->getOverTimeHour($over_time);     
       	$nightdiff = $functions->calculateNightDifferential($dtr_timein, $dtr_timeout);
       	$nightdiff_hol = 0; 
        $nightdiff_dr = 0; 
        
    }
    /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ REGULAR DAY 1/2 DAY MORNING - UMAGA ANG PASOK W/O PAY @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 8)
    {
		$schedTimeOut = strtotime($sched_timeout) - 18000; /* - 4 hours from scheduled time out */
		$sched_timeout = date('H:i', $schedTimeOut);
		$sched_timeout = $functions->combineDateTime($date_trans, $sched_timeout);
	
		$late_minutes=0;$under_time = 0;$over_time = 0;$work_hours = 0;$present=0.00;$absent=0.00;	

        if (!empty($time_in) && !empty($time_out))
        {
            $late_minutes = $functions->checkLateTimeIn($dtr_timein, $sched_timein);
            $under_time = $functions->getUndertime($sched_timeout,$dtr_timeout);
            $over_time = 0;
            $work_hours = 4;
            $present=1.00;
            $absent=0.00;
        }      
        
        $late_minutes = $late_minutes;
        $under_time = $under_time;
        $over_time = $functions->getOverTimeHour($over_time);
        $nightdiff = 0;
       	$nightdiff_hol = 0; 
        $nightdiff_dr = 0;              
    }
    /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ REGULAR DAY 1/2 DAY MORNING - UMAGA ANG PASOK W/PAY @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 9)
    {
		$schedTimeOut = strtotime($sched_timeout) - 18000; /* - 4 hours from scheduled time out */
		$sched_timeout = date('H:i', $schedTimeOut);
		$sched_timeout = $functions->combineDateTime($date_trans, $sched_timeout);
		
		$late_minutes=0;$under_time = 0;$over_time = 0;$work_hours = 0;$present=0.00;$absent=0.00;
		
        if (!empty($time_in) && !empty($time_out))
        {
            $late_minutes = $functions->checkLateTimeIn($dtr_timein, $sched_timein);
            $under_time = $functions->getUndertime($sched_timeout,$dtr_timeout);
            $over_time = $functions->getOverTime($sched_timeout,$dtr_timeout);
            $work_hours = 8;
            $present=1.00;
            $absent=0.00;
        }      
        
        $late_minutes = $late_minutes;
        $under_time = $under_time;
        $over_time = $functions->getOverTimeHour($over_time);
        $nightdiff = 0;
       	$nightdiff_hol = 0;        	       	
        $nightdiff_dr = 0;                
    }
    /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ REGULAR DAY 1/2 DAY AFTERNOON - HAPON ANG PASOK W/O PAY @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 15)
    {
		$schedTimeIn = strtotime($sched_timeout) - 14400; /* - 4 hours from scheduled time in */
		$sched_timein = date('H:i', $schedTimeIn);
		$sched_timein = $functions->combineDateTime($date_trans, $sched_timein);
		
		$late_minutes=0;$under_time = 0;$over_time = 0;$work_hours = 0;$present=0.00;$absent=0.00;
		
        if (!empty($time_in) && !empty($time_out))
        {
            $late_minutes = $functions->checkLateTimeIn($dtr_timein, $sched_timein);
            $under_time = $functions->getUndertime($sched_timeout,$dtr_timeout);
            $over_time = $functions->getOverTime($sched_timeout,$dtr_timeout);
            $work_hours = 4;
            $present=1.00;
            $absent=0.00;
        }      
        
        $late_minutes = $late_minutes;
        $under_time = $under_time;
        $over_time = $functions->getOverTimeHour($over_time);
        $nightdiff = $functions->calculateNightDifferential($dtr_timein, $dtr_timeout);
       	$nightdiff_hol = 0; 
        $nightdiff_dr = 0;
    }
    /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ REGULAR DAY 1/2 DAY AFTERNOON - HAPON ANG PASOK W/PAY @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 14)
    {
		$schedTimeIn = strtotime($sched_timeout) - 14400; /* - 4 hours from scheduled time in */
		$sched_timein = date('H:i', $schedTimeIn);
		$sched_timein = $functions->combineDateTime($date_trans, $sched_timein);
		
		$late_minutes=0;$under_time = 0;$over_time = 0;$work_hours = 0;$present=0.00;$absent=0.00;
		
        if (!empty($time_in) && !empty($time_out))
        {
            $late_minutes = $functions->checkLateTimeIn($dtr_timein, $sched_timein);
            $under_time = $functions->getUndertime($sched_timeout,$dtr_timeout);
            $over_time = $functions->getOverTime($sched_timeout,$dtr_timeout);
            $work_hours = 8;
            $present=1.00;
            $absent=0.00;
        }      
        
        $late_minutes = $late_minutes;
        $under_time = $under_time;
        $over_time = $functions->getOverTimeHour($over_time);
        $nightdiff = $functions->calculateNightDifferential($dtr_timein, $dtr_timeout);
       	$nightdiff_hol = 0; 
        $nightdiff_dr = 0;        
    }
    /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ LEGAL & SPECIAL HOLIDAY @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 2 || $day_type_code == 3)
    {
    	$late_minutes=0;$under_time = 0;$over_time = 0;$work_hours = 0;$present=0.00;$absent=0.00;
        if (!empty($time_in) && !empty($time_out))
        {
            $late_minutes = $functions->checkLateTimeIn($dtr_timein, $sched_timein);
            $under_time = $functions->getUndertime($sched_timeout,$dtr_timeout);
            $over_time = $functions->getOverTime($sched_timeout,$dtr_timeout);
            $work_hours = 8;
            $present=1.00;
            $absent=0.00;
        }      
        
        $late_minutes = $late_minutes;
        $under_time = $under_time;
        $over_time = $functions->getOverTimeHour($over_time);
        $nightdiff = 0;
       	$nightdiff_hol = $functions->calculateNightDifferential($dtr_timein, $dtr_timeout);  
        $nightdiff_dr = 0;
    }
    /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ VACATION LEAVE / SICK LEAVE @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 6 || $day_type_code == 7)
    {			
        $late_minutes=0;
        $under_time = 0;
        $over_time = 0;				
        $work_hours = 0;				
        $present = 1;
        $absent = 0;
        
        $late_minutes = $late_minutes;
        $under_time = $under_time;
        $over_time = $functions->getOverTimeHour($over_time);
        $nightdiff = 0;
       	$nightdiff_hol = 0;  
        $nightdiff_dr = 0;
     }
     /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ PATERNITY / MATERNITY / EMERGENCY LEAVE @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
    if($day_type_code == 4 || $day_type_code == 5 || $day_type_code == 13)
    {			
        $late_minutes=0;
        $under_time = 0;
        $over_time = 0;				
        $work_hours = 0;				
        $present =0;
        $absent= 0;
        
        $late_minutes = 0;
        $under_time = 0;
        $over_time = 0;
        $nightdiff = 0;
       	$nightdiff_hol = 0;  
        $nightdiff_dr = 0;
     }
/* -----------------------------## END HERE ##------------------------------------------------------- */	
	array_push($dtrdata,
        [
            'rowid' => $rowid,
            'regular_hour' => $work_hours,
            'late_time' => $late_minutes,
            'under_time' => $under_time,
            'overtime_hour' => $over_time,
            'present' => $present,
            'absent' => $absent,
            'nightdiff_hours' => $nightdiff,
            'nightdiff_hol' => $nightdiff_hol,
            'nightdiff_dr' => $nightdiff_dr
        ]
    );
}
echo $functions->pushUpdate($dtrdata, $db);
?>
