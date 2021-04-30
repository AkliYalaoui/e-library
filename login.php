<?php
session_start();
if (isset($_SESSION['logged'])) {
  header('Location: index.php');
  exit();
}

$title = "Login";
$loginBody = 'login-body flex';
require_once "includes/templates/header.php";
require_once "includes/env/db.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['email'], $_POST['password'])) {
  $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
  $password = trim(filter_var($_POST['password'], FILTER_SANITIZE_STRING));

  $sql = "SELECT * FROM `users` WHERE email = :email AND password = :password";
  $stmt = $con->prepare($sql);
  $stmt->bindParam(':email', $email);
  $password = sha1($password);
  $stmt->bindParam(':password', $password);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_OBJ);
  if ($stmt->rowCount() > 0) {
    $_SESSION['logged'] = "user";
    $_SESSION['id'] = $user->id;
    $_SESSION['email'] = $user->email;
    $_SESSION['name'] = $user->name;
    $_SESSION['is_admin'] = $user->is_admin;
    $_SESSION['is_active'] = $user->is_active;
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