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

<?php include 'nav.php'; ?>
    <div class="container">
     <div class="container border border-2 rounded-5 border-primary mt-5 shadow-lg px-3">
    <h2 class="text-center  text-primary fw-bold mt-4">Create New Category</h2>
   
	<form method="post" action="createCategory.php">

        <div class=" form-floating mb-3 mt-3">
          <input type="text" class="form-control" id="genre2" placeholder="Enter genre" name="genre">
          <label for="genre2">Genre</label>
        </div>

        <!-- if useranme field has error,display error message--> 
        <?php if(isset($genreError)): ?>
            <span class="error text-primary"><?= $genreError ?></span><br>
        <?php endif ?>

		
      
		<button type="submit" id="addGenre" class="btn btn-primary fs-5">Create Genre</button>	
	</form>

    <?php if(isset($successMesage)): ?>
            <span class="error"><?= $successMesage ?></span><br>
    <?php endif ?>
</div>
	


</div>
</body>
</html>