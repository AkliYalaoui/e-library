<?php

require_once "init.php";

if(!isset($deny)){
  $css = "../../layouts/css";
  $js = "../../layouts/js";
  set_admin_books_links("Gestion dev livres","admin_book");
  require_once "../../includes/templates/header.php";
  require_once "../../includes/templates/nav.php";
}