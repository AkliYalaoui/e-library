<?php
session_start();
if(!isset($_SESSION['logged']) || !$_SESSION['is_admin'] == 0){
    header('Location: ../../login.php');
    exit();
}

$title = "Books Management";
$css = "../../layouts/css";
$js = "../../layouts/js";
$navLinks = [
    "home" => "../../index.php",
    "loan" => "../../onloan.php",
    "book" => "../../books.php",
    "admin_book" => "index.php",
    "admin_user" => "../users/index.php",
    "profile" => "../../profile.php",
    "logout" => "../../logout.php"
];
require_once "../../includes/templates/header.php";
require_once "../../includes/env/db.php";
require_once "../../includes/templates/nav.php";

$id = isset($_GET['id']) && is_numeric($_GET['id'])  ? intval($_GET['id']):0;
$sql = "SELECT * FROM `books` WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_OBJ);
if(!$book){
  header('Location: index.php');
  exit();
}

if( $_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['title'],$_POST['author'],$_POST['isbn'],$_POST['publisher'],$_POST['pages'],$_POST['publication_date'],$_POST['loan_duration'],$_POST['overview'],$_FILES['thumbnail'])){

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
        $err_title = "Title's length should be between 4 and 255 characters";
    }
    if(strlen($overview) < 4 || strlen($overview) > 500){
        $err_overview = "Overview's length should be between 4 and 500 characters";
    }
    if(strlen($author) < 4 || strlen($author) > 255){
        $err_author = "Author's length should be between 4 and 255 characters";
    }
    if(strlen($publisher) < 4 || strlen($publisher) > 255){
        $err_publisher = "Publisher's length should be between 4 and 255 characters";
    }
    if(!strtotime($publication_date)){
        $err_publication_date = "Please provide a valid Date";
    }
    if(!is_numeric($isbn)){
        $err_isbn= "Please provide a valid isbn";
    }
    if(!is_numeric($pages)){
        $err_pages= "Please provide a valid value";
    }
    if(!is_numeric($loan_duration)){
        $err_loan_duration = "Please provide a valid value";
    }

    if(!isset($err_title,$err_publication_date,$err_loan_duration,$err_isbn,$err_pages,$err_author,$err_overview,$err_publisher)){
        //check if book is not already saved in the database
        $stmt = $con->prepare('SELECT * FROM `books` WHERE title = :title AND id !=:id');
        $stmt->bindParam(':title',$title);
        $stmt->bindParam(':id',$id);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            $err_create_book = "Book already exists";
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
                  $err_thumbnail = "Provide a valid book cover image : png , jpg, jpeg or gif";
              }else if ($file_size > 2**22){
                  $err_thumbnail = "File size exceed maximum size which is 2MB";
              }else if($file_error !== 0){
                  $err_thumbnail = "File upload failed";
              }else if(!move_uploaded_file($tmp_name,$real_name)){
                  $err_thumbnail = "Ops, we couldn't finalize the process of uploading the file";
              }
            }else{
              rename($path,$real_name);
            }
            if(!isset($err_thumbnail)){
                $stmt = $con->prepare('UPDATE `books` SET title=:title,overview=:overview,author=:author,thumbnail=:thumbnail,loan_duration=:loan_duration,publication_date=:publication_date,pages=:pages,publisher=:publisher,isbn=:isbn WHERE id=:id');
                $stmt->bindParam(":title",$title);
                $stmt->bindParam(":overview",$overview);
                $stmt->bindParam(":author",$author);
                $stmt->bindParam(":thumbnail",$cover_link);
                $stmt->bindParam(":loan_duration",$loan_duration);
                $stmt->bindParam(":publication_date",$publication_date);
                $stmt->bindParam(":pages",$pages);
                $stmt->bindParam(":publisher",$publisher);
                $stmt->bindParam(":isbn",$isbn);
                $stmt->bindParam(":id",$id);
                if(!$stmt->execute()){
                    $err_create_book = "Error, we couldn't create the book";
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

      <label for="title" class="label">Title :</label>
      <div class="form-group">
        <input type="text" class="input" name="title" id="title" placeholder="title's length between 4 and 255 char"
          value="<?php echo $_POST['title'] ?? $book->title?>">
        <i class="fa fa-book"></i>
      </div>
      <?php if(isset($err_title)): ?>
      <div class="form-error"><?php echo $err_title; ?></div>
      <?php endif; ?>

      <label for="overview" class="label">Overview :</label>
      <div class="form-group">
        <textarea class="input text-input" name="overview"
          id="overview"><?php echo $_POST['overview'] ?? $book->overview?></textarea>
        <i class="fa fa-paragraph"></i>
      </div>
      <?php if(isset($err_overview)): ?>
      <div class="form-error"><?php echo $err_overview; ?></div>
      <?php endif; ?>

      <label for="thumbnail" class="label">Thumbnail :</label>
      <div class="form-group">
        <input type="file" class="input" name="thumbnail" id="thumbnail">
        <i class="fa fa-image"></i>
      </div>
      <?php if(isset($err_thumbnail)): ?>
      <div class="form-error"><?php echo $err_thumbnail; ?></div>
      <?php endif; ?>

      <label for="author" class="label">Author :</label>
      <div class="form-group">
        <input type="text" class="input" name="author" id="author" placeholder="author's length between 4 and 255 char"
          value="<?php echo $_POST['author'] ?? $book->author?>">
        <i class="fa fa-user"></i>
      </div>
      <?php if(isset($err_author)): ?>
      <div class="form-error"><?php echo $err_author; ?></div>
      <?php endif; ?>

      <label for="publisher" class="label">Publisher :</label>
      <div class="form-group">
        <input type="text" class="input" name="publisher" id="publisher"
          placeholder="publisher's length between 4 and 255 char"
          value="<?php echo $_POST['publisher'] ?? $book->publisher?>">
        <i class="fa fa-newspaper"></i>
      </div>
      <?php if(isset($err_publisher)): ?>
      <div class="form-error"><?php echo $err_publisher; ?></div>
      <?php endif; ?>

      <label for="publication_date" class="label">Publication Date :</label>
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

      <label for="loan_duration" class="label">Loan Duration :</label>
      <div class="form-group">
        <input type="number" class="input" name="loan_duration" id="loan_duration"
          value="<?php echo $_POST['loan_duration'] ?? $book->loan_duration?>">
        <i class="fa fa-id-card"></i>
      </div>
      <?php if(isset($err_loan_duration)): ?>
      <div class="form-error"><?php echo $err_loan_duration; ?></div>
      <?php endif; ?>
      <label for="pages" class="label">Pages :</label>
      <div class="form-group">
        <input type="number" class="input" name="pages" id="pages" value="<?php echo $_POST['pages'] ?? $book->pages?>">
        <i class="fa fa-id-card"></i>
      </div>
      <?php if(isset($err_pages)): ?>
      <div class="form-error"><?php echo $err_pages; ?></div>
      <?php endif; ?>

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