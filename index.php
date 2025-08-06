<?php
include 'init.php';
if(!isset($_SESSION['timekeeping_username']))
{
	require 'Controllers/user_login.php';
	exit();
} else {	

	if($Main->GetMainConfig('site_maintenance',$db) == 1)
	{
		if($_SESSION['timekeeping_userlevel'] >= 80)
		{
			require 'main.php';
		}
		elseif($_SESSION['timekeeping_userlevel'] < 80)
		{
			require 'Sources/maintenance.php';
		}

	} else {
		require 'main.php';
	}
}