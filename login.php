<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page allows a user with registered account to login and have CRUD admin access

****************/

session_start();
require('connect.php');

//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();

$emptyfieldError;
$loginError;
$successMessage;

$email = "";

if($_POST){
	if ( empty($_POST['email']) || empty( $_POST['password'])) {
		// Could not get the data that should have been sent.
		$emptyfieldError = '* Fields cannot be empty. Please fill both email and password fields!';
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
				$loginError =  '* Login Failed. Incorrect password!';
		}
	    }
	    else {
			// Incorrect email
			$loginError = "* Login Failed.Incorrect email and/or password!";
	    }
        }
		
	}
?>

<?php include 'nav.php'; ?>

	<div class="container">
	<div class="container border border-2 rounded-5 border-primary mt-5 shadow-lg">
	<h1 class="text-center  text-primary fw-bold mt-4">Login Form</h1>

		<div>
		 <?php if(isset($loginError)):?>
        	<span class="text-primary"><?= $loginError?></span><br>
        <?php endif ?>

        <?php if(isset($emptyfieldError)):?>
        	<span class="text-primary"><?= $emptyfieldError?></span><br>
        <?php endif ?>

        <?php if(isset($successMessage)):?>
        	<h3><?= $successMessage ?></h3>
        <?php endif ?>
	</div>
	<form  method="post" action="login.php">
		<div class=" form-floating mb-3 mt-3">
		  <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
		  <label for="email">Email</label>
		</div>
		<div class="form-floating mb-3 mt-3">
		  <input type="password" class="form-control" id="password" placeholder="Enter password" name="password">
		  <label for="password">Password</label>
		</div>

		<button type="submit"  class ="btn btn-primary fs-5 " value="Login" id="login">	Login</button>
	</form><br>
	<p>Don't have an Account? <a href="registration.php " class=" btn btn-primary mb-3">Create Account</a></p>
</div>

</div>

</body>
</html>