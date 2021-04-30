<?php

function check_user_state(){
  global $con;
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
}