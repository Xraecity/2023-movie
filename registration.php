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


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registration page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
       <nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-gradient py-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Movies CMS</a>
    <?php if(isset($_SESSION['username'])): ?>
    <a class="navbar-brand fw-bold" href="pageAdministration.php"> <?= $_SESSION['username']?></a>
<?php endif ?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="movies.php">Movies</a>
        </li>
          <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="contact.php">Contact us</a>
        </li>

        <?php if(isset($_SESSION['username'])): ?>
         <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="logout.php">Log Out</a>
        </li>
        <?php else:  ?>
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="login.php">Login</a>
        </li>
         <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="registration.php">Sign Up</a>
        </li>
         <?php endif ?>

      
      </ul>
      <form class="d-flex" id="searchForm" action="searchKeyword.php" method="POST">
        <input class="form-control input-lg me-2" type="search" placeholder="Search Movies" aria-label="Search" id="searchKeyword" name="searchKeyword"> 
         <select name="genre" id="genre" class="form-select form-select-sm  me-2 " aria-label="Default select">
            <option value = "">All Genres</option>
           <?php  foreach($genres as $genre):?>
            <option value="<?=$genre['ID']?>"><?=$genre['Name']?></option>
           <?php endforeach?>
        </select>  
        <button class="btn btn-danger" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>
    <div class="container">
    <div class="container border border-2 rounded-5 border-danger mt-5 shadow-lg mb-3 px-3">

    <h1 class="text-danger text-center fw-bold mt-4">Registration Form</h1>
   
	<form method="post" action="registration.php">
        <div class=" form-floating mb-3 mt-3">
          <input type="text" class="form-control" id="username" placeholder="Enter email" name="username">
          <label for="username">Username</label>
        </div>



        <!-- if useranme field has error,display error message--> 
        <?php if(isset($usernameError)): ?>
            <span class="error text-danger"><?= $usernameError ?></span><br>
        <?php endif ?>



		<div class=" form-floating mb-3 mt-3">
          <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
          <label for="email">Email</label>
        </div>


        <!-- if email field has error,display error message--> 
        <?php if(isset($emailError)): ?>
            <span class="error text-danger"><?= $emailError ?></span><br>
        <?php endif ?>

        <div class="form-floating mb-3 mt-3">
          <input type="password" class="form-control" id="password1" placeholder="Enter password" name="password1">
          <label for="password1">Password</label>
        </div>

        <!-- if password field has error,display error message--> 
        <?php if(isset($passwordError1)): ?>
        <span class="error text-danger"><?= $passwordError1 ?></span><br>
        <?php endif ?>

        <div class="form-floating mb-3 mt-3">
          <input type="password" class="form-control" id="password2" placeholder="Enter password" name="password2">
          <label for="password2">Confirm Password</label>
        </div>

        <!-- if password field has error,display error message--> 
        <?php if(isset($passwordError2)): ?>
        <span class="error text-danger"><?= $passwordError2 ?></span><br>
        <?php endif ?>

        <div>Password must contain one digit from 1 to 9, one lowercase letter, one uppercase letter, one special character, no space, and it must be a minimum of 8 characters long.</div>
        <br>

		<button type="submit" class ="btn btn-danger fs-5" value="Register" id="register">Register</button>	
	</form><br>
   
	<p>Already have an account? <a href="login.php" class="btn btn-danger mb-3">Login Here!</a></p>
</div>
</div>



</body>
</html>