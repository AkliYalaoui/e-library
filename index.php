<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('Location: login.php');
  exit();
}
$title = "Home";
$homeBody = "home-body";
$navLinks = [
  "home" => "index.php",
  "loan" => "onloan.php",
  "book" => "books.php",
  "admin_book" => "admin/books/index.php",
  "admin_user" => "admin/users/index.php",
  "profile" => "profile.php",
  "logout" => "logout.php"
];
require_once "includes/templates/header.php";
require_once "includes/env/db.php";
require_once "includes/templates/nav.php";
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

?>
<div class="overlay">
  <form action="searchResult.php" method="get" class="form-search">
    <div class="search-bar">
      <!-- <label for="searchBook" class="label">Search Book :</label>-->
      <input type="search" class="input" id="searchBook" name="search" placeholder="example : le fils du pauvre">
    </div>
    <input type="submit" value="search" class="submit-input cursor-pointer">
  </form>
</div>
<?php
require_once "includes/templates/footer.php";
?>