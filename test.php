<?php
function GetNightDifferentialHours($time_in, $time_out)
{
    $shift_start_date = date('Y-m-d', $time_in);
    $night_start = strtotime($shift_start_date . ' 22:00');
    $night_end = strtotime($shift_start_date . ' +1 day 07:00');
    
    // Initialize the night differential time in seconds
    $night_diff_seconds = 0;

    // SCENARIO 1: Clocked in BEFORE 10 PM and worked into night differential hours
    if ($time_in < $night_start && $time_out > $night_start) {
        $end_time = min($time_out, $night_end);
        $night_diff_seconds = max(0, $end_time - $night_start);

    // SCENARIO 2: Clocked in ON or AFTER 10 PM AND BEFORE MIDNIGHT
    } elseif ($time_in >= $night_start && $time_in < strtotime($shift_start_date . ' +1 day 00:00')) {
        $end_time = min($time_out, $night_end);
        $night_diff_seconds = max(0, $end_time - $time_in);

    // SCENARIO 3: Clocked in AFTER MIDNIGHT and before 7 AM
    } elseif ($time_in >= strtotime($shift_start_date . ' 00:00') && $time_in < $night_end) {
        // Set timeout to 7 AM as the end of the night differential period
        $timeout_date = date("Y-m-d", $time_out);
        $timeout = strtotime($timeout_date . ' 07:00');
        
        $end_time = min($timeout, $night_end);
        $night_diff_seconds = max(0, $end_time - $time_in);
    }

    // Convert seconds to full hours by pagsasahig
    $night_diff_hours = floor($night_diff_seconds / 3600);
    return $night_diff_hours;
}

// Test scenarios
$scenario1 = GetNightDifferentialHours(strtotime('2024-10-22 21:20'), strtotime('2024-10-23 07:37'));
$scenario2 = GetNightDifferentialHours(strtotime('2024-10-22 22:10'), strtotime('2024-10-23 10:30'));
$scenario3 = GetNightDifferentialHours(strtotime('2024-10-23 06:42'), strtotime('2024-10-23 16:03'));

// Display results
echo "Scenario 1 Night Differential Hours: " . $scenario1 . " hours<br>";
echo "Scenario 2 Night Differential Hours: " . $scenario2 . " hours<br>";
echo "Scenario 3 Night Differential Hours: " . $scenario3 . " hours<br>";
?>
