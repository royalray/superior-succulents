<?php

use Neoan3\Apps\Template;

error_reporting('E_ALL');
ini_set('display_errors', 1);

include_once 'adminHelper.php';

$database = new Db('succulents');
$succulents = $database->read('succulent');
// show admin panel
echo Template::embraceFromFile('templates/manage_succulents.html',['succulents'=>$succulents]);