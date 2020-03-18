<?php
include_once 'adminHelper.php';

$database = new Db('succulents');

$database->delete('succulent', $_GET['id']);

header('Location: administrator.php');