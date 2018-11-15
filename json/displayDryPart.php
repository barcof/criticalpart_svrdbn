<?php
	include '../connection.php';

	$page = @$_REQUEST['page'] - 1;
	$limit = @$_REQUEST['limit'];
	$start = ( $page * $limit ) + 1;

	//-------------------------------
	// $trimpart = trim($_REQUEST['partno']);

	$partno = trim(isset($_REQUEST['dryfldsrc']) ? $_REQUEST['dryfldsrc'] : '');

	$sql = $conn->Execute("declare @totalcount as int exec displayDryPart $start, $limit, '{$partno}', @totalcount=@totalcount out");

	// echo "declare @totalcount as int exec displayDetailPart $start, $limit, '{partno}', @totalcount=@totalcount out";

	$totalcount = $sql->fields['9'];
	$return = array();

		for($i=0;!$sql->EOF;$i++) {
			$return[$i]['unid'] 		= trim($sql->fields['0']);
			$return[$i]['id'] 			= trim($sql->fields['1']);
			$return[$i]['partno'] 		= trim($sql->fields['2']);
			$return[$i]['opendate'] 	= trim($sql->fields['3']);
			$return[$i]['scanin'] 		= trim($sql->fields['4']);
			$return[$i]['scanout'] 		= trim($sql->fields['5']);
			$return[$i]['nikopen'] 		= trim($sql->fields['6']);
			$return[$i]['nikin'] 		= trim($sql->fields['7']);
			$return[$i]['nikout'] 		= trim($sql->fields['8']);

			$sql->MoveNext();
		}
		$sql->Close();

	$array = array("success"=>true,"totalcount"=>$totalcount,"data"=>$return);

	echo json_encode($array);

	$conn->Close();
	$conn = NULL;
?>