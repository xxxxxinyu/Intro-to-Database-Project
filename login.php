<?php
    session_start();
    require_once('conn.php');

    header('Content-Type: application/json; charset=UTF-8'); //設定資料類型為 json，編碼 utf-8
    $account = $_POST["Account"];
    $password = $_POST["password"];
    $hash_passsword = hash("sha256", $password);

    $user_data = array();
    $user = "SELECT `UID`, `Account`, `Password` FROM `user` WHERE Binary `Account` = ?"; //這邊要撈資料庫
    $stmt = mysqli_prepare($conn, $user);
    mysqli_stmt_bind_param($stmt, "s", $account);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        if ($hash_passsword != $user_data["Password"]) {
            echo json_encode(array(
                'errorMsg' => '帳號或密碼錯誤'
            ));
        } else {
            $_SESSION["UID"] = $user_data["UID"];
            echo json_encode(array(
                'res' => '1'
            ));
        }
    } else {
        echo json_encode(array(
            'errorMsg' => '未註冊的帳號'
        ));
    }
?>