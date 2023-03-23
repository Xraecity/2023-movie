<?php

 
session_start();

 session_destroy();
    unset($_SESSION['username']);
    unset($_SESSION['id']);
    unset($_SESSION['isAdmin']);
    header("location: index.php");
?>