<link rel="stylesheet" href="../Models/Time_Keeping/styles/styles.css">
<!-- 
<div class="left-sidebar" id="leftnavigation"></div>
<div class="timekeeping-content" id="timekeepingcontent"></div>
-->

<div class="sidebar">
	<div class="navigation" id="navigation"></div>
</div>
<div class="content-wrapper">
	<div class="contents" id="contents"></div>
</div>
<script>
function loadSidebar()
{
	$.post("./Models/Time_Keeping/navigation.php", { },
	function(data) {
		$('#navigation').html(data);
	});
}
$(function()
{
	loadSidebar();
});
</script>