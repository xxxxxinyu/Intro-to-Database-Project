<?php
require_once("conn.php");
if (isset($_GET['username'])) {
    //從資料庫撈帳號
    $account = $_GET['username'];
    $check = "SELECT * FROM user WHERE Binary Account = ?";
    $_check = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($_check, "s", $account);
    mysqli_stmt_execute($_check);
    $result = mysqli_stmt_get_result($_check);
    if (mysqli_num_rows($result) == 0) {  //如果沒撈到
        echo json_encode(array(
            'res' => '此帳號可以使用'
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array(
            'errorMsg' => '此帳號已經有人使用'
        ), JSON_UNESCAPED_UNICODE);
    }
}
?>