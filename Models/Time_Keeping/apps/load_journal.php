<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$cutoff_date = $_SESSION['PAYROLL_DATE'] ?? '';
$cutoff_from = $_SESSION['PAYROLL_FROM'] ?? '';
$cutoff_to = $_SESSION['PAYROLL_TO'] ?? '';
$cluster = $_SESSION['PAYROLL_CLUSTER'] ?? '';
$branch = $_SESSION['PAYROLL_BRANCH'] ?? '';
?>
<table style="width: 350px; white-space: nowrap" class="table table-style">
    <tr>
        <th style="width: 100px">Payroll Date</th>
        <td><input id="cutoff_date" type="date" class="form-control form-control-sm" value="<?php echo $cutoff_date; ?>"></td>
    </tr>
    <tr>
        <th>Cutoff From</th>
        <td><input id="cutoff_from" type="date" class="form-control form-control-sm" value="<?php echo $cutoff_from; ?>" disabled></td>
    </tr>
    <tr>
        <th>Cutoff To</th>
        <td><input id="cutoff_to" type="date" class="form-control form-control-sm" value="<?php echo $cutoff_to; ?>" disabled></td>
    </tr>
</table>
<div style="text-align: right">
    <button class="btn btn-primary btn-sm" onclick="proceedJournal()"><i class="fa-solid fa-arrow-right"></i> Proceed</button>
</div>
<div class="results"></div>

<script>
function proceedJournal() {
    const mode = 'savetimekeepingjournal';
    const cutoff_date = $('#cutoff_date').val();
    const cutoff_from = $('#cutoff_from').val();
    const cutoff_to = $('#cutoff_to').val();
    const cluster = $('#cluster').val();
    const branch = $('#branch').val();

    if (cutoff_date === '') {
        swal("Cutoff Date", "Please select a valid 15th or 30th day of the month", "error");
        return false;
    }
    if (cutoff_from === '') {
        swal("Cutoff From", "Please ensure the cutoff period 'From' date is set", "error");
        return false;
    }
    if (cutoff_to === '') {
        swal("Cutoff To", "Please ensure the cutoff period 'To' date is set", "error");
        return false;
    }
    rms_reloaderOn("Chading Date...");
    setTimeout(function() {
        $.post("./Models/Time_Keeping/actions/actions.php", { mode: mode, cutoff_date: cutoff_date, cutoff_from: cutoff_from, cutoff_to: cutoff_to },
        function(data) {
            $('.results').html(data);
            rms_reloaderOff();
            window.location.reload();  
        });
    }, 1000);
}

function setCutoffPeriod(cutoffDate) {
    var date = new Date(cutoffDate);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();

    if (month < 10) {
        month = '0' + month;
    }

    var cutoffFrom, cutoffTo;

    if (day === 30) {
        cutoffFrom = year + '-' + month + '-06';
        cutoffTo = year + '-' + month + '-20';
    } else if (day === 15) {
        var previousMonth = month - 1;
        var previousYear = year;
        if (previousMonth === 0) {
            previousMonth = 12;
            previousYear--;
        }

        if (previousMonth < 10) {
            previousMonth = '0' + previousMonth;
        }

        cutoffFrom = previousYear + '-' + previousMonth + '-21';
        cutoffTo = year + '-' + month + '-05';
    } else {
        swal("Invalid Cutoff Date", "Please select the 15th or 30th day of the month", "error");
        return false;
    }
    document.getElementById('cutoff_from').value = cutoffFrom;
    document.getElementById('cutoff_to').value = cutoffTo;
}

function getCurrentDate() {
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth() + 1;
    var day = today.getDate();

    var cutoffDay = day <= 15 ? 15 : 30;

    if (month < 10) {
        month = '0' + month;
    }

    var formattedDate = year + '-' + month + '-' + (cutoffDay < 10 ? '0' + cutoffDay : cutoffDay);

    document.getElementById('cutoff_date').value = formattedDate;

    setCutoffPeriod(formattedDate);
}

$(function() {
    var pdate = '<?php echo $cutoff_date; ?>';

    if (pdate === '' || pdate === null) {
        getCurrentDate();
    } else {
        setCutoffPeriod(pdate);
    }

    $('#cutoff_date').on('change blur', function() {
        var selectedDate = this.value;
        setCutoffPeriod(selectedDate);
    });
});
</script>
