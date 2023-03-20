<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 20, 2023
    Description: This is the main page that displays all the movies posted

****************/

require('connect.php');

//select query
$query = "SELECT * FROM blogs ORDER BY date DESC";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 


//fetch all blogs and store in array
$blogsContents = $statement->fetchAll();


//function to truncate blog comment greater than 200 words
function truncate($text) {
    $text = substr($text,0,200);
    return $text;
}


// get and display full blog post
if(isset($_GET['id'])){
    // Sanitize the id. Like above but this time from INPUT_GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Build the parametrized SQL query using the filtered id.
    $query = "SELECT * FROM blogs WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
            
    // Execute the SELECT and fetch the single row returned.
    $statement->execute();
    $fullBlog = $statement->fetch();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Welcome to my Blog!</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div class="block">
            <h1><a href = "index.php">Cats Who Code</a></h1>
            <nav>
                <ul>
                    <li class="first"><a href="index.php">Home</a></li>
                    <li class="second"><a href="post.php">New Post</a></li>
                </ul>
            </nav>
             
            <?php if($_GET):?>
                <h3><?= $fullBlog['title'] ?></h3>
                <p class="date"><?= date("F d, Y, h:i a", strtotime($fullBlog['date']))?> - <a class="edit" href="edit.php?id=<?= $fullBlog['id']?>">Edit</a></p>
                <p class="content"><?= $fullBlog['content'] ?></p>

            
            <?php else: ?>
                <h3>Recently Posted Blogs</h3>

                <?php foreach($blogsContents as $blog): ?>
                     <h3><a href="index.php?id=<?= $blog['id']?>"><?= $blog['title'] ?></a></h3>
                    <p class="date"><?= date("F d, Y, h:i a", strtotime($blog['date']))?> - <a class="edit" href="edit.php?id=<?= $blog['id']?>">Edit</a></p>
                    <?php if(strlen($blog['content']) > 200): ?>
                    <p class="content"><?= truncate($blog['content'])?> <a class="fullblog" href="index.php?id=<?= $blog['id']?>">...Read more</a></p>
                    <?php else: ?>
                    <p class="content"><?=$blog['content']?></p>
                    <?php endif ?>
                    
                <?php endforeach ?>
            <?php endif ?>

    
    </div>
    
</body>
