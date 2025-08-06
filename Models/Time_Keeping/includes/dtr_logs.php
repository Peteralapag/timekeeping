<?php
include $_SERVER['DOCUMENT_ROOT'] . '/init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
?>

<style>
.inputboxes {
    display: flex;
    width: 700px;
}

.export-button {
    margin-left: auto; /* This will push the button to the right */
}

.small-notifs {
    display: none;
}

@media (max-width: 1472px) {
    .inputboxes {
        display: none;
    }
    .small-notifs {
        display: block;
    }
}
</style>


<div class="app-wrapper">
    <div class="title-header">
        <span><i class="fa fa-list-alt" aria-hidden="true"></i> Employee List &nbsp;</span>
        
        <div class="inputboxes">
            <span style="position:relative;width: 240px">
                <input list="data-list" class="form-control form-control-sm" id="search" name="datalist" placeholder="Search Employee..." autocomplete="off">
                <div class="data-list" id="datalist">
                    <div class="listdata" id="listdata"></div>
                </div>
            </span>
            <span style="margin-left:5px;width:240px">
                <select id="cluster" class="form-control form-control-sm">
                    <?php echo $functions->getCluster($cluster, $db) ?>
                </select>
            </span>
            <span style="margin-left:5px;width:240px; display:none" id="branchContainer">
                <select id="branch" class="form-control form-control-sm">
                    <!-- Branch options will be loaded here -->
                </select>
            </span>
        </div>
        
        <div class="export-button">
            <button class="btn btn-sm btn-success" id="exportExcel" style="float: right;">
                <i class="fa fa-file-excel" aria-hidden="true"></i> Export to Excel
            </button>
        </div>

    </div>

    <div class="page-wrapper tableFixHead" id="pagewrapper"></div>
</div>



<script>
$(function () {
    loadEmployees();

    $('#search').on('keyup', function () {
        let keyword = $(this).val();
        loadEmployees(keyword);
    });

    $('#cluster').on('change', function() {
        let clusterVal = $(this).val();

        if (clusterVal) {
            $('#branchContainer').show();
            $('#branch').prop('disabled', false);
            loadBranches(clusterVal);
        } else {
            $('#branchContainer').hide();
            $('#branch').prop('disabled', true);
        }

        loadEmployees();
    });
	
	$('#branch').on('change', function() {
		loadEmployees();
    });
	
	
	function loadBranches(clusterVal) {
		
		$('#branch').val('');
	    $.ajax({
	        url: './Models/Time_Keeping/includes/getBranchesByCluster.php',
	        type: 'POST',
	        data: { cluster: clusterVal },
	        success: function(data) {
	            if (data) {
	                $('#branch').html(data);
	                $('#branch').prop('disabled', false);
	            } else {
	                $('#branch').html('<option value="">No branches available</option>');
	                $('#branch').prop('disabled', true);
	            }
	        },
	        error: function() {
	            console.log("Error loading branches");
	        }
	    });
	}    

    function loadEmployees(search = '') {
        let cluster = $('#cluster').val();
        let branch = $('#branch').val();
        

        let data = { search: search };

        if (cluster) {
            data.cluster = cluster;
        }
        if (branch) {
            data.branch = branch;
        }

        $('#pagewrapper').load('./Models/Time_Keeping/includes/dtr_logs_data.php', data);
    }
    
    
});
</script>
