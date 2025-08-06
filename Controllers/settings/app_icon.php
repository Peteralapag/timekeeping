<?php
include '../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$rowid = $_POST['rowid'];
$iconQUERY = "SELECT * FROM tbl_system_application WHERE id='$rowid'";
$iconRESULTS = mysqli_query($db, $iconQUERY);    
if ( $iconRESULTS->num_rows > 0 ) 
{
    while($ICONS = mysqli_fetch_array($iconRESULTS))  
	{
		$app_icon = $ICONS['application_icon'];
	}
} else {
	$app_icon = "No Icons"	;
}
?>
<label style="font-weight:600">Icon Class</label>
<input type="text" class="form-control" style="width:300px" value="<?php echo $app_icon?>" placeholder="Enter Icon Class">
<div style="margin-top:10px; margin-bottom:10px;text-align:right">
	<button class="btn btn-success">Update</button>
	<button class="btn btn-danger" onclick="closeModal('formmodal')">Close</button>
</div>