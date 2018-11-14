<?php
  // session_start();
  include '../connection.php';
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

  $partno     = isset($_REQUEST['detpart']) ? $_REQUEST['detpart'] : "";
  $htempmin   = isset($_REQUEST['htempmin']) ? $_REQUEST['htempmin'] : 0;
  $htempmax   = isset($_REQUEST['htempmax']) ? $_REQUEST['htempmax'] : 0;
  $humidmin   = isset($_REQUEST['humidmin']) ? $_REQUEST['humidmin'] : 0;
  $humidmax   = isset($_REQUEST['humidmax']) ? $_REQUEST['humidmax'] : 0;
  $lifetime   = isset($_REQUEST['lifetime']) ? $_REQUEST['lifetime'] : 0;
  $btempmin   = isset($_REQUEST['btempmin']) ? $_REQUEST['btempmin'] : 0;
  $btempmax   = isset($_REQUEST['btempmax']) ? $_REQUEST['btempmax'] : 0;
  $periodmin  = isset($_REQUEST['periodmin']) ? $_REQUEST['periodmin'] : 0;
  $periodmax  = isset($_REQUEST['periodmax']) ? $_REQUEST['periodmax'] : 0;
  $raw_nik    = isset($_REQUEST['detnik']) ? $_REQUEST['detnik'] : "";
  $len        = strlen($raw_nik);
  if ($len == 5) { $nik = $raw_nik; } else { $nik = substr($raw_nik,2,5); }
  
  try {
    $sql = $conn->Execute(" EXEC insertDetailPart '{$partno}',{$htempmin},{$htempmax},{$humidmin},{$humidmax},{$lifetime},{$btempmin},{$btempmax},{$periodmin},{$periodmax},'{$nik}' ");
    $sql->Close();

    $var_msg = 1;
  }

  catch(exception $e) {
    $var_msg = $conn->ErrorNo();
  }
  switch ($var_msg) {
    case ($var_msg != 23000) :
      // $error = $conn->ErrorNo();
      $error = $conn->ErrorMsg();
      $error_rep1 = str_replace( "'", "`", $error);
      $error_rep2 = str_replace( "[", " ", $error_rep1);
      $error_rep3 = str_replace( "]", " ", $error_rep2);

      // echo "{'success':'false','msg':$error_msg }";
      echo "{'success': false,'msg': '$error_rep3' }";
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
