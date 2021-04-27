<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('Location: login.php');
  exit();
}

$title = "Books ";
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
require_once "includes/templates/nav.php";
require_once "includes/env/db.php";

$bookid = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $con->prepare("SELECT * FROM `books` WHERE id=:bookid LIMIT 1");
$stmt->bindParam(':bookid', $bookid);
$stmt->execute();
$book = $stmt->fetch(PDO::FETCH_OBJ);
  if(!$book){
    header("Location: includes/templates/404.html");
    exit();
  }
  
?>

<div class="book-parent">
  <h3 class="book-title"><?php echo $book->title ?></h3>
  <div class="book-info">
    <div class="book-thumbnail">
      <img src="<?php echo $book->thumbnail ?>" alt="book thumbnail">
    </div>
    <dl>
      <dt>Author</dt>
      <dd><?php echo $book->author ?></dd>
      <dt>Publisher</dt>
      <dd><?php echo $book->publisher ?></dd>
      <dt>Pages</dt>
      <dd><?php echo $book->pages ?></dd>
      <dt>Isbn</dt>
      <dd><?php echo $book->isbn ?></dd>
      <dt>Publication date</dt>
      <dd>
        <time
          datetime="<?php echo $book->publication_date ?>"><?php echo (new DateTime($book->publication_date))->format("Y-m-d") ?></time>
      </dd>
      <dt>Loan Duration</dt>
      <dd><?php echo $book->loan_duration ?></dd>
    </dl>
  </div>
  <p class="book-overview"><?php echo $book->overview ?></p>
</div>
<?php
require_once "includes/templates/footer.php";
?>