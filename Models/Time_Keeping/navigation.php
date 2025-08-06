<?php
include '../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
if(isset($_SESSION['wms_userlevel']))
{
	$user_level = $_SESSION['wms_userlevel'];
} else {
	$user_level = 0;
}
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
<style>
.sidebar-nav { list-style-type:none; margin:0;padding:0;width: 300px !important}
.navpadleft {margin-left:10px;cursor:pointer; width:100%;}
.sidebar-nav li { display: flex; padding:5px 5px 5px 5px;border-bottom: 1px solid #aeaeae; width:100%; gap: 15px;cursor:pointer}
.sidebar-nav li:hover {background:#e7e7e7;}
.sidebar-nav .nav-icon {width:30px;text-align:center;font-size:18px;}
.sidebar-nav span {right: 0;}
.sidebar-nav .caret-right {margin-left: auto;}
.active-nav {background: #dcdfe0;}
.active {border: 1px solid blue;}
.nav-bottom-btn {bottom: 2px;margin-left:3px;width: 98%;}
.nav-mainshell {display: flex;overflow: hidden;height: 100%;width: 100%;overflow: hidden;width:100% !important;flex-direction: column;align-items: center;}
.payroll-info {width: 100%;margin-top: auto;bottom:0;padding: 5px;}
.lamesa td {padding: 3px;font-size: 12px;}
.title-company {width: 100%;padding:10px 0px 10px 0px ;border-bottom: 5px solid #aeaeae;font-size: 16px;font-weight: bold;color: #333;text-shadow:1px 1px 2px #fff,-1px -1px 2px #000;
padding: 10px;letter-spacing: 2px;text-transform: uppercase;font-family: 'Arial', sans-seri;text-align:center;}
.payroll-info {margin-top: auto;}
</style>
<div class="nav-mainshell">
	<div class="title-company">Jathnier Corporation</div>
	<ul class="sidebar-nav">
	<?php
	$sqlMenu = "SELECT * FROM main_timekeeping_navigation WHERE active=1 ORDER BY ordering ASC";
	$MenuResults = mysqli_query($db, $sqlMenu);    
	if ( $MenuResults->num_rows > 0 ) 
	{
		$m=0;
		while($MENUROW = mysqli_fetch_array($MenuResults))  
		{
			$m++;
	?>
		<li id="nav<?php echo $m; ?>" data-nav="nav<?php echo $m; ?>" onclick="loadPages('<?php echo $MENUROW['page_name']; ?>','<?php echo $MENUROW['menu_name']; ?>')">
			<div class="nav-icon"> <i class="<?php echo $MENUROW['icon_class']; ?>"></i></div> <span><?php echo $MENUROW['menu_name']; ?></span>
		</li>
	<?php } } else { echo "<li>Menu is Empty.</li>"; }?>
	</ul>
	<div class="payroll-info" id="payinfo">
	</div>	
</div>
<div id="resultsdata"></div>
<script>
function loadPages(page)
{
	psaSpinnerOn();
	$.post("./Models/time_keeping/src/menu_pages.php", { page: page },
	function(data) {
		$('#main').html(data);
		psaSpinnerOff();
	});	
}
function payrollInfo()
{
	$.post("./Models/time_keeping/apps/payroll_info.php", { },
	function(data) {
		$('#payinfo').html(data);
	});	
}
$(function()	
{	
	if(sessionStorage.navwms !== 'null')
	{
		$("#"+sessionStorage.navtk).addClass('active-nav');
		$("#"+sessionStorage.navtk).trigger('click');
	}
	$('.sidebar-nav li').click(function()
	{
		var tab_id = $(this).attr('data-nav');
		sessionStorage.setItem("navtk",tab_id);
		$('.sidebar-nav li').removeClass('active-nav');
		$(this).addClass('sidebar-nav');
		$("#"+tab_id).addClass('active-nav');	
	});
	payrollInfo();
});
</script>
