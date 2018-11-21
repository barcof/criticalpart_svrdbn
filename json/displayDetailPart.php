<?php
	include '../connection.php';

	$page = @$_REQUEST['page'] - 1;
	$limit = @$_REQUEST['limit'];
	$start = ( $page * $limit ) + 1;

	//-------------------------------
	// $trimpart = trim($_REQUEST['partno']);

	$detail_fldsrc = trim(isset($_REQUEST['detail_fldsrc']) ? $_REQUEST['detail_fldsrc'] : '');

	$sql = $conn->Execute("declare @totalcount as int exec displayDetailPart $start, $limit, '{$detail_fldsrc}', @totalcount=@totalcount out");

	// echo "declare @totalcount as int exec displayDetailPart $start, $limit, '{partno}', @totalcount=@totalcount out";

	$totalcount = $sql->fields['13'];
	$return = array();

		for($i=0;!$sql->EOF;$i++) {
			$return[$i]['unid'] = trim($sql->fields['0']);
			$return[$i]['id'] = trim($sql->fields['1']);
			$return[$i]['partno'] = trim($sql->fields['2']);
			// $return[$i]['proddate'] = trim($sql->fields['3']);
			$return[$i]['htempmin'] = trim($sql->fields['3']);
			$return[$i]['htempmax'] = trim($sql->fields['4']);
			$return[$i]['humidmin'] = trim($sql->fields['5']);
			$return[$i]['humidmax'] = trim($sql->fields['6']);
			$return[$i]['lifetime'] = trim($sql->fields['7']);
			$return[$i]['btempmin'] = trim($sql->fields['8']);
			$return[$i]['btempmax'] = trim($sql->fields['9']);
			$return[$i]['periodmin'] = trim($sql->fields['10']);
			$return[$i]['periodmax'] = trim($sql->fields['11']);
			// $return[$i]['expdate'] = trim($sql->fields['13']);
			$return[$i]['nik'] = trim($sql->fields['12']);

			$sql->MoveNext();
		}
		$sql->Close();

	$array = array("success"=>true,"totalcount"=>$totalcount,"data"=>$return);

	echo json_encode($array);

	$conn->Close();
	$conn = NULL;
?>