<?php
require("../core/Model.php");
class HomeModel extends Model
{
  private $id;

  public static function getSuggestCategories($parent_id = 1)
  {
    $__conn = DB::getInstance();
    $categories = [];
    $sql = "select * from categories where parent_id = ?";
    $stmt = $__conn->prepare($sql);
    $stmt->execute([$parent_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // foreach ($result as $item) {
    //   $category = [];
    //   $category['id'] = $item['id'];
    //   $category['name'] = $item['name'];
    //   $category['parent_id'] = $item['parent_id'];
    //   $category['image_path'] = $item['image_path'];
    //   $category['sub-cate'] = static::getSuggestCategories($category['id']);
    //   $categories[] = $category;
    // }
    return $result;
  }

  public function getSlideshows() {
    $sql = "SELECT * FROM slideshows WHERE display = true";
    $result = $this->query($sql);
    return $result;
  }

  
}