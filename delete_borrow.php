<?php

$deny =true;
require_once "includes/templates/init_base.php";

if( $_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['id'],$_POST['loan_duration'])){
  $book_id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
  // $loan_duration = filter_var($_POST['loan_duration'],FILTER_SANITIZE_NUMBER_INT);
  $stmt = $con->prepare("DELETE FROM `borrow` WHERE book_id=:book_id AND user_id=:user_id");
  $stmt->bindParam(':user_id',$_SESSION['id']);
  $stmt->bindParam(':book_id',$book_id);
  $stmt->execute();
}
header('Location: onloan.php');
exit();