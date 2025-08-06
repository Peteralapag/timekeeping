<?php
class TKFunctions
{
	
	
	public function getDayType($day_type,$db)
	{
		$dQuery = "SELECT * FROM daytype WHERE daytype='$day_type'";  
		$resulta = mysqli_query($db, $dQuery);  
		while($crow = mysqli_fetch_array($resulta))
		{
			return $crow["description"];					
		}
	}
	public function getDayname($araw)
	{
		$they[0]="Sun";
		$they[1]="Mon";
		$they[2]="Tue";
		$they[3]="Wed";
		$they[4]="Thu";
		$they[5]="Fri";
		$they[6]="Sat";
		$bungang_araw=$they[$araw];
		return $bungang_araw;
	}
	public function searchTimeKeepingData($payroll_date,$db)
	{	
		$query = "SELECT * FROM tbl_journal WHERE payroll_date='$payroll_date'";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
            $return = '<option value="">';
            while($ROW = mysqli_fetch_array($results))  
            {
                $retVal .= $ROW['acctname'];
            }
            return $retVal;
        } else {
            return 000000;
        }
	}
	public function pushJournalRecords($journalData, $db)
	{
	    $pushUpdate = json_encode($journalData);
	    $array = json_decode($pushUpdate, true);	
	    for ($i = 0; $i < count($array); $i++)
	    {
	        $rowid = $array[$i]["rowid"];
	        $idcode = $array[$i]["idcode"];
	        $employee = $array[$i]["employee"];
	        $branch = $array[$i]["branch"];
	        $cluster = $array[$i]["cluster"];			
	        $company = $array[$i]["company"];
	        $position = $array[$i]["position"];
	        $payroll_date = $array[$i]["payroll_date"];
	        $payroll_to = $array[$i]["payroll_from"];
	        $payroll_from = $array[$i]["payroll_to"];
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
	        $date_generated = $array[$i]["date_generated"];
	        $generated_by = $array[$i]["generated_by"];
	
	        $update = "
	            payroll_date = '$payroll_date',
	            payroll_from = '$payroll_from',
	            payroll_to = '$payroll_to',
	            idcode = '$idcode',
	            acctname ='$employee',
	            company ='$company',
	            cluster='$cluster',
	            branch='$branch',
	            position='$position',
	            working_days='$working_days',
	            days_worked='$days_worked',
	            leave_with_pay='$leave',
	            duty_rest='$dutyrest',
	            over_time='$over_time',
	            late_minutes='$late_minutes',
	            under_time='$under_time',
	            nightdiff_reg_hours='$nightdiff_reg_hours',
	            nightdiff_dr_hours='$nightdiff_dr_hours',
	            nightdiff_ho_hours='$nightdiff_hol_hours',
	            date_generated='$date_generated',
	            generated_by='$generated_by'
	        ";
	        
	        return $update;
	        
	        $insertColumn = "
	            `payroll_date`,`payroll_from`,`payroll_to`,`idcode`,`acctname`,`cluster`,`branch`,`position`,`working_days`,`days_worked`,`leave_with_pay`,`duty_rest`,
	            `over_time`,`late_minutes`,`under_time`,`nightdiff_reg_hours`,`nightdiff_dr_hours`,`nightdiff_ho_hours`,`date_generated`,`generated_by`
	        ";	        
	        $insertData = "
	            '$payroll_date','$payroll_from','$payroll_to','$idcode','$employee','$cluster','$branch','$position','$working_days','$days_worked','$leave','$dutyrest',
	            '$over_time','$late_minutes','$under_time','$nightdiff_reg_hours','$nightdiff_dr_hours','$nightdiff_hol_hours','$date_generated','$generated_by'
	        ";
	                    
	        $query = "SELECT * FROM tbl_journal WHERE payroll_date='$payroll_date' AND idcode='$idcode'";
	        $results = $db->query($query);			
	        if($results->num_rows > 0)
	        {
	        //    $this->executeToJournalUpdate($update, $idcode, $payroll_date, $db);
	        } else {
	         //   $this->executeToJournalInsert($insertColumn, $insertData, $payroll_date, $db);
	        }	        
	    }
	}
	
	private function executeToJournalInsert($insertColumn, $insertData, $payroll_date, $db)
	{
	    $queryInsert = "INSERT INTO tbl_journal ($insertColumn) VALUES ($insertData)";
	    if ($db->query($queryInsert) !== TRUE) {
	        return $db->error;
	    }
	}
	
	private function executeToJournalUpdate($update, $idcode, $payroll_date, $db)
	{
	    $queryUpdate = "UPDATE tbl_journal SET $update WHERE payroll_date='$payroll_date' AND idcode='$idcode'";	
	    if ($db->query($queryUpdate) !== TRUE) {
	        return $db->error;
	    } 
	}
	public function checkLokedPayroll($payroll_date,$db)
    {
        $query = "SELECT * FROM dtrv_datelock_checker WHERE payroll_date='$payroll_date' AND lock_status=1";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
        	return 1;
        } else {
            return 0;
        }
    }
	public function calculateNightDifferential($time_in, $time_out)
	{
		$time_in = strtotime($time_in);
		$time_out = strtotime($time_out);
        $shift_start_date = date('Y-m-d', $time_in);
	    $night_start = strtotime($shift_start_date . ' 22:00');
	    $night_end = strtotime($shift_start_date . ' +1 day 07:00');
	    
	    // Initialize the night differential time in seconds
	    $night_diff_seconds = 0;
	
	    // SCENARIO 1: Clocked in BEFORE 10 PM and worked into night differential hours
	    if ($time_in < $night_start && $time_out > $night_start)
		{
	        $end_time = min($time_out, $night_end);
	        $night_diff_seconds = max(0, $end_time - $night_start);
	
	    // SCENARIO 2: Clocked in ON or AFTER 10 PM AND BEFORE MIDNIGHT
	    } 
		else if ($time_in >= $night_start && $time_in < strtotime($shift_start_date . ' +1 day 00:00'))
		{
	        $end_time = min($time_out, $night_end);
	        $night_diff_seconds = max(0, $end_time - $time_in);
	
	    // SCENARIO 3: Clocked in AFTER MIDNIGHT and before 7 AM
	    } 
		else if ($time_in >= strtotime($shift_start_date . '00:00') && $time_in < $night_end)
		{
	        // Set timeout to 7 AM as the end of the night differential period
	        $timeout_date = date("Y-m-d", $time_out);
	        $timeout = strtotime($timeout_date . '07:00');
	        
	        $end_time = min($timeout, $night_end);
	        $night_diff_seconds = max(0, $end_time - $time_in);
	    }
	
	    // Convert seconds to full hours by pagsasahig
	    $night_diff_hours = floor($night_diff_seconds / 3600);
		if($night_diff_hours > 8)
		{
			$night_diff_hours = 8;
		} else {
			$night_diff_hours = $night_diff_hours ;
		}
	    return $night_diff_hours;	    
	}
	
	private function getNightDiffHours($hours_night_diff)
	{
	    if ($hours_night_diff < 1) {
	        return 0;
	    }
	    return min(floor($hours_night_diff), 8);
	}
	
	public function getWorkingDays($payroll_from,$payroll_to)
	{
		$start = new DateTime($payroll_from);
		$end = new DateTime($payroll_to);
		$end = $end->modify('+1 day');
		
		$interval = new DateInterval('P1D');
		$dateRange = new DatePeriod($start, $interval, $end);
		
		$dayCount = 0;
		
		foreach ($dateRange as $date) {
		    if ($date->format('w') != 0) {
		        $dayCount++;
		    }
		}		
		return $dayCount;
	}

	public function getColumnCount($operator,$sumcolumn,$kwiri,$overtime,$db)
	{
		if($overtime == 1)
		{
			$o_time = " AND approved_overtime=1";
		} else {
			$o_time = '';
		}
		$QUERY = "SELECT $operator($sumcolumn) AS total FROM tbl_dtr WHERE $kwiri AND present=1 $o_time";
		$RESULTS = mysqli_query($db,$QUERY);
		$ROW = mysqli_fetch_array($RESULTS);	
		return $ROW['total'];
	}
	public function pushUpdate($dtrdata, $db)
	{
	    $pushUpdate = json_encode($dtrdata);
	    $array = json_decode($pushUpdate, true);	
	    for ($i = 0; $i < count($array); $i++)
	    {
	        $id = $array[$i]["rowid"];
	        $regular_hour = $array[$i]["regular_hour"];
	        $late_time = $array[$i]["late_time"];
	        $under_time = $array[$i]["under_time"];
	        $overtime_hour = $array[$i]["overtime_hour"];
	        $present = $array[$i]["present"];
	        $absent = $array[$i]["absent"];
	        $nightdiff_hours = $array[$i]["nightdiff_hours"];
	        $nightdiff_hol = $array[$i]["nightdiff_hol"];
	        $nightdiff_dr = $array[$i]["nightdiff_dr"];
		
	        $update = "
	            regular_hour='$regular_hour',
	            late_time='$late_time',
	            under_time='$under_time',
	            overtime_hour='$overtime_hour',
	            present='$present',
	            absent='$absent',
	            nightdiff_reg_hours='$nightdiff_hours',
	            nightdiff_hol_hours='$nightdiff_hol',
	            nightdiff_dr_hours='$nightdiff_dr',
	            calculated=1
	        ";
	
	        $queryUpdate = "UPDATE tbl_dtr SET $update WHERE id='$id'";	
	        if ($db->query($queryUpdate) === TRUE)
	        {
	        } else {
	            echo $db->error;
	        }
	    }
    }
	public function getOverTimeHour($over_time)
	{
		if($over_time >= 1 AND $over_time < 2)
		{
			return 1;
		}
		elseif($over_time >= 2 AND $over_time < 3)
		{
			return 2;
		}
		elseif($over_time >= 3 AND $over_time < 4)
		{
			return 3;
		}
		elseif($over_time >= 4)
		{
			return 4;
		} else {
			return 0;
		}
	}
    public function getOverTime($sched_timeout,$dtr_timeout)
    {
        $schedOut = strtotime($sched_timeout);
        $timeOut = strtotime($dtr_timeout);
        if($timeOut > $schedOut)
        {
            $over_time = ($timeOut - $schedOut);
            return abs($over_time) / 60 / 60;
        }
        else if($timeOut <= $schedOut)
        {
            return 0;
        }
    }
	public function getUndertime($sched_timeout, $dtr_timeout)
	{
		$schedOut = strtotime($sched_timeout);
		$timeOut = strtotime($dtr_timeout);
		if($timeOut < $schedOut)
		{
			$under_time = ($schedOut - $timeOut);
			return abs($under_time / 60 / 60) * 60;	
		}
		else if($timeOut >= $schedOut)
		{
			return 0;
		}
	}

    public function checkLateTimeIn($dtr_timein, $sched_timein)
    {
        if($dtr_timein >= $sched_timein)
        {
            $loginTime = strtotime($dtr_timein);
            $checkTime = strtotime($sched_timein);
            $diff = ($loginTime - $checkTime);
            return abs($diff) / 60;
        } else {
            return 0;
        }
    }
    public function combineDateTime($date, $time)
    {
        return date("Y-m-d H:i", strtotime("$date $time"));
    }
    public function updateLeaves($payroll_date,$idcode,$trans_date,$day_type,$day_type_code,$db)
    {
        $update = "day_type='$day_type',day_type_code='$day_type_code',leave_approved=1";
        
        $query = "UPDATE tbl_dtr SET $update WHERE trans_date='$trans_date' AND idcode='$idcode' AND payroll_date='$payroll_date'";		
        if ($db->query($query) === TRUE)
        {
//            return "SUCCESS"; // Successfully updated
        } else { 
            return $db->error;
       }
    }
    public function updateDTRLogs($payroll_date,$idcode,$time_in,$date_in,$time_out,$date_out,$dtr_logs,$trans_date,$db)
    {
        $update = "time_in='$time_in',date_in='$date_in',time_out='$time_out',date_out='$date_out',dtr_logs='$dtr_logs',posted=1";
        
        $query = "UPDATE tbl_dtr SET $update WHERE trans_date='$trans_date' AND idcode='$idcode'";		
        if ($db->query($query) === TRUE)
        {
//            return "SUCCESS"; // Successfully updated
        } else { 
            return $db->error;
        }
    }

    public function checkJournal($idcode,$column,$db)
    {
        $query = "SELECT * FROM tbl_journal WHERE payroll_date='$'";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
            $return = '';
            while($ROW = mysqli_fetch_array($results))  
            {
                $retVal = $ROW[$column];
            }
            return $retVal;
        } else {
            return 000000;
        }
    }

    public function getEmployeeInfo($idcode,$column,$db)
    {
        $query = "SELECT * FROM tbl_employees WHERE idcode='$idcode'";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
            $return = '';
            while($ROW = mysqli_fetch_array($results))  
            {
                $retVal = $ROW[$column];
            }
            return $retVal;
        } else {
            return 000000;
        }
    }
	public function setPayrollDate($payroll_date,$payroll_from,$payroll_to,$payroll_period,$db)
	{
		$_SESSION['PAYROLL_DATE'] = $payroll_date;
		$_SESSION['PAYROLL_FROM'] = $payroll_from;
		$_SESSION['PAYROLL_TO'] = $payroll_to;
		$_SESSION['PAYROLL_PERIOD'] = $payroll_period;
	}
	public function getPayrollDate($cutoff_date_date,$db)
    {
        $query = "SELECT * FROM dtrv_datelock_checker ORDER BY payroll_date DESC";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
            $return = '<option>-- SELECT PAY DATE --</option>';
            while($ROW = mysqli_fetch_array($results))  
            {
    	                $selected = '';
                $retVal = $ROW['payroll_date'];
                $dateName = date("D - M d, Y", strtotime($retVal));
                if($cutoff_date == $retVal)
                {
                    $selected = 'selected';
                }

                $return .= '<option value="'.$retVal.'"  '.$selected.'>'.$dateName.'</option>';
            }
            return $return;
        } else {
            return '<option value="">No Data</option>';
        }
    }
    public function getCluster($cluster,$db)
    {
        $query = "SELECT * FROM tbl_cluster ORDER BY cluster ASC";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
            $return = '<option value="">-- SELECT CLUSTER --</option>';
            while($ROW = mysqli_fetch_array($results))  
            {
                $retVal = $ROW['cluster'];
                $selected = '';
                if($retVal == $cluster)
                {
                    $selected = 'selected';
                }

                $return .= '<option value="'.$retVal.'"  '.$selected.'>'.$retVal.'</option>';
            }
            return $return;
        } else {
            return '<option value="">No Data</option>';
        }
    }
    public function getBranch($branch,$db)
    {
        $query = "SELECT * FROM tbl_branch ORDER BY branch ASC";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
            $return = '<option value="">-- SELECT BRANCH --</option>';
            while($ROW = mysqli_fetch_array($results))  
            {
                $retVal = $ROW['branch'];
                $selected = '';
                if($retVal == $branch)
                {
                    $selected = 'selected';
                }

                $return .= '<option value="'.$retVal.'"  '.$selected.'>'.$retVal.'</option>';
            }
            return $return;
        } else {
            return '<option value="">No Data</option>';
        }
    }
    public function getSelectedCluster($cluster,$db)
    {
        $query = "SELECT * FROM tbl_cluster ORDER BY cluster ASC";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
            $return = '';
            while($ROW = mysqli_fetch_array($results))  
            {
                $retVal = $ROW['cluster'];
                $selected = '';
                if($retVal == $cluster)
                {
                    $selected = 'selected';
                }

                $return .= '<option value="'.$retVal.'"  '.$selected.'>'.$retVal.'</option>';
            }
            return $return;
        } else {
            return '<option value="">No Data</option>';
        }
    }
}
?>
