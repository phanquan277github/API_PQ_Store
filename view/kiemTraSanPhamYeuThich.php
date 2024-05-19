<?php 
    require_once('../controller/getDataProductsController.php');
    header('Content-type: text/html; charset=utf-8');
    header('Content-Type: application/json');
    // $obj = new getDataUserController();

    // $data = array();
    // $data = $obj->list();
    // Kết nối với cơ sở dữ liệu
    $db = new mysqli('localhost', 'root', 'phuoc@2209', 'shopgiay');

    // Xử lý yêu cầu tìm kiếm
    $MaKH = isset($_GET['MaKH']) ? $_GET['MaKH'] : '';
    $MaSP = isset($_GET['MaSP']) ? $_GET['MaSP'] : '';

   // Tạo truy vấn SQL kiểm tra xem sản phẩm này có phải là sp yêu thsich của khách hàng này hay không
   
   $sql = "SELECT * FROM (chitietsanphamyeuthich join sanphamyeuthich on chitietsanphamyeuthich.MaYT = sanphamyeuthich.MaYT) join sanpham on sanpham.MaSP = chitietsanphamyeuthich.MaSP WHERE sanphamyeuthich.MaKH = '$MaKH' and sanpham.MaSP = '$MaSP'";

    // Thực thi truy vấn SQL
    $result = $db->query($sql);

    $data = array();
    if ($result->num_rows > 0) {
        $data = array(
            'tinhTrang' =>    1,
        );
    }else{
        $data = array(
            'tinhTrang' =>    0,
        ); 
    }
    
    header('Content-Type: application/json');
    echo json_encode($data);

?>