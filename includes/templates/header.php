<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo  isset($css) ? $css.'/app.css' : 'layouts/css/app.css'?>">
    <link rel="stylesheet" href="<?php echo  isset($css) ? $css.'/all.min.css' : 'layouts/css/all.min.css'?>">
    <title><?php echo $title ?? "Bibliotheque" ?></title>
    <script src="<?php echo isset($js) ? $js.'/app.js': 'layouts/js/app.js'?>" defer></script>
</head>
<body class="<?php echo ($loginBody ?? '').' '. ($homeBody ?? '').' '.($modalContainer ?? '')?>">
