<?php
include_once 'adminHelper.php';


// insert file

$file = $_FILES["image"]["tmp_name"];
$fileName = $_FILES['image']['name'];
$image_url = 'assets/'.$fileName;

move_uploaded_file($file,__DIR__ . '/assets/'.$fileName);

$database = new Db('succulents');
$database->create('succulent', [
    'name' => $_POST['name'],
    'description' => $_POST['description'],
    'image_url' => $image_url
]);
header('Location: administrator.php');