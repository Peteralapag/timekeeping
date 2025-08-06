<style>
.wrapp {display: flex;width: 100%;height: calc(100vh - 85px);}
.left-nav {width: 400px;padding:10px;transition: transform 0.3s ease;background: rgba(51, 51, 51, 0.7);color: white;height: 100%;overflow:hidden; overflow-y: auto;
position: relative;border-right: 1px solid #aeaeae;backdrop-filter: blur(10px);-webkit-backdrop-filter: blur(10px);}
.left-nav.collapsed {transform: translateX(-150px);}
.left-nav ul {list-style: none;padding: 0;margin: 0;}
.left-nav li {padding: 15px;cursor: pointer;}
.left-nav li:hover {background-color: #575757;}
.right-content {flex-grow: 1;padding: 10px;}
.right-main-content {padding: 10px;height:calc(100vh - 105px); background: rgba(255, 255, 255, 0.7);border-radius:5px 5px 15px 15px;
backdrop-filter: blur(10px);-webkit-backdrop-filter: blur(10px);}
.appbtnicon {
	margin-right:8px;
}
.applications {
	border:1px solid #ccc;
	height:200px;
	overflow:hidden;
	overflow-y: auto;
}
.applications ul {list-style: none;padding: 0;margin: 0;}
.applications li {padding: 5px;cursor: pointer; background:#868686;margin-bottom:3px;border-bottom:1px solid #ccc}
.applications li:hover {background-color: #575757;}
.left-nav label {
	
}
.left-nav label {
	border: 1px solid #fff;
	padding:3px;
	text-align:center;
	margin-top: 5px;
	font-size:12px;
	width: 100%;
	margin-bottom:3px;
	background:#232323;
	border-radius:5px;
}
</style>
<div class="wrapp">
    <div class="left-nav" id="navleft">
    	<label style="margin-top:0;font-weight:600">APPLICATIONS</label>
    	<div class="applications" id="applications"></div>
    	<label style="margin-top:20px;font-weight:600">MODULES <span id="appname"></span></label>
    	<div class="applications" id="modules"></div>
    </div>
	    <div class="right-content">
	    	<div class="right-main-content" id="rightcontent"></div>
	    </div>
</div>
<script>
function showModules(rowid)
{
	$.post("./Controllers/settings/app_icon.php", { rowid: rowid },
	function(data) {
		$('#rightcontebt').html(data);
		callPermissions(rowid);
	});
}
function showModules(rowid,appname)
{
	$.post("./Controllers/settings/application_modules.php", { rowid: rowid },
	function(data) {
		$('#rightcontent').html(data);
		callModules(rowid);
	});
}
function callModules(appid)
{
	$.post("./Controllers/settings/app_modules.php", { appid: appid },
	function(data) {
		$('#modules').html(data);
	});
}
$(function()
{
	$.post("./Controllers/settings/application_data.php", { },
	function(data) {
		$('#applications').html(data);
	});
});
</script>
