<?php
$app_path = $_SERVER['DOCUMENT_ROOT']."/Models/Time_Keeping";
if($_POST['page'] == 'dashboard')
{
	include ($app_path.'/includes/dashboard.php');
} 
else {
	$page = $_POST['page'];
	include ($app_path.'/includes/'.$page.'.php');
}
?>