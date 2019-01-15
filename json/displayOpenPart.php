<?php
	include '../connection.php';

	// koneksi ke SVRDBN\JEINSQL2012P
	$dbasetype = 'odbc_mssql';
	$user = 'sa';
	$pass = 'JvcSql@123';
	$dbase = 'SMTZDBS';
	$server = "Driver={SQL Server};Server=SVRDBN\JEINSQL2012P;Database=$dbase;";

	$dbnp_conn = ADONewConnection($dbasetype);
	$dbnp_conn->Connect($server, $user, $pass);
	// -------------------------------------------------------------------------

	$page = @$_REQUEST['page'] - 1;
	$limit = @$_REQUEST['limit'];
	$start = ( $page * $limit ) + 1;

	//-------------------------------
	// $trimpart = trim($_REQUEST['partno']);

	$stpartfldsrc = trim(isset($_REQUEST['stpartfldsrc']) ? $_REQUEST['stpartfldsrc'] : '');

	// $sql = $conn->Execute("declare @totalcount as int exec displayOpenPart $start, $limit, '{$stpartfldsrc}', @totalcount=@totalcount out");

	$sql = $conn->Execute("declare @totalcount as int exec displayOpenPart_new $start, $limit, '{$stpartfldsrc}', @totalcount=@totalcount out");


	$totalcount = $sql->fields['12'];
	$return = array();

		for($i=0;!$sql->EOF;$i++) {
			$return[$i]['unid'] 		= trim($sql->fields['0']);
			$return[$i]['openid'] 		= trim($sql->fields['1']);
			$return[$i]['partno'] 		= trim($sql->fields['2']);
			$return[$i]['qty'] 			= trim($sql->fields['3']);
			$return[$i]['proddate'] 	= trim($sql->fields['4']);
			$return[$i]['selflife'] 	= trim($sql->fields['5']);
			$return[$i]['opendate'] 	= trim($sql->fields['6']);
			$return[$i]['floorlife'] 	= trim($sql->fields['7']);
			$return[$i]['nik'] 			= trim($sql->fields['8']);
			$return[$i]['place']		= trim($sql->fields['9']);
			$return[$i]['accdate']		= trim($sql->fields['10']);
			$return[$i]['datacode'] 	= trim($sql->fields['11']);

			$sql->MoveNext();
		}
		$sql->Close();

	$array = array("success"=>true,"totalcount"=>$totalcount,"data"=>$return);

	echo json_encode($array);

	$conn->Close();
	$conn = NULL;
?>