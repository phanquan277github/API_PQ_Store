<?php
require("../models/HomeModel.php");
require("../core/DB.php");

$db = DB::getInstance();
$model = new HomeModel();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
  $action = $_GET["action"];
  switch ($action) { 
    case "suggestCategories": $data = $model->getSuggestCategories(); break;
    case "slideshows": $data = $model->getSlideshows(); break;
  }
  echo json_encode($data);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
  $uid = $_POST['uid'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $avatar = $_POST['avatar'];

  $data = [
    "uid" => $uid,
    "name" => $name,
    "email" => $email,
    "avatar" => $avatar
  ];
  
  echo json_encode($data);
  // echo "<pre>";
  // print_r($data);
  // echo "</pre>";
}