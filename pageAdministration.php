<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page is users account page where they can view,update,delete pages

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



//variable to store movies
$movies = [];


//check if session exists
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: login.php');
}

//default movie display
//fetch data from movies table 

// check if is is admin or registered user
if($_SESSION['isAdmin'] == 1){
//select query
$titleQuery = "SELECT * FROM movies ORDER BY name ASC";

// A PDO::Statement is prepared from the query.
$titleStatement = $db->prepare($titleQuery);

// Execution on the DB server is delayed until we execute().
$titleStatement->execute(); 

//fetch all movies and store in array
$movies = $titleStatement->fetchAll();
}
else{
	//select query
	$titleQuery = "SELECT * FROM movies WHERE User_ID = :userID ORDER BY name ASC";

	// A PDO::Statement is prepared from the query.
	$titleStatement = $db->prepare($titleQuery);
	$titleStatement->bindValue(':userID', $_SESSION['id']);

	// Execution on the DB server is delayed until we execute().
	$titleStatement->execute(); 

	//fetch all movies and store in array
	$movies = $titleStatement->fetchAll();

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

<?php include 'nav.php'; ?>


	<div class="container bg-white bg-gradient my-3 py-3">
	
	<div>
		<h2 class="text-primary fw-bold">Page Administration</h2>
		
	</div>

	<div>
		<h3>Welcome <?= $_SESSION['username']?></h3>
	</div>
	
    <div class="container border border-primary border-2 rounded my-4 px-2 shadow-lg">
    <h2 class="fw-bold">Movies List</h2>
    <a class= "btn btn-primary mb-2" href="pageCreate.php">Add Movie</a>

    <ul class="list-group"> 
    	<?php foreach($movies as $movie): ?>
    		<li class="list-group-item mb-2"><?= $movie['Name']?>
    			<a class="btn btn-secondary mb-2" href="pageUpdate.php?id=<?= $movie['Id']?>">Update</a>
    			<a onclick="return confirm('Are you sure you want to delete?')" class="btn btn-outline-danger mb-2" href="pageDelete.php?id=<?= $movie['Id']?>">Delete</a>
    		</li>
    	<?php endforeach ?>

    </ul>
</div>


    <div class="container border border-primary border-2 rounded my-4 px-2 shadow-lg">
    <h2 class="fw-bold">Movie Genres List</h2>
    <a class="btn btn-primary mb-2" href="createCategory.php">Create Genre</a>

    <ul class="list-group">
    	<?php foreach($genres as $genre): ?>
    		<li class="list-group-item mb-2"><?= $genre['Name']?>
    			<a class="btn btn-secondary mb-2" href="updateCategory.php?id=<?= $genre['ID']?>">Update</a>
    		</li>
    	<?php endforeach ?>

    </ul>
</div>



  <?php if($_SESSION['isAdmin'] == 1): ?>
  <div class="container border border-primary border-2 rounded my-4 px-2 shadow-lg">
  	<div>
  		<h2 class="fw-bold">Manage Users</h2>
  		<a class = "btn btn-primary mb-2" href="addUser.php">Add User</a>
  	</div>
  	

    <ul class="list-group">
    	<?php foreach($users as $user): ?>
    		<li class="list-group-item mb-2">
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

    			<a class="btn btn-secondary mb-2" href="updateUser.php?id=<?= $user['ID']?>">Update</a>
    			<a  class= "btn btn-outline-danger mb-2" onclick="return confirm('Are you sure you want to delete?')" href="deleteUser.php?id=<?= $user['ID']?>">Delete</a>
    			
    		</li>
    	<?php endforeach ?>

    </ul>


  <?php endif ?>
</div>


  

</div>

</body>
</html>