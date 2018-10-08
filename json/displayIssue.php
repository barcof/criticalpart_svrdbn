<?php
	include '../connection.php';

	$page = @$_REQUEST['page'] - 1;
	$limit = @$_REQUEST['limit'];
	$start = ( $page * $limit ) + 1;

	$partno = isset($_REQUEST['issue_fldsrc']) ? $_REQUEST['issue_fldsrc'] : "";

	$sql = $conn->Execute("declare @totalcount as int exec displayIssueData $start, $limit, '{$partno}', @totalcount=@totalcount out");

	$totalcount = $sql->fields['14'];
	$return = array();

	for($i = 0;!$sql->EOF;$i++) {
		$return[$i]['unid'] = $sql->fields['0'];
		$return[$i]['id'] = $sql->fields['1'];
		$return[$i]['expid'] = $sql->fields['2'];
		$return[$i]['part_no'] = $sql->fields['3'];
		$return[$i]['part_name'] = $sql->fields['4'];
		$return[$i]['model'] = $sql->fields['5'];
		$return[$i]['qty'] = $sql->fields['6'];
		$return[$i]['lotsize'] = $sql->fields['7'];
		$return[$i]['lotno'] = $sql->fields['8'];
		$return[$i]['nik'] = $sql->fields['9'];
		$return[$i]['opendate'] = $sql->fields['10'];
		$return[$i]['remark'] = $sql->fields['11'];
		$return[$i]['prod_date'] = $sql->fields['12'];
		$return[$i]['exp_date'] = $sql->fields['13'];

		$sql->MoveNext();
	}

	$sql->Close();

	$array = array("success"=>true,"totalcount"=>$totalcount,"data"=>$return);

	echo json_encode($array);

	$conn->Close();
	$conn = NULL;
?>