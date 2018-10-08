<?php
  // session_start();
  include '../connection.php';

  $expid    = isset($_REQUEST['issue_expid']) ? $_REQUEST['issue_expid'] : " ";
  $partno   = isset($_REQUEST['issue_partno']) ? $_REQUEST['issue_partno'] : " ";
  $partname = isset($_REQUEST['issue_partname']) ? $_REQUEST['issue_partname'] : " ";
  $model    = isset($_REQUEST['issue_model']) ? $_REQUEST['issue_model'] : " ";
  $qty      = isset($_REQUEST['issue_qty']) ? $_REQUEST['issue_qty'] : " ";
  $lotsize  = isset($_REQUEST['issue_lotsize']) ? $_REQUEST['issue_lotsize'] : " ";
  $lotno    = isset($_REQUEST['issue_lotno']) ? $_REQUEST['issue_lotno'] : " ";
  $remark   = isset($_REQUEST['issue_remark']) ? $_REQUEST['issue_remark'] : " ";
  $raw_nik  = isset($_REQUEST['issue_nik']) ? $_REQUEST['issue_nik'] : " ";
  $opendate  = isset($_REQUEST['issue_opendate']) ? $_REQUEST['issue_opendate'] : " ";
  $len      = strlen($raw_nik);
  if ($len == 5) { $nik = $raw_nik; } else { $nik = substr($raw_nik,2,5); }
  
  try {
    $sql = $conn->Execute(" EXEC insertIssueData '{$expid}','{$partno}','{$partname}','{$model}',{$qty},{$lotsize},'{$lotno}','{$nik}','{$opendate}','{$remark}' ");
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

      echo "{'success':'false','msg':$sql }";
      break;

    case 1:
      echo "{'success': true,'msg': 'Successfully save data'}";
      break;
  }
  $conn->Close();
  $conn = NULL;
?>
