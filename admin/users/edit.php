<?php
session_start();
if(!isset($_SESSION['logged']) || !$_SESSION['is_admin'] == 0){
    header('Location: ../../login.php');
    exit();
}

$title = "Users Management";
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

$id = isset($_GET['id']) && is_numeric($_GET['id'])  ? intval($_GET['id']):0;
$sql = "SELECT * FROM `users` WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        
if(!$user){
  header('Location: index.php');
  exit();
}

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['name'],$_POST['email'],$_POST['password'],$_POST['password_confirmation'])){

    $name = trim(filter_var($_POST['name'],FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'],FILTER_SANITIZE_EMAIL));
    $password = trim(filter_var($_POST['password'],FILTER_SANITIZE_STRING));
    $password_confirmation = trim(filter_var($_POST['password_confirmation'],FILTER_SANITIZE_STRING));

    $is_admin = 1;
    if(isset($_POST['is_admin'])){
        $is_admin = trim(filter_var($_POST['is_admin'],FILTER_SANITIZE_STRING));
        $is_admin = $is_admin == "on" ? 0:1;
    }

    if(strlen($name) < 4 || strlen($name) > 20){
        $err_name = "Name's length should be between 4 and 20 characters";
    }
    if(filter_var($email,FILTER_VALIDATE_EMAIL) === false){
        $err_email = "Please provide a valid email";
    }
    if(strlen($password) < 6 || strlen($password) > 255){
        $err_password = "Password's length should be between 6 and  255 characters";
    }
    if($password !== $password_confirmation){
        $err_password_confirmation = "Password mismatch,Verify your password again";
    }

    if(strlen($password) === 0 && strlen($password_confirmation) === 0){
      $err_password_confirmation = null;
      $err_password =  null;
      unset($err_password,$err_password_confirmation);
    }
    
    if(!isset($err_name) && !isset($err_email) && !isset($err_password) && !isset($err_password_confirmation) ) {

        $sql = "SELECT * FROM `users` WHERE id != :id AND (email = :email OR `name` = :name)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $user->id);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count === 0) {
            $sql = "UPDATE `users` SET `name`=:name,email=:email,password=:password,is_admin=:isAdmin WHERE id = :id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $user->id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $password = strlen($password) > 0 ? sha1($password) : $user->password;
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':isAdmin',$is_admin);
            $stmt->execute();
            if(!$stmt->execute()){
                $err_create_user = "Error, we couldn't update the user";
            }else{
                header('Location: index.php');
                exit();
            }
        }else{
            $err_create_user = "username or email already been created.Choose another email or password";
        }
    }
}
?>

<div class="flex container form-auth admin-container">
  <div class="form-container">
    <?php if(isset($err_create_user)): ?>
    <div class="form-error"><?php echo $err_create_user; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?id=".$user->id ?>" method="post">

      <label for="name" class="label">Name :</label>
      <div class="form-group">
        <input type="text" class="input" name="name" id="name" placeholder="name's length between 4 and 20 char"
          value="<?php echo $_POST['name'] ?? $user->name?>">
        <i class="fa fa-user"></i>
      </div>
      <?php if(isset($err_name)): ?>
      <div class="form-error"><?php echo $err_name; ?></div>
      <?php endif; ?>

      <label for="email" class="label">Email :</label>
      <div class="form-group">
        <input type="email" class="input" name="email" id="email" placeholder="example: foo@bar.com"
          value="<?php echo $_POST['email'] ?? $user->email?>">
        <i class="fa fa-envelope"></i>
      </div>
      <?php if(isset($err_email)): ?>
      <div class="form-error"><?php echo $err_email; ?></div>
      <?php endif; ?>

      <label for="password" class="label">Password :</label>
      <div class="form-group">
        <input type="password" class="input" name="password" id="password"
          placeholder="Leave it empty if you don't want to update it">
        <i class="fa fa-key"></i>
      </div>
      <?php if(isset($err_password)): ?>
      <div class="form-error"><?php echo $err_password; ?></div>
      <?php endif; ?>

      <label for="password_confirmation" class="label">Password Confirmation :</label>
      <div class="form-group">
        <input type="password" class="input" name="password_confirmation" id="password_confirmation"
          placeholder="Leave it empty if you don't want to update it">
        <i class="fa fa-key"></i>
      </div>
      <?php if(isset($err_password_confirmation)): ?>
      <div class="form-error"><?php echo $err_password_confirmation; ?></div>
      <?php endif; ?>

      <div class="input-check">
        <label for="is_admin">Is Admin :</label>
        <input type="checkbox" name="is_admin" id="is_admin" <?php if($user->is_admin == 0):?> checked<?php endif; ?>>
      </div>

      <input type="submit" value="Update" class="cursor-pointer submit-input ">
    </form>
  </div>
  <div class="img-container">
    <img src="../../layouts/images/book.png" alt="book">
  </div>
</div>

<?php
require_once "../../includes/templates/footer.php";
?>