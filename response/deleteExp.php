<?php
  // session_start();
  include '../connection.php';

  $unid = isset($_REQUEST['unid']) ? $_REQUEST['unid'] : " ";
  
  try {
    $sql = $conn->Execute("exec deleteExpData '{$unid}'");
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
  $conn->Close();
  $conn = NULL;
?>
