<?php
require_once("conn.php");
if (isset($_GET['shopname'])) {
    //從資料庫撈帳號
    $shop = $_GET['shopname'];
    $check = "SELECT * FROM `shop` WHERE `Name` = ?";
    $_check = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($_check, "s", $shop);
    mysqli_stmt_execute($_check);
    $result = mysqli_stmt_get_result($_check);
    if (mysqli_num_rows($result) == 0) {  //如果沒撈到
        $ret = "此店名尚未被註冊";
    } else {
        $ret = "此店名已被註冊";
    }
    echo $ret;
}
?>