<?php
session_start();
if(!isset($_SESSION['logged']) || !$_SESSION['is_admin'] == 0){
    header('Location: ../../login.php');
    exit();
}

$title = "Gestion des utilisateurs";
$css = "../../layouts/css";
$js = "../../layouts/js";
$navLinks = [
        "home" => "../../index.php",
        "loan" => "../../onloan.php",
        "book" => "../../books.php",
        "admin_book" => "../books/index.php",
        "admin_user" => "index.php",
        "profile" => "../../profile.php",
        "logout" => "../../logout.php"
];
require_once "../../includes/templates/header.php";
require_once "../../includes/env/db.php";
require_once "../../includes/templates/nav.php";
require_once "../../includes/functions/fn.php";
check_user_state();

if(isset($_GET['filter']) && filter_var($_GET['filter'],FILTER_SANITIZE_STRING) === "approve" ){
  $stmt = $con->prepare('SELECT * FROM `users` WHERE name != :name AND is_active = 1  ORDER BY created_at DESC');
}else{
  $stmt = $con->prepare('SELECT * FROM `users` WHERE name != :name ORDER BY created_at DESC');
}

$stmt->bindParam(':name',$_SESSION['name']);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<div class="t-container">
  <?php if(!isset($_GET['filter'])): ?>
  <a href="?filter=approve" title="Les utilisateurs qui attendent une validation">Filtrer</a>
  <?php else:?>
  <a href="index.php" title="Tous les utilisateurs">Tous</a>
  <?php endif; ?>
  <a href="create.php">Nouvel utilisateur<i class="fa fa-plus"></i></a>
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>NOM</th>
          <th>EMAIL</th>
          <th>EST_ADMIN</th>
          <th>EST_ACTIF</th>
          <th>CREÉ_LE</th>
          <th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($users) === 0): ?>
        <tr>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
          <td>/</td>
        </tr>
        <?php endif; ?>
        <?php foreach ($users as $user): ?>
        <tr>
          <td><?php echo $user->id ?></td>
          <td><?php echo $user->name ?></td>
          <td><?php echo $user->email ?></td>
          <td>
            <?php echo $user->is_admin == 0 ? '<i class="fa fa-check" style="color: #009688" ></i>':'<i class="fa fa-times" style="color:#e91e63 "></i>' ?>
          </td>
          <td>
            <?php echo $user->is_active == 0 ? '<i class="fa fa-check" style="color: #009688"></i>': '<i class="fa fa-times" style="color:#e91e63 "></i>' ?>
          </td>
          <td><?php echo $user->created_at ?></td>
          <td>
            <a href="edit.php?id=<?php echo $user->id?>" class="edit">modifier</a>
            <form action="delete.php" method="post">
              <input type="hidden" value="<?php echo $user->id?>" name="id">
              <input type="submit" value="supprimer" class="danger" name="delete">
            </form>
            <?php if($user->is_active == 1): ?>
            <form action="approuve.php" method="post">
              <input type="hidden" value="<?php echo $user->id?>" name="id">
              <input type="submit" value="valider" class="approve" name="approve">
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>
<?php
require_once "../../includes/templates/footer.php";
?>