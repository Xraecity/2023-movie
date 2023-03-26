<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page allows a user with registered account to login and have CRUD admin access

****************/

session_start();
require('connect.php');

$emptyfieldError;
$loginError;
$successMessage;

$email = "";

if($_POST){
	if ( empty($_POST['email']) || empty( $_POST['password'])) {
		// Could not get the data that should have been sent.
		$emptyfieldError = 'Fields cannot be empty. Please fill both the username and password fields!';
	}
	else{  
	    if(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) === false){
	            $loginError = "Email is invalid";
	        }
        else{
	    	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);		
          }
          //select query
	    $query = "SELECT * FROM users WHERE Email = :email";

	    // A PDO::Statement is prepared from the query.
	    $statement = $db->prepare($query);
	    $statement->bindValue(':email',$email);

	    // Execution on the DB server is delayed until we execute().
	    $statement->execute(); 
	    $userDetails = $statement->fetch();

	    if(!empty($userDetails)){
	        if (password_verify($_POST['password'], $userDetails['Password'])){
				// Verification success! User has logged-in!
				// Create sessions, so we know the user is logged in.
				$_SESSION['username'] = $userDetails['Username'];
				$_SESSION['email'] = $_POST['email'];
				$_SESSION['id'] = $userDetails['ID'];
				$_SESSION['isAdmin'] = $userDetails['Is_Admin'];
				$successMessage =  'Login successful!';
			    header("Refresh:3,url=pageAdministration.php" );

		       } 

		    else {
				// Incorrect password
				$loginError =  'Login Failed. Incorrect password!';
		}
	    }
	    else {
			// Incorrect email
			$loginError = " Login Failed.Incorrect email and/or password!";
	    }
        }
		
	}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login page</title>
</head>

<body>
	<div class="block">
		<?php include("header.php")?>
	<h1>Login</h1>
	<div>
		 <?php if(isset($loginError)):?>
        	<span class="error"><?= $loginError?></span><br>
        <?php endif ?>

        <?php if(isset($emptyfieldError)):?>
        	<span class="error"><?= $emptyfieldError?></span><br>
        <?php endif ?>

        <?php if(isset($successMessage)):?>
        	<h3><?= $successMessage ?></h3>
        <?php endif ?>
	</div>
	<form method="post" action="login.php">
		<label for="email">Email</label><br>
        <input id="email" name="email" type="email"><br><br>
       
        <label for="password">Password</label><br>
        <input id="password" name="password" type="password" placeholder="password"><br><br>

       

		<button type="submit" value="Login" id="login">	Login</button>
	</form>
	


	<p>Don't have an Account?<a href="registration.php">Create Account</a></p>

</div>

</body>
</html>