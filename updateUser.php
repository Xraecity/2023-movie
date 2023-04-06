<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 21, 2023
    Description: this page allows admin users to edit a users information(validates and sanitizes input fields) and updates it in the users table in database

****************/
require('connect.php');
session_start();

//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();


//variable for field error message
$username_error;
$email_error;
$password_error;
$passwordCheck_error;

//boolean variables to check if field input are valid
$emailValid = true;
$usernameValid = true;
$passwordValid = true;
$samePasswordCheck = true;




// Retrieve user to be edited, if id GET parameter is in URL.
if(isset($_GET['id'])){
    // Sanitize the id. Like above but this time from INPUT_GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    //validate int
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) !== false){
        // Build the parametrized SQL query using the filtered id.
        $query = "SELECT * FROM users WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
                
        // Execute the SELECT and fetch the single row returned.
        $statement->execute();
        $user = $statement->fetch();
    }
    else{
        header("Location:pageAdministration.php");
    }


}

// UPDATE if all user form fields are not empty.
if($_POST && !empty($_POST['update'])){
   if(!empty($_POST['username'])) {    
        $username =filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
   } 
   else {
        $username_error = "* Username is required";
        $usernameValid = false;
   }

   if(!empty($_POST['email'])) {    
        if(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) === false){
            $email_error = "* Email is invalid";
            $emailValid = false;
        }
        else{
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);      
          }       
   } 
   else {
        $email_error = "* Email is required";
        $emailValid = false;
   }

   if(!empty($_POST['password1'])){
        $regex ='/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.* ).{8,}$/';
        if(!preg_match($regex, $_POST['password1'])){
            $password_error = "* Password is Invalid";
            $passwordValid  = false;
         }
        else{
            $password1 = $_POST['password1'];
        }
    }
    else{
        $password_error = "* Password is required";
        $passwordValid  = false;
   }

   if(!empty($_POST['password2'])){  
        if($_POST['password1'] != $_POST['password2']){
            $passwordCheck_error = "* Passwords do not match. Please try again";
            $samePasswordCheck  = false;
        }
    }
    else{
        $passwordCheck_error = "* Please re-enter your password";
        $samePasswordCheck = false;
    }



    if($emailValid && $usernameValid && $passwordValid && $samePasswordCheck){
        $hash_password_salt = password_hash($password1,
        PASSWORD_DEFAULT, array('cost' => 9));

        // if user inputted id is an integer
        if(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) !== false){

            // Build the parameterized SQL query and bind to the above sanitized values.
            $query = "UPDATE users SET Username = :username, Password = :password, Email = :email  WHERE ID = :id";
            $statement = $db->prepare($query);

            $statement->bindValue(':username', $username);        
            $statement->bindValue(':email', $email);
            $statement->bindValue(':password', $hash_password_salt);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
                
            // Execute the UPDATE
            $statement->execute();

            header("Location:pageAdministration.php");
            exit;  
        }
        // if user inputted value is not an integer
        else{
            header("Location: pageAdministration.php"); 
        }  
    }
   
}



    



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Edit this User!</title>
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
        <h2 class="text-center  text-danger fw-bold mt-4">Update User</h2>
   
    <!-- Remember that alternative syntax is good and html inside php is bad -->
            <form method="post">
                <!-- Hidden input for the quote primary key. -->
                <input type="hidden" id="id" name="id" value="<?= $user['ID'] ?>">

                 <!-- Quote title and content are echoed into the input value attributes. -->
                <label for="username" class="form-label fs-5 fw-bold">Username</label><br>
                <input type="text" id="username" class="form-control" name="username" value="<?= $user['Username']?>"><br><br>

                <?php if(isset($username_error)): ?>
                <span class="error text-danger"><?= $username_error ?></span><br>
                <?php endif ?>

                <label for="email" class="form-label fs-5 fw-bold">Email</label><br>
                <input type= "email" id="email" class="form-control" name="email" value="<?= $user['Email']?>"><br><br>

                <?php if(isset($email_error)): ?>
                <span class="error text-danger"><?= $email_error ?></span><br>
                <?php endif ?>

                <label for="password1" class="form-label fs-5 fw-bold">Password</label><br>
                <input type= "password" id="password1" class="form-control" name="password1" value="<?= $user['Password']?>"><br><br>

                <?php if(isset($password_error)): ?>
                <span class="error text-danger"><?= $password_error ?></span><br>
                <?php endif ?>

                <label for="password2" class="form-label fs-5 fw-bold">Re-enter Password</label><br>
                <input type= "password" id="password2" class="form-control" name="password2" value="<?= $user['Password']?>"><br><br>

                <?php if(isset($passwordCheck_error)): ?>
                <span class="error text-danger"><?= $passwordCheck_error ?></span><br>
                <?php endif ?>


                <button type="submit" value="update " id="submit" class="btn btn-danger fs-5 mb-3" name="update">Update</button><br>

            </form> 
            </div>     
            </div>  
</body>
</html>