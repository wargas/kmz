<?php 
header('content-type: application/json');

require 'vendor/autoload.php';



$file = $_FILES['file'];


$kmz = new \Kmz\Kmz($file['tmp_name']);


echo json_encode($kmz->toArray());