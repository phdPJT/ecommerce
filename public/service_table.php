<?php
	include_once('includes/connect_database.php');
	include_once('functions.php');

?>

<div id="content" class="container col-md-12">
	<?php
		// create object of functions class
		$function = new functions;

		// create array variable to store data from database
		$data = array();

		if(isset($_GET['keyword'])){
			// check value of keyword variable
			$keyword = $function->sanitize($_GET['keyword']);
			$bind_keyword = "%".$keyword."%";
		}else{
			$keyword = "";
			$bind_keyword = $keyword;
		}

		// get all data from pemesanan table
		if(empty($keyword)){
			$sql_query = "SELECT ID, Name, Alamat, Number_of_people, Date_n_Time, Phone_number, Status, Email
				FROM tbl_service
				ORDER BY Date_n_Time DESC";
		}else{
			$sql_query = "SELECT ID, Name, Alamat, Number_of_people, Date_n_Time, Phone_number, Status, Email
				FROM tbl_service
				WHERE Name LIKE ?
				ORDER BY Date_n_Time DESC";
		}

		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {
			// Bind your variables to replace the ?s
			if(!empty($keyword)){
				$stmt->bind_param('s', $bind_keyword);
			}
			// Execute query
			$stmt->execute();
			// store result
			$stmt->store_result();
			$stmt->bind_result($data['ID'],
					$data['Name'],
					$data['Alamat'],
					$data['Number_of_people'],
					$data['Date_n_Time'],
					$data['Phone_number'],
					$data['Status'],
					$data['Email']
					);

			// get total records
			$total_records = $stmt->num_rows;
		}

		// check page parameter
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}

		// number of data that will be display per page
		$offset = 20;

		//lets calculate the LIMIT for SQL, and save it $from
		if ($page){
			$from 	= ($page * $offset) - $offset;
		}else{
			//if nothing was given in page request, lets load the first page
			$from = 0;
		}

		// get all data from pemesanan table
		if(empty($keyword)){
			$sql_query = "SELECT ID, Name, Alamat, Number_of_people, Date_n_Time, Phone_number, Status, Email
				FROM tbl_service
				ORDER BY Date_n_Time DESC
				LIMIT ?, ?";
		}else{
			$sql_query = "SELECT ID, Name, Alamat, Number_of_people, Date_n_Time, Phone_number, Status, Email
				FROM tbl_service
				WHERE Name LIKE ?
				ORDER BY Date_n_Time ASC
				LIMIT ?, ?";
		}

		$stmt_paging = $connect->stmt_init();
		if($stmt_paging ->prepare($sql_query)) {
			// Bind your variables to replace the ?s
			if(empty($keyword)){
				$stmt_paging ->bind_param('ss', $from, $offset);
			}else{
				$stmt_paging ->bind_param('sss', $bind_keyword, $from, $offset);
			}
			// Execute query
			$stmt_paging ->execute();
			// store result
			$stmt_paging ->store_result();

			$stmt_paging ->bind_result($data['ID'],
					$data['Name'],
					$data['Alamat'],
					$data['Number_of_people'],
					$data['Date_n_Time'],
					$data['Phone_number'],
					$data['Status'],
					$data['Email']
					);

			// for paging purpose
			$total_records_paging = $total_records;
			//$total_records_paging = 1;
		}

		// if no data on database show "Tidak Ada Pemesanan"
		if($total_records_paging == 0){
	?>
	<center>
		<h1>서비스 신청 정보가 없습니다.</h1>
	</center>
	<hr />

	<?php
		// otherwise, show data
		}else{ $row_number = $from + 1;?>

	<div>

	<div class="col-md-12">
		<h1>서비스 신청 정보</h1>
		<hr/>
	</div>

	<!-- search form -->
	<form class="list_header" method="get">
		<div class="col-md-12">
			<p class="pholder">신청자 이름으로 검색 : </p>
		</div>

		<div class="col-md-3">
			<input type="text" class="form-control" name="keyword"/>
		</div>
		<br>
			&nbsp;&nbsp;&nbsp;
			<input type="submit" class="btn btn-primary" name="btnSearch" value="Search"/>
	</form>
	<!-- end of search form -->

	<br>
	<div class="col-md-12">
	<div class="table-responsive">
	<table class='table table-hover table-condensed table-bordered'>
		<tr class="success">
			<th class="text-center">이름</th>
			<th class="text-center">주소</th>
			<th class="text-center">이메일</th>
			<th class="text-center">서비스정보</th>
			<th class="text-center">신청날짜</th>
			<th class="text-center">연락처</th>
			<th class="text-center">서비스종류</th>
		</tr>
		<?php
			// get all data using while loop
			while ($stmt_paging->fetch()){ ?>
			<tr>
				<td><?php echo $data['Name'];?></td>
				<td><?php echo $data['Alamat'];?></td>
				<td><?php echo $data['Email'];?></td>
				<td><?php echo $data['Number_of_people'];?></td>
				<td><?php echo $data['Date_n_Time'];?></td>
				<td><?php echo $data['Phone_number'];?></td>
				<td>
					<?php
						$flag =  $data['Status'];

						if($flag == 1){
							echo "<span class='label label-info'>브랜딩</span>";
						}
						else if($flag == 2){
							echo "<span class='label label-danger'>크라우드펀딩(일반)</span>";
						}
						else{
							echo "<span class='label label-success'>크라우드펀딩(농가)</span>";
						}
					 ?>
				</td>
					<!--
				<td>
					<a href="service-detail.php?id=<?php echo $data['ID'];?>">
						상세보기
					</a>&nbsp;

					<a href="delete-service.php?id=<?php echo $data['ID'];?>">
						주문삭제
					</a>
				</td>
			-->
			</tr>
		<?php } }?>
	</table>
	</div>
	</div>

	<div class="col-md-12">

	<h4>
		<?php
			// for pagination purpose
			$function->doPages($offset, 'pemesanan.php', '', $total_records, $keyword);
		?>
	</h4>

	</div>
	<div class="separator"> </div>
</div>
</div>

<?php
	$stmt->close();
	include_once('includes/close_database.php'); ?>
