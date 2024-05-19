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
    
    // kiểm tra xem khách hàng đã có sản phẩm yt hay chưa
    $sql = "SELECT * FROM sanphamyeuthich where MaKH = '$MaKH'";
    $result = $db->query($sql);

    // Mã hóa dữ liệu sản phẩm sang JSON
    $data = [];
    //Nếu chưa có sản phẩm yt thì tạo
    
    if ($result->num_rows == 0) {
        $sql1 = "INSERT INTO sanphamyeuthich (MaKH)
        VALUES ('$MaKH')";
        $result = $db->query($sql1);
    }
    //nếu đã có sản phẩm yt thì lấy mã sản phẩm yt và thêm sản phẩm vào chi tiết spyt
    else{
        while ($row = $result->fetch_assoc()) {
            $MaYT = $row['MaYT'];
        }
        //khi có mã yt thực hiện thêm sản phẩm vào chi tiết spyt
        $sql2 = "INSERT INTO chitietsanphamyeuthich (MaYT,MaSP)VALUES ('$MaYT','$MaSP')";
        $result = $db->query($sql2);
        $data = array(
            'tinhTrang' => '1'
        );
    }

    

    
    header('Content-Type: application/json');
    echo json_encode($data);

?>