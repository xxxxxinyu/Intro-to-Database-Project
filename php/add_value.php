<?php
require_once("../conn.php");
$uid = $_GET['uid'];
$value = $_GET['value'];

if(preg_match("/^[1-9][0-9]*$/", $value)){
    $sql = "SELECT `Balance`, `Account` FROM `user` WHERE `UID` = '$uid'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    $account = $data['Account'];
    $balance = $data['Balance'] + $value;

    $sql = "UPDATE `user` SET `Balance` = '$balance' WHERE `UID` = '$uid'";
    $result = mysqli_query($conn, $sql);

    $sql = "INSERT INTO `transaction` (`UID`, `Amount`, `Behavior`, `Trader`) VALUES('$uid', '$value', 'Recharge', '$account')";
    $result = mysqli_query($conn, $sql);
    echo "加值成功";
}
else{
    echo "非正整數";
}

?>