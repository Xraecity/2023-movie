<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 20, 2023
    Description: This page displays all the movies added to the website.

****************/

require('connect.php');
session_start();

//variable to store errors for comment form
$inputError;
$moviepage = [];
$movieListSort = "Title";
$genreError;


//display movies based on categories for non-registered  users
if($_POST && !empty($_POST['genre'])){
    if(filter_input(INPUT_POST,'genre',FILTER_VALIDATE_INT) !== false){
            $genre = filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_NUMBER_INT);

            $query = "SELECT * FROM movies WHERE Genre = :genre ORDER BY Name ASC";


            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query); 
            $statement->bindValue(':genre', $genre);   
             

            // Execution on the DB server is delayed until we execute().
            $statement->execute(); 


            //fetch all movies and store in array
            $movies = $statement->fetchAll();

            if(empty($movies)){
            $genreError = "No movie found";
            }
}
}
else{
    //select query
    $query = "SELECT * FROM movies ORDER BY Name ASC";

    // A PDO::Statement is prepared from the query.
    $statement = $db->prepare($query);

    // Execution on the DB server is delayed until we execute().
    $statement->execute(); 


    //fetch all movies and store in array
    $movies = $statement->fetchAll();

}




// get and display full blog post
if(isset($_GET['id'])){
    // Sanitize the id. Like above but this time from INPUT_GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT) !== false){
    // Build the parametrized SQL query using the filtered id.
    $movieQuery = "SELECT * FROM movies WHERE id = :id";
    $movieStatement = $db->prepare($movieQuery);
    $movieStatement->bindValue(':id', $id, PDO::PARAM_INT);
            
    // Execute the SELECT and fetch the single row returned.
    $movieStatement->execute();
    $moviepage = $movieStatement->fetch();

    //select genre query to get movie genre
    $genreQuery = "SELECT Name FROM genres WHERE ID = :id";
    $genreStatement = $db->prepare($genreQuery);
    $genreStatement->bindValue(':id', $moviepage['Genre'], PDO::PARAM_INT);
    $genreStatement->execute(); 
    //fetch all images  and store in array
    $genreName = $genreStatement->fetch();

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
}

//sorting movies functionality section
if($_POST){
    if(isset($_POST['movieListSort'])){
    $movieListSort = filter_input(INPUT_POST,'movieListSort',FILTER_SANITIZE_STRING);

    if($movieListSort == "Title"){
        //fetch data from movies table 
        //select query
        $titleQuery = "SELECT * FROM movies ORDER BY name ASC";

        // A PDO::Statement is prepared from the query.
        $titleStatement = $db->prepare($titleQuery);

        // Execution on the DB server is delayed until we execute().
        $titleStatement->execute(); 


        //fetch all movies and store in array
        $movies = $titleStatement->fetchAll();
    }

    elseif($movieListSort == "Date-Created"){
        //fetch data from movies table 
        //select query
        $dateCreatedQuery = "SELECT * FROM movies ORDER BY Date_Created DESC";

        // A PDO::Statement is prepared from the query.
        $dateCreatedStatement = $db->prepare($dateCreatedQuery);

        // Execution on the DB server is delayed until we execute().
        $dateCreatedStatement->execute(); 


        //fetch all movies and store in array
        $movies = $dateCreatedStatement->fetchAll();
    }
    elseif($movieListSort = "Release-Date"){
        //fetch data from movies table 
        //select query
        $releaseDateQuery = "SELECT * FROM movies ORDER BY Release_Date DESC";

        // A PDO::Statement is prepared from the query.
        $releaseDateStatement = $db->prepare($releaseDateQuery);

        // Execution on the DB server is delayed until we execute().
        $releaseDateStatement->execute(); 


        //fetch all movies and store in array
        $movies = $releaseDateStatement->fetchAll();
    
    }
    else{
        $selectError = "Please select a sort option";

    }

}
}


       //if a comment is added by a user that's not logged in 
 if( $_POST && !empty($_POST['submit_anonymous'])){
    if(!empty($_POST['comment'])){
            //  Sanitize user input to escape HTML entities and filter out dangerous characters.
            $comment1 = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $id1 = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

           if(filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT) !== false){

                if(!empty($_POST['name'])){
                     $name1 = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                     //  Build the parameterized SQL query and bind to the above sanitized values.
                    $anonymousQuery = "INSERT INTO comments (commenter_name, content,Movie_ID,is_anonymous_user) VALUES (:commenter_name,:content,:Movie_ID,:is_anonymous_user)";
                    $anonymousStatement = $db->prepare($anonymousQuery);

                    //  Bind values to the parameters
                    $anonymousStatement->bindValue(':commenter_name', $name1);
                    $anonymousStatement->bindValue(':content', $comment1);
                    $anonymousStatement->bindValue(':Movie_ID', $id1);
                    $anonymousStatement->bindValue(':is_anonymous_user', TRUE);

                    //  Execute the INSERT.
                    $anonymousStatement->execute();

                    //redirect to home page
                    header("Location: index.php?id=".$id1);
                }
                else{
                     //  Build the parameterized SQL query and bind to the above sanitized values.
                    $anonymousQuery = "INSERT INTO comments (commenter_name, content,Movie_ID,is_anonymous_user) VALUES (:commenter_name,:content,:Movie_ID,:is_anonymous_user)";
                    $anonymousStatement = $db->prepare($anonymousQuery);

                    //  Bind values to the parameters
                    $anonymousStatement->bindValue(':commenter_name', 'Anomymous');
                    $anonymousStatement->bindValue(':content', $comment1);
                    $anonymousStatement->bindValue(':Movie_ID', $id1);
                    $anonymousStatement->bindValue(':is_anonymous_user', TRUE);

                    //  Execute the INSERT.
                    $anonymousStatement->execute();

                    //redirect to home page
                    header("Location: index.php?id=".$id1);
                 
                }
                

           }
           else{
             header("Location: index.php?id=".$id1);

           }
        }
    //if any field is empty
    else{
        $inputError = "* Please enter a comment to submit" ;
    }
 }

    //if a comment is added by a logged-in or admin user 
 if($_POST && !empty($_POST['submit_logged-in'])){
     if(!empty($_POST['comment'])){
            //  Sanitize user input to escape HTML entities and filter out dangerous characters.
            $comment2 = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $name2 = $_SESSION['username'];
            $id2 = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            //  Build the parameterized SQL query and bind to the above sanitized values.
            $loggedQuery = "INSERT INTO comments (commenter_name, content,Movie_ID,is_anonymous_user) VALUES (:commenter_name,:content,:Movie_ID,:is_anonymous_user)";
            $loggedStatement = $db->prepare($loggedQuery);

            //  Bind values to the parameters
            $loggedStatement->bindValue(':commenter_name', $name2);
            $loggedStatement->bindValue(':content', $comment2);
            $loggedStatement->bindValue(':Movie_ID', $id2);
            $loggedStatement->bindValue(':is_anonymous_user', FALSE);

            //  Execute the INSERT.
            $loggedStatement->execute();

            //redirect to home page
            header("Location: index.php?id=".$id2);
        }
    else{
        $inputError = "* Please enter a comment to submit" ;
    }
 }

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Welcome to my Movies CMS</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
      <?php include("header.php")?>
    <div class="block">
      

        <?php if($_GET):?>
               <h3><?= $moviepage['Name'] ?></h3>
                <?php if(isset($_SESSION['username'])): ?>
                <button><a href="pageUpdate.php?id=<?= $moviepage['Id'] ?>">Edit Movie</a></button>  
                <?php else:?>
                <button><a href="login.php">Edit Movie</a></button>    
                <?php endif ?>
                

                <h4>Genre: <?=$genreName['Name']?></h4>
                <p>Release Date:<?=$moviepage['Release_Date']?></p>
                
                <p class="content"><?=$moviepage['Description']?></p>

                <!--image section-->
                <?php if(!empty($images)):?>
                   <h4> Images</h4>
                   <div>
                       <?php foreach($images as $image): ?>
                        <img src="Image_Uploads/<?= $image['name'] ?>" alt= "<?=$moviepage['Name']?>-image">
                       <?php endforeach ?>
                   </div>
                <?php endif ?> 
                  
                <!--comments section--> 
                <?php if(!empty($comments)):?>
                   <h4>Comments</h4>
                   <?php foreach($comments as $comment): ?>
                    <div>
                        <p><b>Posted By: <?= $comment['commenter_name']?></b></p>
                        <p><i><?= date("F d, Y, h:i a", strtotime($comment['date_created']))?> </i></p>
                        <p><?= $comment['content']?></p>
                    </div>
                    <?php endforeach ?>
                <?php endif ?>

                <!--comments form section-->
                <!--form for non-registered users-->
                <?php if(!isset($_SESSION['username'])): ?>
                <form method="post" action="index.php">
                   <!-- Hidden input for the quote primary key. -->
                   <input type="hidden" id="id" name="id" value="<?= $moviepage['Id'] ?>">

                   <label for="comment">Post a comment</label><br>
                   <textarea id="comment" name="comment" rows="10" cols="100"></textarea>
                   <br><br> 
                   <label for="name">Name</label>
                   <input type="text" name="name" id="name"><br>

                   <?php if(isset($inputError)):?>
                   <span class="error"><?= $inputError?></span><br>
                   <?php endif ?>

                   <button type="submit" value="Post" id="submit_anonymous" name ="submit_anonymous">Post Comment</button>
                </form>
                <?php else: ?>
                 <!--form for logged-in users-->
                 <form method="post" action="index.php">
                   <!-- Hidden input for the quote primary key. -->
                   <input type="hidden" id="id" name="id" value="<?= $moviepage['Id'] ?>">
                   <label for="comment">Post a comment</label><br>
                   <textarea id="comment" name="comment" rows="10" cols="100"></textarea>
                   <br><br> 

                   <?php if(isset($inputError)):?>
                   <span class="error"><?= $inputError?></span><br>
                   <?php endif ?>

                   <button type="submit" value="Post" id="submit_logged-in" name ="submit_logged-in">Post Comment</button>

                   <?php if($_SESSION['isAdmin'] == 1 && !empty($comments)): ?>
                    <button><a href="pageUpdate.php?id=<?= $moviepage['Id']?>">Moderate Comment</a></button>
                   <?php endif ?>
                </form>
                <?php endif ?>

        <?php elseif(isset($_SESSION['username'])): ?>
         <form id="home" method="post" action="index.php">
        <label for="movieListSort">Sort Movies List by:</label>
        <select name="movieListSort" id="moviesListSort">
            <option value="Title" selected >Title</option>
            <option value="Date-Created">Date Created</option>
            <option value="Release-Date" >Movie Release Date</option>
        </select>
        <button type="submit" name="submit" id="submit">Submit</button>

        <?php if(isset($selectError)):?>
            <span class="error"><?= $selectError?></span>
        <?php endif?>
      </form>
  
      <p id="homeTitle"><b>Movies sorted by <?= $movieListSort?></b></p>
      <?php foreach($movies as $movie): ?>
            <div class="movie">
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


        <?php else: ?>
            <form id="home" method="post" action="index.php">
                <label for="genre">Sort By Category:</label>
                <select name="genre" id="genre">
                    <option value = "">All Categories</option>
                    <option value="1">Adventure</option>
                    <option value="2">Action</option>
                    <option value="3">Sci-fi</option>
                    <option value="4">Horror</option>
                    <option value="5">Comedy</option>
                    <option value="6">Drama</option>
                    <option value="7">Fantasy</option>
                    <option value="8">Mystery</option>
                    <option value="9">Romance</option>
                </select>

                <button type="submit" name="submit" id="submit">Submit</button>
            </form>
            <!-- if title field is empty or has,display error message--> 
            <?php if(isset($genreError)): ?>
            <h3><?php echo $genreError ?></h3>
            <?php endif ?>

            <?php foreach($movies as $movie): ?>
                <div class="movie">
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
