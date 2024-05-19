<?php
require_once("../models/ProductModel.php");
require_once("../core/DB.php");

$db = DB::getInstance();
$model = new ProductModel();

header('Content-Type: application/json');

$action = $_GET["action"];
switch ($action) { 
  case "spotlight": {
    $data = $model->getSpotlightProducts($_GET["uid"]); 
    break;
  }
  case "catalogProducts": {
    $data = $model->getCatalogProducts($_GET["uid"], $_GET["cateId"]); 
    break;
  }
  case "detail": {
    if(!empty($_GET["id"]))
      $data = $model->getDetail($_GET["id"]);
    else 
      $data = null;
    break;
  }
  case "search": {
    $data = $model->getSearchProducts($_GET["uid"], $_GET["searchKey"]); 
    break;
  }
}
echo json_encode($data);

// echo "<pre>";
// print_r($data);
// echo "</pre>";