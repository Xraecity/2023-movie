<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 20, 2023
    Description: This page searches movies using genre and serachInput.

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

 <!-- menu bar  -->
 <?php include 'nav.php'; ?>

	
	<div id="results" class="container mb-3 py-3">
    <?php if(empty($keywordName) && isset($genreName)): ?>
     <h2 class="text-primary fw-bold">Movies in <?= $genreName['Name']?></h2>
   <?php elseif(empty($keywordName) && empty($genreName)): ?>
   <h2 class="text-primary fw-bold">All Genres</h2>
    <?php elseif(isset($keywordName) && empty($genreName)): ?>
      <h2 class="text-primary fw-bold">'<?= $keywordName?>' in All Genres</h2>
    <?php elseif(isset($keywordName) && isset($genreName)): ?>
       <h2 class="text-primary fw-bold">'<?= $keywordName?>' in <?= $genreName['Name'] ?></h2>
    <?php endif?>

	<?php if(empty($movies)): ?>
		<h2 class="mt-3 text-primary fw-bold"> <?= $inputError ?></h2>
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
            <h3 class="card-title"><a  class="text-primary" href="movies.php?id=<?= $movie['Id']?>"><?= $movie['Name'] ?></a></h3>
            <p class="card-text"><?= truncate($movie['Description'])?>...</p>
             <a class= "btn btn-primary mt-3 mb-2" href="movies.php?id=<?= $movie['Id']?>">View Movie</a>
          </div>
        </div>
      </div>
        <?php else:?>
          <div class="col pb-3">
            <div class="card">
              <img src="" class="card-img-top" alt="">
              <div class="card-body">
                <h3 class="card-title"><a class="text-primary" href="movies.php?id=<?= $movie['Id']?>"><?= $movie['Name'] ?></a></h3>
                <p class="card-text"><?= truncate($movie['Description'])?>...</p>
                <a  class="btn btn-primary mt-3  mb-2" href="movies.php?id=<?= $movie['Id']?>">View Movie</a>
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
  let selectGenre = document.getElementById('genre');
  let searchKeyword = document.getElementById('searchKeyword');
   
  selectGenre.addEventListener('change', function() {
    let genre = selectGenre.value; // get the selected value
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
  
  searchKeyword.addEventListener('input', function(){
    if(searchKeyword.value === ""){
       let genre = selectGenre.value; // get the selected value
       let htr = new XMLHttpRequest(); // create the AJAX object
       htr.onreadystatechange = function() {
      if(htr.readyState === XMLHttpRequest.DONE && htr.status === 200){
        document.getElementById('results').innerHTML = htr.responseText; // update the search results
      }
    };
    htr.open('POST', 'data.php', true);
    htr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    htr.send('genre=' + genre); // send the selected category to the server

    }

  });


</script>

</body>
</html>