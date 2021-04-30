<?php

$deny = true;
require_once "../../includes/templates/init_user.php";

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['delete'],$_POST['id'])){
    $id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
    $stmt = $con->prepare("DELETE FROM `users` WHERE id = :id ");
    $stmt->bindParam(':id',$id);
    $stmt->execute();
}
header('Location: index.php');
exit();