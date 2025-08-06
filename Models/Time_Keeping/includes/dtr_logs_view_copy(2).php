<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$idcode = $_POST['idcode'] ?? '';
$acctname = $_POST['acctname'] ?? '';
$lastsearch = $_POST['lastsearch'] ?? '';

$from = $_SESSION['PAYROLL_FROM'] ?? '';
$to = $_SESSION['PAYROLL_TO'] ?? '';
$payrollDate = $_SESSION['PAYROLL_DATE'] ?? '';

if (empty($from) || empty($to)) {
    echo "<div class='alert alert-danger'>Timekeeping setting is empty. Please configure the payroll dates.</div>";
} else {
    if ($idcode && $from && $to) {

        $sql = "
            SELECT dtr.trans_date, dtr.time_in, dtr.time_out, sched.shift_in, sched.shift_out, 
                   dtr.manually_added, dtr.updated_by, dtr.approved_by
            FROM tbl_dtr_logs dtr
            LEFT JOIN tbl_schedule_dta sched 
                ON dtr.idcode = sched.idcode AND dtr.trans_date = sched.working_date
            WHERE dtr.idcode = ? 
            AND dtr.trans_date BETWEEN ? AND ? 
            ORDER BY dtr.trans_date ASC
        ";

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $db->error);
        }

        $stmt->bind_param("sss", $idcode, $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<div class='card-header bg-primary text-white d-flex justify-content-between align-items-center'>";
        echo "<div>";
        echo "<h5 class='mb-0'>DTR Logs for <span class='fw-bold'>$acctname</span> (ID: <span class='fw-bold'>$idcode</span>)</h5>";
        echo "<small>Period: <strong>" . date("M d, Y", strtotime($from)) . "</strong> to <strong>" . date("M d, Y", strtotime($to)) . "</strong></small>";
        if (!empty($payrollDate)) {
            echo "<div><strong>Payroll Date:</strong> " . date("M d, Y", strtotime($payrollDate)) . "</div>";
        }
        echo "</div>";
        echo "<button class='btn btn-sm btn-light text-dark fw-semibold' onclick='goBack(" . json_encode($lastsearch) . ")'>⟵ Back</button>";
        echo "</div>";

        if ($result->num_rows > 0) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-hover table-sm'>";
            echo "<thead class='table-light'><tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Schedule In</th><th>Schedule Out</th></tr></thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) {
                $date = date("F d, Y", strtotime($row['trans_date']));
                $timeIn = !empty($row['time_in']) ? date("g:i A", strtotime($row['time_in'])) : '--';
                $timeOut = !empty($row['time_out']) ? date("g:i A", strtotime($row['time_out'])) : '--';
                $shiftIn = !empty($row['shift_in']) ? date("g:i A", strtotime($row['shift_in'])) : '--';
                $shiftOut = !empty($row['shift_out']) ? date("g:i A", strtotime($row['shift_out'])) : '--';
                $manuallyAdded = $row['manually_added'];
                $updatedBy = $row['updated_by'] ?? '—';
                $approvedBy = $row['approved_by'] ?? '—';

                // Dynamically style the row based on manual additions
                $rowClass = ($manuallyAdded == 1) ? 'style="background-color: #d6eef3;" title="Created By: ' . $updatedBy . ' | Approved By: ' . $approvedBy . '"' : '';

                echo "<tr $rowClass><td>$date</td><td>$timeIn</td><td>$timeOut</td><td>$shiftIn</td><td>$shiftOut</td></tr>";
            }

            echo "</tbody></table></div>";
        } else {
            echo "<div class='alert alert-warning text-center mb-0'>No DTR logs found for this employee during the selected payroll period.</div>";
        }

        echo "</div></div>";
    } else {
        echo "<div class='alert alert-danger'>Missing ID code or payroll date range.</div>";
    }
}
?>

<script>

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
