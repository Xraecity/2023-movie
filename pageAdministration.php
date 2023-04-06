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


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Administration page</title>
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
	<div class="container bg-white bg-gradient my-3 py-3">
	
	<div>
		<h2 class="text-danger fw-bold">Page Administration</h2>
		
	</div>

	<div>
		<h3>Welcome <?= $_SESSION['username']?></h3>
	</div>
	
    <div class="container border border-danger border-2 rounded my-4 px-2 shadow-lg">
    <h2 class="fw-bold">Movies List</h2>
    <a class= "btn btn-danger mb-2" href="pageCreate.php">Add Movie</a>

    <ul class="list-group"> 
    	<?php foreach($movies as $movie): ?>
    		<li class="list-group-item mb-2"><?= $movie['Name']?>
    			<a class="btn btn-secondary mb-2" href="pageUpdate.php?id=<?= $movie['Id']?>">Update</a>
    			<a onclick="return confirm('Are you sure you want to delete?')" class="btn btn-secondary mb-2" href="pageDelete.php?id=<?= $movie['Id']?>">Delete</a>
    		</li>
    	<?php endforeach ?>

    </ul>
</div>


    <div class="container border border-danger border-2 rounded my-4 px-2 shadow-lg">
    <h2 class="fw-bold">Movie Genres List</h2>
    <a class="btn btn-danger mb-2" href="createCategory.php">Create Genre</a>

    <ul class="list-group">
    	<?php foreach($genres as $genre): ?>
    		<li class="list-group-item mb-2"><?= $genre['Name']?>
    			<a class="btn btn-secondary mb-2" href="updateCategory.php?id=<?= $genre['ID']?>">Update</a>
    		</li>
    	<?php endforeach ?>

    </ul>
</div>



  <?php if($_SESSION['isAdmin'] == 1): ?>
  <div class="container border border-danger border-2 rounded my-4 px-2 shadow-lg">
  	<div>
  		<h2 class="fw-bold">Manage Users</h2>
  		<a class = "btn btn-danger mb-2" href="registration.php">Add User</a>
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
    			<a  class= "btn btn-secondary mb-2" onclick="return confirm('Are you sure you want to delete?')" href="deleteUser.php?id=<?= $user['ID']?>">Delete</a>
    			
    		</li>
    	<?php endforeach ?>

    </ul>


  <?php endif ?>
</div>


  

</div>

</body>
</html>