<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 23, 2023
    Description: This script allows an authenticated user to add a ne movie post by the use of a form. It also sanitizes and validates all input fields.

****************/

require('connect.php');
session_start();
require 'php-image-resize-master\lib\ImageResize.php';
require 'php-image-resize-master\lib\ImageResizeException.php';
use \Gumlet\ImageResize;


//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();

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

//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the SELECT
$genreStatement->execute();
$genres = $genreStatement->fetchAll();



//if post
if($_POST ){
    //validate and sanitize title
    if(!empty($_POST['title'])){ 
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
    }
    else{
        $titleError = "* Title is required";
        $titleValid = false;
    }

    //validate and sanitize genre
    if(!empty($_POST['genre'])){ 
        if(filter_input(INPUT_POST,'genre',FILTER_VALIDATE_INT) !== false){
             $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_NUMBER_INT);
        }
        else{
            $genreError = "* Genre is invalid";
            $genreValid = false;
        }
       
        
    }
    else{
        $genreError = "* Genre is required";
        $genreValid = false;
    }


    //validate and sanitize description
    if(!empty($_POST['description'])){ 
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
    }
    else{
        $descriptionError = "* Description is required";
        $descriptionValid = false;
    }

    //validate and sanitize release date
    if(!empty($_POST['releaseDate'])){ 
        $releaseDate = filter_input(INPUT_POST, 'releaseDate', FILTER_SANITIZE_STRING);
        
    }
    else{
        $releaseDateError = "* Release Date is required";
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
                    $image1->resizeToHeight(600);
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
        $movieQuery = "INSERT INTO movies (Name, Genre, Description, Release_Date, User_ID) VALUES (:name,:genre,:description, :releaseDate, :userID)";
        $movieStatement = $db->prepare($movieQuery);


        //  Bind values to the parameters
        $movieStatement->bindValue(':name', $title);
        $movieStatement->bindValue(':genre', $genre);
        $movieStatement->bindValue(':description', $description);
        $movieStatement->bindValue(':releaseDate', $releaseDate);
        $movieStatement->bindValue(':userID', $_SESSION['id']);

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
             header("Location:movies.php");
        }
        else{
             header("Location:movies.php");
        }

    }

               
}
        
    





           


        
       
    

?>
<?php include 'nav.php'; ?>

     <div class="container">
          <div class="container border border-2 rounded-5 border-primary mt-5 shadow-lg px-3">

            <h2 class="text-center  text-primary fw-bold mt-4">Add a New Movie</h2>
            
           
            <form method="post" action="pageCreate.php" enctype='multipart/form-data'>
                 <!-- Quote title and content are echoed into the input value attributes. -->
                <label for="title" class="form-label fs-5 fw-bold">Title</label><br>
                <input id="title" name="title" type="text" class="form-control"><br><br>

                <!-- if title field is empty or has,display error message--> 
                <?php if(isset($titleError)): ?>
                <span class="error text-primary"><?php echo $titleError ?></span><br>
                <?php endif ?>

                <label for="genre2" class="form-label fs-5 fw-bold">Genre</label><br>
                <select name="genre" id="genre2" class="form-select form-select-lg">
                <option value="">Select a genre</option>
                <?php  foreach($genres as $genre):?>
                    <option value="<?=$genre['ID']?>"><?=$genre['Name']?></option>
                <?php endforeach?>
            
                </select><br><br>

                <!--if genre field is empty or has error,display error message--> 
                <?php if(isset($genreError)): ?>
                <span class="error text-primary"><?php echo $genreError ?></span><br>
                <?php endif ?>

                <label for="description" class="form-label fs-5 fw-bold">Description</label><br>
                <textarea id="description" class="form-control" name="description" rows="10" cols="100"></textarea>
                <br><br> 

                <!-- if description field is empty or has error,display error message--> 
                <?php if(isset($descriptionError)): ?>
                <span class="error text-primary"><?php echo $descriptionError ?></span><br>
                <?php endif ?>

                <label for="releaseDate" class="form-label fs-5 fw-bold">Release Date</label>
                <input type="date" id="releaseDate" class="form-control" name="releaseDate"><br><br>

                <!-- if Releasedate field is empty or has,display error message--> 
                <?php if(isset($releaseDateError)): ?>
                <span class="error text-primary"><?php echo $releaseDateError ?></span><br>
                <?php endif ?>

                <div class="mb-3">
                  <label for="file" class="form-label fs-5 fw-bold">Add Image(PNG, JPG, GIF):</label>
                  <input class="form-control" type="file" id="file" name="file">
                </div>


                 <!-- if filetype has error,display error message--> 
                <?php if(isset($imageError)): ?>
                <span class="error text-primary"><?php echo $imageError ?></span><br>
                <?php endif ?>

                <button type="submit" class= "btn btn-primary fs-5 mb-2" id="submit">Post Movie</button>
                
            </form><br>
          

            <a href="pageAdministration.php" class=" btn btn-primary mb-2">Go back to Admin page</a>
        </div>
        
    </div>
    
</body>
</html>