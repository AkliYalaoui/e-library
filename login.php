<?php
    session_start();
    if(isset($_SESSION['logged'])){
        header('Location: index.php');
        exit();
    }

    $title = "Login";
    $loginBody = 'login-body flex';
    require_once "includes/templates/header.php";
    require_once "includes/env/db.php";

    if( $_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['email'],$_POST['password'])){
        $email = trim(filter_var($_POST['email'],FILTER_SANITIZE_EMAIL));
        $password = trim(filter_var($_POST['password'],FILTER_SANITIZE_STRING));

        $sql = "SELECT * FROM `users` WHERE email = :email AND password = :password";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':email', $email);
        $password = sha1($password);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $_SESSION['logged'] = "user";
            $_SESSION['email'] = $email;
            header('Location: index.php');
            exit();
        }else{
            $err_login = "Invalid Credentials, please try again";
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
           <p>Welcome again dear reader, have a nice day!</p>
       </div>
       <?php if(isset($err_login)): ?>
           <div class="form-error"><?php echo $err_login; ?></div>
       <?php endif; ?>
       <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
           <label for="email" class="label">Email :</label>
           <div class="form-group">
                <input type="email" class="input" name="email" id="email" placeholder="example: foo@bar.com">
                <i class="fa fa-envelope"></i>
           </div>
           <label for="password" class="label">Password :</label>
           <div class="form-group">
                <input type="password" class="input" name="password" id="password">
                <i class="fa fa-key"></i>
           </div>
           <div class="flex">
               <a href="register.php" class="auth-link">You don't have an account ?</a>
               <input type="submit" value="Login" class="cursor-pointer submit-input ">
           </div>
       </form>
   </div>
</div>

<?php
require_once "includes/templates/footer.php";
?>
