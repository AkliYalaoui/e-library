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
require_once "../../includes/templates/nav.php";
require_once "../../includes/env/db.php";

$stmt = $con->prepare('SELECT * FROM `users` WHERE name != :name ORDER BY created_at DESC');
$stmt->bindParam(':name',$_SESSION['name']);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<div class="t-container">
    <a href="create.php">New user<i class="fa fa-plus"></i></a>
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>NAME</th>
                <th>EMAIL</th>
                <th>IS_ADMIN</th>
                <th>IS_ACTIVE</th>
                <th>CREATED_AT</th>
                <th>ACTIONS</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($users) === 0): ?>
                <tr>
                    <td>/</td>
                    <td>/</td>
                    <td>/</td>
                    <td>/</td>
                    <td>/</td>
                    <td>/</td>
                    <td>/</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user->id ?></td>
                    <td><?php echo $user->name ?></td>
                    <td><?php echo $user->email ?></td>
                    <td><?php echo $user->is_admin == 0 ? '<i class="fa fa-check" style="color: #009688" ></i>':'<i class="fa fa-times" style="color:#e91e63 "></i>' ?></td>
                    <td><?php echo $user->is_active == 0 ? '<i class="fa fa-check" style="color: #009688"></i>': '<i class="fa fa-times" style="color:#e91e63 "></i>' ?></td>
                    <td><?php echo $user->created_at ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $user->id?>" class="edit">edit</a>
                        <form action="delete.php" method="post">
                            <input type="hidden" value="<?php echo $user->id?>" name="id">
                            <input type="submit" value="delete" class="danger" name="delete">
                        </form>
                        <?php if($user->is_active == 1): ?>
                            <form action="approuve.php" method="post">
                                <input type="hidden" value="<?php echo $user->id?>" name="id">
                                <input type="submit" value="approve" class="approve" name="approve">
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
<?php
require_once "../../includes/templates/footer.php";
?>
