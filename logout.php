<?php

 
session_start();

 session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['id']);
    unset($_SESSION['isAdmin']);
    unset($_SESSION['email']);
    header("location: index.php");
?>