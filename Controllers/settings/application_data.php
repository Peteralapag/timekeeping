<?php
include '../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$QUERY = "SELECT * FROM tbl_system_application WHERE active=1";
$RESULTS = mysqli_query($db, $QUERY);    
if ( $RESULTS->num_rows > 0 ) 
{
    while($ROW = mysqli_fetch_array($RESULTS))  
	{
		$rowid = $ROW['id'];
		$appname = $ROW['application_name'];
?>	
	<ul>
		<li onclick="showModules('<?php echo $rowid?>','<?php echo $appname?>')">
			<div class="appbtnicon"><i class="<?php echo $ROW['application_icon']?>"></i>
			<?php echo $ROW['application_name']?></li>
	</ul>
<?php
	}
} else {

}