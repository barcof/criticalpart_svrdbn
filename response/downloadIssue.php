<?php

include '../connection.php';
	date_default_timezone_set('Asia/jakarta');

	$today = date('Y/m/d');
	$partno = isset($_REQUEST['src_partno']) ? $_REQUEST['src_partno'] : '';
	$model = isset($_REQUEST['src_model']) ? $_REQUEST['src_model'] : '';
	$lotno = isset($_REQUEST['src_lotno']) ? $_REQUEST['src_lotno'] : '';
	$proddate = isset($_REQUEST['src_proddate']) ? $_REQUEST['src_proddate'] : '';
    
    //create file name
    $fname = 'Issue Part_'.$partno.'_'.$model.'_'.$lotno.'_'.$proddate.'_'.$today;
	
	//echo $fname;
	header("Content-type: application/vnd-ms-excel");
  	header("Content-Disposition: attachment; filename=".$fname.".xls");
?>

	<!DOCTYPE html>
	<html>
		<head>
			<title>Download Data Issue</title>
		</head>
		<body>
			<table border="1">
				<tr>
					<th>NO</th>
					<th>PART NUMBER</th>
					<th>PART NAME</th>
					<th>MODEL</th>
					<th>QTY ISSUE</th>
					<th>LOT SIZE</th>
					<th>LOT NUMBER</th>
					<th>OPEN DATE</th>
					<th>PRODUCTION DATE</th>
					<th>EXP DATE</th>
					<th>INPUT DATE</th>
				</tr>
				<?php
					$no   = 1;
					$sql  = "EXEC downloadIssueData '{$partno}', '{$model}', '{$lotno}', '{$proddate}'";
   					$rs   = $conn->Execute($sql);
   					while($data = $rs->FetchRow()) {
   					?>
   						<tr>
   							<td> <?=$no;?></td>
   							<td> <?=$data[0];?></td>
   							<td> <?=$data[1];?></td>
   							<td> <?=$data[2];?></td>
   							<td> <?=$data[3];?></td>
   							<td> <?=$data[4];?></td>
   							<td> <?=$data[5];?></td>
   							<td> <?=$data[6];?></td>
   							<td> <?=$data[7];?></td>
   							<td> <?=$data[8];?></td>
   							<td> <?=$data[9];?></td>
   						</tr>
   					<?php

   					$no++;
   					}
				?>
			</table>
		</body>
	</html>

<?php
	$conn->Close();
	$conn=null;
?>