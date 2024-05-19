<?php 
    header('Content-type: text/html; charset=utf-8');
    header('Content-Type: application/json');
    // $obj = new getDataUserController();

    // $data = array();
    // $data = $obj->list();
    // Kết nối với cơ sở dữ liệu
    $db = new mysqli('localhost', 'root', 'phuoc@2209', 'shopgiay');
    
    $hot = isset($_GET['hot']) ? $_GET['hot'] : '';
    
    // Xử lý yêu cầu tìm kiếm theo san pham
    $MaSP = isset($_GET['MaSP']) ? $_GET['MaSP'] : '';

    // Tạo truy vấn SQL
    $sql = "SELECT * FROM danhmuc";

    if ($hot == '1') {
        $sql .= " where hot = '$hot'";
    }
    // Thực thi truy vấn SQL
    $result = $db->query($sql);

    // Mã hóa dữ liệu sản phẩm sang JSON
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($products);

?>