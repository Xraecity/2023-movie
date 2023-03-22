<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page allows a user with registered account to login and have CRUD admin access

****************/

session_start();

$emptyfieldError;
$loginError;
$successMessage;

if($_POST){
	if ( empty($_POST['username']) || empty( $_POST['password'])) {
		// Could not get the data that should have been sent.
		$emptyfieldError = 'Fields cannot be empty. Please fill both the username and password fields!';
	}
	else{  
	    require('connect.php');
		//sanitize username field and store in a variable
		$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		//select query
	    $query = "SELECT ID,Password,Is_Admin FROM users WHERE Username = :username";

	    // A PDO::Statement is prepared from the query.
	    $statement = $db->prepare($query);
	    $statement->bindValue(':username', $username);

	    // Execution on the DB server is delayed until we execute().
	    $statement->execute(); 
	    $userDetails = $statement->fetch();

	    if(!empty($userDetails)){
	        if (password_verify($_POST['password'], $userDetails['Password'])){
				// Verification success! User has logged-in!
				// Create sessions, so we know the user is logged in.
				$_SESSION['username'] = $_POST['username'];
				$_SESSION['id'] = $userDetails['ID'];
				$_SESSION['isAdmin'] = $userDetails['Is_Admin'];
				$successMessage =  'Login successful!';
				header("Refresh:5; url=pageAdministration.php");
		       } 

		    else {
				// Incorrect password
				$loginError =  'Login Failed. Incorrect password!';
		}
	    }
	    else {
			// Incorrect username
			$loginError = " Login Failed.Incorrect username and/or password!";
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
	<h1>Login</h1>
	<div>
		 <?php if(isset($loginError)):?>
        	<span class="error"><?= $loginError?></span><br>
        <?php endif ?>

        <?php if(isset($emptyfieldError)):?>
        	<span class="error"><?= $emptyfieldError?></span><br>
        <?php endif ?>
	</div>
	<form method="post" action="login.php">
		<label for="username">Username</label><br>
        <input id="username" name="username" type="text"><br><br>
       
        <label for="password">Password</label><br>
        <input id="password" name="password" type="password" placeholder="password"><br><br>

       

		<button type="submit" value="Login" id="login">	Login</button>
	</form>
	<?php if(isset($successMessage)):?>
        	<h3><?= $successMessage ?></h3>
    <?php endif ?>


	<p>Don't have an Account?<a href="registration.php">Create Account</a></p>



</body>
</html>