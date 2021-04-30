<?php

$title = "Livres";
$key = "book";
require_once "includes/templates/init_base.php";

$start_row = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page'] - 1)*10 : 0;
$books = get_books($start_row);
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
    Nous ne disposons d'aucun livre pour le moment. Merci pour votre compréhension
  </p>
  <img src="layouts/images/404.png" alt="no-results">
</main>
<?php endif; ?>

<?php
require_once "includes/templates/footer.php";
?>