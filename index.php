<?php 
require('connect.php');
session_start();

$query = "SELECT * FROM movies ORDER BY RAND() LIMIT 3";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 


//fetch all movies and store in array
$movies = $statement->fetchAll();

$carouselQuery = "SELECT * FROM movies ORDER BY RAND() LIMIT 5";

// A PDO::Statement is prepared from the query.
$carouselStatement = $db->prepare($carouselQuery);

// Execution on the DB server is delayed until we execute().
$carouselStatement->execute(); 


//fetch all movies and store in array
$carouselMovies = $carouselStatement->fetchAll();

//function to truncate blog comment greater than 200 words
function truncate($text) {
    $text = substr($text,0,100);
    return $text;
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
     <link rel="stylesheet" href="styles.css">
    <title>Welcome to Movies world</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-gradient py-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Movies CMS</a>
    <?php if(isset($_SESSION['username'])): ?>
    <a class="navbar-brand fw-bold userbutton" href="pageAdministration.php"> <?= $_SESSION['username']?></a>
<?php endif ?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item ">
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
    </div>
  </div>
</nav>
<div class="card bg-dark text-white">
  <img src="Image_Uploads/background.jpg" class="card-img" alt="index-background">
  <div class="card-img-overlay d-flex align-items-center ">
    <div class=" card bg-dark center w-50 h-50 bg-gradient rounded-start rounded-5 border border-danger border-3 mx-5 ">
        <h5 class="card-title text-center text-uppercase fs-1 fw-bold pt-5">Movies World</h5>
        <p class="card-text text-center fs-3 fw-normal">Searching for a Good Movie to Watch?</p>
        <p class="card-text text-center fs-3 fw-normal">View and search for latest trending movies</p>
</div>
  </div>
</div>

        <div class="container border border-3 border-danger rounded-4 shadow-lg my-5">
            <h3 class="mt-5 fw-bold text-center">LATEST MOVIES</h3>
            <div class="row mt-4 mb-2 ">
            <?php foreach($movies as $movie): ?>
            <div class="col mb-2">
                <?php
                //select image query to get all page images
                $homeimageQuery = "SELECT * FROM images WHERE Movie_ID = :id";
                $homeimageStatement = $db->prepare($homeimageQuery);
                $homeimageStatement->bindValue(':id', $movie['Id'], PDO::PARAM_INT);
                $homeimageStatement->execute(); 
                //fetch all images  and store in array
                $Homeimages = $homeimageStatement->fetchAll();
                ?>
                <?php if(!empty($Homeimages)): ?>
               <div class="card h-100 rounded shadow" style="width: 18rem;">
                  <img src="Image_Uploads/<?=$Homeimages[0]['name']?>" class="card-img-top" alt="<?=$movie['Name']?>">
                  <div class="card-body">
                    <h4 class="card-title"><?= $movie['Name'] ?></h4>
                    <p class="card-text"><?= truncate($movie['Description'])?>...</p>
                  </div>
                </div>
              <?php else: ?>
                <div class="card h-100" style="width: 18rem;">
                  <img src="" class="card-img-top" alt="">
                  <div class="card-body">
                    <h4 class="card-title"><?= $movie['Name'] ?></h4>
                    <p class="card-text"><?=$movie['Description']?></p>
                  </div>
              </div>
             <?php endif ?>
            </div>
  
        <?php endforeach ?>
            </div><br>
            <a href="movies.php" class="btn btn-danger mb-2">View More</a>
       </div>

      
    


    


</body>
</html>