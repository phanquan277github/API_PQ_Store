<?php 
    header('Content-type: text/html; charset=utf-8');
    header('Content-Type: application/json');
    // $obj = new getDataUserController();

    // $data = array();
    // $data = $obj->list();
    // Kết nối với cơ sở dữ liệu
    $db = new mysqli('localhost', 'root', 'phuoc@2209', 'shopgiay');
    
    $MaKH = isset($_GET['MaKH']) ? $_GET['MaKH'] : '';
    $MaSP = isset($_GET['MaSP']) ? $_GET['MaSP'] : '';

    //Lấy mã sản phẩm yêu thích
    $sql = "SELECT * FROM sanphamyeuthich WHERE MaKH = '$MaKH'";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $MaYT = $row['MaYT'];
    }
    
    $sql1 = "DELETE FROM chitietsanphamyeuthich WHERE MaYT ='$MaYT' and MaSP = '$MaSP'";
    $result1 = $db->query($sql1);
    $data = array(
        'tinhTrang' => '1'
    );
    

    
    header('Content-Type: application/json');
    echo json_encode($data);

?>