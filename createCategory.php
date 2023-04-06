<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 25th, 2023
    Description: This page allows admin and logegd-in users to create a category

****************/

require('connect.php');
session_start();


// error variable for input fields
$genreError = "";
$genreValid = true;
$successMesage = "";

$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();


//variable to save available genre from genretable
$genreArray = [];


//variables to store input fields values
$genre;


//select query to retrieve all database movie genres
$query = "SELECT * FROM genres ";


// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 


//fetch all genres and store in array
$genres = $statement->fetchAll();

//add genres to genre array
foreach($genres as $genre){
    array_push($genreArray, $genre['Name']);
}


if($_POST){
    if(!empty($_POST['genre'])){
        if(in_array($_POST['genre'], $genreArray) === true){
            $genreError = "* Genre exists already. Please enter another genre name";
            $genreValid = false;
        }
        else{
             $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
    }
    else{
        $genreError = "* Genre name is required";
        $genreValid = false;
    }
	

	if($genreValid){
        //  Build the parameterized SQL query and bind to the above sanitized values.
            $insertQuery = "INSERT INTO genres (Name) VALUES (:name)";
            $insertStatement = $db->prepare($insertQuery);


            //  Bind values to the parameters
            $insertStatement->bindValue(':name', $genre);

            //  Execute the INSERT.
            $insertStatement->execute();

            $successMesage = "Genre added successfully";

            //redirect to home page
            header("Refresh:2  url=pageAdministration.php");
	}

	}

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Create New Genre</title>
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
     <div class="container border border-2 rounded-5 border-danger mt-5 shadow-lg px-3">
    <h2 class="text-center  text-danger fw-bold mt-4">Create New Category</h2>
   
	<form method="post" action="createCategory.php">

        <div class=" form-floating mb-3 mt-3">
          <input type="text" class="form-control" id="genre2" placeholder="Enter genre" name="genre">
          <label for="genre2">Genre</label>
        </div>

        <!-- if useranme field has error,display error message--> 
        <?php if(isset($genreError)): ?>
            <span class="error text-danger"><?= $genreError ?></span><br>
        <?php endif ?>

		
      
		<button type="submit" id="addGenre" class="btn btn-danger fs-5">Create Genre</button>	
	</form>

    <?php if(isset($successMesage)): ?>
            <span class="error"><?= $successMesage ?></span><br>
    <?php endif ?>
</div>
	


</div>
</body>
</html>