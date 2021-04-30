<?php
session_start();
if (isset($_SESSION['logged'])) {
  header('Location: index.php');
  exit();
}

$title = "Register";
$loginBody = 'login-body flex';
require_once "includes/functions/fn.php";
require_once "includes/templates/header.php";
require_once "includes/env/db.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['password_confirmation'])) {

  $name = sanitize_string($_POST['name']);
  $email = sanitize_email($_POST['email']);
  $password = sanitize_string($_POST['password']);
  $password_confirmation = sanitize_string($_POST['password_confirmation']);

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

    if (!user_exists($email,$name)) {
      if (!create_user($name,$email,$password)) {
        $err_create_user = "Erreur, nous n'avons pas pu créer ce compte";
      } else {
        set_user_session($email,$name);
        header('Location: index.php');
        exit();
      }
    } else {
      $err_create_user = "nom ou email existe déja. Choisir un autre nom ou email";
    }
  }
}
?>

<div class="flex container form-auth">
  <div class="form-container">
    <div class="text-center">
      <h1>Library.<span>fr</span></h1>
      <p>Hi thanks for joining us new reader, have a nice day!</p>
    </div>

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

      <div class="flex">
        <a href="login.php" class="auth-link">Vous avez déja un compte ?</a>
        <input type="submit" value="Register" class="cursor-pointer submit-input ">
      </div>

    </form>
  </div>
  <div class="img-container">
    <img src="layouts/images/book.png" alt="book">
  </div>
</div>

<?php
require_once "includes/templates/footer.php";
?>