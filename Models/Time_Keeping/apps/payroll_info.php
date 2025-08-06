<?php
session_start();
$payroll_date = $_SESSION['PAYROLL_DATE'] ?? '';
$payroll_from = $_SESSION['PAYROLL_FROM'] ?? '';
$payroll_to = $_SESSION['PAYROLL_TO'] ?? '';
$cluster = $_SESSION['PAYROLL_CLUSTER'] ?? '';
$branch = $_SESSION['PAYROLL_BRANCH'] ?? '';

if($payroll_date != '')
{
	$payrollDate = date("M. d, Y", strtotime($payroll_date));
	$payrollFrom = date("M. d, Y", strtotime($payroll_from));
	$payrollTo = date("M. d, Y", strtotime($payroll_to));
} else {
	$payrollDate = "--/--/--";
	$payrollFrom = "--/--/--";
	$payrollTo = "--/--/--";
}

?>

		<table style="width: 100%" class="table table-bordered lamesa">
			<!--tr>
				<td colspan="2" class="bg-success color-white"><span style="font-size:9px">CLUSTER :</span>&nbsp;&nbsp;<?php echo $cluster?></td>
			</tr>
			<tr>
				<td colspan="2" class="bg-info color-white"><span style="font-size:9px">BRANCH :</span>&nbsp;&nbsp;<?php echo $branch?></td>
			</tr-->
			<tr>
				<td class="bg-secondary color-white">&nbsp;&nbsp;Pay Date</td>
				<td class="bg-warning color-white" style="text-align:center"><?php echo $payrollDate?></td>
			</tr>
			<tr>
				<td class="bg-secondary color-white">&nbsp;&nbsp;Cutoff From</td>
				<td class="bg-warning color-white" style="text-align:center"><?php echo $payrollFrom?></td>
			</tr>
			<tr>
				<td class="bg-secondary color-white">&nbsp;&nbsp;Cutoff To</td>
				<td class="bg-warning color-white" style="text-align:center"><?php echo $payrollTo?></td>
			</tr>
		</table>
