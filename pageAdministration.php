<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page is users account page where they can view,update,delete pages

****************/

session_start();
require('connect.php');


//check if session exists
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: login.php');
}

//if logout button is clicked
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['id']);
    unset($_SESSION['isAdmin']);
    header("location: index.php");
}

//fetch data from movies table 
//select query
$movieQuery = "SELECT * FROM movies ORDER BY name ASC";

// A PDO::Statement is prepared from the query.
$movieStatement = $db->prepare($movieQuery);

// Execution on the DB server is delayed until we execute().
$movieStatement->execute(); 


//fetch all movies and store in array
$movies = $movieStatement->fetchAll();


//fetch data from users table
//select query
$userQuery = "SELECT * FROM users ORDER BY Username ASC";

// A PDO::Statement is prepared from the query.
$userStatement = $db->prepare($userQuery);

// Execution on the DB server is delayed until we execute().
$userStatement->execute(); 


//fetch all blogs and store in array
$users = $userStatement->fetchAll();




?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login page</title>
</head>

<body>
	<div>
		<h2>Page Administration</h2>
		<button><a href="index.php?logout='1'">Log out</a></button>
	</div>

	<div>
		<h3>Welcome <?= $_SESSION['username']?></h3>
		<button><a href="pageCreate.php">Add Movie</a></button>
	</div>
	
    
    <h2>Movies List</h2>

    <ul>
    	<?php foreach($movies as $movie): ?>
    		<li><?= $movie['Name']?>
    			<button><a = href="pageUpdate.php">Update</a></button>
    			<button><a = href="pageDelete.php">Delete</a></button>
    		</li>
    	<?php endforeach ?>

    </ul>


  <?php if($_SESSION['isAdmin'] == 1): ?>
  	<div>
  		<h2>Manage Users</h2>
  		<button><a href="registration.php">Add User</a></button>
  	</div>
  	

    <ul>
    	<?php foreach($users as $user): ?>
    		<li>
    			<p>User_ID:  <?=$user['ID']?></p>
    			<p>Username: <?= $user['Username']?></p>
    			<p>Password: <?= $user['Password']?></p>
    			<p>Email: <?= $user['Email']?></p>
    			<p>Is_Admin: 
    			 <?php if($user['Is_Admin'] == 1):?>
    			 	Yes
    			 <?php else: ?>
    			     No
    			 <?php endif ?>
    			</p>

    			<button><a href="updateUser.php">Update</a></button>
    			<button><a href="deleteUser.php">Delete</a></button>
    			
    		</li>
    	<?php endforeach ?>

    </ul>


  <?php endif ?>



</body>
</html>