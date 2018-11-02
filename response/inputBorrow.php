<?php
  // session_start();
  include '../connection.php';
  date_default_timezone_set("Asia/jakarta");

  $oldpartno = isset($_REQUEST['br_oldpartno']) ? $_REQUEST['br_oldpartno'] : "";
  $newpartno = isset($_REQUEST['br_newpartno']) ? $_REQUEST['br_newpartno'] : "";
  $raw_nik   = isset($_REQUEST['br_nik']) ? $_REQUEST['br_nik'] : "";
  $len       = strlen($raw_nik);
  $getdate   = date('Y-m-d H:i:s');
  // $getdate   = '2018-10-24 23:00:00';
  if ($len == 5) { $nik = $raw_nik; } else { $nik = substr($raw_nik,2,5); };
  

  try {
    $chkexp = $conn->Execute(" SELECT selflife, floorlife FROM tb_ctrlopen WHERE partno = '{$oldpartno}' ");
    $getselflife  = $chkexp->fields[0];
    $getfloorlife = $chkexp->fields[1];
    $chkexp->Close();

    if ($getdate > $getselflife) {
      $var_msg = 2;
    } else {
      if ($getdate > $getfloorlife) {
        $var_msg = 2;
      } else {
        $sql = $conn->Execute(" EXEC borrowPart '{$oldpartno}','{$newpartno}','{$nik}' ");
        $sql->Close();

        $var_msg = 1;
      }
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
      echo "{'success': true,'msg': 'Successfully save data'}";
      break;

    case 2:
      echo "{
        'success': false,
        'msg': 'UNFORTUNATELY THIS PART ALREADY EXPIRED, PLEASE DO BAKING PROCEDURE'
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
