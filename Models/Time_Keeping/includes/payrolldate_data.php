<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$records_per_page = 25;
$offset = ($page - 1) * $records_per_page;

$query = "SELECT * FROM dtrv_datelock_checker ORDER BY payroll_date DESC, id DESC LIMIT $offset, $records_per_page";
$queryResults = $db->query($query);

$total_query = "SELECT COUNT(*) as total FROM dtrv_datelock_checker";
$total_result = $db->query($total_query)->fetch_assoc();
$total_records = $total_result['total'];
$total_pages = ceil($total_records / $records_per_page);
?>
<div id="tableFixHead">
    <table style="width: 100%" class="table table-bordered table-striped table-padding">
        <thead>
            <tr>
                <th style="text-align:center;width:50px;">#</th>
                <th>PAY DATE</th>
                <th>DATE FROM</th>
                <th>DATE TO</th>
                <th>CREATED BY</th>
                <th>LOCKED BY</th>
                <th>LOCK DATE/TIME</th>
                <th>STATUS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($queryResults->num_rows > 0): 
                $i = $offset;
                while ($row = $queryResults->fetch_assoc()):
                    $i++;
                    $rowid = $row['id'];
                    $date_created = !empty($row['created_date']) ? date("Y/m/d @h:i A", strtotime($row['created_date'])) : '';
                    $date_updated = !empty($row['updated_date']) ? date("Y/m/d @h:i A", strtotime($row['updated_date'])) : '';
                    $lock_status_icon = $row['lock_status'] == 1 ? '<i class="fa-solid fa-lock color-red"></i>' : '<i class="fa-solid fa-lock-open color-green"></i>';
                    $btn_dis = $row['lock_status'] == 1 ? 'disabled' : '';
                    $btn_text = $row['lock_status'] == 1 ? 'Locked' : 'Lock';
                    
                    $lockendicator = $row['lock_status'] == 1 ? 'background-color:#f3e8e8': '';
                    
                    
            ?>
            <tr style="<?php echo $lockendicator?>">
                <td style="text-align:center;font-weight:600"><?php echo $i; ?></td>
                <td><?php echo date('F d, Y', strtotime($row['payroll_date']))?></td>
                <td><?php echo date('M d', strtotime($row['payroll_from']))?></td>
                <td><?php echo date('M d',strtotime($row['payroll_to']))?></td>
                <td><?php echo $row['created_by']; ?></td>
                <td><?php echo $row['updated_by']; ?></td>
                <td><?php echo $date_updated; ?></td>
                <td style="text-align:center;width:50px;font-size:18px;padding:2px !important"><?php echo $lock_status_icon; ?></td>
                
                <td style="padding:0 !important; width:100px !important" class="container mt-5">
				    <div class="btn-group" role="group">
				        <button class="btn btn-primary btn-sm" onclick="setThis('<?php echo $rowid; ?>')">
				            <i class="fa-solid fa-bolt-lightning color-yellow"></i> Set
				        </button>
				        
				        <?php if ($row['lock_status'] == 0): ?>
				        
				        	<button <?php echo $btn_dis; ?> class="btn btn-info btn-sm color-white" onclick="lockThis('<?php echo $rowid; ?>')">
					            <i class="fa-solid fa-door-open color-red"></i> <?php echo $btn_text; ?>
					        </button>
				        
				            <button class="btn btn-danger btn-sm" onclick="deleteThis('<?php echo $rowid; ?>')">
				                <i class="fa-solid fa-trash color-white"></i> Del
				            </button>
				        <?php endif; ?>
				    </div>
				</td>
                
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="10" style="text-align:center">
                    <i class="fa fa-bell color-orange"></i> No Records.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <a href="#" class="pagination-link <?php echo ($page <= 1) ? 'disabled' : ''; ?>" data-page="1">FIRST</a>
        <a href="#" class="pagination-link <?php echo ($page <= 1) ? 'disabled' : ''; ?>" data-page="<?php echo $page - 1; ?>">PREVIOUS</a>

        <?php
        $range = 2;
        $start = max(1, $page - $range);
        $end = min($total_pages, $page + $range);
        for ($i = $start; $i <= $end; $i++):
            $active = ($i == $page) ? 'active' : '';
        ?>
        <a href="#" class="pagination-link <?php echo $active; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor;

        if ($end < $total_pages):
            if ($end < $total_pages - 1): echo '<span>...</span>'; endif;
            echo '<a href="#" class="pagination-link" data-page="' . $total_pages . '">' . $total_pages . '</a>';
        endif;
        ?>

        <a href="#" class="pagination-link <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>" data-page="<?php echo $page + 1; ?>">NEXT</a>
        <a href="#" class="pagination-link <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>" data-page="<?php echo $total_pages; ?>">LAST</a>
    </div>
</div>

<div id="results"></div>

<script>
function deleteThis(rowid) {
    var totalPages = <?php echo $total_pages; ?>;
    swal({
        title: "Delete",
        text: "Are you sure you want to delete this cutoff?",
        icon: "warning",
        buttons: ['No', 'Yes'],
        dangerMode: true,
    }).then(function(isConfirm) {
        if (isConfirm) {
            $.post("./Models/time_keeping/actions/actions.php", {
                mode: 'deletecutoff',
                rowid: rowid,
                totalPages: totalPages
            }, function(data) {
                $('#results').html(data);
            });
        }
    });
}

function setThis(rowid) {
    rms_reloaderOn("Locking...");
    setTimeout(function() {
        $.post("./Models/time_keeping/actions/actions.php", {
            mode: 'setpayrolldate',
            rowid: rowid
        }, function(data) {
            $('#results').html(data);
            rms_reloaderOff();
            swal("Payroll Cutoff", "Payroll cutoff has been selected", "success");
            payrollInfo();
        });
    }, 500);
}

function lockThis(rowid) {
    app_confirm("Locked", "Are you sure to lock this cutoff date?", "warning", "lockThisYes", rowid, "red");
    return false;
}

function lockThisYes(rowid) {
    var totalPages = <?php echo $total_pages; ?>;
    rms_reloaderOn("Locking...");
    setTimeout(function() {
        $.post("./Models/time_keeping/actions/actions.php", {
            mode: 'lockcutoff',
            rowid: rowid,
            totalPages: totalPages
        }, function(data) {
            $('#results').html(data);
            rms_reloaderOff();
            loadPage(totalPages);
        });
    }, 500);
}

function reloadPage(page) {
    $.post('./Models/Time_Keeping/includes/payrolldate_data.php', { page: page }, function(data) {
        $('#pagewrapper').html(data);
    });
}
</script>
