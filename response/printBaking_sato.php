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
				$sql = $conn->Execute("SELECT tb_baking.id, expid, tb_baking.part_no, model, tb_baking.qty, tb_baking.lot_no, 
	   									temperature, duration, date_in, date_out, tb_exp.prod_date, tb_exp.exp_date
										FROM tb_baking 
										LEFT JOIN tb_exp ON tb_exp.id = tb_baking.expid
										WHERE tb_baking.id = '".$regno[$count]."'");
				//	declare data
				$id 			= $sql->fields['0'];
				$expid 			= $sql->fields['1'];
				$partno 		= $sql->fields['2'];
				$model	 		= $sql->fields['3'];
				$qty 			= $sql->fields['4'];
				$lot_no			= $sql->fields['5'];
				$temperature	= $sql->fields['6'];
				$duration 		= $sql->fields['7'];
				$date_in 		= substr($sql->fields['8'], 0, 10);
				$time_in 		= substr($sql->fields['8'], 11, 8);
				$date_out 		= substr($sql->fields['9'], 0, 10);
				$time_out 		= substr($sql->fields['9'], 11, 8);
				$prod_date 		= $sql->fields['10'];
				$exp_date 		= $sql->fields['11'];
				$exist	 		= $sql->RecordCount();

				$tahun = substr($exp_date, 0, 4);
				$bulan = substr($exp_date, 5, 2);
				$hari  = substr($exp_date, 8, 2);

				$exp = $tahun.$bulan.$hari;
				
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
							<td width="50px" rowspan="3">
								<img style="max-height: 50px;" src="../img_qrcode/'.$qrname.'" />
							</td>
							<td class="fontsize_title">
								&nbsp; '.$partno.'
							</td>
							<td class="fontsize">
								&nbsp; Baking Part
							</td>
						</tr>
						<tr>
							<td class="fontsize">
								&nbsp; Date In : '.$date_in.'
							</td>
							<td class="fontsize">
								&nbsp; Date Out : '.$date_out.'
							</td>
						</tr>
						<tr>
							<td class="fontsize">
								&nbsp; Qty: '.$qty.'
							</td>
							<td class="fontsize">
								&nbsp; Exp.Date: '.$exp_date.'
							</td>
						</tr>
					</table>';
					
					//	create format SATO
					$labelke = $count+1;
					$e 		= chr(27);
					$c	   	= chr(053);
					$label 	= $e.'A';
					$barcode = $partno . $c . $expid . $c . $id. $c . $exp;
					// $barcode= $id;
					$sato 	= '';
					$sato .= $e . 'A';
					$sato .= $e . 'H0040' . $e . 'V0040' . $e . '2D30,H,03,0,0' . $e . 'DS2,' . $barcode;
					$sato .= $e . 'H0180' . $e . 'V0020' . $e . 'L0202' . $e . 'S' . $partno;
					$sato .= $e . 'H0180' . $e . 'V0052' . $e . 'L0101' . $e . 'M' . 'In : ' . $date_in;
					$sato .= $e . 'H0180' . $e . 'V0082' . $e . 'L0101' . $e . 'M' . '     ' . $time_in;
					$sato .= $e . 'H0180' . $e . 'V0112' . $e . 'L0101' . $e . 'M' . 'Qty : ' . $qty;
					
					$sato .= $e . 'H0520' . $e . 'V0020' . $e . 'L0202' . $e . 'S' . 'Baking Part';
					$sato .= $e . 'H0520' . $e . 'V0052' . $e . 'L0101' . $e . 'M' . 'Out :' . $date_out;
					$sato .= $e . 'H0520' . $e . 'V0082' . $e . 'L0101' . $e . 'M' . '     ' . $time_out;
					$sato .= $e . 'H0520' . $e . 'V0112' . $e . 'L0101' . $e . 'XM' . 'Exp : ' . $exp_date;

					// $sato .= $e . 'H0180' . $e . 'V0030' . $e . 'L0202' . $e . 'S' . $partno;
					// $sato .= $e . 'H0180' . $e . 'V0093' . $e . 'L0101' . $e . 'M' . 'Prod.: ' . $prod_date;
					// $sato .= $e . 'H0180' . $e . 'V0093' . $e . 'L0101' . $e . 'M' . 'Prod.: ' . $prod_date;

					// $sato .= $e . 'H0520' . $e . 'V0030' . $e . 'L0101' . $e . 'M' . 'Qty: ' . $qty;
					// $sato .= $e . 'H0520' . $e . 'V0093' . $e . 'L0101' . $e . 'M' . 'Exp.: ' . $exp_date;
					$sato .= $e . 'Q1';
					$sato .= $e . 'Z';
					$qrcode_label = $sato;
					
					$cekip		= getenv("REMOTE_ADDR");
					$host		= gethostbyaddr($_SERVER['REMOTE_ADDR']);
					// echo '<br>'.$host		= '10.230.30.117';
					
					if($cekip == '10.230.30.125') {
						//echo 'pake ip';
					 //	$myfile = fopen("\\\\$host\\PrintSato\\print_". $Ymd . $His . '_'.$code.'_'.$orderno.'_'.$jigno . ".txt","w") or die(error_get_last());
					 	$host = 'newedp5';
					 	$myfile = fopen("\\\\$host\\PrintSato\\print_". $Ymd . $His . '_'.$id. ".txt","w") or die("Unable to open file! ".error_get_last());
					 	$txt 		= $qrcode_label;
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