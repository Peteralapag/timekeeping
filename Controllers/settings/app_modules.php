<?php
include '../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$appid = $_POST['appid'];
$QUERY = "SELECT * FROM tbl_system_application_modules WHERE application_id='$appid'";
$RESULTS = mysqli_query($db, $QUERY);    
if ( $RESULTS->num_rows > 0 ) 
{
    while($ROW = mysqli_fetch_array($RESULTS))  
	{
		$rowid = $ROW['id'];
?>	
	<ul>
		<li onclick="showPermissions('<?php echo $rowid?>')">
			<div class="appbtnicon"><i class="<?php echo $ROW['application_icon']?>"></i>
			<?php echo $ROW['module_name']?></li>
	</ul>
<?php
	}
} else {
	echo "No Modules";
}