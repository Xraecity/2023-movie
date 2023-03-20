<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 20, 2023
    Description: This page displays all the movies added to the website.

****************/

require('connect.php');

//select query
$query = "SELECT * FROM movies ORDER BY Name ASC";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 


//fetch all blogs and store in array
$movies = $statement->fetchAll();


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
                    <li><a href="post.php">New Post</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="registration.php">Register</a></li>
                </ul>
            </nav>
             

                <?php foreach($movies as $movie): ?>
                     <h3><a href="index.php?id=<?= $movie['Id']?>"><?= $movie['Name'] ?></a></h3>
                    <p class="date"><?= date("F d, Y, h:i a", strtotime($movie['Date_Created']))?> - <a class="edit" href="update.php?id=<?= $movie['Id']?>">Edit</a></p>

                    <h4><?=$movie['Genre']?></h4>
                    <p><?=$movie['Release Date']?></p>
                    
                    <p class="content"><?=$movie['Description']?></p>
                    <img src="Image_Uploads/<?=$movie['images']?>" alt="avengers-image">
                    
                    
                <?php endforeach ?>
          
    
    </div>
    
</body>
