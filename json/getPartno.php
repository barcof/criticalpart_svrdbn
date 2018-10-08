<?php
	include '../connection.php';

	$expid = isset($_REQUEST['issue_partno']) ? $_REQUEST['issue_partno'] : "";

	$sql = $conn->Execute("SELECT id, part_no FROM tb_exp WHERE id = '{$expid}'");

	$return = array();

	for($i = 0;!$sql->EOF;$i++) {
		$return[$i]['id'] = $sql->fields['0'];
		$return[$i]['partno'] = $sql->fields['1'];

		$sql->MoveNext();
	}

	$sql->Close();

	$array = array("success"=>true,"data"=>$return);

	echo json_encode($array);

	$conn->Close();
	$conn = NULL;
?>