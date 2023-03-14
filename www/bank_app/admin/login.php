<?php 

session_start();
include '../includes/db.php';
if(isset($_POST['submit'])){
	
	$error = array();
	if(empty($_POST['email'])){
		$error['email'] = "Enter email";
		}
	if(empty($_POST['hash'])){
		$error['hash'] = "Enter Password";
		}
	if(empty($error)){
		
		$stmt = $conn->prepare("SELECT * FROM admin WHERE email = :em");
		$stmt->bindParam(":em",$_POST['email']);
		$stmt->execute();
		$record = $stmt->fetch(PDO::FETCH_BOTH);
		
		
	//	echo $record['hash'];
		//echo $_POST['hash']; .php
		
		
		if($stmt->rowCount()>0 && password_verify($_POST['hash'],$record['hash'])){
			
			$_SESSION['admin_id'] = $record['admin_id'];
			$_SESSION['admin_name'] = $record['name'];
			header("Location:dashboard.php");
			exit();
			
			}else{
				header("Location:login.php?error=either email or password in correct");
				}
		}	
	
	}








?>







<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin Login</title>
</head>

<body>

<form action="" method="post">
<?php
if(isset($error['email'])){
	echo $error['email'];
	}	
?>
<p>Email: <input type="email" name="email" /> </p>

<?php
if(isset($error['hash'])){
	echo $error['hash'];
	}	
?>
<p>Password: <input type="password" name="hash" /></p>
<br/>
<input type="submit" name="submit" />


</form>







</body>
</html>