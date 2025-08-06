<div class="app-wrapper">
	<div class="title-header">
		<span class="btn btn-sm"><i class="fa-regular fa-calendar-days"></i> PAYROLL DATES</span>
		<div class="btn-navs">
			<button class="btn btn-success btn-sm color-white" onclick="payrollDate()"><i class="fa-solid fa-rotate"></i></button>		
			<button class="btn btn-primary btn-sm color-white" onclick="setPayrollDate()">Create Payroll Date</button>
		</div>
	</div>
	<div class="page-wrapper tableFixHead" id="pagewrapper"></div>
</div>
<script>
function setPayrollDate()
{	
	$('#modaltitle').html("Cutoff Date");
	$.post("./Models/Time_Keeping/apps/set_payroll_cutoff.php", { },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
		payrollInfo();
	});	
}
function payrollDate()
{
	rms_reloaderOn('Loading...');
	$.post("./Models/Time_Keeping/includes/payrolldate_data.php", { },
	function(data) {
		$('#pagewrapper').html(data);
		rms_reloaderOff();
	});
}
function loadPage(page) {
    $.post('./Models/Time_Keeping/includes/payrolldate_data.php', { page: page }, function(data) {
        $('#pagewrapper').html(data);
    });
}
$(function()
{
    $(document).on('click', '.pagination-link:not(.disabled)', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        loadPage(page);
    });
    loadPage(1);
});
</script>