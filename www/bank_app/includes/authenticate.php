<?php

if(!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_name']) ){
	
	header("Location:login.php?error=Login is needed to access this page");
	}


?>