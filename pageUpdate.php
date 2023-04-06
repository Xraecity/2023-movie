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

//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();

//variables
$images = [];
$title = "";
$genre;
$genreName = "";
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
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();



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
   // print_r($_POST);
       if(!empty($_POST['id'])){
         $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
       }

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
                        $image1->save($image_storage_folder);
                        echo("file uploaded successfully");
                    }
                    else{
                    $imageError = "* Wrong file type.Please enter a image file of type (PNG, JPG, GIF).";
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
             header("Location: movies.php?id=".$id);
               
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
     <nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-gradient py-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Movies CMS</a>
    <?php if(isset($_SESSION['username'])): ?>
    <a class="navbar-brand fw-bold" href="pageAdministration.php"> <?= $_SESSION['username']?></a>
<?php endif ?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="movies.php">Movies</a>
        </li>
          <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="contact.php">Contact us</a>
        </li>

        <?php if(isset($_SESSION['username'])): ?>
         <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="logout.php">Log Out</a>
        </li>
        <?php else:  ?>
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="login.php">Login</a>
        </li>
         <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="registration.php">Sign Up</a>
        </li>
         <?php endif ?>

      
      </ul>
        <form class="d-flex" id="searchForm" action="searchKeyword.php" method="POST">
        <input class="form-control input-lg me-2" type="search" placeholder="Search Movies" aria-label="Search" id="searchKeyword" name="searchKeyword"> 
         <select name="genre" id="genre" class="form-select form-select-sm  me-2 " aria-label="Default select">
            <option value = "">All Genres</option>
           <?php  foreach($genres as $genre):?>
            <option value="<?=$genre['ID']?>"><?=$genre['Name']?></option>
           <?php endforeach?>
        </select>  
        <button class="btn btn-danger" type="submit">Search</button>
      </form>

    </div>
  </div>
</nav>
      
     <div class="container">
        <div class="container border border-2 rounded-5 border-danger mt-5 shadow-lg px-3">
        

            <h2 class="text-center  text-danger fw-bold mt-4">Update Movie</h2>
  
            <form method="post" enctype='multipart/form-data'>
                <!-- Hidden input for the quote primary key. -->
                <input type="hidden" id="id" name="id" value="<?= $movie['Id'] ?>">
                 <!-- Quote title and content are echoed into the input value attributes. -->
                <label for="title" class="form-label fs-5 fw-bold">Title</label><br>
                <input id="title" name="title" type="text" class="form-control" value="<?= $movie['Name'] ?>"><br><br>

                <!-- if title field is empty or has,display error message--> 
                <?php if(isset($titleError)): ?>
                <span class="error text-danger"><?php echo $titleError ?></span><br>
                <?php endif ?>

                <label for="genre2" class="form-label fs-5 fw-bold">Genre</label><br>
                <select name="genre" id="genre2" class="form-select form-select-lg">
                    <?php  foreach($genres as $genre):?>
                        <?php if($genre['ID'] == $movie['Genre']): ?>
                             <option value="<?=$genre['ID']?>" selected><?=$genre['Name']?></option>
                        <?php else: ?>
                            <option value="<?=$genre['ID']?>"><?=$genre['Name']?></option>
                        <?php endif ?>
                   
                <?php endforeach?>
                </select><br><br>

                <!--if genre field is empty or has error,display error message--> 
                <?php if(isset($genreError)): ?>
                <span class="error text-danger"><?php echo $genreError ?></span><br>
                <?php endif ?>

                <label for="description" class="form-label fs-5 fw-bold">Description</label><br>
                <textarea id="description" name="description" class= "form-control" rows="10" cols="100" ><?= $movie['Description'] ?></textarea>
                <br><br> 

                <!-- if description field is empty or has error,display error message--> 
                <?php if(isset($descriptionError)): ?>
                <span class="error text-danger"><?php echo $descriptionError ?></span><br>
                <?php endif ?>

                <label for="releaseDate" class="form-label fs-5 fw-bold">Release Date</label>
                <input type="date" id="releaseDate" class="form-control" name="releaseDate" value="<?= $movie['Release_Date'] ?>"><br><br>

                <!-- if Release date field is empty or has,display error message--> 
                <?php if(isset($releaseDateError)): ?>
                <span class="error text-danger"><?php echo $releaseDateError ?></span><br>
                <?php endif ?>

                <?php if(!empty($images)):?>
                    <h3 class="text-danger">Images</h3>
                    <ul class="list-group">
                    <?php foreach($images as $image):?>
                        <li class="list-group-item">
                       <img src="Image_Uploads/<?= $image['name'] ?>" alt= "<?=$movie['Name']?>-image"><br><br>
                        <a onclick="return confirm('Are you sure you want to delete?')" class="btn btn-secondary text-white" role= "button" href="imageDelete.php?id=<?= $image['id']?>&movieID=<?= $movie['Id']?>&fileName=<?=$image['name']?>">Delete Image</a>
                   </li>

                    <?php endforeach ?>
                   </ul>

                <?php endif ?> 

                 <div class="mb-3">
                  <label for="file" class="form-label fs-6 fw-bold">Add Image(PNG, JPG, GIF):</label>
                  <input class="form-control" type="file" id="file" name="file">
                </div>

                 <!-- if filetype has error,display error message--> 
                <?php if(isset($imageError)): ?>
                <span class="error text-danger"><?php echo $imageError ?></span><br>
                <?php endif ?>


                
                <?php if($_SESSION['isAdmin'] == 1):?>
                    <?php if(!empty($comments)):?>
                        <h3 class="text-danger">Comments</h3>
                        <ul class="list-group">
                        <?php foreach($comments as $comment):?>
                        <li class="list-group-item">
                            <p><?= $comment['content']?></p>
                          <a onclick="return confirm('Are you sure you want to delete?')" class ="btn btn-secondary text-white" role= "button" href="commentDelete.php?id=<?= $comment['ID']?>&movieID=<?= $movie['Id']?>">Delete Comment</a>
                       </li>

                        <?php endforeach ?>
                       </ul>

                    <?php endif ?> 

                <?php endif ?>
                <button type="submit" value="update" id="submit"  class="btn btn-danger fs-5 my-3" name="update">Update Movie</button>

                
                
            </form>

            <a href="pageAdministration.php" class=" btn btn-danger mb-3">Go back to Admin page</a>
 
        </div>
        
    </div>
    
</body>
</html>