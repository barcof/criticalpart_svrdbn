<?php
	include '../connection.php';

	$page = @$_REQUEST['page'] - 1;
	$limit = @$_REQUEST['limit'];
	$start = ( $page * $limit ) + 1;

	$partno = isset($_REQUEST['baking_fldsrc']) ? $_REQUEST['baking_fldsrc'] : " ";

	$sql = $conn->Execute("declare @totalcount as int exec displayBakingData $start, $limit, '{$partno}', @totalcount=@totalcount out");

	$totalcount = $sql->fields['15'];
	$return = array();

	for($i = 0;!$sql->EOF;$i++) {
		$return[$i]['unid'] = $sql->fields['0'];
		$return[$i]['id'] = $sql->fields['1'];
		$return[$i]['expid'] = $sql->fields['2'];
		$return[$i]['part_no'] = $sql->fields['3'];
		$return[$i]['model'] = $sql->fields['4'];
		$return[$i]['process'] = $sql->fields['5'];
		$return[$i]['qty'] = $sql->fields['6'];
		$return[$i]['lotno'] = $sql->fields['7'];
		$return[$i]['temperature'] = $sql->fields['8'];
		$return[$i]['duration'] = $sql->fields['9'];
		$return[$i]['nikin'] = $sql->fields['10'];
		$return[$i]['nikout'] = $sql->fields['11'];
		$return[$i]['datein'] = $sql->fields['12'];
		$return[$i]['dateout'] = $sql->fields['13'];
		$return[$i]['remark'] = $sql->fields['14'];

		$sql->MoveNext();
	}

	$sql->Close();

	$array = array("success"=>true,"totalcount"=>$totalcount,"data"=>$return);

	echo json_encode($array);

	$conn->Close();
	$conn = NULL;
?>