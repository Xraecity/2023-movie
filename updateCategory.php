<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 21, 2023
    Description: this page allows admin users to edit a genres information(validates and sanitizes input fields) and updates it in the genre table in database

****************/
require('connect.php');
session_start();

$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();

//variable for field error message
$genre_error;


//boolean variables to check if field input are valid
$genreValid = true;


// Retrieve category to be edited, if id GET parameter is in URL.
if(isset($_GET['id'])){
    // Sanitize the id. Like above but this time from INPUT_GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    //validate int
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) !== false){
        // Build the parametrized SQL query using the filtered id.
        $query = "SELECT * FROM genres WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
                
        // Execute the SELECT and fetch the single row returned.
        $statement->execute();
        $genre = $statement->fetch();
    }
    else{
        header("Location:pageAdministration.php");
    }


}

// UPDATE if all user form fields are not empty.
if($_POST && !empty($_POST['update'])){
   if(!empty($_POST['genre'])) {    
        $newGenre =filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
   } 
   else {
        $genre_error = "Genre name is required";
        $genreValid = false;
   }


    if($genreValid){
        // if user inputted id is an integer
        if(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) !== false){

            // Build the parameterized SQL query and bind to the above sanitized values.
            $genrequery = "UPDATE genres SET Name = :name WHERE ID = :id";
            $genrestatement = $db->prepare($genrequery);

            $genrestatement->bindValue(':name', $newGenre);    
            $genrestatement->bindValue(':id', $id);     
                
            // Execute the UPDATE
            $genrestatement->execute();

            header("Location:pageAdministration.php");
            exit;  
        }
        // if user inputted value is not an integer
        else{
            header("Location: pageAdministration.php"); 
        }  
    }
   
}



    



?>

 <!-- menu bar  -->
 <?php include 'nav.php'; ?>

    <div class="container">
    <div class="container border border-2 rounded-5 border-primary mt-5 shadow-lg px-3">
        <h2   class="text-center  text-primary fw-bold mt-4">Update Category</h2>
  
    <!-- Remember that alternative syntax is good and html inside php is bad -->
     
            <form method="post">
                <!-- Hidden input for the quote primary key. -->
                <input type="hidden" id="id" name="id" value="<?= $genre['ID'] ?>">
                 <div class="mb-3">
                  <label for="genre1" class="form-label fs-5 fw-bold">Genre</label>
                  <input type="text" class="form-control" id="genre1" placeholder="Enter genre" name="genre" value="<?= $genre['Name']?>">
              </div>
                 
               


                <?php if(isset($genre_error)): ?>
                <span class="error"><?= $genre_error ?></span><br>
                <?php endif ?>

                <button type="submit" value="update" class="btn btn-primary fs-5" id="update" name="update">Update</button><br>

            </form><br>      
            </div>  
        </div>
</body>
</html>