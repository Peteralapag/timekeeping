<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$idcode = $_POST['idcode'] ?? '';
$acctname = $_POST['acctname'] ?? '';
$lastsearch = $_POST['lastsearch'] ?? '';

$from = $_SESSION['PAYROLL_FROM'] ?? '';
$to = $_SESSION['PAYROLL_TO'] ?? '';
$payrollDate = $_SESSION['PAYROLL_DATE'] ?? '';
$payrollPeriod = $_SESSION['PAYROLL_PERIOD'] ?? '';

if (empty($from) || empty($to)) {
    showAlert('danger', 'Timekeeping setting is empty. Please configure the payroll dates.');
    exit;
}

if (!$idcode) {
    showAlert('danger', 'Missing ID code or payroll date range.');
    exit;
}

$rows = fetchDtrLogs($db, $idcode, $from, $to, $payrollPeriod);
renderHeader($acctname, $idcode, $from, $to, $payrollDate, $lastsearch);
renderTable($rows);

function fetchDtrLogs($db, $idcode, $from, $to, $payrollPeriod)
{
    
    $sql = "
	    SELECT dtr.trans_date, dtr.time_in, dtr.time_out, 
	           sched.shift_in, sched.shift_out, 
	           dtr.manually_added, dtr.updated_by, dtr.approved_by
	    FROM tbl_dtr_logs dtr
	    LEFT JOIN tbl_schedule_dta sched 
	        ON dtr.idcode = sched.idcode AND dtr.trans_date = sched.working_date
	    WHERE dtr.idcode = ? 
	      AND dtr.trans_date BETWEEN ? AND ? 
	      AND (dtr.manually_added = 0 OR dtr.approved = 1)
	      AND (sched.payroll_period = ? OR sched.payroll_period IS NULL)
	    ORDER BY dtr.trans_date ASC
	";


    $stmt = $db->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $db->error);
    }

    $stmt->bind_param("ssss", $idcode, $from, $to, $payrollPeriod);
    $stmt->execute();
    return $stmt->get_result();
}

function renderHeader($acctname, $idcode, $from, $to, $payrollDate, $lastsearch)
{
    echo "<div class='card-header bg-primary text-white d-flex justify-content-between align-items-center'>";
    echo "<div>";
    echo "<h5 class='mb-0 d-flex align-items-center gap-2'>";
    echo "DTR Logs for <span class='fw-bold'>$acctname</span> ";
    echo "(ID: <span class='fw-bold'>$idcode</span>)";
    echo "<button type='button' class='btn btn-sm btn-outline-light ms-2' onclick='viewSchedule(\"$idcode\")'>View My Schedule</button>";
    echo "</h5>";
    echo "<small>Period: <strong>" . date("M d, Y", strtotime($from)) . "</strong> to <strong>" . date("M d, Y", strtotime($to)) . "</strong></small>";
    if (!empty($payrollDate)) {
        echo "<div><strong>Payroll Date:</strong> " . date("M d, Y", strtotime($payrollDate)) . "</div>";
    }
    echo "</div>";
    echo "<button class='btn btn-sm btn-light text-dark fw-semibold' onclick='goBack(" . json_encode($lastsearch) . ")'>⟵ Back</button>";
    echo "</div>";
}

function renderTable($result)
{
    if ($result->num_rows === 0) {
        showAlert('warning', 'No DTR logs found for this employee during the selected payroll period.', true);
        return;
    }

    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered table-hover table-sm'>";
    echo "<thead class='table-primary text-white'>
            <tr>
                <th>#</th><th>Date</th><th>Time In</th><th>Time Out</th><th>Schedule In</th><th>Schedule Out</th>
            </tr>
        </thead>";
    echo "<tbody>";

    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $date = date("F d, Y", strtotime($row['trans_date']));
        $timeIn = !empty($row['time_in']) ? date("g:i A", strtotime($row['time_in'])) : '--';
        $timeOut = !empty($row['time_out']) ? date("g:i A", strtotime($row['time_out'])) : '--';
        $shiftIn = !empty($row['shift_in']) ? date("g:i A", strtotime($row['shift_in'])) : '--';
        $shiftOut = !empty($row['shift_out']) ? date("g:i A", strtotime($row['shift_out'])) : '--';
        $rowClass = '';

        if ($row['manually_added'] == 1) {
            $updatedBy = $row['updated_by'] ?? '—';
            $approvedBy = $row['approved_by'] ?? '—';
            $rowClass = 'style="background-color: #d6eef3;" title="Created By: ' . $updatedBy . ' | Approved By: ' . $approvedBy . '"';
        }

        echo "<tr $rowClass>
                <td>$i</td><td>$date</td><td>$timeIn</td><td>$timeOut</td><td>$shiftIn</td><td>$shiftOut</td>
              </tr>";
        $i++;
    }

    echo "</tbody></table></div>";
}

function showAlert($type, $message, $center = false)
{
    $centerClass = $center ? ' text-center mb-0' : '';
    echo "<div class='alert alert-$type$centerClass'>$message</div>";
}
?>



<script>
function viewSchedule(idcode) {
	psaSpinnerOn();
    $('#modaltitle').html("My Schedule");
    $.post("./Models/Time_Keeping/apps/view_my_schedule.php", { idcode: idcode }, function(data) {
        $('#formmodal_page').html(data);
        $('#formmodal').show();
        psaSpinnerOff();
    });
}

function goBack(lastsearch) {
    var data = {};
    if (lastsearch === 'cluster' && $('#cluster').length) {
        data.cluster = $('#cluster').val();
    }
    if (lastsearch === 'branch' && $('#branch').length) {
        data.branch = $('#branch').val();
    }
    $('#pagewrapper').load('./Models/Time_Keeping/includes/dtr_logs_data.php', data);
}
</script>
