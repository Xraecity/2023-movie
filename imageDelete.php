<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 21, 2023
    Description: this page allows admin users to delete a image on a movie page from the image table in database.

****************/
require('connect.php');


// Retrieve comment post to be deleted, if id GET parameter is in URL.
if(isset($_GET['id'])){
    // Sanitize the id. Like above but this time from INPUT_GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    //sanitize and validate movie id
    if(filter_input(INPUT_GET, 'movieID', FILTER_VALIDATE_INT) !== false ){
        $moviepageId = filter_input(INPUT_GET, 'movieID', FILTER_SANITIZE_NUMBER_INT);
    }
    //sanitize image path
    $file =  filter_input(INPUT_GET, 'filePath', FILTER_SANITIZE_STRING);
    echo($file);

    //delete image 
    if(file_exists($file)){
        unlink($file);
    }

   

    //validate image int
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) !== false){
        // Build the parametrized delete SQL query using the filtered id.
        $query = "DELETE FROM images WHERE id = :id LIMIT 1";
        $statement = $db->prepare($query);


        //  Bind values to the parameters
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
                
        // Execute the DELETE
        $statement->execute();
        header("Location:pageUpdate.php?id=".$moviepageId);
        
    }
    else{
        header("Location:pageUpdate.php?id=".$moviepageId);
    }
}

?>