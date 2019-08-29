<?php
  // session_start();
  include '../connection.php';

  // new connection to database EDI

  $dbasetype = 'odbc_mssql';
  $user = 'sa';
  // $pass = 'JvcSql@123';
  $pass = 'password';
  $dbase = 'EDI';
  // $server = "Driver={SQL Server};Server=SVRDBN\JEINSQL2012;Database=$dbase;";
  $server = "Driver={SQL Server};Server=SVRDBZ\JeinSql2017S;Database=$dbase;";

  $dbs_con = ADONewConnection($dbasetype);
  $dbs_con->Connect($server, $user, $pass);

  //-------------------------------

  $unid = isset($_REQUEST['unid']) ? $_REQUEST['unid'] : " ";
  $supplier = isset($_REQUEST['supplier']) ? $_REQUEST['supplier'] : " ";
  $partno = isset($_REQUEST['partno']) ? $_REQUEST['partno'] : " ";
  $qty = isset($_REQUEST['qty']) ? $_REQUEST['qty'] : " ";
  $lotno = isset($_REQUEST['lotno']) ? $_REQUEST['lotno'] : " ";
  $prod_date = isset($_REQUEST['prod_date']) ? $_REQUEST['prod_date'] : " ";
  
  try {
    $get_suppname = $dbs_con->Execute("SELECT SuppName from Supplier where SuppCode = '{$supplier}' ");
    $suppname = trim($get_suppname->fields['0']);
    $get_suppname->Close();

    $sql = $conn->Execute("exec updateExpData '{$unid}', '{$supplier}', '{$suppname}', '{$partno}', {$qty}, '{$lotno}', '{$prod_date}'");
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
      echo "{'success': true,'msg': 'Successfully update data'}";
      break;
  }
  $conn->Close();
  $conn = NULL;
?>
