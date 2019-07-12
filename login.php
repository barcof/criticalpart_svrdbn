<?php
//start session
session_start();

//include database connection
include 'connection.php';

$jvc =& ADONewConnection('odbc_mssql');
$dsn = "Driver={SQL Server};Server=SVRDBN\JEINSQL2012;Database=JVC;";
$jvc->Connect($dsn,'sa','JvcSql@123');

	// Define $username and $password
	$username 	= $_REQUEST['username'];
	$userpass 	= $_REQUEST['password'];

	// untuk decrypt password MD5
	//$sqlselect = "select * from data_user where pass=MD5('$pass') AND userid='$id'";
	
	// Checking if username & password are available
	// $sqlcheck = "select count(*) from tb_users where username='{$username}' and userpass='{$userpass}'";
	// $check = $conn->Execute($sqlcheck);
	$sqlcheck = $jvc->execute("select count(*) from useridms where user_id = '{$username}' and password = '{$userpass}'");
	$check = $sqlcheck->fields[0];
	$sqlcheck->Close();
	// return $check;

	if ($check > 0) {
		$getauth = "select user_name, password, userlevel from useridms where user_id='{$username}' and password='{$userpass}'";
		$rs = $jvc->Execute($getauth);
		$name = $rs->fields[0];
		$pass = $rs->fields[1];
		$auth = $rs->fields[2];
			
		if (!$rs-> EOF)
		{ // Initializing Session
			$_SESSION['username']=$name;
			$_SESSION['userpass']=$pass;
			$_SESSION['userauth']=$auth;
		} else {
			$_SESSION['username']='';
			$_SESSION['userpass']='';
			$_SESSION['userauth']='';
		}
		echo "{	'success':'true','msg': '<p align=\"center\">SELAMAT,<br>ANDA BERHASIL LOGIN</p>' }";
		return;
	} else {
		echo "{ 'failure':'true','msg': '<p align=\"center\">ANDA KURANG BERUNTUNG,<br>SILAKAN COBA LAGI</p>' }";
		return;
	}
	// $select = "select * from userrs where username = '".$id."' and passwd = '".$pass."'";
	// $rs = $conn->Execute($select);
	// $userno = $rs->fields[0];
	// $userid = $rs->fields[1];
	// $passwd = $rs->fields[2];
	// $dept   = $rs->fields[3];
	// $level  = $rs->fields[5];
	// $signid = $rs->fields[6];
	// $pic    = $rs->fields[7];
		
	// if (!$rs-> EOF)
	// {
	// 	$_SESSION['userno']=$userno;
	// 	$_SESSION['userid']=$id; 		// Initializing Session
	// 	$_SESSION['pass']=$passwd; 		// Initializing Session
	// 	$_SESSION['costdept']=$dept; 	// Initializing Session
	// 	$_SESSION['pic']=$pic; 			// Initializing Session
	// 	$_SESSION['signid']=$signid;
	// 	$_SESSION['userlevel']=$level; 	// Initializing Session
	// 	$_SESSION['current_time']=time();
	// }
	// else {
	// 	$error = "<br> <br> Invalid Username or Password";
	// }

	$jvc->Close(); // Closing Connection
	$conn->Close(); // Closing Connection

?>