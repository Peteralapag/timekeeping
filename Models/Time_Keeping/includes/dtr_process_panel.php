<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$cutoff_date = $_SESSION['PAYROLL_DATE'] ?? '';
$cutoff_from = $_SESSION['PAYROLL_FROM'] ?? '';
$cutoff_to = $_SESSION['PAYROLL_TO'] ?? '';
$cluster = $_SESSION['PAYROLL_CLUSTER'] ?? '';
$branch = $_SESSION['PAYROLL_BRANCH'] ?? '';

$session_start = !empty($_SESSION['PAYROLL_DATE']) ? 1 : 0;
$payroll_locked = $functions->checkLokedPayroll($cutoff_date,$db);
?>
<style>
.panel-wrapper {position:relative;display: flex;flex-direction: column;height: 100%;gap: 10px;}
.panel-top {height: auto;}
.panel-bottom {padding:3px;}
.status-box {display: none;padding: 5px 5px 5px 10px;border: 1px solid #f1f1f1;background-color:#f6f6f6;margin-bottom: 5px;border-radius: 8px}
.lock-notification {display: none;position: absolute;top: 50%;left: 50%;-webkit-transform: translate(-50%, -50%);transform: translate(-50%, -50%);overflow: hidden;	font-size: 50px;width: 100%;text-align:center;}
.panel-top button {
	margin-bottom: 10px;	
}
</style>

<div class="panel-wrapper">
    <div class="panel-top">
    	<button id="fetchSchedule" class="btn btn-warning color-white"><i class="fa-sharp fa-regular fa-calendar-arrow-down"></i>&nbsp;&nbsp;Fetch Schedule</button>
    	<button id="fetchEmployees" class="btn btn-info color-white"><i class="fa-solid fa-address-book"></i>&nbsp;&nbsp;Fetch Employees</button>
    	<button id="fethTimeLogs" class="btn btn-primary"><i class="fa-solid fa-clock"></i>&nbsp;&nbsp;Fetch Time Logs</button>
    	<button id="fetchSil" class="btn btn-warning color-white"><i class="fa-solid fa-house-person-leave"></i>&nbsp;&nbsp;Fetch S.I.L.</button>
    	<button id="processNow" class="btn btn-secondary"><i class="fa-solid fa-chalkboard-user"></i>&nbsp;&nbsp;Process DTR Now</button>
    	<button id="postToJournal" class="btn btn-danger"><i class="fa-solid fa-book"></i>&nbsp;&nbsp;Post to Journal</button>
    </div>
    <div class="panel-bottom" id="paneldata">
    	<div class="status-box" id="schedule"></div>
    	<div class="status-box" id="employees"></div>
    	<div class="status-box" id="timelogs"></div>
    	<div class="status-box" id="leaves"></div>
    	<div class="status-box" id="process"></div>
    	<div class="status-box" id="Journal"></div>
    </div>
	<div class="fetchresults" style="margin-top:20px;"></div>
	<div class="lock-notification" id="locked">This cutoff is now locked.</div>
	<div class="lock-notification" id="session">Please Set/Add Payroll Cutoff in the TimeKeeping Settings</div>
</div>
<script>
$(document).ready(function()
{
	const session = '<?php echo $session_start?>';
	const locked = '<?php echo $payroll_locked?>';
	if(locked == 1)
	{
		buttonControl(1);
		$('#locked').show();
		$('#daterange').show();
	}
	if(session == 0)
	{
		buttonControl(1);
		$('#daterange').hide();
		$('#session').show();
	}
    function fetchData(buttonId, statusId, url, successMessage) {
        $(buttonId).click(function() {
            buttonControl(1);
            $(statusId).show();
            $(statusId).html('<i class="fa-solid fa-chevrons-right"></i>&nbsp;&nbsp;Executing command, please wait... <i class="fa fa-spinner fa-spin"></i>');
            setTimeout(function()
            {
	            $.post(url, {}, function(data) {
	                $('.fetchresults').html(data);
	  	            $(statusId).html('<i class="fa-solid fa-chevrons-right"></i>&nbsp;&nbsp;' + successMessage + ' <i class="fa-solid fa-check-double text-success"></i>');
	                buttonControl(0);
	            });
	        },1000);
        });
    }
    fetchData('#fetchSchedule', '#schedule', './Models/Time_Keeping/actions/fetch_schedule.php', 'Schedule successfully fetched.');
    fetchData('#fetchEmployees', '#employees', './Models/Time_Keeping/actions/fetch_employees.php', 'Employees successfully fetched.');
    fetchData('#fethTimeLogs', '#timelogs', './Models/Time_Keeping/actions/fetch_timelogs.php', 'Time logs successfully fetched.');
    fetchData('#fetchSil', '#leaves', './Models/Time_Keeping/actions/fetch_leaves.php', 'Service Incentive Leaves has been fetched .');    
    fetchData('#processNow', '#process', './Models/Time_Keeping/actions/process_calculation.php', 'Calculations successfully executed.');
    fetchData('#postToJournal', '#process', './Models/Time_Keeping/actions/process_journal.php', 'Journal successfuly posted.');
});

function buttonControl(num) {
    $('.btn').attr('disabled', num === 1);
}
</script>