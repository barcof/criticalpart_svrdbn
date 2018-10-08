<?php
include 'adodb/adodb.inc.php';
include 'adodb/adodb-errorhandler.inc.php';
include 'adodb/adodb-errorpear.inc.php';

  $dbasetype = 'odbc_mssql';
  $user = 'sa';
  $pass = 'JvcSql@123';
  $dbase = 'CRITICALPART';
  $server = "Driver={SQL Server};Server=SVRDBN\JEINSQL2012trc;Database=$dbase;";

  $conn = ADONewConnection($dbasetype);
  $conn->Connect($server, $user, $pass);
// Server in the this format: <computer>\<instance name> or
// <server>,<port> when using a non default port number
// $conn = ADONewConnection('mssql');
// $conn->Execute('SVRDBN\JEINSQL2012', 'sa', 'JvcSql@123', 'db_jncp') or die ("not connecting");
?>