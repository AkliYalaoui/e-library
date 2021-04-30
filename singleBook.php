<?php

$title = "Livre";
$key = null;
require_once "includes/templates/init_base.php";

$bookid = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
$book = get_book_by_id($bookid);

if(!$book){
  header("Location: 404.php");
  exit();
}
  
?>

<div class="book-parent">
  <div class="borrow-book">
    <?php if($_SESSION['is_active'] == 0):  ?> <?php 
    if(is_available_book($bookid)):
  ?>
    <form action="borrow.php" method="post">
      <input type="hidden" value="<?php echo $book->id?>" name="id">
      <input type="hidden" value="<?php echo $book->loan_duration?>" name="loan_duration">
      <input type="submit" value="Emprunter ce livre" name="borrow">
    </form>
    <?php elseif($book_borrowed->user_id == $_SESSION['id']): ?>
    <form action="delete_borrow.php" method="post">
      <input type="hidden" value="<?php echo $book->id?>" name="id">
      <input type="hidden" value="<?php echo $book->loan_duration?>" name="loan_duration">
      <input type="submit" value="Rendre ce livre" name="delete_borrow">
    </form>
    <?php else: ?>
    <div class="form-error">Ce livre est indisponible à l'emprunt</div>
    <?php endif;endif; ?>
    <h3 class="book-title"><?php echo $book->title ?>
    </h3>
  </div>
  <div class="book-info">
    <div class="book-thumbnail">
      <img src="<?php echo $book->thumbnail ?>" alt="book thumbnail">
    </div>
    <dl>
      <dt>Auteur</dt>
      <dd><?php echo $book->author ?></dd>
      <dt>Edition</dt>
      <dd><?php echo $book->publisher ?></dd>
      <dt>Pages</dt>
      <dd><?php echo $book->pages ?></dd>
      <dt>Isbn</dt>
      <dd><?php echo $book->isbn ?></dd>
      <dt>Date de Publication</dt>
      <dd>
        <time
          datetime="<?php echo $book->publication_date ?>"><?php echo (new DateTime($book->publication_date))->format("Y-m-d") ?></time>
      </dd>
      <dt>Durée d'emprunt</dt>
      <dd><?php echo $book->loan_duration ?></dd>
    </dl>
  </div>
  <p class="book-overview"><?php echo $book->overview ?></p>
</div>
<?php
require_once "includes/templates/footer.php";
?>