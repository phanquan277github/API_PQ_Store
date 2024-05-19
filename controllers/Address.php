<?php
class AddressModel {
  public $code;
  public $name;
  public $name_with_type;
  public $path_with_type;
  public $parent_code;

  public function __construct($data) {
      $this->code = $data['code'];
      $this->name = $data['name'];
      $this->name_with_type = $data['name_with_type'];
      $this->path_with_type = isset($data['path_with_type']) ? $data['path_with_type'] : "";
      $this->parent_code = isset($data['parent_code']) ? $data['parent_code'] : "";
  }
}
function convertToAddressModels($data) {
  $models = [];
  foreach ($data as $item) {
      $models[] = new AddressModel($item);
  }
  return $models;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $action = $_GET['action'];
  switch ($action) {
    case "tinh_tp": {
      $jsonFilePath = '../addressVN/tinh_tp.json';
      $jsonData = file_get_contents($jsonFilePath);
      $response = json_decode($jsonData, true);
      $response = array_values($response);
      $response = convertToAddressModels($response);
      break;
    }
    case "quan_huyen": {
      $id = $_GET['parentId'];
      $jsonFilePath = "../addressVN/quan-huyen/$id.json";
      $jsonData = file_get_contents($jsonFilePath);
      $response = json_decode($jsonData, true);
      $response = array_values($response);
      $response = convertToAddressModels($response);
      break;
    }
    case "xa_phuong": {
      $id = $_GET['parentId'];
      $jsonFilePath = "../addressVN/xa-phuong/$id.json";
      $jsonData = file_get_contents($jsonFilePath);
      $response = json_decode($jsonData, true);
      $response = array_values($response);
      $response = convertToAddressModels($response);
      break;
    }
  }
}
echo json_encode($response);