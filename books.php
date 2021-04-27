<?php
session_start();
if(!isset($_SESSION['logged'])){
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

$start_row = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page'] - 1)*10 : 0;

$stmt = $con->prepare('SELECT * FROM `books` ORDER BY created_at DESC LIMIT :row,10');
$stmt->bindParam(":row",$start_row,PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_OBJ);
if(count($books) === 0){
  header("Location: includes/templates/404.html");
  exit();
}
$count = $con->query("SELECT count(id) FROM `books`")->rowCount();

?>
<main class="b-container">
  <nav>
    <ul>
      <?php for ($i = 0; $i <= ($count/10);$i++): ?>
      <li>
        <a href="?page=<?php echo $i+1 ?>"><?php echo $i+1 ?></a>
      </li>
      <?php endfor; ?>
    </ul>
  </nav>
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
<?php
require_once "includes/templates/footer.php";
?>