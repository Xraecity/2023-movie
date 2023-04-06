<?php
require('connect.php');
session_start();

// Show PHP errors (Disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include library PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


// Start
$mail = new PHPMailer(true);

//select all categories
// Build the parameterized SQL query and bind to the above sanitized values.
$genreQuery = "SELECT * FROM genres";
$genreStatement = $db->prepare($genreQuery);  
    
// Execute the UPDATE
$genreStatement->execute();
$genres = $genreStatement->fetchAll();

$emailError;
$messageError;
$messageSuccess;
$subjectError;

$emailValid = true;
$messageValid = true;
$subjectValid= true;

$message= "";
$email="";
$subject = "";
$email_from = "mgambo87@gmail.com";

if($_POST){
	if(!empty($_POST['email'])){
        if(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) === false){
            $emailError = "Email is invalid";
            $emailValid = false;
        }
 
        else{
	    	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            }
        }
    else{
        $emailError = "Email is required";
        $emailValid = false;
    }

    if(empty($_POST['subject'])){
      $subjectError = "Please enter a subject";
      $subjectValid= false;
    }
    else{
    	$subject =  filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }


    if(empty($_POST['message'])){
      $messageError = "Please enter a message";
      $messageValid= false;
    }
    else{
    	$message =  filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if($emailValid && $messageValid && $subjectValid){
    	$messageSuccess = "Message sent successfully.";
    	header("Refresh:2,url=index.php");
    	
	//     	try {
	// // // Configuration SMTP
	// $mail->isSMTP();
	// $mail->Host = 'sandbox.smtp.mailtrap.io';
	// $mail->SMTPAuth = true;
	// $mail->Username = '070e1454ebe260'; 
	// $mail->Password = '01a51b0d70a6a1'; 
	// $mail->SMTPSecure = 'tls';
	// $mail->Port = 2525;	

	// $mail->setFrom('info@mailtrap.io', 'Mailtrap');
	// $mail->addReplyTo('info@mailtrap.io', 'Mailtrap');
	// $mail->addAddress($email, 'Receiver');

	
	// // Mail content
	// $mail->isHTML(true);
	// $mail->Subject = $subject;
	// $mail->Body = $message;
	// $mail->AltBody = $message;
	// $mail->send();
	// echo 'The message has been sent';
	// header('refresh:2, url= index.php');
	// } catch (Exception $e) {
	// echo "Message has not been sent. Mailer Error: {$mail->ErrorInfo}";
	// }
	//     }


}


}


 ?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Contact Us</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
	 <nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-gradient py-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Movies CMS</a>
    <?php if(isset($_SESSION['username'])): ?>
    <a class="navbar-brand fw-bold" href="pageAdministration.php"> <?= $_SESSION['username']?></a>
<?php endif ?>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="movies.php">Movies</a>
        </li>
          <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="contact.php">Contact us</a>
        </li>

        <?php if(isset($_SESSION['username'])): ?>
         <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="logout.php">Log Out</a>
        </li>
        <?php else:  ?>
        <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="login.php">Login</a>
        </li>
         <li class="nav-item">
          <a class="nav-link fw-bold text-white" href="registration.php">Sign Up</a>
        </li>
         <?php endif ?>

      
      </ul>
        <form class="d-flex" id="searchForm" action="searchKeyword.php" method="POST">
        <input class="form-control input-lg me-2" type="search" placeholder="Search Movies" aria-label="Search" id="searchKeyword" name="searchKeyword"> 
         <select name="genre" id="genre" class="form-select form-select-sm  me-2 " aria-label="Default select">
            <option value = "">All Genres</option>
           <?php  foreach($genres as $genre):?>
            <option value="<?=$genre['ID']?>"><?=$genre['Name']?></option>
           <?php endforeach?>
        </select>  
        <button class="btn btn-danger" type="submit">Search</button>
      </form>

    </div>
  </div>
</nav>
	 <div class="container">
	 	<div class="container border border-2 rounded-5 border-danger mt-5 shadow-lg">
	 <h1 class="text-center  text-danger fw-bold mt-4">Contact Us</h1>
		<form method="post" action="contact.php">
			<?php if(isset($messageSuccess)):?>
	        <span class="fw-bold"><?= $messageSuccess?></span><br>
	     <?php endif ?>

		  <div class=" form-floating mb-3 mt-3">
	          <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
	          <label for="email">Email</label>
          </div>

	        <!-- if email field has error,display error message--> 
	        <?php if(isset($emailError)): ?>
	            <span class="error text-danger"><?= $emailError ?></span><br>
	        <?php endif ?>

	        <div class=" form-floating mb-3 mt-3">
	          <input type="text" class="form-control" id="subject" placeholder="Enter subject" name="subject">
	          <label for="subject">Subject</label>
           </div>

	        <!-- if email field has error,display error message--> 
	        <?php if(isset($subjectError)): ?>
	            <span class="error text-danger"><?= $subjectError ?></span><br>
	        <?php endif ?>

		   <div class="form-floating mb-3 mt-3">
		      <textarea class="form-control" id="message" name="message" placeholder="Comment goes here" rows="10" cols="100"></textarea>
		      <label for="message">Add message</label>
		    </div>

	        <?php if(isset($messageError)):?>
	        <span class="error text-danger"><?= $messageError?></span><br>
	        <?php endif ?>

	        <button type="submit" class="btn btn-danger fs-5 mb-3" value="Post" id="contact" name="contact">Submit</button>
			
		</form>
</div>
</div>

</body>
</html>