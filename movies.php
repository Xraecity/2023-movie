<?php

/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date: March 20, 2023
    Description: This page displays all the movies added to the website.

****************/

require('connect.php');
session_start();

//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();

//variable to store errors for comment form
$inputError;
$moviepage = [];
$movieListSort = "Title";

    //select query
    $query = "SELECT * FROM movies ORDER BY Name ASC";

    // A PDO::Statement is prepared from the query.
    $statement = $db->prepare($query);

    // Execution on the DB server is delayed until we execute().
    $statement->execute(); 


    //fetch all movies and store in array
    $movies = $statement->fetchAll();





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
            //  Sanitize user input to escape HTML entities and filter out primaryous characters.
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
                    header("Location: movies.php?id=".$id1);
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

                    //redirect to movies page
                    header("Location: movies.php?id=".$id1);
                 
                }
                

           }
           else{
             header("Location: movies.php?id=".$id1);

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
            //  Sanitize user input to escape HTML entities and filter out primaryous characters.
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
            header("Location: movies.php?id=".$id2);
        }
    else{
        $inputError = "* Please enter a comment to submit" ;
    }
 }

 //function to truncate blog comment greater than 200 words
function truncate($text) {
    $text = substr($text,0,100);
    return $text;
}

?>


<title>movies</title>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
      <?php include 'nav.php';?>
    <div class="container mb-3 py-3 bg-white shadow-lg rounded-2 my-3">
        <?php if($_GET):?>
               <h3 class="text-primary fw-bold"><?= $moviepage['Name'] ?></h3>
               <?php if(isset($_SESSION['username']) && $_SESSION['isAdmin'] == 1): ?>
                  <a class="btn btn-secondary mb-2" href="pageUpdate.php?id=<?= $moviepage['Id'] ?>">Edit Movie</a>

                <?php elseif(isset($_SESSION['username']) && ($_SESSION['id'] === $moviepage['User_ID'])): ?>
                    <a class="btn btn-primary mb-2" href="pageUpdate.php?id=<?= $moviepage['Id'] ?>">Edit Movie</a> 
                <?php endif ?>
                
                <h5>Genre: <?=$genreName['Name']?></h5>
                <p>Release Date:<?=$moviepage['Release_Date']?></p><br>
                <p class="fw-bold fs-4">Description</p>
                
                <p><?=$moviepage['Description']?></p>

                <!--image section-->
                <?php if(!empty($images)):?>
                   <h4 class="fw-bold mt-3"> Images</h4>
                   <div>
                       <?php foreach($images as $image): ?>
                        <img src="Image_Uploads/<?= $image['name'] ?>" alt= "<?=$moviepage['Name']?>-image">
                       <?php endforeach ?>
                   </div>
                <?php endif ?> 
                  
                <!--comments section--> 
                <?php if(!empty($comments)):?>
                   <h4 class="fw-bold mt-3">Recent Comments</h4>
                    <div class="row">
                    <div class="col-md-8 col-lg-6">
                    <div class="card shadow-0 border" style="background-color: #f0f2f5;">
                    <?php foreach($comments as $comment): ?>
                          <div class="card-body p-4">
                            <div class="card ">
                              <div class="card-body">
                                <p class="fw-bold">Posted By: <?= $comment['commenter_name']?></p>
                                <p><i><?= date("F d, Y, h:i a", strtotime($comment['date_created']))?> </i></p>
                                <p><?= $comment['content']?></p>
                              </div>
                            </div>
                          </div>
                            <?php endforeach ?>
                        </div>
                      </div>    
                    </div>
                <?php endif ?>

                <!--comments form section-->
                <!--form for non-registered users-->
                <?php if(!isset($_SESSION['username'])): ?>
                <form class="mt-4 bg-white" method="post" action="movies.php">
                   <!-- Hidden input for the quote primary key. -->
                   <input class="form-control" type="hidden" id="id" name="id" value="<?= $moviepage['Id'] ?>">

                   <label class="form-label fw-bold fs-6" for="comment">Post a comment</label><br>
                   <textarea id="comment" name="comment" rows="10" cols="100"></textarea>
                   <br><br> 
                   <label class="form-label fw-bold fs-6" for="name">Name</label>
                   <input type="text" name="name" id="name"><br>
                   

                   <?php if(isset($inputError)):?>
                   <span class="text-primary"><?= $inputError?></span><br>
                   <?php endif ?>

                   <button  class=" btn btn-primary mb-2" type="submit" value="Post" id="submit_anonymous" name ="submit_anonymous">Post Comment</button>
                   </form>
                  <?php else: ?>
                 <!--form for logged-in users-->
                 <form class="mt-4" method="post" action="movies.php">
                   <!-- Hidden input for the quote primary key. -->
                    <input class="form-control" type="hidden" id="id" name="id" value="<?= $moviepage['Id'] ?>">

                   <label class="form-label fw-bold fs-6" for="comment">Post a comment</label><br>
                   <textarea id="comment" name="comment" rows="10" cols="100"></textarea>
                   <br><br> 

                   <?php if(isset($inputError)):?>
                   <span class="text-primary"><?= $inputError?></span><br>
                   <?php endif ?>

                   <button class="btn btn-primary mb-2" type="submit" value="Post" id="submit_logged-in" name ="submit_logged-in">Post Comment</button>

                   <?php if($_SESSION['isAdmin'] == 1 && !empty($comments)): ?>
                    <a  class="btn btn-primary mb-2" href="pageUpdate.php?id=<?= $moviepage['Id']?>">Moderate Comment</a>
                   <?php endif ?>
                </form>
                <?php endif ?>

      
        <?php elseif(isset($_SESSION['username'])): ?>
            <div class="container bg-white border border-primary border-2 my-3 py-2 rounded-2">
         <form id="home" method="post" action="movies.php" class="row g-3">
            <div class="col-auto">
            <label for="moviesListSort" class="form-label mt-2 fw-bold">Sort Movies List by:</label>
        </div>
           <div class="col-auto">
            <select name="movieListSort" id="moviesListSort" class="form-select" aria-label="Default select example">
                <option value="Title"  >Title</option>
                <option value="Date-Created">Date Created</option>
                <option value="Release-Date" >Movie Release Date</option>
            </select>
        </div>
           <div class="col-auto">
            <button type="submit" name="submit" id="submit" class="btn btn-primary mb-2">sort</button>
        </div>


            <?php if(isset($selectError)):?>
                <span class="error"><?= $selectError?></span>
            <?php endif?>
      </form>
  </div>
  
     <h4 class="fw-bold">Movie sorted by <?= $movieListSort?></h4>
    <div class="row row-cols-1 row-cols-md-3 g-4 my-3 shadow-lg">
     <?php foreach($movies as $movie): ?>
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
      <div class="col pb-3">
        <div class="card h-100">
          <img src="Image_Uploads/<?=$Homeimages[0]['name']?>" class="card-img-top" alt="<?=$movie['Name']?>">
          <div class="card-body">
            <h3 class="card-title text-primary"><?= $movie['Name'] ?></h3>
            <p class="card-text"><?= truncate($movie['Description'])?>...</p>
             <a class= "btn btn-primary mt-3 mb-2" href="movies.php?id=<?= $movie['Id']?>">View Movie</a>
          </div>
        </div>
      </div>
        <?php else:?>
          <div class="col pb-3">
            <div class="card">
              <div class="card-body">
                <h3 class="card-title text-primary"><?= $movie['Name'] ?></h3>
                <p class="card-text"><?= truncate($movie['Description'])?>...</p>
                <a  class="btn btn-primary mt-3  mb-2" href="movies.php?id=<?= $movie['Id']?>">View Movie</a>
              </div>
            </div>
        </div>
        <?php endif ?>
            
        <?php endforeach ?>
         </div>


        <?php else: ?>
     <div class="row row-cols-1 row-cols-md-3 g-4 my-3 shadow-lg">
       <?php foreach($movies as $movie): ?>
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
      <div class="col pb-3">
        <div class="card h-100">
          <img src="Image_Uploads/<?=$Homeimages[0]['name']?>" class="card-img-top" alt="<?=$movie['Name']?>">
          <div class="card-body">
            <h3 class="card-title text-primary"><?= $movie['Name'] ?></h3>
            <p class="card-text"><?= truncate($movie['Description'])?>...</p>
             <a class="btn btn-primary mt-3  mb-2" href="movies.php?id=<?= $movie['Id']?>">View Movie</a>
          </div>
        </div>
      </div>
        <?php else:?>
          <div class="col pb-3">
            <div class="card">
              <div class="card-body">
                <h3 class="card-title text-primary"><?= $movie['Name'] ?></h3>
                <p class="card-text"><?= truncate($movie['Description'])?>...</p>
                <a class="btn btn-primary mt-3 mb-2" href="movies.php?id=<?= $movie['Id']?>">View Movie</a>
              </div>
            </div>
        </div>
        <?php endif ?>
            
        <?php endforeach ?>
         </div>
        <?php endif ?>

             

          
    
    </div>
    
</body>
</html>