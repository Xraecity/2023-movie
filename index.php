<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 20, 2023
    Description: This page displays all the movies added to the website.

****************/

require('connect.php');
session_start();

//select query
$query = "SELECT * FROM movies ORDER BY Name ASC";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 


//fetch all blogs and store in array
$movies = $statement->fetchAll();


// get and display full blog post
if(isset($_GET['id'])){
    // Sanitize the id. Like above but this time from INPUT_GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Build the parametrized SQL query using the filtered id.
    $movieQuery = "SELECT * FROM movies WHERE id = :id";
    $movieStatement = $db->prepare($movieQuery);
    $movieStatement->bindValue(':id', $id, PDO::PARAM_INT);
            
    // Execute the SELECT and fetch the single row returned.
    $movieStatement->execute();
    $moviepage = $movieStatement->fetch();

    //select image query
    $imageQuery = "SELECT * FROM images WHERE Movie_ID = :id";
    $imageStatement = $db->prepare($imageQuery);
    $imageStatement->bindValue(':id', $id, PDO::PARAM_INT);
    $imageStatement->execute(); 
    //fetch all images  and store in array
    $images = $imageStatement->fetchAll();

    //select comment query
    $commentQuery = "SELECT * FROM comments WHERE Movie_ID = :id ORDER BY date_created DESC";
    $commentStatement = $db->prepare($commentQuery);
    $commentStatement->bindValue(':id', $id, PDO::PARAM_INT);
    $commentStatement->execute(); 
    //fetch all images  and store in array
    $comments = $commentStatement->fetchAll();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Welcome to my Movies CMS</title>
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

            <?php if($_GET):?>
                   <h3><?= $moviepage['Name'] ?></h3>
                    <?php if(isset($_SESSION['username'])): ?>
                    <button><a href="pageAdministration.php">Edit Movie</a></button>  
                    <?php else:?>
                    <button><a href="login.php">Edit Movie</a></button>    
                    <?php endif ?>
                    

                    <h4>Genre: <?=$moviepage['Genre']?></h4>
                    <p>Release Date:<?=$moviepage['Release Date']?></p>
                    
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
                    <?php if(!isset($_SESSION['username'])): ?>
                    <form>
                           <label for="comment">Post a comment</label><br>
                           <textarea id="comment" name="comment" rows="10" cols="100"></textarea>
                           <br><br> 
                           <label for="name">Name</label>
                           <input type="" name=""><br>
                           <button type="submit" value="Post" id="submit_anonymous">Post Blog</button>
                    </form>
                    <?php else: ?>
                     <form>
                           <label for="comment">Post a comment</label><br>
                           <textarea id="comment" name="comment" rows="10" cols="100"></textarea>
                           <br><br> 
                           <button type="submit" value="Post" id="submit_logged-in">Post Blog</button>
                    </form>
                    <?php endif ?>

                    

            <?php else: ?>

                <?php foreach($movies as $movie): ?>
                    <div>
                        <img src="Image_Uploads/<?=$movie['images']?>" alt="avengers-image">
                        <h3><a href="index.php?id=<?= $movie['Id']?>"><?= $movie['Name'] ?></a></h3>
                        <p class="content"><?=$movie['Description']?></p>
                        <button ><a  href="index.php?id=<?= $movie['Id']?>">View Movie</a></button>
                    </div>    
                <?php endforeach ?>
            <?php endif ?>

             

          
    
    </div>
    
</body>
