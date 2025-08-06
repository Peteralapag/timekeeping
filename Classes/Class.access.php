<?php
class AppAccess
{
	public function GetAppAccess($username,$user_app,$db)
	{
		$QUERY = "SELECT * FROM tbl_system_privilege WHERE applications='$user_app' AND username='$username'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		} 
		mysqli_close($db);
	}
}