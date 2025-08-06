<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$search = isset($_POST['search']) ? $_POST['search'] : '';
$cluster = isset($_POST['cluster']) ? $_POST['cluster'] : '';
$branch = isset($_POST['branch']) ? $_POST['branch'] : '';

$sql = "SELECT id, idcode, acctname, position, branch, cluster FROM tbl_employees WHERE status = 'Active'";

$conditions = [];
$parameters = [];

$lastsearch = '';

if (!empty($search)) {
    $conditions[] = "acctname LIKE ?";
    $parameters[] = "%$search%";
}

if (!empty($cluster)) {
    $conditions[] = "cluster = ?";
    $parameters[] = $cluster;
    $lastsearch = 'cluster';
}

if (!empty($branch)) {
    $conditions[] = "branch = ?";
    $parameters[] = $branch;
    $lastsearch = 'branch';
}


if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}


$stmt = $db->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $db->error);
}

if (!empty($parameters)) {
    $types = str_repeat('s', count($parameters));
    $stmt->bind_param($types, ...$parameters);
}

$stmt->execute();
$result = $stmt->get_result();

$employees = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
$i = 0;

?>

<div class="dtrlogsdata">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Cluster</th>
                <th>Branch</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($employees)): ?>
                <?php foreach ($employees as $employee): $i++; $idcode = $employee['idcode']; $acctname = $employee['acctname'];?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td style="font-size: 13px;"><?php echo $acctname; ?></td>
                        <td><?php echo $employee['cluster']; ?></td>
                        <td><?php echo $employee['branch']; ?></td>
                        <td>
                            <span onclick="viewDtrLogs('<?php echo $idcode?>','<?php echo $acctname?>','<?php echo $lastsearch?>')" class="btn btn-sm btn-primary btn-view">
                                View DTR Logs
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No employees found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>



<script>

function viewDtrLogs(idcode,acctname,lastsearch) {
    
    $('#pagewrapper').html('<div>Loading DTR logs...</div>');

    let data = { idcode: idcode, acctname: acctname, lastsearch: lastsearch };

    $('#pagewrapper').load('./Models/Time_Keeping/includes/dtr_logs_view.php', data);
}

</script>
