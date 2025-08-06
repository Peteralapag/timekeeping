<?php
class Main
{
	public function GetMainConfig($column,$db)
	{
		$QUERY = "SELECT * FROM main_timekeeping_config WHERE id=10000";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$return = $ROW[$column];
			}
			if($column == 'company_name')
			{
				return ' '. $return;
			} else {
				return $return;
			}
		} else {
			return 0;
		} 
		mysqli_close($db);
	}
	public function GetUserWallpaper($username,$db)
	{
		return 'default_wallpaper.jpg';
	}
}