<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page is users account page where they can view,update,delete pages

****************/

session_start();
require('connect.php');

//variable to store movies
$movies = [];
$selectError="";
$movieListSort = "title";


//check if session exists
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: login.php');
}

//default movie display
//fetch data from movies table 
//select query
$titleQuery = "SELECT * FROM movies ORDER BY name ASC";

// A PDO::Statement is prepared from the query.
$titleStatement = $db->prepare($titleQuery);

// Execution on the DB server is delayed until we execute().
$titleStatement->execute(); 

//fetch all movies and store in array
$movies = $titleStatement->fetchAll();

//sort movie list
if($_POST){
if(isset($_POST['movieListSort'])){
	$movieListSort = filter_input(INPUT_POST,'movieListSort',FILTER_SANITIZE_STRING);
	echo($movieListSort);

	if($movieListSort == "title"){
		//fetch data from movies table 
		//select query
		$titleQuery = "SELECT * FROM movies ORDER BY name ASC";

		// A PDO::Statement is prepared from the query.
		$titleStatement = $db->prepare($titleQuery);

		// Execution on the DB server is delayed until we execute().
		$titleStatement->execute(); 


		//fetch all movies and store in array
		$movies = $titleStatement->fetchAll();
	}

	elseif($movieListSort == "Date-Created"){
		//fetch data from movies table 
		//select query
		$dateCreatedQuery = "SELECT * FROM movies ORDER BY Date_Created DESC";

		// A PDO::Statement is prepared from the query.
		$dateCreatedStatement = $db->prepare($dateCreatedQuery);

		// Execution on the DB server is delayed until we execute().
		$dateCreatedStatement->execute(); 


		//fetch all movies and store in array
		$movies = $dateCreatedStatement->fetchAll();
	}
	elseif($movieListSort = "Release-Date"){
		//fetch data from movies table 
		//select query
		$releaseDateQuery = "SELECT * FROM movies ORDER BY Release_Date DESC";

		// A PDO::Statement is prepared from the query.
		$releaseDateStatement = $db->prepare($releaseDateQuery);

		// Execution on the DB server is delayed until we execute().
		$releaseDateStatement->execute(); 


		//fetch all movies and store in array
		$movies = $releaseDateStatement->fetchAll();
	
	}
	else{
		$selectError = "Please select a sort option";

	}

}
}



//fetch data from users table
//select query
$userQuery = "SELECT * FROM users ORDER BY ID ASC";

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
	<h1><a href = "index.php">Movies CMS</a></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    
                    <?php if(isset($_SESSION['username'])): ?>
                    <li><a href="pageAdministration.php"><?= $_SESSION['username']?></a></li>
                    <button><a href="logout.php">Log out</a></button>

                    <?php else:?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="registration.php">Register</a></li>
                    <?php endif?>

                </ul>
            </nav>
	<div>
		<h2>Page Administration</h2>
		
	</div>

	<div>
		<h3>Welcome <?= $_SESSION['username']?></h3>
		<button><a href="pageCreate.php">Add Movie</a></button>
	</div>
	
    
    <h2>Movies List</h2>
    <!--sort movies list by title, cretaion date or release date-->
    <form method="post" accept="pageAdministration.php">
    	<label for="movieListSort">Sort Movies List by:</label>
        <select name="movieListSort" id="moviesListSort">
	        <option value="title" selected >Title</option>
		    <option value="Date-Created">Date Created</option>
		    <option value="Release-Date" >Movie Release Date</option>
	    </select>
	    <button type="submit" name="submit" id="submit">Submit</button>

	    <?php if(isset($selectError)):?>
	    	<span class="error"><?= $selectError?></span>
	    <?php endif?>
    </form>

    <p><b>Movies sorted by <?= $movieListSort?></b></p>
    

    <ul>
    	<?php foreach($movies as $movie): ?>
    		<li><?= $movie['Name']?>
    			<button><a = href="pageUpdate.php?id=<?= $movie['Id']?>">Update</a></button>
    			<button onclick="return confirm('Are you sure you want to delete?')"><a = href="pageDelete.php?id=<?= $movie['Id']?>">Delete</a></button>
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
    			<p>Username:  <?=$user['Username']?></p>
    			<p>Email: <?= $user['Email']?></p>
    			<p>Password: <?= $user['Password']?></p>
    			<p>Is_Admin: 
    			 <?php if($user['Is_Admin'] == 1):?>
    			 	Yes
    			 <?php else: ?>
    			     No
    			 <?php endif ?>
    			</p>

    			<button><a href="updateUser.php?id=<?= $user['ID']?>">Update</a></button>
    			<button onclick="return confirm('Are you sure you want to delete?')"><a href="deleteUser.php?id=<?= $user['ID']?>">Delete</a></button>
    			
    		</li>
    	<?php endforeach ?>

    </ul>


  <?php endif ?>


  



</body>
</html>