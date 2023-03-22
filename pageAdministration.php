<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page is users account page where they can view,update,delete pages

****************/

session_start();

//check if session exists
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: login.php');
}

//if logout button is clicked
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['id']);
    unset($_SESSION['isAdmin']);
    header("location: index.php");
}




?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login page</title>
</head>

<body>
	<div>
		<h2>Page Administration</h2>
		<button><a href="pageCreate.php">Add Movie</a></button>
		<button><a href="index.php?logout='1'">Log out</a></button>
	</div>
	
    <h3>Welcome <?= $_SESSION['username']?></h3>

  <?php if($_SESSION['isAdmin'] == 1): ?>

  <?php endif ?>



</body>
</html>