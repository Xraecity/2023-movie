<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 23, 2023
    Description: This script allows an authenticated user to add a ne movie post by the use of a form. It also sanitizes and validates all input fields.

****************/

require('connect.php');
require '\xampp\htdocs\WD2\challenges\Challenge7_Maryam_Gambo\php-image-resize-master\lib\ImageResize.php';
require '\xampp\htdocs\WD2\challenges\Challenge7_Maryam_Gambo\php-image-resize-master\lib\ImageResizeException.php';

use \Gumlet\ImageResize;
session_start();

//variables
$title = "";
$genre = "";
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


//if Get is set
// Retrieve quote to be edited, if id GET parameter is in URL.
if(isset($_GET['id'])){
    // Sanitize the id. Like above but this time from INPUT_GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    //validate int
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) !== false){
        // Build the parametrized SQL query using the filtered id.
        $query = "SELECT * FROM movies WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
                
        // Execute the SELECT and fetch the single row returned.
        $statement->execute();
        $movie = $statement->fetch();
    }
    else{
        header("Location:index.php");
    }

     //select image query to get all page images
    $imageQuery = "SELECT * FROM images WHERE Movie_ID = :id";
    $imageStatement = $db->prepare($imageQuery);
    $imageStatement->bindValue(':id', $id, PDO::PARAM_INT);
    $imageStatement->execute(); 
    //fetch all images  and store in array
    $images = $imageStatement->fetchAll();

    //select comment query to get all page comments
    $commentQuery = "SELECT * FROM comments WHERE Movie_ID = :id ORDER BY date_created DESC";
    $commentStatement = $db->prepare($commentQuery);
    $commentStatement->bindValue(':id', $id, PDO::PARAM_INT);
    $commentStatement->execute(); 
    //fetch all images  and store in array
    $comments = $commentStatement->fetchAll();
}

//if post
if($_POST && !empty($_POST['update'])){
       if(!empty($_POST['id'])){
         $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
       }

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
            $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
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
            echo($releaseDate);
            
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
                        $image1->resizeToWidth(400);
                        $image1->save($image_storage_folder);
                        echo("file uploaded successfully");
                    }
                    else{
                    $imageError = "Wrong file type.Please enter a image file.";
                    }     
                }
                   //catch exception
                catch(Exception $e) {
                      echo'Message: ' .$e->getMessage();
                      echo("file not uploaded successfully");
                    }   
        }
        if($titleValid && $genreValid && $descriptionValid && $releaseDateValid && filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) !== false){
             //  Build the parameterized SQL query to update into movies table and bind to the above sanitized values.
            $movieQuery = "UPDATE movies SET Name = :name, Genre = :genre, Description = :description, Release_Date = :releaseDate WHERE id = :id";
            $movieStatement = $db->prepare($movieQuery);


            //  Bind values to the parameters
            $movieStatement->bindValue(':name', $title);
            $movieStatement->bindValue(':genre', $genre);
            $movieStatement->bindValue(':description', $description);
            $movieStatement->bindValue(':releaseDate', $releaseDate);
            $movieStatement->bindValue(':id', $id, PDO::PARAM_INT);

            //  Execute the UPDATE.
            $movieStatement->execute();


            if($imageInvalid == false){
                $imageQuery = "INSERT INTO images (name,Movie_ID) VALUES (:name,:movieID)";
                $imageStatement = $db->prepare($imageQuery);


                //  Bind values to the parameters
                $imageStatement->bindValue(':name', $filename);
                $imageStatement->bindValue(':movieID', $id);
              

                //  Execute the INSERT.
                $imageStatement->execute();
                echo("image successfully loaded");
            }

            //redirect to movie page
            header("Location: index.php?id=".$id);
               
       }
   }



   
   
        
    

    


           


        
       
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies CMS</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
     <div class="block">
           <h1><a href = "index.php">Movies CMS</a></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    
                    <?php if(isset($_SESSION['username'])): ?>
                    <li><a href="pageAdministration.php"><?= $_SESSION['username']?></a></li>
                    <button><a href="logout.php">Log out</a></button>

                    <?php else:?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="registration.php">Register</a></li>
                    <?php endif?>

                </ul>
            </nav>

            <h2>Update Movie</h2>


            <form method="post" enctype='multipart/form-data'>
                <!-- Hidden input for the quote primary key. -->
                <input type="hidden" id="id" name="id" value="<?= $movie['Id'] ?>">
                 <!-- Quote title and content are echoed into the input value attributes. -->
                <label for="title">Title</label><br>
                <input id="title" name="title" type="text" value="<?= $movie['Name'] ?>"><br><br>

                <!-- if title field is empty or has,display error message--> 
                <?php if(isset($titleError)): ?>
                <span class="error"><?php echo $titleError ?></span><br>
                <?php endif ?>

                <label for="genre">Genre</label><br>
                <input id="genre" name="genre" type="text" value="<?= $movie['Genre'] ?>"><br><br>

                <!--if genre field is empty or has error,display error message--> 
                <?php if(isset($genreError)): ?>
                <span class="error"><?php echo $genreError ?></span><br>
                <?php endif ?>

                <label for="description">Description</label><br>
                <textarea id="description" name="description" rows="10" cols="100" ><?= $movie['Description'] ?></textarea>
                <br><br> 

                <!-- if description field is empty or has error,display error message--> 
                <?php if(isset($descriptionError)): ?>
                <span class="error"><?php echo $descriptionError ?></span><br>
                <?php endif ?>

                <label for="releaseDate">Release Date</label>
                <input type="date" id="releaseDate" name="releaseDate" value="<?= $movie['Release_Date'] ?>"><br><br>

                <!-- if Release date field is empty or has,display error message--> 
                <?php if(isset($releaseDateError)): ?>
                <span class="error"><?php echo $releaseDateError ?></span><br>
                <?php endif ?>

                <?php if(!empty($images)):?>
                    <h3>Images</h3>
                    <ul>
                    <?php foreach($images as $image):?>
                        <li>
                        <img src="Image_Uploads/<?= $image['name'] ?>" alt= "<?=$movie['Name']?>-image"><br>
                        <button type="submit" id="deleteImage" name="deleteImage"  onclick="return confirm('Are you sure you want to delete?')"><a href="imageDelete.php?id=<?= $image['id']?>&movieID=<?= $movie['Id']?>&filePath=<?= file_Upload_Path($image['name'])?>">Delete Image</a></button>
                   </li>

                    <?php endforeach ?>
                   </ul>

                <?php endif ?> 

                 <label for='file'>Add Image:</label>
                 <input type='file' name='file' id='file'><br><br>

                 <!-- if filetype has error,display error message--> 
                <?php if(isset($imageError)): ?>
                <span class="error"><?php echo $imageError ?></span><br>
                <?php endif ?>


                
                <?php if($_SESSION['isAdmin'] == 1):?>
                    <?php if(!empty($comments)):?>
                        <h3>Comments</h3>
                        <ul>
                        <?php foreach($comments as $comment):?>
                        <li>
                            <p><?= $comment['content']?></p>
                            <button type="submit" id="deleteComment" name="deleteComment"  onclick="return confirm('Are you sure you want to delete?')"><a href="commentDelete.php?id=<?= $comment['ID']?>&movieID=<?= $movie['Id']?>">Delete Comment</a></button>
                       </li>

                        <?php endforeach ?>
                       </ul>

                    <?php endif ?> 

                <?php endif ?>
                <button type="submit" value="update" id="submit" name="update">Update Movie</button>

                
                
            </form>

            <a href="pageAdministration.php">Go back to Admin page</a>


        
    </div>
    
</body>
</html>