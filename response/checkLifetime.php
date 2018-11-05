<?php
  // session_start();
  include '../connection.php';
  date_default_timezone_set("Asia/jakarta");

  $drycheck = isset($_REQUEST['drycheck']) ? $_REQUEST['drycheck'] : "";
  $getdate   = date('Y-m-d H:i:s');
  // $getdate   = '2018-10-24 23:00:00';
  // if ($len == 5) { $nik = $raw_nik; } else { $nik = substr($raw_nik,2,5); };
  

  try {
    $chklifetime = $conn->Execute(" EXEC checkLifetime '{$drycheck}' ");
    $sisajam     = $chklifetime->fields[3];
    $sisamenit   = $chklifetime->fields[4];
    $expstatus   = $chklifetime->fields[5];
    $chklifetime->Close();

    // check expired status
    if ($expstatus > 0) { 
      $var_msg = 2; 
    } else { 
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

      echo "{'success': false,'msg':$error_msg}";
      break;

    case 1:
      echo "{
        'success': true,
        'msg': '<h3>REMAINING TIME UNTIL EXPIRED <b style=\"color:red\">{$sisajam} HOUR, {$sisamenit} MINUTES</b></h3>'}";
      break;

    case 2:
      echo "{
        'success': false,
        'msg': '<h3 style=\"color:#b71c1c;text-align:center\">UNFORTUNATELY THIS PART ALREADY EXPIRED !<br> PLEASE DO BAKING PROCEDURE</h3>'
      }";
      break;
  }
  $conn->Close();
  $conn = NULL;
?>
