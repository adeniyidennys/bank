<?php
if(!isset($_SESSION['id'])){
	header("location:login.php?error = This page requires login");
	die();
	
	}
?>	