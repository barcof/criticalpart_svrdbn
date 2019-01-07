<?php
  // session_start();
  include '../connection.php';
  date_default_timezone_set("Asia/jakarta");

  $dryscanout = isset($_REQUEST['dryscanout']) ? $_REQUEST['dryscanout'] : "";
  $raw_nik   = isset($_REQUEST['drynik']) ? $_REQUEST['drynik'] : "";
  $scancode  = isset($_REQUEST['scancode']) ? $_REQUEST['scancode'] : "";
  $len       = strlen($raw_nik);
  $getdate   = date('Y-m-d H:i:s');
  // $getdate   = '2018-10-24 23:00:00';
  $nik = "";
  if ($len == 5) { $nik = $raw_nik; } else if ($len == 8) { $nik = substr($raw_nik,2,5); } else { $var_msg = 10; };
  // +============================+
  // | WARNING MESSAGE            |
  // | 1 = PART SUDAH DI OPEN     |
  // | 2 = PART BELUM SCAN IN     |
  // | 3 = PART BELUM SCAN OUT    |
  // | 4 = PART BELUM DI REGISTER |
  // +============================+
  try {
    $chkexp = $conn->Execute(" SELECT selflife, floorlife FROM tb_ctrlopen WHERE partno = '{$dryscanout}' ");
    $getselflife = $chkexp->fields[0];
    $getfloorlife = $chkexp->fields[1];
    $chkexp->Close();

     $var_msg = "";
    if ($getselflife == NULL) {
      $var_msg = 3;
    } 
    elseif ($getdate > $getselflife) {
      // SHOW MESSAGE IF PART EXPIRED
      $var_msg = 2;
    } else {
      if (($getdate > $getfloorlife) && $getfloorlife != ''){
        // SHOW MESSAGE IF PART EXPIRED
        $var_msg = 2;
      } else {
        // echo "EXEC insertDryPart_New '{$dryscanout}','{$nik}','{$scancode}'";
        $sql = $conn->Execute(" EXEC insertDryPart_New '{$dryscanout}','{$nik}','{$scancode}' ");
        $message = $sql->fields[0];
        $sql->Close();

        if ($message) {
          $var_msg = $message;
        } else {
          $var_msg = 1;
        }
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

      echo "{'success': false,'msg':$error_msg}";
    break;

    case 1:
      echo "{'success': true,'msg': 'Successfully save data'}";
    break;

    case 2:
      echo "{
        'success': false,
        'msg': '<h3 style=\"color:#b71c1c;text-align:center\">UNFORTUNATELY THIS PART ALREADY EXPIRED !<br> PLEASE DO BAKING PROCEDURE</h3>'
      }";
    break;

    case 3:
      echo "{
        'success': false,
        'msg' : '<h3 style=\"text-align:center\">PART NOT YET REGISTER</h3>'
      }";
    break;

    case 5:
      echo "{
        'success': false,
        'msg' : '<h3 style=\"text-align:center\">PART NOT YET SCAN IN</h3>'
      }";
    break;

    case 7:
      echo "{
        'success': false,
        'msg' : '<h3 style=\"text-align:center\">PART ALREADY SCAN OUT</h3>'
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
