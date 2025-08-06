<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Initialize session variables
$payroll_date = $_SESSION['PAYROLL_DATE'] ?? '';
$payroll_from = $_SESSION['PAYROLL_FROM'] ?? '';
$payroll_to = $_SESSION['PAYROLL_TO'] ?? '';
$cluster = $_SESSION['PAYROLL_CLUSTER'] ?? '';
$branch = $_SESSION['PAYROLL_BRANCH'] ?? '';

$schedule_columns = "idcode, acctname, payroll_date, payroll_from, payroll_to, payroll_period, working_date, shift_code, shift_in, shift_out, time_sched, daytype, daytype_code, location, branch";
$query = $db->prepare("SELECT $schedule_columns FROM tbl_schedule_dta WHERE payroll_date=? AND acctname!=',' AND idcode!=''");
$query->bind_param("s", $payroll_date);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'idcode' => $row['idcode'],
            'acctname' => $row['acctname'],
            'payroll_date' => $row['payroll_date'],
            'pay_period' => $row['payroll_period'],
            'trans_date' => $row['working_date'],
            'shifting_code' => $row['shift_code'],
            'shift_timein' => $row['shift_in'],
            'shift_timeout' => $row['shift_out'],
            'shifting' => $row['time_sched'],
            'day_type' => $row['daytype'],
            'day_type_code' => $row['daytype_code'],
            'location' => $row['location'],
            'branch' => $row['branch']
        ];
    }
    pushToDTR($data, $db);
} else {
    echo "NO SCHEDULE FOUND";
}

function pushToDTR($data, $db) {
    $values = [];
    $cnt = count($data);

    foreach ($data as $row) {
        // Prepare data for insertion
        $checkQuery = $db->prepare("SELECT * FROM tbl_dtr WHERE payroll_date=? AND trans_date=? AND idcode=?");
        $checkQuery->bind_param("sss", $row['payroll_date'], $row['trans_date'], $row['idcode']);
        
        if ($checkQuery->execute()) {
            $checkResult = $checkQuery->get_result();

            if ($checkResult->num_rows === 0) {
                $values[] = "('" 
                				. $db->real_escape_string($row['idcode']) . "', '" . 
                                $db->real_escape_string($row['acctname']) . "', '" . 
                                $db->real_escape_string($row['payroll_date']) . "', '" . 
                                $db->real_escape_string($row['pay_period']) . "', '" . 
                                $db->real_escape_string($row['trans_date']) . "', '" . 
                                $db->real_escape_string($row['shifting_code']) . "', '" . 
                                $db->real_escape_string($row['shift_timein']) . "', '" . 
                                $db->real_escape_string($row['shift_timeout']) . "', '" . 
                                $db->real_escape_string($row['shifting']) . "', '" . 
                                $db->real_escape_string($row['day_type']) . "', '" . 
                                $db->real_escape_string($row['day_type_code']) . "', '" . 
                                $db->real_escape_string($row['location']) . "', '" . 
                                $db->real_escape_string($row['branch']) . "')";
            }
        } else {
            echo $db->error;
        }
    }

    if (count($values) > 0) {
        insertToDTR($values, $db);
    } else {
//        echo '<script>showFinish("schedule","Schedule is already fetched","btn-danger");</script>';
    }
}

function insertToDTR($values, $db) {
    $sqlInsert = "INSERT INTO tbl_dtr (idcode, acctname, payroll_date, pay_period, trans_date, shifting_code, shift_timein, shift_timeout, shifting, day_type, day_type_code, location, branch) VALUES " . implode(', ', $values);
    
    if ($db->query($sqlInsert) === TRUE) {
//        echo '<script>showFinish("schedule","SCHEDULE: Finished!","btn-success");</script>';
    } else {
        echo $db->error . "<br>";
    }
}
