<?php

$dsn = "mysql:host=localhost;dbname=bibliotheque";
$user = "root";
$password = "";

try {
    $con = new PDO($dsn,$user,$password);
}catch (Exception $e){
    echo "Error".$e->getMessage();
}