<?php
  // session_start();
  include '../connection.php';

  $unid     = isset($_REQUEST['issue_unid']) ? $_REQUEST['issue_unid'] : " ";
  $partno   = isset($_REQUEST['issue_partno']) ? $_REQUEST['issue_partno'] : " ";
  $partname = isset($_REQUEST['issue_partname']) ? $_REQUEST['issue_partname'] : " ";
  $model    = isset($_REQUEST['issue_model']) ? $_REQUEST['issue_model'] : " ";
  $opendate = isset($_REQUEST['issue_opendate']) ? $_REQUEST['issue_opendate'] : " ";
  $qty      = isset($_REQUEST['issue_qty']) ? $_REQUEST['issue_qty'] : " ";
  $lotsize  = isset($_REQUEST['issue_lotsize']) ? $_REQUEST['issue_lotsize'] : " ";
  $lotno    = isset($_REQUEST['issue_lotno']) ? $_REQUEST['issue_lotno'] : " ";
  $remark   = isset($_REQUEST['issue_remark']) ? $_REQUEST['issue_remark'] : " ";

  // echo 'Date IN = '.$raw_datein.' '.$raw_timein;
  // echo '<br>';
  // echo 'Date OUT = '.$raw_dateout.' '.$raw_timeout;
  
  try {

    // GET EXPIRED ID FROM TB_ISSUE
    $getexpid = $conn->Execute(" SELECT expid FROM tb_issue WHERE unid = '{$unid}'");
    $expid = $getexpid->fields['0'];
    $getexpid->Close();

    // GET EXPIRED DATE
    $getexpdate = $conn->Execute(" SELECT exp_date FROM tb_exp WHERE id  = '{$expid}'");
    $expdate = $getexpdate->fields['0'];
    $getexpdate->Close();

    if($opendate >= $expdate) {
      $var_msg = 2;
    } else {
      $sql = $conn->Execute(" EXEC updateIssueData '{$unid}','{$partno}','{$partname}','{$model}',{$qty},{$lotsize},'{$lotno}','{$opendate}','{$remark}' ");
      $sql->Close();

      $var_msg = 1;
    }

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
      echo "{'success': true,'msg': '<h1 style=\"color:#43a047;text-align:center;\">Successfully save data</h1>'}";
      break;
    case 2:
      echo "{'success': false,'msg': '<h1 style=\"color:#b71c1c;text-align:center;\">Open Date over than Expired Date,<br><br>Part should be bake</h1>'}";
      break;
  }
  $conn->Close();
  $conn = NULL;
?>
