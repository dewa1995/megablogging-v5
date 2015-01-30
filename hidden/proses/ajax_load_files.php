<?PHP
include(dirname(dirname(dirname(__FILE__)))."/config.php");
include(dirname(dirname(__FILE__))."/_session.php");
include(dirname(dirname(__FILE__))."/anti_xss.php");
error_reporting(0);
$max_per_table = $c_max_per_table;
$total = $db->num_rows("select id from files where user='$admin_id'");
if ($total == 0){
	echo "No Data Found!";
	exit();
}
if (isset($_POST['action'])){
	$action = $_POST['action'];
	$action_val = '"'."$action".'"';
}else{
	$action = 'all_files';
	$action_val = '"all_files"';
}
if (isset($_POST['q'])){
	$q = $_POST['q'];
	$q_val = '"'.$q.'"';
}else{
	$q = "";
}
if (empty($q)){
	$action = 'all_files';
	$action_val = '"all_files"';
}
?>
			<table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Filename</th>
					  <th>Date Uploaded</th>
					  <th>Hits</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
				  <!-- mulai dari sini untuk type get filesnya seperti apa -->
				  <?PHP
				  if ($action == "all_files"){
				  ?>
				  <?PHP
					if(isset($_POST["page"])){
						$page = abs((int)$_POST["page"]);
						$no = 1 + $max_per_table * $page - $max_per_table;
					}
					else{
						$page = 1;
						$no = 1;
					}
					$calc = $max_per_table * $page;
					$start = $calc - $max_per_table;
					$data_art = $db->select('files', "id, real_filename, hits, date, time", "user='$admin_id'", "files.id DESC", "$start, $max_per_table");
					foreach ($data_art as $data){
				  ?>
                   <tr>
                      <td><?PHP echo $no; ?></td>
                      <td><?PHP echo $data['real_filename']; ?></td>
					  <td><?PHP echo tanggalIndonesiaString($data['date'])." - ".$data['time']; ?></td>
					  <td><?PHP echo $data['hits']." Times"; ?></td>
                      <td>
					  <div class="btn-group">
						  <a target='_blank' href="<?PHP echo "$c_go_url/files/$data[id]"; ?>" class="btn btn-xs btn-primary" title='View Detail'> <i class="fa fa-search"></i> </a> 
						  <a href="files.mgb?act=delete&id=<?PHP echo $data['id']; ?>" class="btn btn-xs btn-danger"> <i class="fs fs-remove" title='Delete This files'></i> </a> 
					  </div>
					  </td>
                    </tr>
				   <?PHP
				    $no++;
					}
				   ?>
                  </tbody>
                </table>
							<div class="bp-docs-example" style='margin-top:-30px;cursor:pointer;'>
									<div class="pagination">
									<ul class='pagination'>
										<?PHP
										if(isset($page)){
										$totalPages = ceil($total / $max_per_table);
										if ($totalPages == 0){
										$totalPages = 1;
										}
										$show_page = 7;
										$i=1;
										if($page <=1 ){
										echo "<li class='active'><a title='NOW IS FIRTS PAGE' class='tipsy-atas'>Firts</a></li>";
										}
										else{
										$j = $page - 1;
										echo "<li><a title='GOTO FIRTS PAGE' class='tipsy-atas' onclick='changePagination(1, $action_val)'>Firts</a></li>";
										}
										
										if ($page >= $show_page){
										$total_prev = $page - 3; #4 5 6 7 8 9 10
										$total_next = $page + 3; #10
										if ($total_next >= $totalPages){
										$total_next = $totalPages;
										$total_prev = $total_next - 6;
										}
										$i = $total_prev;
										while ($i <= $total_next){
										if($i<>$page){
										echo "<li><a title='GOTO PAGE $i' class='tipsy-atas' onclick='changePagination($i, $action_val)'>$i</a></li>";
										}
										else{
										echo "<li class='active'><a title='NOW IS PAGE $i' class='tipsy-atas'>$i</a></li>";
										}
										$i++;
										}
										}else{
										while($i <= $show_page and $i < $totalPages + 1){
										
										if($i<>$page){
										echo "<li><a title='GOTO PAGE $i' class='tipsy-atas' onclick='changePagination($i, $action_val)'>$i</a></li>";
										}
										else{
										echo "<li class='active'><a title='NOW IS PAGE $i' class='tipsy-atas'>$i</a></li>";
										}
										$i++;
										}
										
										}
										if($page == $totalPages){
										echo "<li class='active'><a title='NOW IS LAST PAGE' class='tipsy-atas'>Last</a></li>";
										}
										else{
										$j = $page + 1;
										echo "<li><a title='GOTO NEXT PAGE' class='tipsy-atas' onclick='changePagination($j, $action_val)'>Next</a></li>";
										echo "<li><a title='GOTO LAST PAGE' class='tipsy-atas' onclick='changePagination($totalPages, $action_val)'>Last</a></li>";
										}
										
										}
										?>
									</ul>
									</div>
							</div>
						</ul>
						</div>
				</div>
				<?PHP
				}else if($action == 'search'){
				?>
				
				<!-- searching -->
				<?PHP
					if(isset($_POST["page"])){
						$page = abs((int)$_POST["page"]);
						$no = 1 + $max_per_table * $page - $max_per_table;
					}
					else{
						$page = 1;
						$no = 1;
					}
					$calc = $max_per_table * $page;
					$start = $calc - $max_per_table;
					$data_art = $db->select('files', "id, name, link", "name filename '%$q%' or real_filename like '%$q%' or id like '%$q%' and user='$admin_id'", "files.id DESC", "$start, $max_per_table");
					if (!is_array($data_art)){
						echo "No Data Found!";
						exit();
					}
					foreach ($data_art as $data){
				  ?>
                    <tr>
                      <td><?PHP echo $no; ?></td>
                      <td><?PHP echo $data['name']; ?></td>
                      <td>
					  <div class="btn-group">
						  <a target='_blank' href="<?PHP echo "$c_go_url/files/$data[id]"; ?>" class="btn btn-xs btn-primary"> <i class="fa fa-search"></i> </a> 
						  <a href="files.mgb?act=delete&id=<?PHP echo $data['id']; ?>" class="btn btn-xs btn-danger"> <i class="fs fs-remove"></i> </a> 
					  </div>
					  </td>
                    </tr>
				   <?PHP
				    $no++;
					}
				   ?>
                  </tbody>
                </table>
							<div class="bp-docs-example" style='margin-top:-30px;cursor:pointer;'>
									<div class="pagination">
									<ul class='pagination'>
										<?PHP
										if(isset($page)){
										$total = $db->num_rows("select id from files where name filename '%$q%' or real_filename like '%$q%' or id like '%$q%' and user='$admin_id'");
										$totalPages = ceil($total / $max_per_table);
										if ($totalPages == 0){
										$totalPages = 1;
										}
										$show_page = 7;
										$i=1;
										if($page <=1 ){
										echo "<li class='active'><a title='NOW IS FIRTS PAGE' class='tipsy-atas'>Firts</a></li>";
										}
										else{
										$j = $page - 1;
										echo "<li><a title='GOTO FIRTS PAGE' class='tipsy-atas' onclick='changePagination(1, $action_val, $q_val)'>Firts</a></li>";
										}
										
										if ($page >= $show_page){
										$total_prev = $page - 3; #4 5 6 7 8 9 10
										$total_next = $page + 3; #10
										if ($total_next >= $totalPages){
										$total_next = $totalPages;
										$total_prev = $total_next - 6;
										}
										$i = $total_prev;
										while ($i <= $total_next){
										if($i<>$page){
										echo "<li><a title='GOTO PAGE $i' class='tipsy-atas' onclick='changePagination($i, $action_val, $q_val)'>$i</a></li>";
										}
										else{
										echo "<li class='active'><a title='NOW IS PAGE $i' class='tipsy-atas'>$i</a></li>";
										}
										$i++;
										}
										}else{
										while($i <= $show_page and $i < $totalPages + 1){
										
										if($i<>$page){
										echo "<li><a title='GOTO PAGE $i' class='tipsy-atas' onclick='changePagination($i, $action_val, $q_val)'>$i</a></li>";
										}
										else{
										echo "<li class='active'><a title='NOW IS PAGE $i' class='tipsy-atas'>$i</a></li>";
										}
										$i++;
										}
										
										}
										if($page == $totalPages){
										echo "<li class='active'><a title='NOW IS LAST PAGE' class='tipsy-atas'>Last</a></li>";
										}
										else{
										$j = $page + 1;
										echo "<li><a title='GOTO LAST PAGE' class='tipsy-atas' onclick='changePagination($totalPages, $action_val, $q_val)'>Last</a></li>";
										}
										}
										?>
									</ul>
									</div>
							</div>
						</ul>
						</div>
				</div>
				<?PHP } ?>