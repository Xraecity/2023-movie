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



// categories query
$genreQuery = "SELECT * FROM genres";

// A PDO::Statement is prepared from the query.
$genreStatement = $db->prepare($genreQuery);

// Execution on the DB server is delayed until we execute().
$genreStatement->execute(); 


//fetch all blogs and store in array
$genres = $genreStatement->fetchAll();




?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Administration page</title>
	<link rel="stylesheet" href="styles.css">
</head>

<body>
	<?php include("header.php")?>
	<div class="block">
	
	<div>
		<h2>Page Administration</h2>
		
	</div>

	<div>
		<h3>Welcome <?= $_SESSION['username']?></h3>
	</div>
	
    
    <h2>Movies List</h2>
    <button><a href="pageCreate.php">Add Movie</a></button><br><br>
    <!--sort movies list by title, creation date or release date-->
    <form method="post" action="pageAdministration.php">
    	<label for="movieListSort">Sort Movies List by:</label>
        <select name="movieListSort" id="moviesListSort">
	        <option value="title" selected >Title</option>
		    <option value="Date-Created">Date Created</option>
		    <option value="Release-Date" >Movie Release Date</option>
	    </select>
	    <button type="submit" name="submit" id="submit">Submit</button><br>

	    <?php if(isset($selectError)):?>
	    	<span class="error"><?= $selectError?></span>
	    <?php endif?>
    </form>

    <p><b>Movies sorted by <?= $movieListSort?></b></p>
    

    <ul>
    	<?php foreach($movies as $movie): ?>
    		<li class="lists"><?= $movie['Name']?>
    			<button><a = href="pageUpdate.php?id=<?= $movie['Id']?>">Update</a></button>
    			<button onclick="return confirm('Are you sure you want to delete?')"><a = href="pageDelete.php?id=<?= $movie['Id']?>">Delete</a></button>
    		</li>
    	<?php endforeach ?>

    </ul>



    <h2>Movie Genres List</h2>
    <button><a href="createCategory.php">Create Genre</a></button>

    <ul>
    	<?php foreach($genres as $genre): ?>
    		<li class="lists"><?= $genre['Name']?>
    			<button><a = href="updateCategory.php?id=<?= $genre['ID']?>">Update</a></button>
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
    		<li class="lists">
    			<p>User_ID:  <?=$user['ID']?></p>
    			<p>Username:  <?=$user['Username']?></p>
    			<p>Email: <?= $user['Email']?></p>
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


  

</div>

</body>
</html>