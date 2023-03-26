<?php

require('connect.php');
session_start();

//variables 
$movies = [];
$inputError = "";
$keywordName = "";
$genre;

// $results_per_page;
// $current_page = 1;
// $start_from = 0;
// echo(is_int($current_page -1));

// //check if number of results per page is set and set it to a variable
// if(isset($_GET['resultPerPage'])){
// 	$results_per_page = filter_input(INPUT_GET,'resultPerPage', FILTER_SANITIZE_NUMBER_INT);
// }
// else{
// 	$results_per_page = 2;
// }

// //check the current page and set it and the total number of results
// if(isset($_GET['page'])){
// 	$current_page = $_GET['page'];
// }
// $subtract = $current_page - 1;

// $start_from = $subtract * (int)$results_per_page;

if(empty($_GET['searchKeyword'])){
	$inputError = "Please enter a movie name";
}
else{

	if(empty($_GET['genre'])){
		$keywordName  =  filter_input(INPUT_GET, 'searchKeyword',FILTER_SANITIZE_FULL_SPECIAL_CHARS );
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
		if(filter_input(INPUT_GET,'genre',FILTER_VALIDATE_INT) !== false){
            $genre = filter_input(INPUT_GET, 'genre', FILTER_SANITIZE_NUMBER_INT);

            $keywordName  =  filter_input(INPUT_GET, 'searchKeyword',FILTER_SANITIZE_FULL_SPECIAL_CHARS );
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

            } 

	}
	
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Search By Keyword page</title>
</head>
<body>
	<div class="block">
		<?php include("header.php")?>

		<?php if(empty($movies)): ?>
			<h2> <?= $inputError ?></h2>
		<?php else: ?>
			<?php foreach($movies as $movie): ?>
                <div>
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
                        <img src="Image_Uploads/<?=$Homeimages[0]['name']?>" alt="<?=$movie['Name']?>">
                        <h3><a href="index.php?id=<?= $movie['Id']?>"><?= $movie['Name'] ?></a></h3>
                        <p class="content"><?=$movie['Description']?></p>
                        <button ><a  href="index.php?id=<?= $movie['Id']?>">View Movie</a></button>
                    <?php else:?>
                        <h3><a href="index.php?id=<?= $movie['Id']?>"><?= $movie['Name'] ?></a></h3>
                        <p class="content"><?=$movie['Description']?></p>
                        <button ><a  href="index.php?id=<?= $movie['Id']?>">View Movie</a></button>
                    <?php endif ?>
                </div>    
            <?php endforeach ?>
	    <?php endif ?>
     
	</div>

</body>
</html>