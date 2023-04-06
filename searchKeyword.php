<?php

require('connect.php');
session_start();

//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();


//variables 
$movies = [];
$inputError = "";
$keywordName = "";
$genre;

if(empty($_POST['genre'])){
	$keywordName  =  filter_input(INPUT_POST, 'searchKeyword',FILTER_SANITIZE_FULL_SPECIAL_CHARS );
    $pattern = "%". $keywordName ."%" ;
	//select query
	$query = "SELECT * FROM movies WHERE Name LIKE :pattern ORDER BY Name ASC"; 


	// A PDO::Statement is prepared from the query.
	$statement = $db->prepare($query);
	$statement->bindValue(':pattern', $pattern);  
	 

	// Execution on the DB server is delayed until we execute().
	$statement->execute(); 


	//fetch all movies and store in array
	$movies = $statement->fetchAll();

	if(empty($movies)){
	$inputError = "No movie found";
    }

}
else{
	if(isset($_POST['searchKeyword'])){
		if(filter_input(INPUT_POST,'genre',FILTER_VALIDATE_INT) !== false){
        $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_NUMBER_INT);

        $keywordName  =  filter_input(INPUT_POST, 'searchKeyword',FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $pattern = "%". $keywordName ."%" ;

        $query = "SELECT * FROM movies WHERE Name LIKE :pattern AND Genre = :genre ORDER BY Name ASC";

		// A PDO::Statement is prepared from the query.
		$statement = $db->prepare($query);
		$statement->bindValue(':pattern', $pattern); 
		$statement->bindValue(':genre', $genre);   
		 

		// Execution on the DB server is delayed until we execute().
		$statement->execute(); 


		//fetch all movies and store in array
		$movies = $statement->fetchAll();

		if(empty($movies)){
		$inputError = "No movie found";
	    }
      $genreNameQuery = "SELECT Name FROM genres WHERE id =:id";
      $genreNameStatement = $db->prepare($genreNameQuery);
      $genreNameStatement-> bindValue(':id', $genre);
      $genreNameStatement->execute();
      $genreName = $genreNameStatement->fetch();

      } 
      
    

	}
	else{
		if(filter_input(INPUT_POST,'genre',FILTER_VALIDATE_INT) !== false){
        $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_NUMBER_INT);


        $query = "SELECT * FROM movies WHERE Genre = :genre ORDER BY Name ASC";


		// A PDO::Statement is prepared from the query.
		$statement = $db->prepare($query);
		$statement->bindValue(':genre', $genre);   
		 

		// Execution on the DB server is delayed until we execute().
		$statement->execute(); 


		//fetch all movies and store in array
		$movies = $statement->fetchAll();

		if(empty($movies)){
		$inputError = "No movie found";
	    }
       $genreNameQuery = "SELECT Name FROM genres WHERE id =:id";
      $genreNameStatement = $db->prepare($genreNameQuery);
      $genreNameStatement-> bindValue(':id', $genre);
      $genreNameStatement->execute();
      $genreName = $genreNameStatement->fetch();

      } 

	}
	

}

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
	<title>Search By Keyword page</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body >
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
      <form class="d-flex" action="searchKeyword.php" method="POST">
        <input class="form-control input-lg me-2" type="search" placeholder="Search Movies" aria-label="Search" id="searchKeyword" name="searchKeyword" value="<?php echo $keywordName?>"> 
         <select name="genre" id="genre" class="form-select form-select-sm  me-2 " aria-label="Default select" >
            <option value = "">All Genres</option>
           <?php  foreach($genres as $genre):?>
            <option value="<?=$genre['ID']?>"><?=$genre['Name']?></option>
           <?php endforeach?>
        </select>  
        <button class="btn btn-danger mx-2" type="submit">Search</button>
        <button  onClick="resetForm()" class="btn btn-danger" type="submit" name="reset" id="reset">Reset</button>
      </form>
    </div>
  </div>
</nav>


	
	<div id="results" class="container mb-3 py-3">
    <?php if(empty($keywordName) && isset($genreName)): ?>
     <h2 class="text-danger fw-bold">Movies in <?= $genreName['Name']?></h2>
   <?php elseif(empty($keywordName) && empty($genreName)): ?>
   <h2 class="text-danger fw-bold">All Genres</h2>
    <?php elseif(isset($keywordName) && empty($genreName)): ?>
      <h2 class="text-danger fw-bold">'<?= $keywordName?>' in All Genres</h2>
    <?php elseif(isset($keywordName) && isset($genreName)): ?>
       <h2 class="text-danger fw-bold">'<?= $keywordName?>' in <?= $genreName['Name'] ?></h2>
    <?php endif?>

	<?php if(empty($movies)): ?>
		<h2 class="mt-3 text-danger fw-bold"> <?= $inputError ?></h2>
	<?php else:?>
	<div  class="row row-cols-1 row-cols-md-3 g-4 my-3 shadow-lg">
     <?php foreach($movies as $movie): ?>
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
      <div class="col pb-3">
        <div class="card h-100">
          <img src="Image_Uploads/<?=$Homeimages[0]['name']?>" class="card-img-top" alt="<?=$movie['Name']?>">
          <div class="card-body">
            <h3 class="card-title"><a  class="text-danger" href="movies.php?id=<?= $movie['Id']?>"><?= $movie['Name'] ?></a></h3>
            <p class="card-text"><?= truncate($movie['Description'])?>...</p>
             <a class= "btn btn-danger mt-3 mb-2" href="movies.php?id=<?= $movie['Id']?>">View Movie</a>
          </div>
        </div>
      </div>
        <?php else:?>
          <div class="col pb-3">
            <div class="card">
              <img src="" class="card-img-top" alt="">
              <div class="card-body">
                <h3 class="card-title"><a class="text-danger" href="movies.php?id=<?= $movie['Id']?>"><?= $movie['Name'] ?></a></h3>
                <p class="card-text"><?= truncate($movie['Description'])?>...</p>
                <a  class="btn btn-danger mt-3  mb-2" href="movies.php?id=<?= $movie['Id']?>">View Movie</a>
              </div>
            </div>
        </div>
        <?php endif ?>
            
        <?php endforeach ?>
         </div>
	    <?php endif ?>
     
	</div>

  <script>
    function resetForm(){
      document.getElementById('searchKeyword').value = "";
      document.getElementById('genre').selectedIndex = 0;
     }
  let select = document.getElementById('genre');
  let searchKeyword = document.getElementById('searchKeyword');
   console.log(document.getElementById("results"));
  select.addEventListener('change', function() {
    let genre = select.value; // get the selected value
    var query = searchKeyword.value; // get the search query
    let xhr = new XMLHttpRequest(); // create the AJAX object
    xhr.onreadystatechange = function() {
      if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
        document.getElementById('results').innerHTML = xhr.responseText; // update the search results
      }
    };
    xhr.open('POST', 'data.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send('genre=' + genre + '&searchKeyword=' + query); // send the selected category to the server
  });
</script>

</body>
</html>