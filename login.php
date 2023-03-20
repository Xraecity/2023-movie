<?php
/*******w******** 
    
    Name: Maryam Ayemlo Gambo
    Date:March 20th, 2023
    Description: This page allows a user with registerd account to login

****************/

require('connect.php');
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login page</title>
</head>

<body>
	<form method="post" action="login.php">
		<label for="username">Username</label><br>
        <input id="username" name="username" type="text"><br><br>
        <label for="password">Password</label><br>
        <input id="password" name="password" type="text"><br><br>




		<input type="submit" value="Login" id="login">	
	</form>



</body>
</html>