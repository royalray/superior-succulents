<?php
/*
include_once "vendor/autoload.php";
define('path',__DIR__);
$array=['title'=>'rayspage','numbers'=>[1,2,3]];
echo \Neoan3\Apps\Template::embraceFromFile('templates/main.html',$array);
exit();
*/
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Superior Succulents</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="style.css">


</head>

<body>
<?php
require 'database_connection.php';
$db=new Db('succulent');
$succulents=$db->read('succulents');
$currentSucculent = false;
if(isset($_GET['id'])){
    // find succulent
    foreach($succulents as $succulent){
        if($succulent['id'] == $_GET['id']){
            $currentSucculent = $succulent;
        }
    }
}
if(!$currentSucculent){
    $currentSucculent = $succulents[0];
}
include "header.php";
?>
<div class="container">
    <div class="row">
        <div class="col m8 s12">
            <h1><?= $currentSucculent['name']; ?></h1>
            <!--
                        <div class="product_image"></div>
            -->
            <div class="card" style="max-width: 80%">
                <div class="card-image waves-effect waves-block waves-light">
                    <img alt="some cactus" class="activator" src="<?= $currentSucculent['image_url']; ?>">
                </div>
                <div class="card-content">
                    <p><?= $currentSucculent['description']; ?></p>
                </div>

            </div>
        </div>
        <div class="col m4 s12">
            <div class="sidebar_right">

            </div>
            <?php
            foreach ($succulents as $succulent){
                ?>
                <div class="card">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img alt="<?= $succulent['name']; ?>-image" class="activator" src="<?= $succulent['image_url']; ?>">;
                    </div>
                    <div class="card-content">
                    <span class="card-title activator grey-text text-darken-4"><?= $succulent['name']; ?><i
                            class="material-icons right">more_vert</i></span>
                        <p><a href="index.php?id=<?= $succulent['id']; ?>">Open</a></p>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4"><?= $succulent['name']; ?><i class="material-icons right">close</i></span>
                        <p><?= $succulent['description']; ?></p>
                    </div>
                </div>
            <?php

            }
            ?>


        </div>

    </div>
</div>
<?php

?>
<footer class="page-footer teal">
    <div class="container">
        <div class="row">
            <div class="col l6 s12">
                <h5 class="white-text">Footer Content</h5>
                <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer
                    content.</p>
            </div>
            <div class="col l4 offset-l2 s12">
                <h5 class="white-text">Links</h5>
                <ul>
                    <li><a class="grey-text text-lighten-3" href="#!">Link 1</a></li>
                    <li><a class="grey-text text-lighten-3" href="#!">Link 2</a></li>
                    <li><a class="grey-text text-lighten-3" href="#!">Link 3</a></li>
                    <li><a class="grey-text text-lighten-3" href="#!">Link 4</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-copyright">
        <div class="container">
            © 2014 Copyright Text
            <a class="grey-text text-lighten-4 right" href="#!">More Links</a>
        </div>
    </div>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>

