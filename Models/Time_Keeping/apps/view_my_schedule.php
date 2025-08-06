<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Input handling
$idcode = $_POST['idcode'] ?? '';
$datefrom = $_SESSION['PAYROLL_FROM'] ?? '';
$dateto = $_SESSION['PAYROLL_TO'] ?? '';
$payrollDate = $_SESSION['PAYROLL_DATE'] ?? '';

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

?>

<style>
    table {
        font-family: 'Inter', sans-serif;
    }
</style>

<table class="table table-bordered table-hover table-sm" style="width:700px">
  <thead class="text-white" style="background:#2baafa">
    <tr>
      <th>#</th>
      <th>Date</th>
      <th>Sched. In</th>
      <th>Sched. Out</th>
      <th>Day Type</th>
    </tr>
  </thead>
  <tbody>

<?php
if ($idcode && $datefrom && $dateto && $payrollDate) {
    $sql = "SELECT working_date, shift_in, shift_out, daytype 
            FROM tbl_schedule_dta 
            WHERE idcode = ? 
              AND payroll_date = ? 
              AND payroll_from = ? 
              AND payroll_to = ?
            ORDER BY working_date ASC";

    $stmt = $db->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssss", $idcode, $payrollDate, $datefrom, $dateto);
        $stmt->execute();
        $result = $stmt->get_result();

        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $i++;
            $date = date("M d, Y", strtotime($row['working_date']));
            $shiftIn = !empty($row['shift_in']) ? date("g:i A", strtotime($row['shift_in'])) : '--';
            $shiftOut = !empty($row['shift_out']) ? date("g:i A", strtotime($row['shift_out'])) : '--';
            $dayType = htmlspecialchars($row['daytype']);

            echo "<tr>";
            echo "<td>{$i}</td>";
            echo "<td>{$date}</td>";
            echo "<td>{$shiftIn}</td>";
            echo "<td>{$shiftOut}</td>";
            echo "<td>{$dayType}</td>";
            echo "</tr>";
        }

        if ($i === 0) {
            echo "<tr><td colspan='5' class='text-center text-muted'>No schedule found for the given period.</td></tr>";
        }

        $stmt->close();
    } else {
        echo "<tr><td colspan='5' class='text-danger'>Query preparation failed: {$db->error}</td></tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-danger'>Missing required parameters.</td></tr>";
}
?>

  </tbody>
</table>
