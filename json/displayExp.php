<?php
	include '../connection.php';

	$page = @$_REQUEST['page'] - 1;
	$limit = @$_REQUEST['limit'];
	$start = ( $page * $limit ) + 1;

	$partno = isset($_REQUEST['fldsrc']) ? $_REQUEST['fldsrc'] : " ";

	$sql = $conn->Execute("declare @totalcount as int exec displayExpData $start, $limit, '{$partno}', @totalcount=@totalcount out");

	$totalcount = $sql->fields['12'];
	$return = array();

	for($i = 0;!$sql->EOF;$i++) {
		$return[$i]['unid'] = $sql->fields['0'];
		$return[$i]['id'] = $sql->fields['1'];
		$return[$i]['suppcode'] = $sql->fields['2'];
		$return[$i]['suppname'] = $sql->fields['3'];
		$return[$i]['part_no'] = $sql->fields['4'];
		$return[$i]['qty'] = $sql->fields['5'];
		$return[$i]['balance'] = $sql->fields['6'];
		$return[$i]['lotno'] = $sql->fields['7'];
		$return[$i]['prod_date'] = $sql->fields['8'];
		$return[$i]['exp_date'] = $sql->fields['9'];
		$return[$i]['exp_after'] = $sql->fields['10'];
		$return[$i]['nik'] = $sql->fields['11'];

		$sql->MoveNext();
	}

	$sql->Close();

	$array = array("success"=>true,"totalcount"=>$totalcount,"data"=>$return);

	echo json_encode($array);

	$conn->Close();
	$conn = NULL;
?>