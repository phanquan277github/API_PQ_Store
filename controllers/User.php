<?php
require ("../models/UserModel.php");
require ("../core/DB.php");

$db = DB::getInstance();
$model = new UserModel();

$response = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'];
  switch ($action) {
    case "login": {
        // Lấy các trường dữ liệu từ đối tượng userData
      $data = [
        "oauth_uid" => $_POST['uid'],
        "name" => $_POST['name'],
        "email" => $_POST['email'],
        "avatar" => $_POST['avatar']
      ];
  
      $result = $model->login($data);
      if ($result) {
        $response["success"] = 1;
        $response["message"] = "Thêm thành công !";
      } else {
        $response["success"] = 0;
        $response["message"] = "Thêm không thành công !";
      }
      break;
    }
    case "favorite" : {
      $result = $model->favorite($_POST['uid'], $_POST['product_id']);

      if ($result) {
        $response["success"] = 1;
        $response["message"] = "Đã thêm vào danh sách yêu thích!";
      } else {
        $response["success"] = 0;
        $response["message"] = "Đã xóa khỏi danh sách yêu thích!";
      }
      break;
    }
    case "addToCart" : {
      $result = $model->addToCart($_POST['uid'], $_POST['product_id'], $_POST['size']);
      if ($result) {
        $response["success"] = 1;
        $response["message"] = "Đã thêm vào giỏ hàng!";
      } else {
        $response["success"] = 0;
        $response["message"] = "Thêm vào giỏ hàng thất bại!";
      }
      break;
    }
    case "cartItemQuantity" : {
      $cartItemId = $_POST['cartItemId'];
      $quantity = $_POST['quantity'];
      if ($quantity > 0) {
        $result = $model->updateCartItemQuantity($cartItemId, $quantity);
      } else {
        $result = $model->deleteCartItemQuantity($cartItemId);
      }
      if ($result) {
        $response["success"] = 1;
        $response["message"] = "Cập nhật thành công";
      } else {
        $response["success"] = 0;
        $response["message"] = "Cập nhật thất bại!";
      }
      break;
    }
    case "createAddress": {
      $uid = $_POST['uid'];
      if (!empty($uid)) {
        // Lấy các trường dữ liệu từ đối tượng userData
        $data = [
          "full_name" => $_POST['name'],
          "address" => $_POST['address'],
          "street_address" => $_POST['street'],
          "phone_number" => $_POST['phone']
        ];
        $result = $model->createAddress($uid, $data);
        if ($result) {
          $response["success"] = 1;
          $response["message"] = "Thêm thành công ! ";
        } else {
          $response["success"] = 0;
          $response["message"] = "Thêm không thành công !";
        }
      } else {
        $response["success"] = 0;
        $response["message"] = "Bạn chưa nhập dữ liệu !";
      }
      break;
    }
    case "updateAddress": {
      $uid = $_POST['uid'];
      $addressId = $_POST['addressId'];
      if (!empty($uid)) {
        $data = [
          "full_name" => $_POST['name'],
          "address" => $_POST['address'],
          "street_address" => $_POST['street'],
          "phone_number" => $_POST['phone']
        ];
        $result = $model->updateAddress($uid, $addressId, $data);
        if ($result) {
          $response["success"] = 1;
          $response["message"] = "Thêm thành công ! ";
        } else {
          $response["success"] = 0;
          $response["message"] = "Thêm không thành công !";
        }
      } else {
        $response["success"] = 0;
        $response["message"] = "Bạn chưa nhập dữ liệu !";
      }
      break;
    }
    case "deleteAddress": {
      $id = $_POST['addressId'];
      if (!empty($id)) {
        $result = $model->deleteAddress($id);
        if ($result) {
          $response["success"] = 1;
          $response["message"] = "Thêm thành công ! ";
        } else {
          $response["success"] = 0;
          $response["message"] = "Thêm không thành công !";
        }
      } else {
        $response["success"] = 0;
        $response["message"] = "Bạn chưa nhập dữ liệu !";
      }
      break;
    }
    case "createOrder": {
      $uid = $_POST['uid'];
      if (!empty($uid)) {
        // Lấy các trường dữ liệu từ đối tượng userData
        $addressId = $_POST['addressId'];
        $data = [
          "payment_methods" => $_POST['paymentMethods'],
          "note" => $_POST['note']
        ];
        $result = $model->createOrder($uid, $addressId, $data);
        if ($result) {
          $response["success"] = 1;
          $response["message"] = "Thêm thành công ! ";
        } else {
          $response["success"] = 0;
          $response["message"] = "Thêm không thành công !";
        }
      } else {
        $response["success"] = 0;
        $response["message"] = "Bạn chưa nhập dữ liệu !";
      }
      break;
    }
    case "deleteCart": {
      $uid = $_POST['uid'];
      if (!empty($uid)) {
        // Lấy các trường dữ liệu từ đối tượng userData
        $result = $model->deleteCart($uid);
        if ($result) {
          $response["success"] = 1;
          $response["message"] = "Thêm thành công ! ";
        } else {
          $response["success"] = 0;
          $response["message"] = "Thêm không thành công !";
        }
      } else {
        $response["success"] = 0;
        $response["message"] = "Bạn chưa nhập dữ liệu !";
      }
      break;
    }
  }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $action = $_GET["action"];
  switch ($action) { 
    case "cart": {
      if(!empty($_GET["uid"]))
        $response = $model->getCartItems($_GET["uid"]);
      else 
        $response = null;
      break;
    }
    case "favorite": {
      if(!empty($_GET["uid"]))
        $response = $model->getFavorites($_GET["uid"]);
      else 
        $response = null;
      break;
    }
    case "address": {
      if(!empty($_GET["uid"]))
        $response = $model->getAddress($_GET["uid"]);
      else 
        $response = null;
      break;
    }
    case "order": {
      if(!empty($_GET["uid"]))
        $response = $model->getOrder($_GET["uid"], $_GET['order_status']);
      else 
        $response = null;
      break;
    }
  }
}
echo json_encode($response);
