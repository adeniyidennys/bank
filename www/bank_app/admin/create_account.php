<?php
session_start();
include('../includes/authenticate.php');
include('../includes/db.php');
if(isset($_POST['submit'])){
	$error = array();
	if(empty($_POST['account_name'])){
		$error['account_name'] = "Enter Account Name";
		}
	if(empty($_POST['account_balance'])){
		$error['account_balance'] = "Enter Account Balance";
		}
	if(empty($_POST['account_type'])){
		$error['account_type'] = "Select Account Type";
		}
		
	if(!is_numeric($_POST['account_balance'])){
		$error['account_balance'] = "Numeric Value Required";
		}
		
	if(empty($error)){
		
		$account = "309".rand(1000000,9999999);
		$stmt = $conn->prepare("INSERT INTO customer VALUES(NULL,:anm,:an,:at,:ab,NOW(),NOW())");
		
		$data = array(
		":anm"=>$_POST['account_name'],
		":an"=>$account,
		":at"=>$_POST['account_type'],
		":ab"=>$_POST['account_balance']
		
		);
		
		$stmt->execute($data);
		
		header("location:view_account.php");
		
		}		
	
	
	}






?>










<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Create Account</title>
</head>

<body>
<?php
include 'admin_header.php';

?>

<form action="" method="post">
<p>Account Name <input type="text" name="account_name"/></p> 
<p>Account balance <input type="text" name="account_balance"/></p>

<select name="account_type">
<option disabled="disabled" selected="selected" >--Select Account Type</option>
<option value="savings">Savings</option>
<option value="current">Current</option>
</select>
<br/>
<br/>
<input type="submit" name="submit" value="Create Account"/>




</form>








</body>
</html>