<?php

session_start();
include 'authenticate.php';
include '../includes/db.php';
include 'user_info.php';



if(isset($_POST['pay'])){
	$issue = array();
	
if(empty($_POST['account_number'])){
	
	$issue['account_number'] = "Please Enter Account Number";
}elseif(!is_numeric($_POST['account_number'])){
	$issue['account_number'] = "Enter a numeric value";
	}	
if(empty($_POST['amount'])){
	$issue['amount'] = "specify amount";
	
}elseif(!is_numeric($_POST['amount'])){
	$issue['amount'] = "Enter a numeric value";
	}		
if(empty($issue)){
	
	//check if current user has up to that amount
	if($_POST['amount'] > $current_user_data['account_balance']){
		header('location:transfer.php?error=insufficient funds ');
		exit();
		
		}
	$fetch_beneficiary = $conn->prepare("SELECT * FROM customer WHERE account_number = :an");
	$fetch_beneficiary->bindParam(":an",$_POST['account_number']);
	$fetch_beneficiary->execute();
	
	//if the record of the beneficiary does not exist
	if($fetch_beneficiary->rowCount() < 1){
		header("location:transfer.php?error=Account Number Does not exist");
		exit();
		}	
	//	if it exist, collect beneficiary record
	$beneficiary_record = $fetch_beneficiary->fetch(PDO::FETCH_BOTH);
	
	//check if user is playing on the app
	if($current_user_data['customer_id'] == $beneficiary_record['customer_id']){
		header('location:transfer.php?=You cannot send money to yorself');
		exit();
		}
		//DEBIT TRANSACTION
		$senders_open_balance = $current_user_data['account_balance'];
		$senders_closing_balance = $senders_open_balance - $_POST['amount'];
		
	$debit = $conn->prepare("UPDATE customer SET account_balance = :ab WHERE account_number =:cua");
	$debit->bindParam(":ab",$senders_closing_balance);
	$debit->bindParam(":cua",$current_user_data['account_number']);
	$debit->execute();
		
	//log a transaction
  $debit_transaction = $conn->prepare("INSERT INTO transaction VALUES(NULL,:sa,:ra,:ta,:pb,:fb,:tt,:cst,NOW(),NOW())");
  $data = array(
     ":sa"=>$current_user_data['account_number'],
	 ":ra"=>$beneficiary_record['account_number'],
	 ":ta"=>$_POST['amount'],
	 ":pb"=>$senders_open_balance,
	 ":fb"=>$senders_closing_balance,
	 ":tt"=>"debit",
	 ":cst"=>$current_user_data['customer_id']
  ); 		
	
	$debit_transaction->execute($data);
	
	//CREDIT TRANSACTION
	
	$beneficiary_opening_balance = $beneficiary_record['account_balance'];
	$beficiary_closing_balance = $beneficiary_opening_balance + $_POST['amount'];
	
	$credit = $conn->prepare("UPDATE customer SET account_balance = :ab WHERE account_number = :ban");
	$credit->bindParam(":ab",$beficiary_closing_balance);
	$credit->bindParam(":ban",$beneficiary_record['account_number']);
	$credit->execute();
	header("location:transfer.php");
	
	//log a transaction
	
	$credit_transaction = $conn->prepare("INSERT INTO transaction VALUES(NULL,:sa,:ra,:ta,:pb,:fb,:tt,:cst,NOW(),NOW())");
	$credit_data = array(
	         ":sa"=>$current_user_data['account_number'],
	        ":ra"=>$beneficiary_record['account_balance'],
			":ta"=>$_POST['amount'],
			":pb"=>$beneficiary_opening_balance,
			":fb"=>$beficiary_closing_balance,
			":tt"=>"credit",
			":cst"=>$beneficiary_record['customer_id']          
	       );
	$credit_transaction->execute($credit_data);
	
	header("location:transfer.php?success=Transfer Successful");
	
	}	
	
	
	}




?>





<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Transfer</title>
</head>

<body>

<?php

include 'user_header.php';

if(isset($_GET['error'])){
	
	echo "<p style='color:red'>".$_GET['error']."</p>";
	}
if(isset($_GET['success'])){
	
	echo "<p style='color:green'>".$_GET['success']."</p>";
	}

?>


<form action="" method="post">
<?php
if(isset($issue['account_number'])){
	echo $issue['account_number'];
	}


?>

<p>Account Number: <input type="text" name="account_number"/></p>
<p>Transaction Amount: <input type="text" name="amount"/></p>
<input type="submit" name="pay" value="Transfer"/>


</form>









</body>
</html>