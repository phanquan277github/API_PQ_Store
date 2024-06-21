<?php
require("../core/Model.php");

class ProductModel extends Model
{
  private $id;

  public function getSearchProducts($uid, $searchKey)
  {
    $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
    $userId = $user["id"];

    $sql = "SELECT p.*, CASE WHEN f.product_id IS NOT NULL THEN true ELSE false END AS favorite
            FROM products p
            LEFT JOIN favorites f ON p.id = f.product_id AND f.user_id = $userId
            WHERE p.name LIKE '%$searchKey%'";
    $rs = $this->queryCustom($sql);
    $result = [];
    foreach ($rs as $item) {
      $temp = [];
      $temp = $item;
      $temp['favorite'] = $item['favorite'] ? true : false;
      $result[] = $temp;
    }
    return $result;
  }

  public function getSpotlightProducts($uid)
  {
    $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
    $userId = $user["id"];

    $sql = "SELECT p.*, CASE WHEN f.product_id IS NOT NULL THEN true ELSE false END AS favorite
            FROM products p
            LEFT JOIN favorites f ON p.id = f.product_id AND f.user_id = $userId";
    $rs = $this->queryCustom($sql);
    $result = [];
    foreach ($rs as $item) {
      $temp = [];
      $temp = $item;
      $temp['favorite'] = $item['favorite'] ? true : false;
      $result[] = $temp;
    }
    return $result;
  }

  public function getCatalogProducts($uid, $cateId, $orderBy = "id ASC")
  {
    $user = $this->getRow("SELECT id FROM users WHERE oauth_uid = '$uid'");
    $userId = $user["id"];

    $sql = "SELECT p.*, CASE WHEN f.product_id IS NOT NULL THEN true ELSE false END AS favorite
            FROM (products p LEFT JOIN products_category pc ON p.id = pc.product_id) 
            LEFT JOIN favorites f ON p.id = f.product_id AND f.user_id = $userId
            WHERE pc.category_id IN (SELECT id FROM categories WHERE id = $cateId or parent_id = $cateId)
            ORDER BY $orderBy";
    $rs = $this->queryCustom($sql);
    $result = [];
    foreach ($rs as $item) {
      $temp = [];
      $temp = $item;
      $temp['favorite'] = $item['favorite'] ? true : false;
      $result[] = $temp;
    }
    return $result;
  }
  

  public function getDetail($id) {
    $result = $this->query('SELECT * FROM products WHERE id = ' . $id);
    $result[0]['images'] = $this->query('SELECT * FROM images WHERE product_id = ' . $id);
    $result[0]['sizes'] = $this->query('SELECT * FROM sizes WHERE product_id = ' . $id);
    // $result[0]['descriptions'] = $this->query('SELECT * FROM descriptions WHERE product_id = ' . $id);
    return $result[0];
  }


}