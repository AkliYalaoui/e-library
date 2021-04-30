<?php
require_once "../../includes/templates/init_book.php";

$id = isset($_GET['id']) && is_numeric($_GET['id'])  ? intval($_GET['id']):0;
$sql = "SELECT * FROM `books` WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_OBJ);
if(!$book){
  header('Location: ../../404.php');
  exit();
}

if( $_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['title'],$_POST['author'],$_POST['isbn'],$_POST['publisher'],$_POST['pages'],$_POST['publication_date'],$_POST['overview'],$_FILES['thumbnail'])){

    $title = trim(filter_var($_POST['title'],FILTER_SANITIZE_STRING));
    $title = str_replace("\\","",str_replace("/","",$title));
    $author = trim(filter_var($_POST['author'],FILTER_SANITIZE_STRING));
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

    if(!isset($err_title,$err_publication_date,$err_isbn,$err_pages,$err_author,$err_overview,$err_publisher)){
        //check if book is not already saved in the database
        $stmt = $con->prepare('SELECT * FROM `books` WHERE title = :title AND id !=:id');
        $stmt->bindParam(':title',$title);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $err_create_book = "Ce livre existe déja";
        }
        if(!isset($err_create_book)){
            //validate the uploaded image
            $file_name = $book_cover['name'];
            $allowed_ext = ['png','jpg','jpeg','gif'];
            if($file_ext != ""){
              $file_ext = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
            }else{
              $file_ext = strtolower(pathinfo($book->thumbnail,PATHINFO_EXTENSION));
            }
            $file_size = $book_cover['size'];
            $file_error = $book_cover['error'];
            $real_name = "../../data/books/".$title.".".$file_ext;
            $cover_link = "data/books/".$title.".".$file_ext;
            $tmp_name = $book_cover['tmp_name'];
            $path = "../../".$book->thumbnail;
            if($file_name != ""){
              if(file_exists($path) ){
                unlink($path);
              } 
              if(!in_array($file_ext,$allowed_ext)){
                $err_thumbnail = " Saisir une image valide : png , jpg, jpeg or gif";
              }else if ($file_size > 2**22){
                $err_thumbnail = "La taille maixmale d'une image est 2MB";
              }else if($file_error !== 0){
                $err_thumbnail = "Erreur lors de chargement du fichier";
              }else if(!move_uploaded_file($tmp_name,$real_name)){
                $err_thumbnail = "Ops, nous n'avons pas pu finaliser ce processus";
              }
            }else{
              rename($path,$real_name);
            }
            if(!isset($err_thumbnail)){
                $stmt = $con->prepare('UPDATE `books` SET title=:title,overview=:overview,author=:author,thumbnail=:thumbnail,publication_date=:publication_date,pages=:pages,publisher=:publisher,isbn=:isbn WHERE id=:id');
                $stmt->bindParam(":title",$title);
                $stmt->bindParam(":overview",$overview);
                $stmt->bindParam(":author",$author);
                $stmt->bindParam(":thumbnail",$cover_link);
                $stmt->bindParam(":publication_date",$publication_date);
                $stmt->bindParam(":pages",$pages);
                $stmt->bindParam(":publisher",$publisher);
                $stmt->bindParam(":isbn",$isbn);
                $stmt->bindParam(":id",$id);
                if(!$stmt->execute()){
                  $err_create_book = "Erreur, nous n'avons pas pu ajouter ce livre";
                }else {
                    header("Location: index.php?file=$path");
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
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?id=".$book->id ?>" method="post"
      enctype="multipart/form-data">

      <label for="title" class="label">Titre :</label>
      <div class="form-group">
        <input type="text" class="input" name="title" id="title"
          placeholder="La longueur de ce champs doit etre comprise entre 4 and 255 charactères"
          value="<?php echo $_POST['title'] ?? $book->title?>">
        <i class="fa fa-book"></i>
      </div>
      <?php if(isset($err_title)): ?>
      <div class="form-error"><?php echo $err_title; ?></div>
      <?php endif; ?>

      <label for="overview" class="label">Resumé :</label>
      <div class="form-group">
        <textarea class="input text-input" name="overview"
          id="overview"><?php echo $_POST['overview'] ?? $book->overview?></textarea>
        <i class="fa fa-paragraph"></i>
      </div>
      <?php if(isset($err_overview)): ?>
      <div class="form-error"><?php echo $err_overview; ?></div>
      <?php endif; ?>

      <label for="thumbnail" class="label">Image de couverture :</label>
      <div class="form-group">
        <input type="file" class="input" name="thumbnail" id="thumbnail">
        <i class="fa fa-image"></i>
      </div>
      <?php if(isset($err_thumbnail)): ?>
      <div class="form-error"><?php echo $err_thumbnail; ?></div>
      <?php endif; ?>

      <label for="author" class="label">Auteur :</label>
      <div class="form-group">
        <input type="text" class="input" name="author" id="author"
          placeholder="La longueur de ce champs doit etre comprise entre 4 and 255 charactères"
          value="<?php echo $_POST['author'] ?? $book->author?>">
        <i class="fa fa-user"></i>
      </div>
      <?php if(isset($err_author)): ?>
      <div class="form-error"><?php echo $err_author; ?></div>
      <?php endif; ?>

      <label for="publisher" class="label">Edition :</label>
      <div class="form-group">
        <input type="text" class="input" name="publisher" id="publisher"
          placeholder="La longueur de ce champs doit etre comprise entre 4 and 255 charactères"
          value="<?php echo $_POST['publisher'] ?? $book->publisher?>">
        <i class="fa fa-newspaper"></i>
      </div>
      <?php if(isset($err_publisher)): ?>
      <div class="form-error"><?php echo $err_publisher; ?></div>
      <?php endif; ?>

      <label for="publication_date" class="label">Date de Publication :</label>
      <div class="form-group">
        <input type="date" class="input" name="publication_date" id="publication_date"
          value="<?php echo $_POST['publication_date'] ?? date("Y-m-d",strtotime($book->publication_date))?>">
        <i class="fa fa-calendar"></i>
      </div>
      <?php if(isset($err_publication_date)): ?>
      <div class="form-error"><?php echo $err_publication_date; ?></div>
      <?php endif; ?>

      <label for="isbn" class="label">Isbn :</label>
      <div class="form-group">
        <input type="number" class="input" name="isbn" id="isbn" value="<?php echo $_POST['isbn'] ?? $book->isbn?>">
        <i class="fa fa-id-card"></i>
      </div>
      <?php if(isset($err_isbn)): ?>
      <div class="form-error"><?php echo $err_isbn; ?></div>
      <?php endif; ?>

      <label for="pages" class="label">Pages :</label>
      <div class="form-group">
        <input type="number" class="input" name="pages" id="pages" value="<?php echo $_POST['pages'] ?? $book->pages?>">
        <i class="fa fa-id-card"></i>
      </div>
      <?php if(isset($err_pages)): ?>
      <div class="form-error"><?php echo $err_pages; ?></div>
      <?php endif; ?>

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