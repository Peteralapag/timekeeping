<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (isset($_POST['cluster'])) {
    $cluster = $_POST['cluster'];


    $query = "SELECT branch FROM tbl_branch WHERE location = ?";
    $stmt = $db->prepare($query);

    if (!$stmt) {
        echo "Error preparing statement: " . $db->error;
        exit;
    }

    $stmt->bind_param("s", $cluster);
    if (!$stmt->execute()) {
        echo "Error executing query: " . $stmt->error;
        exit;
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<option value=''>Select Branch</option>";

        while ($row = $result->fetch_assoc()) {

            $branch = htmlspecialchars($row['branch'], ENT_QUOTES, 'UTF-8');
            echo "<option value='" . $branch . "'>" . $branch . "</option>";
        }
    } else {
        echo "<option value=''>No branches available</option>";
    }


    $stmt->close();
} else {
    echo "Cluster value not received.";
}

$db->close();
?>
