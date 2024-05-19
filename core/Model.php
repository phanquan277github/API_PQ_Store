<?php
class Model
{
  protected $__conn;
  function __construct()
  {
    $this->__conn = DB::getInstance();
  }

  // lấy key của mảng data làm column và giá trị là value tương ứng
  function insertData($table, $data)
  {
    if (!empty($data)) {
      $fieldStr = '';
      $valueStr = '';
      foreach ($data as $key => $value) {
        $fieldStr .= $key . ',';
        $valueStr .= "'" . $value . "',";
      }
      $fieldStr = rtrim($fieldStr, ',');
      $valueStr = rtrim($valueStr, ',');

      $sql = "insert into $table($fieldStr) values ($valueStr) ";
      $status = $this->__conn->query($sql);
      return $status;
    }
    return false;
  }

  // lấy key của mảng data làm column và giá trị là value tương ứng
  function updateData($table, $data, $conditions = '')
  {
    if (!empty($data)) {
      $updateStr = '';
      foreach ($data as $key => $value) {
        $updateStr .= "$key='$value',";
      }
      $updateStr = rtrim($updateStr, ',');

      if (!empty($conditions)) {
        $sql = "update $table set $updateStr where $conditions";
      } else {
        $sql = "update $table set $updateStr";
      }
      $status = $this->__conn->query($sql);
      return $status;
    }
    return false;
  }

  function deleteData($table, $conditions = '')
  {
    if (!empty($conditions)) {
      $sql = "delete from $table where $conditions";
    } else {
      $sql = "delete from $table";
    }
    return $this->__conn->query($sql);
  }

  function queryCustom($sql, $fetch = PDO::FETCH_ASSOC)
  {
    try {
      $stmt = $this->__conn->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll($fetch);
    } catch (Exception $e) {
      $mess = $e->getMessage();
      echo "ERROR: $mess";
      die();
    }
  }


  function query($sql)
  {
    try {
      $stmt = $this->__conn->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die();
    }
  }

  function getList($sql)
  {
    try {
      $result = $this->__conn->prepare($sql);
      $result->execute();
      if ($result->rowCount() > 0) {
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        $result->closeCursor();
        return $row;
      }
      return false;
    } catch (Exception $e) {
      $mess = $e->getMessage();
      App::$app->loadError('database', ['message' => $mess]);
      die();
    }
  }
  function getListCondition($sql, $params = [])
  {
    try {
      $result = $this->__conn->prepare($sql);
      $result->execute($params);
      if ($result->rowCount() > 0) {
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        $result->closeCursor();
        return $row;
      }
      return false;
    } catch (Exception $e) {
      $mess = $e->getMessage();
      // echo "$mess";
      die();
    }
  }
  function getRow($sql, $params = [])
  {
    try {
      $result = $this->__conn->prepare($sql);
      $result->execute($params);
      if ($result->rowCount() > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $result->closeCursor();
        return $row;
      }
      return false;
    } catch (Exception $e) {
      $mess = $e->getMessage();
      die();
    }
  }

  function lastInsertId()
  {
    return $this->__conn->lastInsertId();
  }
}
