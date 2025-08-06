			<span style="position:relative;width: 240px">
				<input list="data-list" class="form-control form-control-sm" id="search" name="datalist" placeholder="Search..." autocomplete="off">
				<i class="fa-solid fa-circle-xmark x-clear" onclick="xClear()"></i>	  
				<div class="data-list" id="datalist">
					<div class="listdata" id="listdata"></div>
				</div>
			</span>
			<span style="margin-left:5px;width:240px">
				<select id="cluster" class="form-control form-control-sm" onchange="onchangeCluster()">
					<?php echo $functions->getCluster($cluster,$db)?>
				</select>
			</span>
			<span style="margin-left:5px;width:240px">
				<select id="branch" class="form-control form-control-sm" onchange="loadJournalDataBranch(this.value)">
					<?php echo $functions->getBranch($branch,$db)?>
				</select>
			</span>