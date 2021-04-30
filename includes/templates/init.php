<?php

session_start();
if (!isset($_SESSION['logged']) || !$_SESSION['is_admin'] == 0) {
header('Location: ../../login.php');
exit();
}


require_once "../../includes/env/db.php";
require_once "../../includes/functions/fn.php";
check_user_state();