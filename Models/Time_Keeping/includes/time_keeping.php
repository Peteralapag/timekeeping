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
<style>
.inputboxes {
	display: flex;
	width: 700px;
}
.small-notifs {
	display: none;
}
@media (max-width: 1472px) {
    .inputboxes {
        display: none;
    }
    .small-notifs {
		display: block;
	}
}
</style>
<div class="app-wrapper">
	<div class="title-header">
		<span class="btn btn-sm" style="font-weight:600; letter-spacing: 5px"><i class="fa-solid fa-clock"></i> TIME KEEPING</span>
		<span style="font-size:20px">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
		<span class="small-notifs btn color-orange">Screen too small!</span>
		<div class="inputboxes">
			<span style="position:relative;width: 240px">
				<input list="data-list" class="form-control form-control-sm" id="search" name="datalist" placeholder="Search..." autocomplete="off">
				<i class="fa-solid fa-circle-xmark x-clear" onclick="xClear()"></i>	  
				<div class="data-list" id="datalist">
					<div class="listdata" id="listdata"></div>
				</div>
			</span>
			<span style="margin-left:5px;width:240px">
				<select id="cluster" class="form-control form-control-sm" onchange="onchangeCluster()">
					<?php echo $functions->getCluster($cluster,$db)?>
				</select>
			</span>
			<span style="margin-left:5px;width:240px">
				<select id="branch" class="form-control form-control-sm" onchange="loadJournalDataBranch(this.value)">
					<?php echo $functions->getBranch($branch,$db)?>
				</select>
			</span>
		</div>
		<div class="btn-navs">
			<button class="btn btn-info btn-sm color-white" onclick="loadJournal()">Load Journal</button>
			<button class="btn btn-success btn-sm color-white" onclick="loadJournalData()"><i class="fa-solid fa-rotate"></i></button>
		</div>
	</div>
	<div class="page-wrapper tableFixHead" id="pagewrapper"></div>
</div>
<script>
function xClear()
{
	$('#search').val('');
	loadJournalData();
}
function passInfo(rowid,acctname)
{
	loadJournalSearch(acctname);
	document.getElementById('cluster').selectedIndex = 0;
	document.getElementById('branch').selectedIndex = 0;
}
function loadJournalSearch(acctname)
{
	rms_reloaderOn("Searching...");
	setTimeout(function()
	{
		$.post("./Models/Time_Keeping/includes/load_journal_data.php", { acctname: acctname },
		function(data) {
			$('#pagewrapper').html(data);
			$('#search').val(acctname);
			$('#datalist').fadeOut();
			rms_reloaderOff();
		});
	},500);
}
function loadJournal()
{	
	$('#modaltitle').html("Load Timekeeping Journal");
	$.post("./Models/Time_Keeping/apps/load_journal.php", { },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
function loadJournalData()
{
	rms_reloaderOn("Loading...");
	const payroll_date = '<?php echo $cutoff_date?>';
	const payroll_from = '<?php echo $cutoff_from?>';
	const payroll_to = '<?php echo $cutoff_to?>';
	setTimeout(function()
	{	
		$.post("./Models/Time_Keeping/includes/load_journal_data.php", { payroll_date: payroll_date, payroll_from: payroll_from, payroll_to: payroll_to },
		function(data) {
			$('#pagewrapper').html(data);
			rms_reloaderOff();
		});
	},500);		
}
function loadJournalDataCluster(cluster)
{
	rms_reloaderOn("Loading...");
	const payroll_date = '<?php echo $cutoff_date?>';
	const payroll_from = '<?php echo $cutoff_from?>';
	const payroll_to = '<?php echo $cutoff_to?>';
	setTimeout(function()
	{	
		$.post("./Models/Time_Keeping/includes/load_journal_data.php", { cluster: cluster, payroll_date: payroll_date, payroll_from: payroll_from, payroll_to: payroll_to },
		function(data) {
			$('#pagewrapper').html(data);
			rms_reloaderOff();
		});
	},500);		
}
function loadJournalDataBranch(branch)
{
	rms_reloaderOn("Loading...");
	const payroll_date = '<?php echo $cutoff_date?>';
	const payroll_from = '<?php echo $cutoff_from?>';
	const payroll_to = '<?php echo $cutoff_to?>';
	const cluster = '';
	setTimeout(function()
	{	
		$.post("./Models/Time_Keeping/includes/load_journal_data.php", { cluster: cluster, branch: branch, payroll_date: payroll_date, payroll_from: payroll_from, payroll_to: payroll_to },
		function(data) {
			$('#pagewrapper').html(data);
			document.getElementById('cluster').selectedIndex = 0;
			rms_reloaderOff();
		});
	},500);		
}

$(function()
{
	$('#search').keypress(function()
	{
		const search = $('#search').val();
		$.post('./Models/Time_Keeping/includes/data_list.php', { search: search }, function(data) {
            $('#listdata').html(data);
            $('#datalist').slideDown();
        });
	});
	
    function loadPage(page) {
        $.post('./Models/Time_Keeping/includes/load_journal_data.php', { page: page }, function(data) {
            $('#pagewrapper').html(data);
        });
    }

    $(document).on('click', '.pagination-link:not(.disabled)', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        loadPage(page);
    });
   loadJournalData(1);
});
function onchangeCluster()
{
    const cluster = $('#cluster').val();
    const branch = '<?php echo $branch ?>';
    if (cluster) {
        $.post('./Models/Time_Keeping/includes/fetch_branches.php', { cluster: cluster, branch: branch }, function(data) {
        	loadJournalDataCluster(cluster);
        	$('#search').val('');
        	$('#branch').html(data);
        });
    } else {
       $('#branch').html('<?php echo $functions->getBranch($branch,$db)?>');
       loadJournalData(1);
    }
}

</script>