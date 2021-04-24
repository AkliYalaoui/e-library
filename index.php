<?php
session_start();
if(!isset($_SESSION['logged'])){
 header('Location: login.php');
 exit();
}

$title = "Home";
require_once "includes/templates/header.php";
?>

<?php
 require_once "includes/templates/footer.php";
 ?>
