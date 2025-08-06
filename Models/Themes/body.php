<?php
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$Main = new Main;
?>
<link rel="stylesheet" href="../Models/Time_Keeping/styles/styles.css">
<style>
.wrapper {display: flex;flex-direction: column;height: 100vh;}
.header-wrapper {height: 50px;background-color: #f1f1f1;padding: 10px;width: 100%;box-sizing: border-box;}
.main-content-wrapper {display: flex;flex: 1;width: 100%;}
.left-wrapper {width: 300px;background-color: #fff;box-sizing: border-box;}
.main-content {flex-grow: 1;padding: 10px;box-sizing: border-box;padding: 10px;display: flex; overflow:hidden}
.main {width: 100vw;background: #fff;border-radius:5px; overflow: auto}
.footer-text {box-sizing: border-box;width:100%;text-align:center;bottom:0px;font-size:0.8rem;font-style:italic;color:#fff;padding:0.5rem;
background: rgba(255, 255, 255, 0.5);border-top:1px solid #aeaeae;backdrop-filter: blur(10px);-webkit-backdrop-filter: blur(10px);}
</style>
<div class="wrapper">
    <div class="header-wrapper">
    			<ul>
			<li class="menu"><i class="fa fa-bars"></i></li>
			<li class="app-name"><?php echo $Main->GetMainConfig('application_name',$db)?></li>
			<li class="centered" id="modulename"></li>
			<div class="right-items-wrapper">
				<li class="user-name right-spacer" onclick="myProfile()"><i class="fa-solid fa-user">
				</i>&nbsp;&nbsp;<?php echo ucwords($_SESSION['timekeeping_appnameuser'])?>
					<div class="drop-wrapper" id="myprofile">MY PROFILE</div>
				</li>
				<li class="right-item nav-icon right-spacer" onclick="signOut()"><i class="fa-solid fa-right-from-bracket"></i></li>
				<?php if($_SESSION['timekeeping_userlevel'] >= 80 && $_SESSION['timekeeping_userlevel'] <= 100) { ?>
				<li class="right-item nav-icon" title="Settings" onclick="adminSettings()">
					<i class="fa-solid fa-gear"></i>
					<div class="drop-wrapper" id="adminsettings">
						<ul>
							<li>
								<div class="iconics"><i class="fa-solid fa-user-gear text-primary"></i></div>
								User Management
							</li>
							<li onclick="executeSecurity()">
								<div class="iconics"><i class="fa-solid fa-shield color-gold"></i></div>
								Security & Permissions
							</li>
							<li><div class="iconics"><i class="fa-brands fa-app-store color-dodger"></i></div>Application Management</li>
						</ul>
					</div>
				</li>
				<?php } ?>
			</div>
		</ul>
    </div>    
    <div class="main-content-wrapper">
        <div class="left-wrapper" id="leftwrapper"></div>
        <div class="main-content">
            <main class="main" id="main" style="position:relative"></main>
        </div>
    </div>

    <div class="footer-text">Design & Coded By: Ronan Sarbon 2023</div>
</div>
<script>
function loadSidebar()
{
	$.post("./Models/Time_Keeping/navigation.php", { },
	function(data) {
		$('#leftwrapper').html(data);
	});
}
function myProfile()
{
	if ($('#myprofile').is(':visible'))
	{
		$('#myprofile').slideUp();
	} else {
		$('#myprofile').slideDown();
	}
}
function adminSettings()
{
	if ($('#adminsettings').is(':visible'))
	{
		$('#adminsettings').slideUp();
	} else {
		$('#adminsettings').slideDown();
	}	
	$.post("./Controllers/app_settings.php", { },
	function(data) {
		$('#adminsettings').html(data);
	});
}
$(function()
{
	if(sessionStorage.navset !== null && sessionStorage.navset !== '')
	{
		$('#modulename').html(sessionStorage.ModuleTitle);
		executeSecurity(sessionStorage.navset);
	}
	loadSidebar();
});
</script>