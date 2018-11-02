<?php
  // session_start();
  include '../connection.php';
  date_default_timezone_set("Asia/jakarta");

  $drpbk_partno = isset($_REQUEST['drpbk_partno']) ? $_REQUEST['drpbk_partno'] : "";
  // $substrpart = substr($drpbk_partno,0,15);
  $raw_nik   = isset($_REQUEST['drpbk_nik']) ? $_REQUEST['drpbk_nik'] : "";
  $len       = strlen($raw_nik);
  $getdate   = date('Y-m-d H:i:s');
  $datenow   = date_create($getdate);
  if ($len == 5) { $nik = $raw_nik; } else { $nik = substr($raw_nik,2,5); };
  

  try {
    // GET ESTIMATE TIME
    $chkest = $conn->Execute(" SELECT est_min, est_max, bakestate FROM tb_drybaking WHERE partno = '{$drpbk_partno}' ");
    $getmin   = $chkest->fields[0];
    $getmax   = $chkest->fields[1];
    $getstate = $chkest->fields[2];
    $chkest->Close();

    // CALL THE PROCEDURE THAT CONVERTING TIME TO HOUR & MINUTES
    $remaintime = $conn->Execute(" EXEC checkBakingDuration '{$getmin}'");
    $h_remain = $remaintime->fields[0];
    $m_remain = $remaintime->fields[1];
    $remaintime->Close();

    if ($getstate == NULL) {
      $sql = $conn->Execute(" EXEC insertDryBaking '{$drpbk_partno}','{$nik}' ");
      $sql->Close();
      $var_msg = 1;
    } else {
      if ($getstate == 0) {
        if ($getdate < $getmin) {
          $var_msg = 2;
        } else {
          $sql = $conn->Execute(" EXEC insertDryBaking '{$drpbk_partno}','{$nik}' ");
          $sql->Close();
          $var_msg = 1;
        }
      } else {
        $var_msg = 3;
      }
    }


    // if ($getdate < $getmin) {
    //   $var_msg = 2;
    // } else {
    //   $var_msg = 1;
      // if ($getdate > $getmax) {
      //   $var_msg = 3;
      // } else {
      //   $sql = $conn->Execute(" EXEC insertDryBaking '{$drpbk_partno}','{$nik}' ");
      //   $sql->Close();

      //   $var_msg = 1;
      // }
    // }

  }

  catch(exception $e) {
    $var_msg = $conn->ErrorNo();
  }
  switch ($var_msg) {
    case $conn->ErrorNo() :
      $error = $conn->ErrorMsg();
      $error_msg = str_replace(chr(50), "", $error);

      echo "{'success': false,'msg':$error_msg}";
      break;

    case 1:
      echo "{'success': true,'msg': '<h3>Successfully save data</h3>'}";
      break;

    case 2:
      echo "{
        'success': false,
        'msg': '<h3 style=\"text-align:center;\"><span style=\"color:#b71c1c;\">$h_remain HOUR $m_remain MINUTE</span><br><br>REMAINING UNTIL BAKING IS DONE</h3>'
      }";
      break;

    case 3:
      echo "{
        'success': false,
        'msg': '<h3 style=\"text-align:center;\">PART ALREADY BAKING</h3>'
      }";
      break;

    case 23000:
      echo "{
        'success': false,
        'msg': 'Duplicate Data.'
      }";
      break;
  }
  $conn->Close();
  $conn = NULL;
?>
