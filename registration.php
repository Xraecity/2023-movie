<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page allows non-registered users to create an account

****************/

require('connect.php');

//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();


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
            $usernameError = "* Username exists already. Please enter another username";
            $usernameValid = false;
        }
        else{
             $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
    }
    else{
        $usernameError = "* Username is required";
        $usernameValid = false;

    }
	if(!empty($_POST['email'])){
        if(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) === false){
            $emailError = "* Email is invalid";
            $emailValid = false;
        }
        elseif(in_array($_POST['email'], $emailArray) === true){
            $emailError = "* Email is used already. Please enter another email";
            $emailValid = false;
        }
        else{
	    	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            }
        }
    else{
        $emailError = "* Email is required";
        $emailValid = false;
    }


		if(!empty($_POST['password1'])){
			$regex ='/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,}$/';
            if(!preg_match($regex, $_POST['password1'])){
                $passwordError1 = "* Password is Invalid";
                $passwordValid =  false;
             }
             else{
             	$password1 = $_POST['password1'];
             }
        }
        else{
            $passwordError1 = "* Password is required";
            $passwordValid =  false;
        }

        if(!empty($_POST['password2'])){  
            if($_POST['password1'] != $_POST['password2']){
                $passwordError2 = "* Passwords do not match. Please try again";
                $samePasswordCheck =  false;
             }
        }
        else{
            $passwordError2 = "* Please re-enter your password";
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

<?php include 'nav.php'; ?>
    <div class="container">
    <div class="container border border-2 rounded-5 border-primary mt-5 shadow-lg mb-3 px-3">

    <h1 class="text-primary text-center fw-bold mt-4">Registration Form</h1>
   
	<form method="post" action="registration.php">
        <div class=" form-floating mb-3 mt-3">
          <input type="text" class="form-control" id="username" placeholder="Enter email" name="username">
          <label for="username">Username</label>
        </div>



        <!-- if useranme field has error,display error message--> 
        <?php if(isset($usernameError)): ?>
            <span class="error text-primary"><?= $usernameError ?></span><br>
        <?php endif ?>



		<div class=" form-floating mb-3 mt-3">
          <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
          <label for="email">Email</label>
        </div>


        <!-- if email field has error,display error message--> 
        <?php if(isset($emailError)): ?>
            <span class="error text-primary"><?= $emailError ?></span><br>
        <?php endif ?>

        <div class="form-floating mb-3 mt-3">
          <input type="password" class="form-control" id="password1" placeholder="Enter password" name="password1">
          <label for="password1">Password</label>
        </div>

        <!-- if password field has error,display error message--> 
        <?php if(isset($passwordError1)): ?>
        <span class="error text-primary"><?= $passwordError1 ?></span><br>
        <?php endif ?>

        <div class="form-floating mb-3 mt-3">
          <input type="password" class="form-control" id="password2" placeholder="Enter password" name="password2">
          <label for="password2">Confirm Password</label>
        </div>

        <!-- if password field has error,display error message--> 
        <?php if(isset($passwordError2)): ?>
        <span class="error text-primary"><?= $passwordError2 ?></span><br>
        <?php endif ?>

        <div>Password must contain one digit from 1 to 9, one lowercase letter, one uppercase letter, one special character, no space, and it must be a minimum of 8 characters long.</div>
        <br>

		<button type="submit" class ="btn btn-primary fs-5" value="Register" id="register">Register</button>	
	</form><br>
   
	<p>Already have an account? <a href="login.php" class="btn btn-primary mb-3">Login Here!</a></p>
</div>
</div>



</body>
</html>