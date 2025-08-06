<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (isset($_POST['payroll_date'])) {
    $_SESSION['PAYROLL_DATE'] = $_POST['payroll_date'];
}

if (isset($_POST['payroll_from'])) {
    $_SESSION['PAYROLL_FROM'] = $_POST['payroll_from'];
}

if (isset($_POST['payroll_to'])) {
    $_SESSION['PAYROLL_TO'] = $_POST['payroll_to'];
}

if (isset($_POST['cluster'])) {
    $_SESSION['PAYROLL_CLUSTER'] = $_POST['cluster'];
}

if (isset($_POST['branch'])) {
    $_SESSION['PAYROLL_BRANCH'] = $_POST['branch'];
}

if (isset($_SESSION['PAYROLL_DATE'])) {
    $payroll_date = $_SESSION['PAYROLL_DATE'];
    $q = "AND payroll_date='$payroll_date'";
} else {
	$q='';
}
/* ######################################################################################## */
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$records_per_page = 25;
$offset = ($page - 1) * $records_per_page;

$conditions = [];

if (isset($_POST['acctname']) && !empty($_POST['acctname'])) {
    $acctname = mysqli_real_escape_string($db, $_POST['acctname']);
    $conditions[] = "acctname='$acctname' $q";
}
else if (isset($_POST['cluster']) && !empty($_POST['cluster'])) {
    $cluster = mysqli_real_escape_string($db, $_POST['cluster']);
    $conditions[] = "cluster='$cluster' $q";
}
else if (isset($_POST['branch']) && !empty($_POST['branch'])) {
    $branch = mysqli_real_escape_string($db, $_POST['branch']);
    $conditions[] = "branch='$branch' $q";
} else {
	$conditions[] = "payroll_date='$payroll_date'";;
}

$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
$QUERY = "SELECT * FROM tbl_journal $where_clause LIMIT $offset, $records_per_page";
$QUERYResults = mysqli_query($db, $QUERY);
// echo $QUERY;
$total_records_query = "SELECT COUNT(*) as total FROM tbl_journal $where_clause";
$total_result = mysqli_query($db, $total_records_query);
$total_records = mysqli_fetch_assoc($total_result)['total'];

$total_pages = ceil($total_records / $records_per_page);

$session_start = !empty($_SESSION['PAYROLL_DATE']) ? 1 : 0;
/* ######################################################################################## */
?>
<style>
.numpos {text-align:center;}
.approved {background-color: green !important;color: white;}
.pending {background-color: red !important;	color: white;}
</style>
<div id="tableFixHead">
    <table style="width: 90%" class="table table-bordered table-striped table-padding table-hover">
        <thead>
            <tr>
                <th style="text-align:center;width:70px !important;">#</th>
                <th>VIEW DTR</th>
                <th>EMPLOYEE</th>
                <th>COMPANY</th>
                <th>CLUSTER</th>
                <th>BRANCH</th>
                <th>POSITION</th>
                <th>WORKING DAYS</th>
                <th>DAYS WORKED</th>
                <th>LATE (Min)</th>
                <th>UNDERTIME (Min)</th>
                <th>OVERTIME (HR)</th>
                <th>DUTYREST</th>
                <th>NIGHTDIFF</th>
                <th>NIGHTDIFF DR</th>
                <th>NIGHTDIFF HOL</th>
                <th>LEAVE</th>
            </tr>
        </thead>
        <tbody>
<?php
if($session_start == 1)
{
if ($QUERYResults->num_rows > 0) {
    $i = $offset;
    while ($ROWS = mysqli_fetch_array($QUERYResults))
    {
        $i++;
        $rowid = $ROWS['id'];
        $idcode = $ROWS['idcode'];
        $acctname = $ROWS['acctname'];
        $position = $functions->getEmployeeInfo($idcode,'position',$db);
        $leave_approved = $ROWS['leave_approved'];
        if($ROWS['nightdiff_dr_hours'] > 0)
        {
        	$nightdiff_dr_hours = $ROWS['nightdiff_dr_hours'];
        	$nightdiff_reg_hours = '0.00';
        } else {
        	$nightdiff_reg_hours = $ROWS['nightdiff_reg_hours'];
        	$nightdiff_dr_hours = '0.00';
        }
?>
                <tr ondblclick="viewAttendannce('<?php echo $idcode?>','<?php echo $acctname?>')">
                    <td style="text-align:center;font-weight:600;width: 70px !important"><?php echo $i; ?></td>
                    <td style="padding:0 !important"><button class="btn btn-primary btn-sm w-100" onclick="viewAttendannce('<?php echo $idcode?>','<?php echo $acctname?>')">View <i class="fa-solid fa-eye"></i></button></td>
                    <td><?php echo $ROWS['acctname']; ?></td>
                    <td><?php echo $ROWS['company']; ?></td>
                    <td><?php echo $ROWS['cluster']; ?></td>
                    <td><?php echo $ROWS['branch']; ?></td>
                    <td><?php echo $position; ?></td>
                    <td class="numpos"><?php echo $ROWS['working_days']; ?></td>
                    <td class="numpos"><?php echo $ROWS['days_worked']; ?></td>
                    <td class="numpos"><?php echo $ROWS['late_minutes']; ?></td>
                    <td class="numpos"><?php echo $ROWS['under_time']; ?></td>
                    <td class="numpos"><?php echo $ROWS['over_time']; ?></td>
                    <td class="numpos"><?php echo $ROWS['duty_rest']; ?></td>
                    <td class="numpos"><?php echo $nightdiff_reg_hours; ?></td>
                    <td class="numpos"><?php echo $nightdiff_dr_hours; ?></td>
                    <td class="numpos"><?php echo $ROWS['nightdiff_hol_hours']; ?></td>
                    <td class="numpos"><?php echo $ROWS['leave_with_pay']; ?></td>
                </tr>
<?php
    }
} else {
?>
                <tr>
                    <td colspan="17" style="text-align:center"><i class="fa fa-bell color-orange"></i> No Records.</td>
                </tr>
<?php } ?>
<?php } else { ?>        
		<tr>
                    <td colspan="17" style="text-align:center"><i class="fa fa-bell color-orange"></i> No payroll date has been chosen.</td>
        </tr>
<?php } ?>
        </tbody>
    </table>
<!-- ######################################################################################################## -->
<?php
if($session_start == 1)
{
?>
<div class="pagination">
        <a href="#" class="pagination-link <?php echo ($page <= 1) ? 'disabled' : ''; ?>" data-page="1">FIRST</a>
        <a href="#" class="pagination-link <?php echo ($page <= 1) ? 'disabled' : ''; ?>" data-page="<?php echo $page - 1; ?>">PREVIOUS</a>
<?php
	$range = 2;
	$start_page = max(1, $page - $range);
	$end_page = min($total_pages, $page + $range);
	
	for ($i = $start_page; $i <= $end_page; $i++)
	{
	    if ($i == $page)
	    {
	        echo '<a href="#" class="pagination-link active" data-page="' . $i . '">' . $i . '</a>';
	    } else {
	        echo '<a href="#" class="pagination-link" data-page="' . $i . '">' . $i . '</a>';
	    }
	}
	if ($end_page < $total_pages)
	{
	    if ($end_page < $total_pages - 1)
	    {
	        echo '<span>...</span>';
	    }
	    echo '<a href="#" class="pagination-link" data-page="' . $total_pages . '">' . $total_pages . '</a>';
	}
?>
        <a href="#" class="pagination-link <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>" data-page="<?php echo $page + 1; ?>">NEXT</a>
        <a href="#" class="pagination-link <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>" data-page="<?php echo $total_pages; ?>">LAST</a>
    </div>
<!-- ######################################################################################################## -->    
</div>
<?php } ?>
<div id="results"></div>
<script>
function viewAttendannce(idcode,acctname)
{	


	$('#modalicon').html('<i class="fa-solid fa-clock color-red"></i>');
	$('#modaltitle').html(acctname);
	$.post("./Models/time_keeping/apps/attendance.php", { idcode: idcode }, function(data) {
        $('#formmodal_page').html(data);
        $('#formmodal').show();
    });

}
function lockThis(rowid) {
    app_confirm("Locked", "Are you sure to lock this cutoff date?", "warning", "lockThisYes", rowid, "red");
    return false;
}
var totalPages = <?php echo $total_pages; ?>; 
function lockThisYes(rowid)
{
    const mode = 'lockcutoff';
    var totalPages = <?php echo $total_pages; ?>; 
    rms_reloaderOn("Locking...");
    setTimeout(function() {
        $.post("./Models/time_keeping/actions/actions.php", { mode: mode, rowid: rowid, totalPages: totalPages }, function(data) {
            $('#results').html(data);
            rms_reloaderOff();
            loadPage(totalPages); // Reload the first page after locking
        });
    }, 500);
}
function reloadPage(page) {
    $.post('./Models/Time_Keeping/includes/payrolldate_data.php', { page: page }, function(data) {
        $('#pagewrapper').html(data);
    });
}
$(function()
{
	payrollInfo();
});
</script>
