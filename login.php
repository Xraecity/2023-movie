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


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login page</title>
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
	<div class="container border border-2 rounded-5 border-danger mt-5 shadow-lg">
	<h1 class="text-center  text-danger fw-bold mt-4">Login Form</h1>

		<div>
		 <?php if(isset($loginError)):?>
        	<span class="text-danger"><?= $loginError?></span><br>
        <?php endif ?>

        <?php if(isset($emptyfieldError)):?>
        	<span class="text-danger"><?= $emptyfieldError?></span><br>
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

		<button type="submit"  class ="btn btn-danger fs-5 " value="Login" id="login">	Login</button>
	</form><br>
	<p>Don't have an Account? <a href="registration.php " class=" btn btn-danger mb-3">Create Account</a></p>
</div>

</div>

</body>
</html>