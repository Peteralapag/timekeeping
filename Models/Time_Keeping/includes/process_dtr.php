<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$cutoff_date = $_SESSION['PAYROLL_DATE'] ?? '';
$cutoff_from = $_SESSION['PAYROLL_FROM'] ?? '';
$cutoff_to = $_SESSION['PAYROLL_TO'] ?? '';
$cluster = $_SESSION['PAYROLL_CLUSTER'] ?? '';
$branch = $_SESSION['PAYROLL_BRANCH'] ?? '';
$cutoff_range = date("F d, Y", strtotime($cutoff_from)) . ' - ' .date("F d, Y", strtotime($cutoff_to));
?>
<div class="app-wrapper">
	<div class="title-header">
		<span class="btn btn-sm" style="font-weight:600; letter-spacing: 5px"><i class="fa-sharp fa-solid fa-calendar-days"></i> PROCESS ATTENDANCE</span>
		<span id="daterange" class="btn btn-secondary btn-sm" style="color:#fff">Processing (<?php echo $cutoff_range?>)</span>
		<div class="btn-navs">
			<button class="btn btn-success btn-sm color-white" onclick="processDTR()"><i class="fa-solid fa-rotate"></i></button>
		</div>
	</div>
	<div class="page-wrapper tableFixHead" id="pagewrapper"></div>
</div>
<script>
function processDTR()
{
	$.post("./Models/Time_Keeping/includes/dtr_process_panel.php", { },
	function(data) {
		$('#pagewrapper').html(data);
	});
}
$(function()
{
	processDTR();
});
</script>