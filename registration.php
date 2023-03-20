<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page allows non-registered users to create an account

****************/

require('connect.php');


// error variable for input fields
$usernameError;
$emailError;
$passwordError ;

//variables to store input fields values
$email;
$password;
$username;


if($_POST){
	print_r($_POST);

	function validateEmail(){
		if(isset($_POST['email'])){
            if(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) === false){
                $emailError = "Email is invalid";
                return false;
            }
            else{
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
				return true;
            }
        }
        else{
            $emailError = "Email is required";
            return false;
        }
	}


	function validateUsername(){
		if(empty($_POST['username']))
            {
                $usernameError = "Username is required";
                return false;
            }
        else{
        	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        	return true;
        }

	}

	function validatePassword(){
		if(isset($_POST['password'])){
			$regex ='/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/';
            if(!preg_match($regex, $_POST['password'])){
                $passwordError = "Password is Invalid";
                return false;
             }
             else{
             	$password = $_POST['password'];
             	return true;
             }
        }
        else{
            $passwordError = "Password is required";
            return false;

        }
	}
}
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registration page</title>
</head>

<body>
	<form method="post" action="registration.php">
		<label for="email">Email</label><br>
        <input id="email" name="email" type="email"><br><br>

        <!-- if email field has error,display error message--> 
        <?php if(isset($emailError)): ?>
            <span class="error"><?= $emailError ?></span><br>
        <?php endif ?>

        <label for="username">Username</label><br>
        <input id="username" name="username" type="text"><br><br>

        <!-- if username field has error,display error message--> 
        <?php if(isset($usernameError)): ?>
            <span class="error"><?= $usernameError ?></span><br>
        <?php endif ?>

        <label for="password">Password</label><br>
        <input id="password" name="password" type="text"><br><br>

        <!-- if password field has error,display error message--> 
        <?php if(isset($passwordError)): ?>
        <span class="error"><?= $passwordError ?></span><br>
        <?php endif ?>

		<button type="submit" value="Register" id="register">Register</button>	
	</form>



</body>
</html>