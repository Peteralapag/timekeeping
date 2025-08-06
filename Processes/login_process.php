<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if(isset($_POST['mode']))
{
	$mode = $_POST['mode'];
} else {
	print_r('
		<script>
			app_alert("Warning"," The Mode you are trying to pass does not exist","warning","Ok","","no");
		</script>
	');
	exit();
}
if($mode=='c10cc6b684e1417e8ffa924de1e58373')
{
	$uname = $_POST['username'];
	$pass = $_POST['password'];
	$user_app = "TIMEKEEPING";
	$username = mysqli_real_escape_string($db, $uname);
	$password = $encpass->encryptedPassword($pass,$db);
	$sqlLogin = "SELECT * FROM tbl_system_user WHERE username='$username' AND password='$password'";
	$result = mysqli_query($db, $sqlLogin);    
    if ( $result->num_rows > 0 ) 
    { 
	    while($listrow = mysqli_fetch_array($result))  
		{

			$username = $listrow['username'];
			$user_level = $listrow['level'];
			$user_role = $listrow['role'];
			$employee = $listrow['firstname']." ".$listrow['lastname'];	
			$void = $listrow['void_access'];
		}
		if($user_role != 'Owner' AND 'Administrator' AND $user_level < 80)
		{
			$checkPolicy = $checkAppAccess->GetAppAccess($username,$user_app,$db);
			if($checkPolicy == 0)
			{
				$cmd = '';
				$cmd .='				
					<script>
						app_alert("Access Denied","You have no access to this application","warning","Ok","","");
					</script>
				';
				print_r($cmd);
				exit();
			}
		}
		if($void == 0)
		{
			$_SESSION['timekeeping_username'] = $username;

			$_SESSION['timekeeping_appnameuser'] = $employee;
			$_SESSION['timekeeping_userlevel'] = $user_level;
			$_SESSION['timekeeping_userrole'] = $user_role;
			$_SESSION['timekeeping_application'] = $user_app;		
			$cmd = '';
			$cmd .='				
				<script>
					$(".wrapper").hide();

					sessionStorage.setItem("username", "'.$username.'");
					app_alert("Sign In Success","You have successfuly Signing-In","success","Ok","","loginsuccess");
				</script>
			';
			print_r($cmd);
			mysqli_close($db);
			exit();						
		} else {
			$cmd = '';
			$cmd .='				
				<script>
					app_alert("System Message","Your account is locked, Please contact the sytem Administrator","warning","Ok","","");
				</script>
			';
			print_r($cmd);
		}
	} else {
		$cmd = '';
		$cmd .='				
			<script>
				$("#upass").val("");
				$("#uname").val("");
				app_alert("Login Error","Invalid Username or Password","warning","Ok","","");
			</script>
		';
		print_r($cmd);
		exit();
	}
	mysqli_close($db);
}
