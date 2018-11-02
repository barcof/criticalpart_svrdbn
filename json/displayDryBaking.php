<?php
	include '../connection.php';

	$page = @$_REQUEST['page'] - 1;
	$limit = @$_REQUEST['limit'];
	$start = ( $page * $limit ) + 1;

	$partno = isset($_REQUEST['drpbk_fldsrc']) ? $_REQUEST['drpbk_fldsrc'] : "";

	$sql = $conn->Execute("declare @totalcount as int exec displayDryBaking $start, $limit, '{$partno}', @totalcount=@totalcount out");

	$totalcount = $sql->fields['9'];
	$return = array();

	for($i = 0;!$sql->EOF;$i++) {
		$return[$i]['unid'] = $sql->fields['0'];
		$return[$i]['opid'] = $sql->fields['1'];
		$return[$i]['partno'] = $sql->fields['2'];
		$return[$i]['scanin'] = $sql->fields['3'];
		$return[$i]['scanout'] = $sql->fields['4'];
		$return[$i]['estmin'] = $sql->fields['5'];
		$return[$i]['estmax'] = $sql->fields['6'];
		$return[$i]['nik'] = $sql->fields['8'];

		$sql->MoveNext();
	}

	$sql->Close();

	$array = array("success"=>true,"totalcount"=>$totalcount,"data"=>$return);

	echo json_encode($array);

	$conn->Close();
	$conn = NULL;
?>