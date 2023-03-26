<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 21, 2023
    Description: this page allows admin users to edit a genres information(validates and sanitizes input fields) and updates it in the genre table in database

****************/
require('connect.php');
session_start();


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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Edit this Genre!</title>
</head>
<body>
    <div class="block">
    <?php include("header.php")?>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
            <form method="post">
                <!-- Hidden input for the quote primary key. -->
                <input type="hidden" id="id" name="id" value="<?= $genre['ID'] ?>">

                 <!-- Quote title and content are echoed into the input value attributes. -->
                <label for="genre">Genre</label><br>
                <input type= "genre" id="genre" name="genre" value="<?= $genre['Name']?>"><br><br>

                <?php if(isset($genre_error)): ?>
                <span class="error"><?= $genre_error ?></span><br>
                <?php endif ?>

                <button type="submit" value="update " id="update" name="update">Update</button>

            </form>      
            </div>  
</body>
</html>