<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$payroll_date = $_SESSION['PAYROLL_DATE'] ?? '';
$payroll_from = $_SESSION['PAYROLL_FROM'] ?? '';
$payroll_to = $_SESSION['PAYROLL_TO'] ?? '';
$cluster = $_SESSION['PAYROLL_CLUSTER'] ?? '';
$branch = $_SESSION['PAYROLL_BRANCH'] ?? '';
$idcode = $_POST['idcode'];

$query = "SELECT * FROM tbl_dtr WHERE idcode='$idcode' AND payroll_date='$payroll_date' ORDER BY branch ASC";
$results = $db->query($query);
?>
<style>
.table-lamesa th, td {
	padding: 5px !important;
	font-size: 12px;
}
.table-lamesa th {
	background: #f1f1f1
}
</style>
<table style="width: 100%;white-space:nowrap" class="table table-bordered table-lamesa table-hover">
	<tr>
		<th style="text-align:center;width:60px !important">#</th>
		<th>LOG DATE</th>
		<th>DAY TYPE</th>
		<th>SCHEDULE</th>
		<th>REG.(Hrs)</th>		
		<th>TIME IN</th>
		<th>TIME OUT</th>		
		<th>LATE (Mins)</th>
		<th>UT (Mins)</th>
		<th>OT (Hrs)</th>
		<th>OT APPROVED</th>
		<th>PRESENT</th>
		<th>TIME LOGS</th>
	</tr>
<?php
    if ($results->num_rows > 0)
    {
    	$p=0;$total_hours=0;$total_late_minutes=0;$total_undertime=0;$total_overtime=0;$total_present=0;
    	while ($DTRROWS = mysqli_fetch_array($results))
        {
        	$p++;
        	$trans_date = $DTRROWS['trans_date'];
        	$day_type = $DTRROWS['day_type_code'];
        	$approved_overtime = $DTRROWS['approved_overtime'];
        	$day_name = date('w', strtotime($trans_date));
        	
        	$time_in = !empty($DTRROWS['time_in']) ? $DTRROWS['time_in'] : '--:--';
        	$time_out = !empty($DTRROWS['time_out']) ? $DTRROWS['time_out'] : '--:--';
        	
        	if ($DTRROWS['approved_overtime'] == 1 && $DTRROWS['overtime_hour'] > 0) {
			    $ot_approved = "Yes";
			} elseif ($DTRROWS['approved_overtime'] == 0 && $DTRROWS['overtime_hour'] > 0) {
			    $ot_approved = "No";
			} else {
			    $ot_approved = '--';
			}
			if($DTRROWS['present'] == 1 AND $DTRROWS['absent'] == 0)
			{
				$attendance = "Yes";
			} else {
				$attendance = "No";
			}
			$total_hours +=  $DTRROWS['regular_hour'];
			$total_late_minutes += $DTRROWS['late_time'];
			$total_undertime += $DTRROWS['under_time'];
			$total_overtime += $DTRROWS['overtime_hour'];
			$total_present += $DTRROWS['present'];
?>
	<tr>
		<td style="text-align:center;background:#f1f1f1"><?php echo $p?></td>
		<td><?php echo $trans_date; ?> - <?php echo $functions->getDayname($day_name); ?></td>		
		<td><?php echo $functions->getDayType($day_type,$db)?></td>
		<td style="text-align:center"><?php echo $DTRROWS['shifting'];?></td>		
		<td style="text-align:center"><?php echo $DTRROWS['regular_hour'];?></td>
		<td style="text-align:center"><?php echo $time_in?></td>
		<td style="text-align:center"><?php echo $time_out?></td>
		<td style="text-align:center"><?php echo $DTRROWS['late_time'];?></td>
		<td style="text-align:center"><?php echo $DTRROWS['under_time'];?></td>
		<td style="text-align:center"><?php echo $DTRROWS['overtime_hour'];?></td>
		<td style="text-align:center"><?php echo $ot_approved?></td>
		<td style="text-align:center"><?php echo $attendance ?></td>
		<td style="text-align:center"><?php echo $DTRROWS['dtr_logs']?></td>
	</tr>
<?php } ?>
	<tr>
		<th colspan="4" style="text-align:center">TOTAL</th>
		<th style="text-align:center" class="auto-style1"><?php echo $total_hours?></th>
		<th colspan="2"></th>
		<th style="text-align:center" class="auto-style1"><?php echo $total_late_minutes?></th>
		<th style="text-align:center" class="auto-style1"><?php echo $total_undertime?></th>
		<th style="text-align:center" class="auto-style1"><?php echo $total_overtime?></th>
		<th style="text-align:center"></th>
		<th style="text-align:center"><?php echo $total_present?></th>
		<th></th>
		
	</tr>
<?php } else { ?>	
	<tr>
		<td colspan="15">No Records</td>
	</tr>
<?php } ?>	
</table>

