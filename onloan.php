<?php

$title = "Livres empruntés";
$key = "loan";
require_once "includes/templates/init_base.php";

$start_row = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page'] - 1)*10 : 0;
$books =get_borrowed_books($start_row);

if(count($books) > 0):
$count = $con->query("SELECT count(id) FROM `books`")->rowCount();
?>
<main class="b-container">

  <nav>
    <ul>
      <?php for ($i = 0; $i <= ($count/10);$i++): ?>
      <li>
        <?php if(isset($_GET['page']) && $_GET['page'] == $i+1 ): ?>
        <a href="?page=<?php echo $i+1 ?>"
          style="background-color: var(--main-color);color:#fff;"><?php echo $i+1 ?></a>
        <?php else: ?>
        <a href="?page=<?php echo $i+1 ?>"><?php echo $i+1 ?></a>
        <?php endif; ?>
      </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <div class="book-container">
    <?php foreach ($books as $book): ?>
    <div class="book">
      <div>
        <div class="borrow-info"><?php echo "Ce livre à rendre avant le ".date("Y-m-d",strtotime($book->expires_at)) ?>
        </div>
        <form action="delete_borrow.php" method="post" class="borrowed-action">
          <input type="hidden" value="<?php echo $book->id?>" name="id">
          <input type="hidden" value="<?php echo $book->loan_duration?>" name="loan_duration">
          <input type="submit" value="Rendre ce livre" name="delete_borrow">
        </form>
        <a href="singleBook.php?id=<?php echo $book->id?>" title="voir detail"><i class="fa fa-arrow-right"></i></a>
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
    Vous n'avez emprunté aucun livre pour le moment. Emprunter livre à partir d'<a href="books.php">ICI</a>
  </p>
  <img src="layouts/images/404.png" alt="no-results">
</main>
<?php endif; ?>
<?php
require_once "includes/templates/footer.php";
?>