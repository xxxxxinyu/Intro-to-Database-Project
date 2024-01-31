<?php
session_start();
header('Content-Type: application/json; charset=UTF-8'); //設定資料類型為 json，編碼 utf-8
require_once("conn.php");
if ($_SERVER['REQUEST_METHOD'] == "POST") { //如果是 POST 請求
    $name = $_POST["name"];
    $category = $_POST["category"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    if ($name == null || $category == null || $latitude == null || $longitude == null) { //如果有欄位空白
        echo json_encode(array(
            'errorMsg' => '資料未輸入完全'
        ));
    } elseif ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        echo json_encode(array(
            'errorMsg' => '經緯度超出範圍'
        ));
    } else {
        //檢查店名是否重複
        $check = "SELECT * FROM `shop` WHERE Binary `Name` = ?";
        $_check = mysqli_prepare($conn, $check);
        if($_check !== FALSE) {
            mysqli_stmt_bind_param($_check, "s", $name);
            mysqli_stmt_execute($_check);
            $result = mysqli_stmt_get_result($_check);
            if(mysqli_num_rows($result) != 0){
                echo json_encode(array(
                    'errorMsg' => '店名已被註冊'
                ));
            }
            else{
                //更新使用者身分
                $uid = $_SESSION['UID'];
                $update = "UPDATE `user` SET `Identity` = 'shopowner' WHERE `UID` = '$uid'";
                mysqli_query($conn, $update);
                //把店家資料寫進資料庫
                $sql = "INSERT INTO `shop`(`Name`, `Location`, `Type`, `UID`) VALUES(?, ST_GeomFromText('POINT(" .$longitude. " " .$latitude. ")', 0), ?, '$uid')";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $name, $category);
                mysqli_stmt_execute($stmt);
                echo json_encode(array(
                    'res' => 1
                ));
            }
        } 
        else {
            echo json_encode(array(
                'errorMsg' => 'prepare() failed: ' . htmlspecialchars($conn->error)
            ));
        }
    }
        
}
    else {
    echo json_encode(array(
        'errorMsg' => '請求無效，只允許 POST 方式訪問！'
    ));
    }
?>

