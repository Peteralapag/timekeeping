<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AMS LOGIN <?php echo $Main->GetMainConfig('company_name',$db)?></title>
<link rel="stylesheet" href="../Models/Styles/fa/css/all.css">
<link rel="stylesheet" href="../Models/Styles/bootstrap-5.0.2/bootstrap.min.css">
<link rel="stylesheet" href="../Models/Styles/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="../Models/Styles/login_style.css">
<link rel="stylesheet" href="../Libraries/loader/loader.css">
<link rel="stylesheet" href="../Models/Styles/jquery-ui.css">
<script src="../Models/Scripts/jquery.min.js"></script>
<script src="../Models/Scripts/bootstrap-5.0.2/bootstrap.min.js"></script>
<script src="../Models/Scripts/sweetalert.min.js"></script>
<style>
.caps-warning {
	display: none;
}
</style>
</head>
<body id="loginbody" class="login-body">
	<div class="logo" id="lowgow"></div>
		<div class="app-name"><h1>TIMEKEEPING MANAGEMENT SYSTEMssssss</h1></div>
	<div class="loginBtn"><span>Sign In</span> <i class="fa-solid fa-arrow-right" style="margin-left:auto"></i></div>
    <div id="frosted" class="frosted frosted-glass">
      	<div class="logo"></div>
        <div class="wrapper">
        	<div class="form-box login">
			<h2>Sign In</h2>
			<div class="input-box">
				<span class="icon"><i class="fa-solid fa-user"></i></span>
				<input id="uname" type="text" required>
				<label>Username</label>
			</div>
			<div class="input-box">
				<span class="icon"><i class="fa-solid fa-key"></i></span>
				<input id="upass" type="password" required>
				<label>Password</label>
			</div>
			<div class="login-button" style="margin-top:30px">
				<button id="submitlogin" class="btn btn-danger btn-lg w-100" onclick="pushLogin()">Sign In</button>
			</div>
			<!-- div class="caps-warning">Caps Lock is On</div -->
		</div>        	
        </div>
        <div class="page-loader-bd">
		<div class="page-loader"><i class="fa fa-spinner fa-spin"></i></div>
	</div>
        <div class="loginresults"></div>
    </div>	
	<div class="login-footer">Design & Coded By: Ronan Sarbon - AMS v2.0 2024</div>
</body>
<script src="Models/Scripts/user_login.js"></script>
<script src="Models/Scripts/random_wallpaper.js"></script>
<script src="Models/Scripts/default_scripts.js"></script>
<script src="Libraries/loader/loader.js"></script>
<script src="Models/Scripts/jquery-ui.js"></script>
<script src="Models/Scripts/jquery.dataTables.min.js"></script>
<script src="Models/Scripts/dataTables.bootstrap.min.js"></script>
</html>
