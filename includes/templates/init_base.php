<?php

session_start();
if (!isset($_SESSION['logged']) || $_SESSION['is_active'] == 1) {
  header('Location: login.php');
  exit();
}

require_once "includes/functions/fn.php";

if(!isset($deny)){
  set_user_links($title, $key);

  require_once "includes/templates/header.php";
  require_once "includes/env/db.php";
  require_once "includes/templates/nav.php";
}

check_user_state();