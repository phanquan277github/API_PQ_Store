<?php 
    header('Content-type: text/html; charset=utf-8');
    header('Content-Type: application/json');
    // $obj = new getDataUserController();

    // $data = array();
    // $data = $obj->list();
    // Kết nối với cơ sở dữ liệu
    $db = new mysqli('localhost', 'root', 'phuoc@2209', 'shopgiay');
    
    $MaKH = isset($_GET['MaKH']) ? $_GET['MaKH'] : '';
    $MaSizeSP = isset($_GET['MaSizeSP']) ? $_GET['MaSizeSP'] : '';
    $SoLuong = isset($_GET['SoLuong']) ? $_GET['SoLuong'] : '';
    
    // Xử lý yêu cầu tìm kiếm theo san pham


    // kiểm tra xem khách hàng đã có giỏ hàng hay chưa
    $sql = "SELECT * FROM giohang where MaKH = '$MaKH'";
    $result = $db->query($sql);

    // Mã hóa dữ liệu sản phẩm sang JSON
    $data = [];
    //Nếu chưa có giỏ hàng thì tạo giỏ hàng
    if ($result->num_rows == 0) {
        $sql1 = "INSERT INTO giohang (MaKH)
        VALUES ('$MaKH')";
        $result = $db->query($sql1);
    }
    //nếu đã có giỏ hàng thì lấy mã giỏ hàng và thêm sản phẩm vào giỏ hàng 
    else{
        while ($row = $result->fetch_assoc()) {
            $MaGH = $row['MaGH'];
        }
        //khi có mã giỏ hàng thực hiện thêm sản phẩm vào giỏ hàng
        $sql2 = "INSERT INTO chitietgiohang (MaGH,MaSizeSP,SoLuong)VALUES ('$MaGH','$MaSizeSP',' $SoLuong')";
        $result = $db->query($sql2);
        $data = array(
            'tinhTrang' => '1'
        );
    }

    //

    
    header('Content-Type: application/json');
    echo json_encode($data);

?>