<?php
	include '../connection.php';

	// new connection to database EDI

	$dbasetype = 'odbc_mssql';
	$user = 'sa';
	$pass = 'password';
	$dbase = 'EDI';
	$server = "Driver={SQL Server};Server=SVRDBS;Database=$dbase;";

	$dbs_con = ADONewConnection($dbasetype);
	$dbs_con->Connect($server, $user, $pass);

	//-------------------------------

	$supp = isset($_REQUEST['supplier']) ? $_REQUEST['supplier'] : "";

	$page = @$_REQUEST['page'] - 1;
	$limit = @$_REQUEST['limit'];
	$start = ( $page * $limit ) + 1;

	$sql = $dbs_con->Execute("SELECT SuppCode, SuppName FROM Supplier WHERE SuppCode LIKE '$supp%' OR SuppName LIKE '$supp%'");
	$return = array();

		for($i=0;!$sql->EOF;$i++) {
			$return[$i]['suppcode'] = trim($sql->fields['0']);
			$return[$i]['suppname'] = trim($sql->fields['1']);

			$sql->MoveNext();
		}
		$sql->Close();

	$array = array("success"=>true,"data"=>$return);

	echo json_encode($array);

	$dbs_con->Close();
	$dbs_con = NULL;
?>