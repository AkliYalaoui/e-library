<?php
session_start();
if(!isset($_SESSION['logged']) || $_SESSION['is_active'] == 1){
    header('Location: login.php');
    exit();
}

$title = "Livres empruntés";
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
$start_row = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page'] - 1)*10 : 0;

$stmt = $con->prepare('SELECT books.*,borrow.borrowed_at,borrow.expires_at FROM `books`
                        INNER JOIN `borrow` on books.id = book_id 
                        WHERE borrow.user_id = :user_id 
                        ORDER BY created_at DESC LIMIT :row,10');
                        
$stmt->bindParam(":user_id",$_SESSION['id'],PDO::PARAM_INT);
$stmt->bindParam(":row",$start_row,PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_OBJ);
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