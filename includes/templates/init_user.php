<?php

require_once "init.php";

if(!isset($deny)){
  $css = "../../layouts/css";
  $js = "../../layouts/js";
  set_admin_links("Gestion des utilisateurs","admin_user");
  require_once "../../includes/templates/header.php";
  require_once "../../includes/templates/nav.php";
}