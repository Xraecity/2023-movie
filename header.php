<?php 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Header nav</title>
</head>
<body>
       <nav>
            <h1><a href = "index.php">Movies CMS</a></h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                 <li>
                    <form action="searchKeyword.php" method="GET"> 
                        <label  for="searchKeyword">Search Movies</label>
                        <input id="searchKeyword" name="searchKeyword" type="text">
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

                        <button>Search</button>
                    </form>
                </li>
                
                <?php if(isset($_SESSION['username'])): ?>
                <li><a href="pageAdministration.php"><?= $_SESSION['username']?></a></li>
                <button><a href="logout.php">Log out</a></button>

                <?php else:?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="registration.php">Register</a></li>
                <?php endif?>



            </ul>
        </nav> 

</body>
</html>