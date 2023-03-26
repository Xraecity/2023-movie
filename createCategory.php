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


//variable to save used email address and username from users table
$genreArray = [];


//variables to store input fields values
$genre;


//select query to retrieve all database email addresses
$query = "SELECT * FROM genres ";


// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 


//fetch all blogs and store in array
$genres = $statement->fetchAll();

//add user emails to email array
foreach($genres as $genre){
    array_push($genreArray, $genre['Name']);
}


if($_POST){
    if(!empty($_POST['genre'])){
        if(in_array($_POST['genre'], $genreArray) === true){
            $genreError = "Genre exists already. Please enter another genre name";
            $genreValid = false;
        }
        else{
             $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
    }
    else{
        $genreError = "Genre name is required";
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
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Create New Genre</title>
</head>

<body>
    <div class="block">
    <?php include("header.php")?>
	<form method="post" action="createCategory.php">

        <label for="genre">Genre</label><br>
        <input id="genre" name="genre" type="text"><br><br>


        <!-- if useranme field has error,display error message--> 
        <?php if(isset($genreError)): ?>
            <span class="error"><?= $genreError ?></span><br>
        <?php endif ?>

		
      
		<button type="submit" id="addGenre">Create Genre</button>	
	</form>

    <?php if(isset($successMesage)): ?>
            <span class="error"><?= $successMesage ?></span><br>
    <?php endif ?>
	


</div>
</body>
</html>