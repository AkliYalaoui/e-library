<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['is_active'] == 1) {
  header('Location: login.php');
  exit();
}
require_once "includes/env/db.php";
$id = $_SESSION['id'];
$sql = "SELECT * FROM `users` WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);
if(!$user){
  header('Location: logout.php');
  exit();
}else{
  $_SESSION['logged'] = "user";
  $_SESSION['id'] = $user->id;
  $_SESSION['email'] = $user->email;
  $_SESSION['name'] = $user->name;
  $_SESSION['is_admin'] = $user->is_admin;
  $_SESSION['is_active'] = $user->is_active;
}
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