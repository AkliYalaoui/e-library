<?php
session_start();
if (isset($_SESSION['logged'])) {
  header('Location: index.php');
  exit();
}

$title = "Login";
$loginBody = 'login-body flex';
require_once "includes/functions/fn.php";
require_once "includes/templates/header.php";
require_once "includes/env/db.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['email'], $_POST['password'])) {
  $email = sanitize_email($_POST['email']);
  $password = sanitize_string($_POST['password']);

  if (user_login($email,$password)) {
    set_user_session($user->email,$user->name,$user->id,$user->is_admin,$user->is_active);
    header('Location: index.php');
    exit();
  } else {
    $err_login = "Données invalides, essayez une autre fois";
  }
}
?>

<div class="flex container form-auth">
  <div class="img-container">
    <img src="layouts/images/book.png" alt="book">
  </div>
  <div class="form-container">
    <div class="text-center">
      <h1>Library.<span>fr</span></h1>
      <p>Bienvenue une autre fois cher lecteur, passez une bonne journée!</p>
    </div>
    <?php if (isset($err_login)) : ?>
    <div class="form-error"><?php echo $err_login; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
      <label for="email" class="label">Email :</label>
      <div class="form-group">
        <input type="email" class="input" name="email" id="email" placeholder="exemple: foo@bar.com"
          value="<?php echo $_POST['email'] ?? ''?>">
        <i class="fa fa-envelope"></i>
      </div>
      <label for="password" class="label">mot de passe :</label>
      <div class="form-group">
        <input type="password" class="input" name="password" id="password">
        <i class="fa fa-key"></i>
      </div>
      <div class="flex">
        <a href="register.php" class="auth-link">Vous n'avez pas un compte ?</a>
        <input type="submit" value="Login" class="cursor-pointer submit-input ">
      </div>
    </form>
  </div>
</div>

<?php
require_once "includes/templates/footer.php";
?>