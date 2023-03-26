<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 23, 2023
    Description: This script allows an authenticated user to add a ne movie post by the use of a form. It also sanitizes and validates all input fields.

****************/

require('connect.php');
session_start();
require '\xampp\htdocs\WD2\challenges\Challenge7_Maryam_Gambo\php-image-resize-master\lib\ImageResize.php';
require '\xampp\htdocs\WD2\challenges\Challenge7_Maryam_Gambo\php-image-resize-master\lib\ImageResizeException.php';

use \Gumlet\ImageResize;

//variables
$title = "";
$genre;
$description = "";
$releaseDate = "";

// error variable
$titleError;
$genreError;
$descriptionError;
$releaseDateError;
$imageError;

//is valid variables for field
$titleValid = true;
$genreValid= true;
$descriptionValid = true;
$releaseDateValid = true;
$imageInvalid = true;




// file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
 // Default upload path is an 'uploads' sub-folder in the current folder.
function file_upload_path($original_filename, $upload_subfolder_name = 'Image_Uploads') {
   $current_folder = dirname(__FILE__);
   
   // Build an array of paths segment names to be joins using OS specific slashes.
   $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
   
   // The DIRECTORY_SEPARATOR constant is OS specific.
   return join(DIRECTORY_SEPARATOR, $path_segments);
}


// file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
    
    $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type        = mime_content_type($temporary_path);
    
    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
    
    return $file_extension_is_valid && $mime_type_is_valid;
}


//if post
if($_POST ){
    //validate and sanitize title
    if(!empty($_POST['title'])){ 
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
    }
    else{
        $titleError = "Title is required";
        $titleValid = false;
    }

    //validate and sanitize genre
    if(!empty($_POST['genre'])){ 
        if(filter_input(INPUT_POST,'genre',FILTER_VALIDATE_INT) !== false){
             $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_NUMBER_INT);
        }
        else{
            $genreError = "Genre is invalid";
            $genreValid = false;
        }
       
        
    }
    else{
        $genreError = "Genre is required";
        $genreValid = false;
    }


    //validate and sanitize description
    if(!empty($_POST['description'])){ 
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
    }
    else{
        $descriptionError = "Description is required";
        $descriptionValid = false;
    }

    //validate and sanitize release date
    if(!empty($_POST['releaseDate'])){ 
        $releaseDate = filter_input(INPUT_POST, 'releaseDate', FILTER_SANITIZE_STRING);
        
    }
    else{
        $releaseDateError = "Release Date is required";
        $releaseDateValid = false;
    }
   

    //validate image
    if(isset($_FILES['file']) && ($_FILES['file']['error'] === 0)){  
        print_r($_FILES['file']);
            try {
                $filename = $_FILES['file']['name'];
                $temporary_path  = $_FILES['file']['tmp_name'];
                $image_storage_folder  = file_upload_path($filename);
                if (file_is_an_image($temporary_path, $image_storage_folder)) {
                    $imageInvalid = false;
                    $image1 = new ImageResize($temporary_path);
                    $image1->resizeToWidth(200);
                    $image1->save($image_storage_folder);
                    echo("file uploaded successfully");
                }
                else{
                $imageError = "Wrong file type.Please enter a image file of type(PNG, JPG, GIF).";
                }     
            }
               //catch exception
            catch(Exception $e) {
                  echo'Message: ' .$e->getMessage();
                  echo("file not uploaded successfully");
                }   
        }

    if($titleValid && $genreValid && $descriptionValid && $releaseDateValid){
         //  Build the parameterized SQL query to insert into movies table and bind to the above sanitized values.
        $movieQuery = "INSERT INTO movies (Name, Genre, Description, Release_Date) VALUES (:name,:genre,:description, :releaseDate)";
        $movieStatement = $db->prepare($movieQuery);


        //  Bind values to the parameters
        $movieStatement->bindValue(':name', $title);
        $movieStatement->bindValue(':genre', $genre);
        $movieStatement->bindValue(':description', $description);
        $movieStatement->bindValue(':releaseDate', $releaseDate);

        //  Execute the INSERT.
        $movieStatement->execute();
        $lastInsertedId = $db->lastInsertId();

        if($imageInvalid == false){
            $imageQuery = "INSERT INTO images (name,Movie_ID) VALUES (:name,:movieID)";
            $imageStatement = $db->prepare($imageQuery);


            //  Bind values to the parameters
            $imageStatement->bindValue(':name', $filename);
            $imageStatement->bindValue(':movieID', $lastInsertedId);
          

            //  Execute the INSERT.
            $imageStatement->execute();
            echo("image successfully loaded");
             header("Location:index.php");
        }
        else{
             header("Location:index.php");
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
    <title>Movies CMS</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
     <div class="block">
           <?php include("header.php")?>

            <h2>Add a New Movie</h2>


            <form method="post" action="pageCreate.php" enctype='multipart/form-data'>
                 <!-- Quote title and content are echoed into the input value attributes. -->
                <label for="title">Title</label><br>
                <input id="title" name="title" type="text"><br><br>

                <!-- if title field is empty or has,display error message--> 
                <?php if(isset($titleError)): ?>
                <span class="error"><?php echo $titleError ?></span><br>
                <?php endif ?>

                <label for="genre">Genre</label><br>
                <select name="genre" id="genre">
                <option value="">Select a genre</option>
                <option value="1">Adventure</option>
                <option value="2">Action</option>
                <option value="3">Sci-fi</option>
                <option value="4">Horror</option>
                <option value="5">Comedy</option>
                <option value="6">Drama</option>
                <option value="7">Fantasy</option>
                <option value="8">Mystery</option>
                <option value="9">Romance</option>
                </select><br><br>

                <!--if genre field is empty or has error,display error message--> 
                <?php if(isset($genreError)): ?>
                <span class="error"><?php echo $genreError ?></span><br>
                <?php endif ?>

                <label for="description">Description</label><br>
                <textarea id="description" name="description" rows="10" cols="100"></textarea>
                <br><br> 

                <!-- if description field is empty or has error,display error message--> 
                <?php if(isset($descriptionError)): ?>
                <span class="error"><?php echo $descriptionError ?></span><br>
                <?php endif ?>

                <label for="releaseDate">Release Date</label>
                <input type="date" id="releaseDate" name="releaseDate"><br><br>

                <!-- if Releasedate field is empty or has,display error message--> 
                <?php if(isset($releaseDateError)): ?>
                <span class="error"><?php echo $releaseDateError ?></span><br>
                <?php endif ?>

                 <label for='file'>Add Image(PNG, JPG, GIF):</label>
                 <input type='file' name='file' id='file'><br><br>

                 <!-- if filetype has error,display error message--> 
                <?php if(isset($imageError)): ?>
                <span class="error"><?php echo $imageError ?></span><br>
                <?php endif ?>

                <button type="submit" id="submit">Post Movie</button>
                
            </form>
          

            <a href="pageAdministration.php">Go back to Admin page</a>
        
    </div>
    
</body>
</html>