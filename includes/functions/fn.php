<?php

function get_user_by_id($id){
  global $con;
  
  $sql = "SELECT * FROM `users` WHERE id = :id";
  $stmt = $con->prepare($sql);
  $stmt->bindParam(':id', $id);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_OBJ);
}

function get_all_users(){
  global $con;
  $stmt = $con->prepare('SELECT * FROM `users` WHERE name != :name ORDER BY created_at DESC');
  $stmt->bindParam(':name',$_SESSION['name']);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_OBJ);
}
function get_filtred_users(){
  global $con;
  $stmt = $con->prepare('SELECT * FROM `users` WHERE name != :name AND is_active = 1  ORDER BY created_at DESC');
  $stmt->bindParam(':name',$_SESSION['name']);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_OBJ);
}
function get_books($start_row){
  global $con;
  
  $stmt = $con->prepare('SELECT * FROM `books` ORDER BY created_at DESC LIMIT :row,10');
  $stmt->bindParam(":row",$start_row,PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_OBJ);
}
function get_borrowed_books($start_row){
  global $con;
  
  $stmt = $con->prepare('SELECT books.*,borrow.borrowed_at,borrow.expires_at FROM `books`
  INNER JOIN `borrow` on books.id = book_id 
  WHERE borrow.user_id = :user_id 
  ORDER BY created_at DESC LIMIT :row,10');

  $stmt->bindParam(":user_id",$_SESSION['id'],PDO::PARAM_INT);
  $stmt->bindParam(":row",$start_row,PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_OBJ);
}
function search_books($needle){
  global $con;
  $stmt = $con->prepare('SELECT * FROM `books` WHERE title LIKE :search  ORDER BY created_at DESC');
  $stmt->bindValue(":search","%$needle%");
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_OBJ);
}
function is_available_book($bookid){
  global $book_borrowed,$con;

  $stmt = $con->prepare("SELECT * FROM `borrow` WHERE book_id=:book_id");
  $stmt->bindParam(':book_id', $bookid);
  $stmt->execute();
  $book_borrowed = $stmt->fetch(PDO::FETCH_OBJ);
  return $stmt->rowCount() === 0;
}
function get_book_by_id($bookid){
  global $con;
  $stmt = $con->prepare("SELECT * FROM `books` WHERE id=:bookid LIMIT 1");
  $stmt->bindParam(':bookid', $bookid);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_OBJ);
}
function set_user_session($email,$name,$id=0,$is_admin = 1,$is_active = 1){
  $_SESSION['logged'] = "user";
  $_SESSION['id'] = $id;
  $_SESSION['email'] = $email;
  $_SESSION['name'] = $name;
  $_SESSION['is_admin'] = $is_admin;
  $_SESSION['is_active'] = $is_active;
}
function update_user($user,$name,$email,$password){
  global $con;
  $sql = "UPDATE `users` SET `name`=:name,email=:email,password=:password WHERE id = :id";
  $stmt = $con->prepare($sql);
  $stmt->bindParam(':id', $user->id);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':email', $email);
  $password = strlen($password) > 0 ? sha1($password) : $user->password;
  $stmt->bindParam(':password', $password);
  return $stmt->execute() && $stmt->rowCount() > 0;
}
function create_user($name,$email,$password){
  global $con;
  
  $sql = "INSERT INTO `users`(`name`,email,password,is_admin,is_active) VALUES (:name,:email,:password,1,1)";
  $stmt = $con->prepare($sql);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':email', $email);
  $password = sha1($password);
  $stmt->bindParam(':password', $password);
  $stmt->execute();
  return $stmt->rowCount() > 0 ? true : false;
}
function user_login($email,$password){
  
    global $con,$user;
    $sql = "SELECT * FROM `users` WHERE email = :email AND password = :password";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email);
    $password = sha1($password);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);
    return $stmt->rowCount() > 0 ? true : false;
}
function user_exists($email,$name,$id=0){
  
    global $con;
    $sql = "SELECT * FROM `users` WHERE id != :id AND (email = :email OR `name` = :name)";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? true : false;
}
function sanitize_string($str){
  return trim(filter_var($str, FILTER_SANITIZE_STRING));
}
function sanitize_email($email){
  return trim(filter_var($email, FILTER_SANITIZE_EMAIL));
}
function set_admin_books_links($page_title,$active_link = null){
global $title,$navLinks,$pageName;

$title = $page_title;
$navLinks = [
"home" => "../../index.php",
"loan" => "../../onloan.php",
"book" => "../../books.php",
"admin_book" => "index.php",
"admin_user" => "../users/index.php",
"profile" => "../../profile.php",
"logout" => "../../logout.php"
];
$pageName = $navLinks[$active_link];
}
function set_admin_links($page_title,$active_link = null){
global $title,$navLinks,$pageName;

$title = $page_title;
$navLinks = [
"home" => "../../index.php",
"loan" => "../../onloan.php",
"book" => "../../books.php",
"admin_book" => "../books/index.php",
"admin_user" => "index.php",
"profile" => "../../profile.php",
"logout" => "../../logout.php"
];
$pageName = $navLinks[$active_link];
}
function set_user_links($page_title,$active_link = null){
global $title,$navLinks,$pageName;

$title = $page_title;
$navLinks = [
"home" => "index.php",
"loan" => "onloan.php",
"book" => "books.php",
"admin_book" => "admin/books/index.php",
"admin_user" => "admin/users/index.php",
"profile" => "profile.php",
"logout" => "logout.php",
"" => ""
];
$pageName = $navLinks[$active_link ?? ""];
}
function check_user_state(){
global $con,$user;
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