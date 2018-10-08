<?php
  // session_start();
  include '../connection.php';

  $expid        = isset($_REQUEST['baking_expid']) ? $_REQUEST['baking_expid'] : " ";
  $partno       = isset($_REQUEST['baking_partno']) ? $_REQUEST['baking_partno'] : " ";
  $model        = isset($_REQUEST['baking_model']) ? $_REQUEST['baking_model'] : " ";
  $process      = isset($_REQUEST['baking_process']) ? $_REQUEST['baking_process'] : " ";
  $qty          = isset($_REQUEST['baking_qty']) ? $_REQUEST['baking_qty'] : " ";
  $lotno        = isset($_REQUEST['baking_lotno']) ? $_REQUEST['baking_lotno'] : " ";
  $temp         = isset($_REQUEST['baking_temp']) ? $_REQUEST['baking_temp'] : " ";
  $duration     = isset($_REQUEST['baking_duration']) ? $_REQUEST['baking_duration'] : " ";

  $raw_datein   = isset($_REQUEST['baking_date_in']) ? $_REQUEST['baking_date_in'] : NULL;
  $raw_timein   = isset($_REQUEST['baking_time_in']) ? $_REQUEST['baking_time_in'] : NULL;

  $raw_dateout  = isset($_REQUEST['baking_date_out']) ? $_REQUEST['baking_date_out'] : NULL;
  $raw_timeout  = isset($_REQUEST['baking_time_out']) ? $_REQUEST['baking_time_out'] : NULL;

  $date_in      = $raw_datein.' '.$raw_timein;
  $date_out     = $raw_dateout.' '.$raw_timeout;

  $raw_nik_in   = isset($_REQUEST['baking_nik_in']) ? $_REQUEST['baking_nik_in'] : " ";
  $raw_nik_out  = isset($_REQUEST['baking_nik_out']) ? $_REQUEST['baking_nik_out'] : " ";

  $remark       = isset($_REQUEST['baking_remark']) ? $_REQUEST['baking_remark'] : " ";
  // $nik_in       = substr($raw_nik_in,2,5);
  // $nik_out      = substr($raw_nik_out,2,5);
  $len_in          = strlen($raw_nik_in);
  $len_out          = strlen($raw_nik_out);

  if ($len_in == 5) { $nik_in = $raw_nik_in; } else { $nik_in = substr($raw_nik_in,2,5); }
  if ($len_out == 5) { $nik_out = $raw_nik_out; } else { $nik_out = substr($raw_nik_out,2,5); }
  
  try {
    $sql = $conn->Execute(" EXEC insertBakingData '{$expid}','{$partno}','{$model}','{$process}',{$qty},'{$lotno}',
                                                  '{$temp}','{$duration}','{$nik_in}','{$nik_out}',
                                                  '{$date_in}','{$date_out}','{$remark}' ");
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
