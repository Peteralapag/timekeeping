<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$payroll_date = $_SESSION['PAYROLL_DATE'];
?>
<style>
.datalistings {list-style-type: none;margin:0;padding:0;}
.datalistings li {background: #f6f6f6;font-size: 12px;border-bottom: 1px solid #aeaeae;padding: 5px;cursor: pointer;}
.datalistings li:last-child {border-bottom:0;}
.datalistings li:hover {background: #f1f1f1;}
</style>
<ul class="datalistings">
<?php
	$search = $db->real_escape_string($_POST['search']);
    $query = "SELECT * FROM tbl_journal WHERE cluster LIKE '%$search%' OR acctname LIKE '%$search%' AND payroll_date='$payroll_date' ORDER BY acctname ASC LIMIT 25";
    $results = $db->query($query);

    if ($results->num_rows > 0) {
        while ($ROW = mysqli_fetch_array($results))
        {
        	$rowid = $ROW['id'];
        	$retVal = $ROW['acctname'];
?>
	<li onclick="passInfo('<?php echo $rowid?>','<?php echo $retVal?>')"><i class="fa-solid fa-square text-primary"></i>&nbsp;&nbsp;<?php echo $retVal?></li>
<?php

        }
    } else {
        echo '<li>No Employee Found.</li>';
    }

?>

</ul>