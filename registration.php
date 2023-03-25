<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page allows non-registered users to create an account

****************/

require('connect.php');


// error variable for input fields
$emailError;
$usernameError;
$passwordError1;
$passwordError2;

//variable to save used email address and username from users table
$emailArray = [];
$usernameArray =[];

//variables to store input fields values
$email;
$username;
$password1;
$password2;

//boolean variables to check if field input are valid
$emailValid = true;
$usernameValid = true;
$passwordValid = true;
$samePasswordCheck = true;


//select query to retrieve all database email addresses
$query = "SELECT * FROM users ";


// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 


//fetch all blogs and store in array
$users = $statement->fetchAll();

//add user emails to email array
foreach($users as $user){
    array_push($emailArray, $user['Email']);
    array_push($usernameArray,$user['Username']);
}


if($_POST){
    if(!empty($_POST['username'])){
        if(in_array($_POST['username'], $usernameArray) === true){
            $usernameError = "Username exists already. Please enter another username";
            $usernameValid = false;
        }
        else{
             $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
    }
    else{
        $usernameError = "Username is required";
        $usernameValid = false;

    }
	if(!empty($_POST['email'])){
        if(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) === false){
            $emailError = "Email is invalid";
            $emailValid = false;
        }
        elseif(in_array($_POST['email'], $emailArray) === true){
            $emailError = "Email is used already. Please enter another email";
            $emailValid = false;
        }
        else{
	    	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            }
        }
    else{
        $emailError = "Email is required";
        $emailValid = false;
    }


		if(!empty($_POST['password1'])){
			$regex ='/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,}$/';
            if(!preg_match($regex, $_POST['password1'])){
                $passwordError1 = "Password is Invalid";
                $passwordValid =  false;
             }
             else{
             	$password1 = $_POST['password1'];
             }
        }
        else{
            $passwordError1 = "Password is required";
            $passwordValid =  false;
        }

        if(!empty($_POST['password2'])){  
            if($_POST['password1'] != $_POST['password2']){
                $passwordError2 = "Passwords do not match. Please try again";
                $samePasswordCheck =  false;
             }
        }
        else{
            $passwordError2 = "Please re-enter your password";
            $samePasswordCheck =  false;
        }

	if($emailValid && $usernameValid && $passwordValid && $samePasswordCheck){
		$hash_password_salt = password_hash($password1,
        PASSWORD_DEFAULT, array('cost' => 9));

        //  Build the parameterized SQL query and bind to the above sanitized values.
            $insertQuery = "INSERT INTO users (Username,Password,Email,Is_Admin) VALUES (:username,:password,:email,:Is_Admin)";
            $insertStatement = $db->prepare($insertQuery);


            //  Bind values to the parameters
            $insertStatement->bindValue(':username', $username);
            $insertStatement->bindValue(':email', $email);
            $insertStatement->bindValue(':password', $hash_password_salt);
            $insertStatement->bindValue(':Is_Admin', 0);

            //  Execute the INSERT.
            $insertStatement->execute();

            //redirect to home page
            header("Location: index.php");
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

        <label for="username">Username</label><br>
        <input id="username" name="username" type="text"><br><br>


        <!-- if useranme field has error,display error message--> 
        <?php if(isset($usernameError)): ?>
            <span class="error"><?= $usernameError ?></span><br>
        <?php endif ?>

		<label for="email">Email</label><br>
        <input id="email" name="email" type="email"><br><br>


        <!-- if email field has error,display error message--> 
        <?php if(isset($emailError)): ?>
            <span class="error"><?= $emailError ?></span><br>
        <?php endif ?>


        <label for="password1">Password</label><br>
        <input id="password1" name="password1" type="password"><br>

        <!-- if password field has error,display error message--> 
        <?php if(isset($passwordError1)): ?>
        <span class="error"><?= $passwordError1 ?></span><br>
        <?php endif ?>

        <label for="password2">Re-enter Password</label><br>
        <input id="password2" name="password2" type="password"><br>

        <!-- if password field has error,display error message--> 
        <?php if(isset($passwordError2)): ?>
        <span class="error"><?= $passwordError2 ?></span><br>
        <?php endif ?>

        <div>Password must contain one digit from 1 to 9, one lowercase letter, one uppercase letter, one special character, no space, and it must be a minimum of 8 characters long.</div>

		<button type="submit" value="Register" id="register">Register</button>	
	</form>
	<p>Already having an account?<a href="login.php">  Login Here!</a></p>



</body>
</html>