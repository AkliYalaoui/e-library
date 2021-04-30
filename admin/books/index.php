<?php
session_start();
if (!isset($_SESSION['logged']) || !$_SESSION['is_admin'] == 0) {
  header('Location: ../../login.php');
  exit();
}

$title = "Gestion dev livres";
$css = "../../layouts/css";
$js = "../../layouts/js";
$navLinks = [
  "home" => "../../index.php",
  "loan" => "../../onloan.php",
  "book" => "../../books.php",
  "admin_book" => "index.php",
  "admin_user" => "../users/index.php",
  "profile" => "../../profile.php",
  "logout" => "../../logout.php"
];
$pageName = $navLinks["admin_book"];
require_once "../../includes/templates/header.php";
require_once "../../includes/env/db.php";
require_once "../../includes/templates/nav.php";
require_once "../../includes/functions/fn.php";
check_user_state();

$stmt = $con->prepare('SELECT * FROM `books` ORDER BY created_at DESC');
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<div class="t-container">
  <a href="create.php">Nouveau livre<i class="fa fa-plus"></i></a>
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>TITRE</th>
          <th>AUTEUR</th>
          <th>ISBN</th>
          <th>EDITION</th>
          <th>DATE DE PUBLICATION</th>
          <th>PAGES</th>
          <th>DUREÉ D'EMPRUNT</th>
          <th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($books) === 0) : ?>
        <tr>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
        </tr>
        <?php endif; ?>
        <?php foreach ($books as $book) : ?>
        <tr>
          <td><?php echo $book->id ?></td>
          <td><?php echo $book->title ?></td>
          <td><?php echo $book->author ?></td>
          <td><?php echo $book->isbn ?></td>
          <td><?php echo $book->publisher ?></td>
          <td><?php echo date("Y-m-d",strtotime($book->publication_date)) ?></td>
          <td><?php echo $book->pages ?></td>
          <td><?php echo $book->loan_duration ?></td>
          <td>
            <a href="../../singleBook.php?id=<?php echo $book->id ?>" class="approve">voir</a>
            <a href="edit.php?id=<?php echo $book->id ?>" class="edit">modifier</a>
            <form action="delete.php" method="post">
              <input type="hidden" value="<?php echo $book->thumbnail ?>" name="thumbnail">
              <input type="submit" value="supprimer" class="danger" name="delete">
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
require_once "../../includes/templates/footer.php";
?>