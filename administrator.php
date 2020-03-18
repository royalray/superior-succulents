<?php
session_start();
include_once 'vendor/autoload.php';
use Neoan3\Apps\Template;
define('path',__DIR__);

if(isset($_POST['password']) && $_POST['password'] == '123456'){
    $_SESSION['logged_in'] = true;
}

if(!isset($_SESSION['logged_in']) && !$_SESSION['logged_in']){
    echo Template::embraceFromFile('templates/login.html',[]);
} else {
    // show admin panel
    echo 'logged in';
}
