<?php

$homeBody = "home-body";
$title = "Resultats de recherche";
$key = null;
require_once "includes/templates/init_base.php";

if(isset($_GET['search'])){
  $search = trim(filter_var($_GET['search'],FILTER_SANITIZE_STRING));
}else{
  header('Location: index.php');
  exit();
}

$books = search_books($search);
if(count($books) > 0):
?>
<main class="b-container">
  <div class="book-container">
    <?php foreach ($books as $book): ?>
    <div class="book">
      <div>
        <a href="singleBook.php?id=<?php echo $book->id?>" title="see detail"><i class="fa fa-arrow-right"></i></a>
        <img src="<?php echo $book->thumbnail?>" alt="<?php echo $book->title?>">
      </div>
      <h3><?php echo $book->title?></h3>
    </div>
    <?php endforeach; ?>
  </div>
</main>
<?php else: ?>
<main class="b-container no-results">
  <p class="form-error text-center">
    Nous n'avons trouvé aucun livre avec un titre similaire à votre recherche. Essayez une <a href="index.php">autre
      fois</a>
  </p>
  <img src="layouts/images/404.png" alt="no-results">
</main>
<?php endif; ?>
<?php
require_once "includes/templates/footer.php";
?>