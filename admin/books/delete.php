<?php
$deny = true;
require_once "../../includes/templates/init_book.php";

if( $_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['thumbnail'])){
  $thumbnail = filter_var($_POST['thumbnail'],FILTER_SANITIZE_STRING);
  $stmt = $con->prepare("DELETE FROM `books` WHERE thumbnail = :thumbnail ");
  $stmt->bindParam(':thumbnail',$thumbnail);
  $path = "../../$thumbnail";
  if($stmt->execute() && file_exists($path) ){
    unlink($path);
  }
}
header('Location: index.php');
exit();