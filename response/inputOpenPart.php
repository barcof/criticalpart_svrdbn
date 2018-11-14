<?php
  // session_start();
  include '../connection.php';
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

  $openpartno   = isset($_REQUEST['openpartno']) ? $_REQUEST['openpartno'] : "";
  $openproddate = isset($_REQUEST['openproddate']) ? $_REQUEST['openproddate'] : '';
  $openexpdate  = isset($_REQUEST['openexpdate']) ? $_REQUEST['openexpdate'] : '';
  $raw_nik      = isset($_REQUEST['opennik']) ? $_REQUEST['opennik'] : "";
  $len          = strlen($raw_nik);
  if ($len == 5) { $nik = $raw_nik; } else { $nik = substr($raw_nik,2,5); };

  if ($openexpdate == 'EXPIRED DATE') {
    $openexpdate = '';
  } else {
    $openexpdate = $_REQUEST['openexpdate'];
  }
  
  try {
    $sql = $conn->Execute(" EXEC insertOpenPart '{$openpartno}','{$openproddate}','{$openexpdate}',NULL,NULL,'{$nik}' ");
    $sql->Close();

    $var_msg = 1;
  }

  catch(exception $e) {
    $var_msg = $conn->ErrorNo();
    // echo 'Message: ' .$e->getMessage();
  }
  switch ($var_msg) {
    case ($var_msg != 1 && $var_msg != 23000):
      $err        = $conn->ErrorMsg();
      $error      = str_replace( "'", "`", $err);
      $error_msg  = str_replace( "[Microsoft][ODBC SQL Server Driver][SQL Server]", "", $error);
      
      echo "{
        'success': false,
        'msg': '$error_msg'
      }";
    break;

    case 1:
      echo "{'success': true,'msg': 'Successfully save data'}";
    break;

    case 23000:
      echo "{
        'success': false,
        'msg': '<h1 style=\"text-align:center;color:red\">Duplicate Data</h1>'
      }";
    break;
  }
  $conn->Close();
  $conn = NULL;
?>
