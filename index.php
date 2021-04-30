<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('Location: login.php');
  exit();
}
$title = "Acceuil";
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
require_once "includes/functions/fn.php";
check_user_state();

?>
<div class="overlay">
  <form action="searchResult.php" method="get" class="form-search">
    <div class="search-bar">
      <!-- <label for="searchBook" class="label">Search Book :</label>-->
      <input type="search" class="input" id="searchBook" name="search" placeholder="exemple : le fils du pauvre">
    </div>
    <input type="submit" value="Rechercher" class="submit-input cursor-pointer">
  </form>
</div>
<?php
require_once "includes/templates/footer.php";
?>