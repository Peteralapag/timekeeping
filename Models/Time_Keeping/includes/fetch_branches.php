<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (isset($_POST['cluster'])) {
	
    $cluster = $db->real_escape_string($_POST['cluster']);
	$branch = $db->real_escape_string($_POST['branch']);
    $query = "SELECT * FROM tbl_branch WHERE location='$cluster' ORDER BY branch ASC";
    $results = $db->query($query);

    if ($results->num_rows > 0) {
    	echo '<option value="">-- SELECT BRANCH --</option>';
        while ($ROW = mysqli_fetch_array($results))
        {
        	$retVal = $ROW['branch'];
        	$selected = ($retVal == $branch) ? 'selected' : '';
            echo '<option value="' . $ROW['branch'] . '" '.$selected.'>' . $ROW['branch'] . '</option>';
        }
    } else {
        echo '<option value="">No Branches Available</option>';
    }
} else {
 //   echo '<option value="">Select a branch</option>';
}
?>