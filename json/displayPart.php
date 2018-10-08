<?php
	include '../connection.php';

	// new connection to database EDI

	$dbasetype = 'odbc_mssql';
	$user = 'sa';
	$pass = 'JvcSql@123';
	$dbase = 'EDI';
	$server = "Driver={SQL Server};Server=SVRDBN\JeinSql2012;Database=$dbase;";

	$dbs_con = ADONewConnection($dbasetype);
	$dbs_con->Connect($server, $user, $pass);

	//-------------------------------
	// $trimpart = trim($_REQUEST['partno']);

	$supp = isset($_REQUEST['supplier']) ? $_REQUEST['supplier'] : "";
	$partno = trim(isset($_REQUEST['partno']) ? $_REQUEST['partno'] : "");

	$sql = $dbs_con->Execute("SELECT DISTINCT TOP 1000 PartNumber, PartName, StdPack FROM StdPack WHERE SuppCode LIKE '$supp%' AND PartNumber LIKE '$partno%'");
	$return = array();

		for($i=0;!$sql->EOF;$i++) {
			$return[$i]['partno'] = trim($sql->fields['0']);
			$return[$i]['partname'] = trim($sql->fields['1']);
			$return[$i]['stdpack'] = trim($sql->fields['2']);

			$sql->MoveNext();
		}
		$sql->Close();

	$array = array("success"=>true,"data"=>$return);

	echo json_encode($array);

	$dbs_con->Close();
	$dbs_con = NULL;
?>