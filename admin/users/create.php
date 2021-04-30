<?php

require_once "../../includes/templates/init_user.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['password_confirmation'])) {

  $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
  $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
  $password = trim(filter_var($_POST['password'], FILTER_SANITIZE_STRING));
  $password_confirmation = trim(filter_var($_POST['password_confirmation'], FILTER_SANITIZE_STRING));

  $is_admin = 1;
  if (isset($_POST['is_admin'])) {
    $is_admin = trim(filter_var($_POST['is_admin'], FILTER_SANITIZE_STRING));
    $is_admin =  $is_admin == "on" ? 0 : 1;
  }

  if (strlen($name) < 4 || strlen($name) > 20) {
    $err_name = "La longueur de ce champs doit etre comprise entre 4 and 20 charactères";
  }
  if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $err_email = "Veuillez saisir une adresse email correcte";
  }
  if (strlen($password) < 6 || strlen($password) > 255) {
    $err_password = "La longueur de ce champs doit etre comprise entre 6 and 255 charactères";
  }
  if ($password !== $password_confirmation) {
    $err_password_confirmation = "Verifier votre mot de passe une autre fois";
  }

  if (!isset($err_name) && !isset($err_email) && !isset($err_password) && !isset($err_password_confirmation)) {

    $sql = "SELECT * FROM `users` WHERE email = :email OR `name` = :name";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($count === 0) {
      $sql = "INSERT INTO `users`(`name`,email,password,is_admin,is_active) VALUES (:name,:email,:password,:isAdmin,:isActive)";
      $stmt = $con->prepare($sql);
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':email', $email);
      $password = sha1($password);
      $stmt->bindParam(':password', $password);
      $stmt->bindParam(':isAdmin', $is_admin);
      $stmt->bindParam(':isActive', $is_admin);
      $stmt->execute();
      if ($stmt->rowCount() === 0) {
        $err_create_user = "Erreur, nous n'avons pas pu créer cet utilisateur";
      } else {
        header('Location: index.php');
        exit();
      }
    } else {
      $err_create_user = "nom ou email existe déja. Choisir un autre nom ou email";
    }
  }
}
?>

<div class="flex container form-auth admin-container">
  <div class="form-container">
    <?php if (isset($err_create_user)) : ?>
    <div class="form-error"><?php echo $err_create_user; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">

      <label for="name" class="label">Nom :</label>
      <div class="form-group">
        <input type="text" class="input" name="name" id="name"
          placeholder="La longueur de ce champs doit etre comprise entre 4 and 20 charactères"
          value="<?php echo $_POST['name'] ?? '' ?>">
        <i class="fa fa-user"></i>
      </div>
      <?php if (isset($err_name)) : ?>
      <div class="form-error"><?php echo $err_name; ?></div>
      <?php endif; ?>

      <label for="email" class="label">Email :</label>
      <div class="form-group">
        <input type="email" class="input" name="email" id="email" placeholder="exemple: foo@bar.com"
          value="<?php echo $_POST['email'] ?? '' ?>">
        <i class="fa fa-envelope"></i>
      </div>
      <?php if (isset($err_email)) : ?>
      <div class="form-error"><?php echo $err_email; ?></div>
      <?php endif; ?>

      <label for="password" class="label">Mot de passe :</label>
      <div class="form-group">
        <input type="password" class="input" name="password" id="password"
          placeholder="mot de passe fort entre 6 and 255 charactères">
        <i class="fa fa-key"></i>
      </div>
      <?php if (isset($err_password)) : ?>
      <div class="form-error"><?php echo $err_password; ?></div>
      <?php endif; ?>

      <label for="password_confirmation" class="label">Confirmer le mot de passe :</label>
      <div class="form-group">
        <input type="password" class="input" name="password_confirmation" id="password_confirmation"
          placeholder="mot de passe fort entre 6 and 255 charactères">
        <i class="fa fa-key"></i>
      </div>
      <?php if (isset($err_password_confirmation)) : ?>
      <div class="form-error"><?php echo $err_password_confirmation; ?></div>
      <?php endif; ?>

      <div class="input-check">
        <label for="is_admin">Est Admin :</label>
        <input type="checkbox" name="is_admin" id="is_admin">
      </div>

      <input type="submit" value="Créer" class="cursor-pointer submit-input ">
    </form>
  </div>
  <div class="img-container">
    <img src="../../layouts/images/book.png" alt="book">
  </div>
</div>

<?php
require_once "../../includes/templates/footer.php";
?>