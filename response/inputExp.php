<?php
  // session_start();
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
  $stdpack = isset($_REQUEST['stdpack']) ? $_REQUEST['stdpack'] : $_REQUEST['qty'];
  $raw_nik = isset($_REQUEST['nik']) ? $_REQUEST['nik'] : " ";
  $supplier = isset($_REQUEST['supplier']) ? $_REQUEST['supplier'] : " ";
  $partno = isset($_REQUEST['partno']) ? $_REQUEST['partno'] : " ";
  $qty = isset($_REQUEST['qty']) ? $_REQUEST['qty'] : " ";
  $lotno = isset($_REQUEST['lotno']) ? $_REQUEST['lotno'] : " ";
  $prod_date = isset($_REQUEST['prod_date']) ? $_REQUEST['prod_date'] : " ";

  if (strlen($raw_nik) == 5) {
    $nik = $raw_nik;
  } else {
    $nik = substr($raw_nik,2,5);
  }
  
  try {
    $get_suppname = $dbs_con->Execute("SELECT SuppName from Supplier where SuppCode = '{$supplier}' ");
    $suppname = trim($get_suppname->fields['0']);
    $get_suppname->Close();

    $sql = $conn->Execute("EXEC insertExpData '{$nik}','{$supplier}','{$suppname}','{$partno}',{$qty},'{$lotno}','{$prod_date}','{$stdpack}'");
    $sql->Close();

    $var_msg = 1;
  }

  catch(exception $e) {
    $var_msg = $conn->ErrorNo();
  }
  switch ($var_msg) {
    case $conn->ErrorNo() :
      $error = $conn->ErrorMsg();
      $error_msg = str_replace(chr(50), "", $error);

      echo "{'success':'false','msg':$error_msg}";
      break;

    case 1:
      echo "{'success': true,'msg': 'Successfully save data'}";
      break;
  }
  $dbs_con->Close();
  $conn->Close();
  $dbs_con = NULL;
  $conn = NULL;
?>
