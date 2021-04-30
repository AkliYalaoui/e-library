<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('Location: login.php');
  exit();
}

$title = "livre";
$navLinks = [
  "home" => "index.php",
  "loan" => "onloan.php",
  "book" => "books.php",
  "admin_book" => "admin/books/index.php",
  "admin_user" => "admin/users/index.php",
  "profile" => "profile.php",
  "logout" => "logout.php"
];
$pageName = "";
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

$bookid = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $con->prepare("SELECT * FROM `books` WHERE id=:bookid LIMIT 1");
$stmt->bindParam(':bookid', $bookid);
$stmt->execute();
$book = $stmt->fetch(PDO::FETCH_OBJ);
  if(!$book){
    header("Location: 404.php");
    exit();
  }
  
?>

<div class="book-parent">
  <div class="borrow-book">
    <?php if($_SESSION['is_active'] == 0):  ?> <?php 
    $stmt = $con->prepare("SELECT * FROM `borrow` WHERE book_id=:book_id");
    $stmt->bindParam(':book_id', $bookid);
    $stmt->execute();
    $book_borrowed = $stmt->fetch(PDO::FETCH_OBJ);
    if($stmt->rowCount() === 0):
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