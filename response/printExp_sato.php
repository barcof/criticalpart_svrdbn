<!DOCTYPE html>
<?php
//# saat release ganti 
//#	1.	$db_jnccIT >>> $dbjncc
//#	2. 	call dbtes_jncc >>> call db_jncc
//#	3. 	include "../../adodb/con_jncc_IT.php" >>> include "../../adodb/con_jncc.php";
//#	----------------------------------------------------------------------------------
	include('../asset/phpqrcode/qrlib.php');
	include "../connection.php";
	
	date_default_timezone_set('Asia/Jakarta');
	$Ymd = gmdate("Ymd");
	$His = date('His');
	
	$total 		= $_REQUEST['total'];
	$cb 		= $_REQUEST['cb'];
	$regno 		= explode("/",$cb);
?>
<html>
	<head>
		<!-- <link rel="shortcut icon" href="../asset/icon/Shipping.png"/> -->
		<!-- <title>LABEL PREVIEW CHECKER - JNCC</title> -->
		<style>
			body{
				margin	:0;
				padding	:0;
				clear	:both;
			}
			#mid-table{
				border-collapse: collapse;
				width		: 100%;
				font-size	: 12px;
				font-weight	: bold;
			}
			#ver-table{
				border-collapse: collapse;
				width		: 100%;
				font-size	: 12px;
			}
			
			.fontsize{
				font-size	:	18px;
				padding		:	0; 
				margin		:	0;
			}
			.fontsize_title{
				font-size	:	20px;
				padding		:	0; 
				margin		:	0;
			}
			
			</style>
	</head>
	<body>
		<table border="0">
		<!--	<tr>
				<td>
					<img src="../asset/img/hdr_prv_ckr.png" /> <img src="../asset/img/80.png" />
				</td>
			</tr>	-->
<?php
	$count = 0;
	for ($k=1;$k<=$total;$k++) {
		echo "<tr>";
		for ($j=1;$j<=1;$j++) {
			echo "<td>";
			if($count < $total){
				$count;
				
				//	select data
				$sql = $conn->Execute("select id, part_no, qty, prod_date, exp_date, exp_afteropen from tb_exp where id = '".$regno[$count]."'");
				//	declare data
				$id 		= $sql->fields['0'];
				$partno 	= $sql->fields['1'];
				$qty 		= $sql->fields['2'];
				$prod_date	= $sql->fields['3'];
				$exp_date	= $sql->fields['4'];
				$exp_after	= $sql->fields['5'];
				$exist	 	= $sql->RecordCount();
				
				if ($exp_after == NULL) {
					$tahun = substr($exp_date, 0, 4);
					$bulan = substr($exp_date, 5, 2);
					$hari  = substr($exp_date, 8, 2);

					$exp 	= $tahun.$bulan.$hari;
				} else {
					$tahun = substr($exp_after, 0, 4);
					$bulan = substr($exp_after, 5, 2);
					$hari  = substr($exp_after, 8, 2);

					$exp = $tahun.$bulan.$hari;
					$exp_date = $exp_after;
				}

				//	create qrcode
				if($exist == 0){}
				else{
					//	set qrcode value
					$content  = $partno . chr(073) . $prod_date .  chr(073) . $id;
					//generate
					$tempDir = '../img_qrcode/';
					$qrname = $Ymd . $His . '_'.$id.'.png';
					QRcode::png($content, $tempDir . $qrname, QR_ECLEVEL_L, 3);
				}

				echo '<table width="430px" cellpadding="0" cellspacing="0">
						<tr>
							<td width="50px" rowspan="5">
								<img style="max-height: 50px;" src="../img_qrcode/'.$qrname.'" />
							</td>
							<td class="fontsize_title">
								&nbsp; '.$partno.'
							</td>
							<td class="fontsize">
								&nbsp; Critical Part
							</td>
						</tr>
						<tr>
							<td class="fontsize">

							</td>
							<td class="fontsize">
								&nbsp; Exp.Date: '.$exp_date.'
							</td>
						</tr>
						<tr>
							<td class="fontsize">
								&nbsp; Qty : '.$qty.'
							</td>
							<td class="fontsize">
								&nbsp; Prod. : '.$prod_date.'
							</td>
						</tr>
					</table>';
					
					//	create format SATO
					$labelke = $count+1;
					$e 		 = chr(27);
					$c	   	 = chr(053);
					$space	 = chr(32);
					$label 	 = $e.'A';
					// $barcode= $partno . $c . $qty . $c . $id;
					$barcode = $partno . $c . $id . $c . $exp;
					$sato 	 = '';
					$sato .= $e . 'A';
					$sato .= $e . 'H0040' . $e . 'V0030' . $e . '2D30,H,03,0,0' . $e . 'DS2,' . $barcode;
					$sato .= $e . 'H0180' . $e . 'V0030' . $e . 'L0202' . $e . 'S' . $partno;
					$sato .= $e . 'H0180' . $e . 'V0099' . $e . 'L0101' . $e . 'M' . 'Qty: ' . $qty;
					$sato .= $e . 'H0520' . $e . 'V0069' . $e . 'L0101' . $e . 'M' . 'Prod.: ' . $prod_date;
					$sato .= $e . 'H0520' . $e . 'V0099' . $e . 'L0101' . $e . 'XM' . 'Exp.: ' . $exp_date;
					$sato .= $e . 'H0520' . $e . 'V0030' . $e . 'L0202' . $e . 'S' . 'Critical Part';
					$sato .= $e . 'Q1';
					$sato .= $e . 'Z';
					$qrcode_label = $sato;
					
					$cekip		= getenv("REMOTE_ADDR");
					$host		= gethostbyaddr($_SERVER['REMOTE_ADDR']);
					// echo '<br>'.$host		= '10.230.30.117';
					
					if($cekip == '10.230.30.125') {
						//echo 'pake ip';
					 //	$myfile = fopen("\\\\$host\\PrintSato\\print_". $Ymd . $His . '_'.$code.'_'.$orderno.'_'.$jigno . ".txt","w") or die(error_get_last());
					 	$host 	= 'newedp5';
					 	$myfile = fopen("\\\\$host\\PrintSato\\print_". $Ymd . $His . '_'.$id. ".txt","w") or die("Unable to open file! ".error_get_last());
					 	$txt 	= $qrcode_label;
					 	fwrite($myfile, $txt);
					 	fclose($myfile);
					} else if($cekip == '10.230.36.3') {
						//echo 'pake ip';
					 //	$myfile = fopen("\\\\$host\\PrintSato\\print_". $Ymd . $His . '_'.$code.'_'.$orderno.'_'.$jigno . ".txt","w") or die(error_get_last());
					 	$host 	= 'mc46';
					 	$myfile = fopen("\\\\$host\\PrintSato\\print_". $Ymd . $His . '_'.$id. ".txt","w") or die("Unable to open file! ".error_get_last());
					 	$txt 	= $qrcode_label;
					 	fwrite($myfile, $txt);
					 	fclose($myfile);
					} else {
						//echo 'pake host';
						$myfile = fopen("\\\\$host\\PrintSato\\print_". $Ymd . $His . '_'.$id. ".txt","w") or die("Unable to open file! ".error_get_last());
						$txt 	= $qrcode_label;
						fwrite($myfile, $txt);
						fclose($myfile);
					}
					
			}
			echo "</td>";
			++$count;
		}
		echo "</tr>";
	}
	
	
	$conn->Close();
?>
		</table>
	</body>
</html>