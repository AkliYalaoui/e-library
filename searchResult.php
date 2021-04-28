<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('Location: login.php');
  exit();
}

$title = "Home";
$homeBody = "home-body";
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
if(isset($_GET['search'])){
  $search = trim(filter_var($_GET['search'],FILTER_SANITIZE_STRING));
}else{
  header('Location: index.php');
  exit();
}

$stmt = $con->prepare('SELECT * FROM `books` WHERE title LIKE :search  ORDER BY created_at DESC');
$stmt->bindValue(":search","%$search%");
var_dump($stmt->queryString);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_OBJ);
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