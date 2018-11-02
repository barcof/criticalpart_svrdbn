<?php
  // session_start();
  include '../connection.php';
  date_default_timezone_set("Asia/jakarta");

  $drypartno = isset($_REQUEST['drypartno']) ? $_REQUEST['drypartno'] : "";
  $raw_nik   = isset($_REQUEST['drynik']) ? $_REQUEST['drynik'] : "";
  $len       = strlen($raw_nik);
  $getdate   = date('Y-m-d H:i:s');
  // $getdate   = '2018-10-24 23:00:00';
  if ($len == 5) { $nik = $raw_nik; } else { $nik = substr($raw_nik,2,5); };
  

  try {
    $chkexp = $conn->Execute(" SELECT selflife, floorlife FROM tb_ctrlopen WHERE partno = '{$drypartno}' ");
    $getselflife = $chkexp->fields[0];
    $getfloorlife = $chkexp->fields[1];
    $chkexp->Close();

    // echo $getselflife;
    // echo '||';
    // echo $getdate;
    // echo '||';
    // echo $getfloorlife;
    // echo '||';
     $var_msg = "";

    if ($getdate > $getselflife) {
      // MESSAGE FOR BAKING
      $var_msg = 2;
    } else {
      // if ($getfloorlife == '') {
      //   $sql = $conn->Execute(" EXEC insertDryPart '{$drypartno}','{$nik}' ");
      //   $sql->Close();

      //   $var_msg = 1;
      // }
      // else 
      if (($getdate > $getfloorlife) && $getfloorlife != ''){
        // MESSAGE FOR BAKING
        $var_msg = 2;
      } else {
        $sql = $conn->Execute(" EXEC insertDryPart '{$drypartno}','{$nik}' ");
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
