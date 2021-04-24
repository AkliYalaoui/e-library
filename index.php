<?php
session_start();
if(!isset($_SESSION['logged'])){
 header('Location: login.php');
 exit();
}

$title = "Home";
$homeBody = "home-body";
require_once "includes/templates/header.php";
require_once "includes/templates/nav.php";
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
