<?php

require_once "../../includes/templates/init_user.php";

$id = isset($_GET['id']) && is_numeric($_GET['id'])  ? intval($_GET['id']):0;
$user = get_user_by_id($id);
        
if(!$user){
  header('Location: ../../404.php');
  exit();
}

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['name'],$_POST['email'],$_POST['password'],$_POST['password_confirmation'])){

    $name = sanitize_string($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $password = sanitize_string($_POST['password']);
    $password_confirmation = sanitize_string($_POST['password_confirmation']);

    $is_admin = 1;
    if(isset($_POST['is_admin'])){
        $is_admin = sanitize_string($_POST['is_admin']);
        $is_admin = $is_admin == "on" ? 0:1;
    }

    if(strlen($name) < 4 || strlen($name) > 20){
        $err_name = "La longueur de ce champs doit etre comprise entre 4 and 20 charactères";
    }
    if(filter_var($email,FILTER_VALIDATE_EMAIL) === false){
        $err_email = "Veuillez saisir une adresse email correcte";
    }
    if(strlen($password) < 6 || strlen($password) > 255){
        $err_password = "La longueur de ce champs doit etre comprise entre 6 and 255 charactères";
    }
    if($password !== $password_confirmation){
        $err_password_confirmation = "Verifier votre mot de passe une autre fois";
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
                $err_create_user = "Erreur, nous n'avons pas pu modifier cet utilisateur";
            }else{
                header('Location: index.php');
                exit();
            }
        }else{
            $err_create_user = "nom ou email existe déja. Choisir un autre nom ou email";
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

      <label for="name" class="label">Nom :</label>
      <div class="form-group">
        <input type="text" required class="input" name="name" id="name"
          placeholder="La longueur de ce champs doit etre comprise entre 4 and 20 charactères"
          value="<?php echo $_POST['name'] ?? $user->name?>">
        <i class="fa fa-user"></i>
      </div>
      <?php if(isset($err_name)): ?>
      <div class="form-error"><?php echo $err_name; ?></div>
      <?php endif; ?>

      <label for="email" class="label">Email :</label>
      <div class="form-group">
        <input type="email" required class="input" name="email" id="email" placeholder="exemple: foo@bar.com"
          value="<?php echo $_POST['email'] ?? $user->email?>">
        <i class="fa fa-envelope"></i>
      </div>
      <?php if(isset($err_email)): ?>
      <div class="form-error"><?php echo $err_email; ?></div>
      <?php endif; ?>

      <label for="password" class="label">Mot de passe :</label>
      <div class="form-group">
        <input type="password" class="input" name="password" id="password"
          placeholder="Laisser ce champ vide si vous ne souhaitez pas changer votre mot de passe">
        <i class="fa fa-key"></i>
      </div>
      <?php if(isset($err_password)): ?>
      <div class="form-error"><?php echo $err_password; ?></div>
      <?php endif; ?>

      <label for="password_confirmation" class="label">Confirmer le mot de passe :</label>
      <div class="form-group">
        <input type="password" class="input" name="password_confirmation" id="password_confirmation"
          placeholder="Laisser ce champ vide si vous ne souhaitez pas changer votre mot de passe">
        <i class="fa fa-key"></i>
      </div>
      <?php if(isset($err_password_confirmation)): ?>
      <div class="form-error"><?php echo $err_password_confirmation; ?></div>
      <?php endif; ?>

      <div class="input-check">
        <label for="is_admin">Est Admin :</label>
        <input type="checkbox" name="is_admin" id="is_admin" <?php if($user->is_admin == 0):?> checked<?php endif; ?>>
      </div>

      <input type="submit" value="Modifier" class="cursor-pointer submit-input ">
    </form>
  </div>
  <div class="img-container">
    <img src="../../layouts/images/book.png" alt="book">
  </div>
</div>

<?php
require_once "../../includes/templates/footer.php";
?>