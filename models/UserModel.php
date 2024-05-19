<?php
require("../core/Model.php");
class UserModel extends Model
{
  public function login($data) {
    if (!empty($data)) {
      $checkQuery = "SELECT * FROM users WHERE oauth_uid = ?";
      $checkResult = $this->getRow($checkQuery, [$data['oauth_uid']]);
      if ($checkResult) {
        // Update user data in the database 
        $whereConditions = "oauth_uid = '" . $data['oauth_uid'] . "'";
        $result = $this->updateData('users', $data, $whereConditions);
      } else {
        // insert người dùng nếu chưa có trong database
        $result = $this->insertData("users", $data);
      }
      $userData = $this->getRow($checkQuery, [$data['oauth_uid']]);
      return !empty($userData) ? $userData : false;
    }
    return false;
  }

  public function favorite($uid, $productId) {
    $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
    $userId = $user["id"];

    $checkQuery = "SELECT id FROM favorites WHERE user_id = $userId AND product_id = $productId";
    $checkResult = $this->getRow($checkQuery, []);
    
    if ($checkResult != null) {
      return $this->deleteData('favorites', "id = " . $checkResult['id']);
    } else {
      return $this->insertData("favorites", ['user_id' => $userId, 'product_id' => $productId]);
    }
  }

  public function addToCart($uid, $productId, $size)
  {
    if (!empty($uid) && !empty($productId)) {
      $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
      $userId = $user["id"];
      $checkQuery = "SELECT c.id as cart_id, u.id as user_id from carts c inner join users u on c.user_id = u.id where u.oauth_uid = ?";
      $checkResult = $this->getRow($checkQuery, [$uid]);
      if ($checkResult) {
        // người dùng đã có giỏ hàng => kiểm tra nếu giỏ đã có sản phẩm thì tăng số lượng
        $sql = "SELECT quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id;";
        $quantity = $this->getRow($sql, [
          ':cart_id' => $checkResult['cart_id'],
          ':product_id' => $productId
        ]);
        if ($quantity) {
          $condition = "cart_id = " . $checkResult['id'] . " AND product_id = $productId";
          $dataUpdate = [
            'quantity' => $quantity['quantity'] + 1,
            'size' => $size
          ];
          $this->updateData('cart_items', $dataUpdate, $condition);
        } else {
          return $this->insertData('cart_items', [
            'cart_id' => $checkResult['cart_id'],
            'product_id' => $productId,
            'quantity' => 1,
            'size' => $size,
          ]);
        }
      } else {
        // người dùng chưa có giỏ hàng => tạo giỏ hàng mới và insert sản phẩm vào cart_items
        $this->insertData('carts', ['user_id' => $userId]);
        $dataInsert = [
          'cart_id' => $this->lastInsertId(),
          'product_id' => $productId,
          'size' => $size,
          'quantity' => 1,
        ];
        return $this->insertData('cart_items', $dataInsert);
      }
      return false;
    }
  }

  public function getCartItems($uid) {
    $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
    $userId = $user["id"];

    $sql = "SELECT p.name, p.price, p.thumbnail_path, ci.id as id, ci.quantity, ci.size, p.id as product_id FROM products p
      LEFT JOIN (carts c RIGHT JOIN cart_items ci ON c.id = ci.cart_id)
      ON p.id = ci.product_id WHERE c.user_id = $userId ;";
    return $this->queryCustom($sql);
  }

  public function getFavorites($uid) {
    $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
    $userId = $user["id"];

    $sql = "SELECT p.*, CASE WHEN f.user_id IS NOT NULL THEN TRUE ELSE FALSE END AS favorite 
            FROM products p INNER JOIN favorites f ON p.id = f.product_id
            WHERE user_id = $userId";
    $rs = $this->queryCustom($sql);
    $data = [];
    foreach ($rs as $item) {
      $temp = [];
      $temp = $item;
      $temp['favorite'] = $item['favorite'] ? true : false;
      $data[] = $temp;
    }
    return $data;
  }

  public function createAddress($uid, $data) {
    if (!empty($data)) {
      $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
      $userId = $user["id"];
      if ($userId) {
        $data["user_id"] = $userId;
        $result = $this->insertData("addresses", $data);
      }
      return $result;
    }
  }
  public function updateAddress($uid, $addressId, $data) {
    if (!empty($data)) {
      $condition = "id = $addressId";
      return $this->updateData("addresses", $data, $condition);
    }
  }
  public function deleteAddress($addressId) {
    if (!empty($addressId)) {
      $conditions = "id = $addressId";
      return $this->deleteData("addresses", $conditions);
    }
    return false;
  }
  public function getAddress($uid) {
    $sql = "SELECT * FROM addresses
            WHERE user_id = (SELECT id FROM users WHERE oauth_uid = '$uid')";
    return $this->queryCustom($sql);;
  }

  public function createOrder($uid, $addressId, $orderData) {
    if (!empty($uid)) {
      $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
      $userId = $user["id"];
      $address = $this->getRow("SELECT * FROM addresses WHERE id = $addressId");
      $cartItems = $this->queryCustom("SELECT product_id, quantity, size FROM cart_items
                                       WHERE cart_id = (SELECT id FROM carts WHERE user_id = $userId)");

      if ($userId) {
        $orderData["user_id"] = $userId;
        $orderData["full_name"] = $address['full_name'];
        $orderData["phone_number"] = $address['phone_number'];
        $orderData["address"] = $address['street_address'] . " " . $address['address'];
        $totalSql = "SELECT SUM(ci.quantity * p.price) AS total_cost
                     FROM cart_items ci JOIN products p ON ci.product_id = p.id
                     WHERE ci.cart_id = (SELECT id FROM carts WHERE user_id = $userId);";
        $total = $this->getRow($totalSql);
        $orderData["total"] = $total['total_cost'];
        $this->insertData("orders", $orderData);

        $orderId = $this->lastInsertId();
        foreach ($cartItems as $item) {
          $item['order_id'] = $orderId;
          $this->insertData("order_items", $item);
        }
        return true;
      }
      return false;
    }
    return false;
  }

  public function deleteCart($uid) {
    if (!empty($uid)) {
      $conditions = "user_id = (SELECT id FROM users WHERE oauth_uid = '$uid')";
      return $this->deleteData("carts", $conditions);
    }
    return false;
  }
  public function updateCartItemQuantity($id, $quantity) {
    if (!empty($id)) {
      $conditions = "id = $id";
      return $this->updateData("cart_items", ['quantity' => $quantity] ,$conditions);
    }
    return false;
  }

  public function deleteCartItemQuantity($id) {
    if (!empty($id)) {
      $conditions = "id = $id";
      return $this->deleteData("cart_items", $conditions);
    }
    return false;
  }


  public function getOrder($uid, $orderStatus) {
    if (!empty($uid)) {
      $result = $this->queryCustom("SELECT * FROM orders WHERE user_id = (SELECT id FROM users WHERE oauth_uid = '$uid') 
                               AND order_status = '$orderStatus'");

      foreach ($result as $index => $order) { 
        $sql = "SELECT p.name, p.price, p.thumbnail_path, ci.id as id, ci.quantity, ci.size FROM products p
        LEFT JOIN order_items ci ON p.id = ci.product_id WHERE ci.order_id = " . $order['id'];
        $result[$index]['order_items'] = $this->queryCustom($sql);
      }
      return $result;
    } 
    return false;
  }

}