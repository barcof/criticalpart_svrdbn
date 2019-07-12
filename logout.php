<?php
session_start();

// session_unset();

session_destroy();

if (isset($_SESSION['username'])) {
	echo "{	'success':'true','msg': '<p align=\"center\">Byeeee...</p>' }";
	return;
} else {
	echo "{	'success':'false','msg': '<p align=\"center\">Terjadi kesalahan saat logout</p>' }";
	return;
}

?>