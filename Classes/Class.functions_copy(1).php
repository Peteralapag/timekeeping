<?php
class TKFunctions
{
	public function pushUpdate($dtrdata,$db)
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
	
			$update = "
				regular_hour='$regular_hour',
				late_time='$late_time',
				under_time='$under_time',
				overtime_hour='$overtime_hour',
				present='$present',
				absent='$absent',
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

    public function updateDTRLogs($payroll_date,$idcode,$time_in,$date_in,$time_out,$date_out,$dtr_logs,$trans_date,$db)
    {
        $update = "time_in='$time_in',date_in='$date_in',time_out='$time_out',date_out='$date_out',dtr_logs='$dtr_logs',posted=1";
        
        $query = "UPDATE tbl_dtr SET $update WHERE trans_date='$trans_date' AND idcode='$idcode'";		
        if ($db->query($query) === TRUE)
        {
            return "SUCCESS"; // Successfully updated
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

    public function getBranch($branch,$db)
    {
        $query = "SELECT * FROM tbl_branch ORDER BY branch ASC";
        $results = $db->query($query);			
        if($results->num_rows > 0)
        {
            $return = '';
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
	public function setPayrollDate($payroll_date,$payroll_from,$payroll_to,$db)
	{
		$_SESSION['PAYROLL_DATE'] = $payroll_date;
		$_SESSION['PAYROLL_FROM'] = $payroll_from;
		$_SESSION['PAYROLL_TO'] = $payroll_to;
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
