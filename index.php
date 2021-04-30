<?php
$homeBody = "home-body";
$title = "Acceuil";
$key = "home";
require_once "includes/templates/init_base.php";

?>
<div class="overlay">
  <form action="searchResult.php" method="get" class="form-search">
    <div class="search-bar">
      <!-- <label for="searchBook" class="label">Search Book :</label>-->
      <input type="search" required class="input" id="searchBook" name="search"
        placeholder="exemple : le fils du pauvre">
    </div>
    <input type="submit" value="Rechercher" class="submit-input cursor-pointer">
  </form>
</div>
<?php
require_once "includes/templates/footer.php";
?>