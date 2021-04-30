<?php
require_once "../../includes/templates/init_book.php";

if( $_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['title'],$_POST['author'],$_POST['isbn'],$_POST['publisher'],$_POST['pages'],$_POST['publication_date'],$_POST['loan_duration'],$_FILES['thumbnail'],$_POST['overview'])){

    $title = trim(filter_var($_POST['title'],FILTER_SANITIZE_STRING));
    $title = str_replace("\\","",str_replace("/","",$title));
    $author = trim(filter_var($_POST['author'],FILTER_SANITIZE_STRING));
    $loan_duration = trim(filter_var($_POST['loan_duration'],FILTER_SANITIZE_STRING));
    $overview = trim(filter_var($_POST['overview'],FILTER_SANITIZE_STRING));
    $isbn= trim(filter_var($_POST['isbn'],FILTER_SANITIZE_NUMBER_INT));
    $pages= trim(filter_var($_POST['pages'],FILTER_SANITIZE_NUMBER_INT));
    $publisher = trim(filter_var($_POST['publisher'],FILTER_SANITIZE_STRING));
    $publication_date = trim(filter_var($_POST['publication_date'],FILTER_SANITIZE_STRING));
    $book_cover = $_FILES['thumbnail'];


    if(strlen($title) < 4 || strlen($title) > 255){
        $err_title = "La longueur de ce champs doit etre comprise entre 4 and 255 charactères";
    }
    if(strlen($overview) < 20 || strlen($overview) > 500){
        $err_overview = "La longueur de ce champs doit etre comprise entre 20 and 500 charactères";
    }
    if(strlen($author) < 4 || strlen($author) > 255){
        $err_author = "La longueur de ce champs doit etre comprise entre 4 and 255 charactères";
    }
    if(strlen($publisher) < 4 || strlen($publisher) > 255){
        $err_publisher = "La longueur de ce champs doit etre comprise entre 4 and 255 charactères";
    }
    if(!strtotime($publication_date)){
        $err_publication_date = "Veuillez saisir une date correcte";
    }
    if(!is_numeric($isbn)){
        $err_isbn= "Veuillez saisir une valeur isbn correcte";
    }
    if(!is_numeric($pages)){
        $err_pages= "Veuillez saisir une valeur correcte";
    }
    if(!is_numeric($loan_duration)){
        $err_loan_duration = "Veuillez saisir une valeur correcte";
    }

    if(!isset($err_title,$err_publication_date,$err_loan_duration,$err_isbn,$err_pages,$err_author,$err_overview,$err_publisher)){
        //check if book is not already saved in the database
        $stmt = $con->prepare('SELECT * FROM `books` WHERE title = :title');
        $stmt->bindParam(':title',$title);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $err_create_book = "Ce livre existe déja";
        }
        if(!isset($err_create_book)){
            //validate the uploaded image
            $file_name = $book_cover['name'];
            $allowed_ext = ['png','jpg','jpeg','gif'];
            $file_ext = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
            $file_size = $book_cover['size'];
            $file_error = $book_cover['error'];
            $real_name = "../../data/books/".$title.".".$file_ext;
            $cover_link = "data/books/".$title.".".$file_ext;
            $tmp_name = $book_cover['tmp_name'];

            if(!in_array($file_ext,$allowed_ext)){
                $err_thumbnail = " Saisir une image valide : png , jpg, jpeg or gif";
            }else if ($file_size > 2**22){
                $err_thumbnail = "La taille maixmale d'une image est 2MB";
            }else if($file_error !== 0){
                $err_thumbnail = "Erreur lors de chargement du fichier";
            }else if(!move_uploaded_file($tmp_name,$real_name)){
                $err_thumbnail = "Ops, nous n'avons pas pu finaliser ce processus";
            }
            if(!isset($err_thumbnail)){
                $stmt = $con->prepare('INSERT INTO `books` (title,overview,author,thumbnail,loan_duration,publication_date,pages,publisher,isbn) VALUES (:title,:overview,:author,:thumbnail,:loan_duration,:publication_date,:pages,:publisher,:isbn)');
                $stmt->bindParam(":title",$title);
                $stmt->bindParam(":overview",$overview);
                $stmt->bindParam(":author",$author);
                $stmt->bindParam(":thumbnail",$cover_link);
                $stmt->bindParam(":loan_duration",$loan_duration);
                $stmt->bindParam(":publication_date",$publication_date);
                $stmt->bindParam(":pages",$pages);
                $stmt->bindParam(":publisher",$publisher);
                $stmt->bindParam(":isbn",$isbn);
                $stmt->execute();
                if($stmt->rowCount() === 0){
                    $err_create_book = "Erreur, nous n'avons pas pu ajouter ce livre";
                }else {
                    header("Location: index.php");
                    exit();
                }
            }
        }
    }

}
?>

<div class="flex container form-auth admin-container">
  <div class="form-container">
    <?php if(isset($err_create_book)): ?>
    <div class="form-error"><?php echo $err_create_book; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">

      <label for="title" class="label">Titre :</label>
      <div class="form-group">
        <input type="text" required class="input" name="title" id="title"
          placeholder="La longueur de ce champs doit etre comprise entre 4 and 255 charactères"
          value="<?php echo $_POST['title'] ?? ''?>">
        <i class="fa fa-book"></i>
      </div>
      <?php if(isset($err_title)): ?>
      <div class="form-error"><?php echo $err_title; ?></div>
      <?php endif; ?>

      <label for="overview" class="label">Resumé :</label>
      <div class="form-group">
        <textarea class="input text-input" required name="overview"
          id="overview"><?php echo $_POST['overview'] ?? ''?></textarea>
        <i class="fa fa-paragraph"></i>
      </div>
      <?php if(isset($err_overview)): ?>
      <div class="form-error"><?php echo $err_overview; ?></div>
      <?php endif; ?>

      <label for="thumbnail" class="label">Image de couverture :</label>
      <div class="form-group">
        <input type="file" required class="input" name="thumbnail" id="thumbnail">
        <i class="fa fa-image"></i>
      </div>
      <?php if(isset($err_thumbnail)): ?>
      <div class="form-error"><?php echo $err_thumbnail; ?></div>
      <?php endif; ?>

      <label for="author" class="label">Auteur :</label>
      <div class="form-group">
        <input type="text" required class="input" name="author" id="author"
          placeholder="La longueur de ce champs doit etre comprise entre 4 and 255 charactères"
          value="<?php echo $_POST['author'] ?? ''?>">
        <i class="fa fa-user"></i>
      </div>
      <?php if(isset($err_author)): ?>
      <div class="form-error"><?php echo $err_author; ?></div>
      <?php endif; ?>

      <label for="publisher" class="label">Edition :</label>
      <div class="form-group">
        <input type="text" required class="input" name="publisher" id="publisher"
          placeholder="La longueur de ce champs doit etre comprise entre 4 and 255 charactères"
          value="<?php echo $_POST['publisher'] ?? ''?>">
        <i class="fa fa-newspaper"></i>
      </div>
      <?php if(isset($err_publisher)): ?>
      <div class="form-error"><?php echo $err_publisher; ?></div>
      <?php endif; ?>

      <label for="publication_date" class="label">Date De Publication :</label>
      <div class="form-group">
        <input type="date" required class="input" name="publication_date" id="publication_date"
          value="<?php echo $_POST['publication_date'] ?? ''?>">
        <i class="fa fa-calendar"></i>
      </div>
      <?php if(isset($err_publication_date)): ?>
      <div class="form-error"><?php echo $err_publication_date; ?></div>
      <?php endif; ?>

      <label for="isbn" class="label">Isbn :</label>
      <div class="form-group">
        <input type="number" required class="input" name="isbn" id="isbn" value="<?php echo $_POST['isbn'] ?? ''?>">
        <i class="fa fa-id-card"></i>
      </div>
      <?php if(isset($err_isbn)): ?>
      <div class="form-error"><?php echo $err_isbn; ?></div>
      <?php endif; ?>

      <label for="loan_duration" class="label">Dureé d'emprunt :</label>
      <div class="form-group">
        <input type="number" required class="input" name="loan_duration" id="loan_duration"
          value="<?php echo $_POST['loan_duration'] ?? ''?>">
        <i class="fa fa-id-card"></i>
      </div>
      <?php if(isset($err_loan_duration)): ?>
      <div class="form-error"><?php echo $err_loan_duration; ?></div>
      <?php endif; ?>
      <label for="pages" class="label">Pages :</label>
      <div class="form-group">
        <input type="number" requiredclass="input" name="pages" id="pages" value="<?php echo $_POST['pages'] ?? ''?>">
        <i class="fa fa-id-card"></i>
      </div>
      <?php if(isset($err_pages)): ?>
      <div class="form-error"><?php echo $err_pages; ?></div>
      <?php endif; ?>

      <input type="submit" value="Ajouter" class="cursor-pointer submit-input ">
    </form>
  </div>
  <div class="img-container">
    <img src="../../layouts/images/book.png" alt="book">
  </div>
</div>

<?php
require_once "../../includes/templates/footer.php";
?>