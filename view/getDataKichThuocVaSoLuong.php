<?php 
    header('Content-type: text/html; charset=utf-8');
    header('Content-Type: application/json');
    // $obj = new getDataUserController();

    // $data = array();
    // $data = $obj->list();
    // Kết nối với cơ sở dữ liệu
    $db = new mysqli('localhost', 'root', 'phuoc@2209', 'shopgiay');

    // Xử lý yêu cầu tìm kiếm theo san pham
    $MaSP = isset($_GET['MaSP']) ? $_GET['MaSP'] : '';

   // Tạo truy vấn SQL
   $sql = "SELECT size.so_size, size_sanpham.SoLuong FROM size_sanpham join size on size_sanpham.MaSize = size.MaSize where size_sanpham.MaSP = '$MaSP'";

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