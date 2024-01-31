<?php
require_once("../conn.php");
$pid = $_GET['pid'];
$price = $_GET['price'];
$quantity = $_GET['quantity'];

//確定沒在未完成訂單中
//搜尋所有包含此商品的訂單id(含取消及已完成)
$all_order_id = array();
$sql = "SELECT `OID` FROM `order_item` WHERE `PID`= '$pid'";
$result = mysqli_query($conn, $sql);
if ($result) {
  if (mysqli_num_rows($result)>0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $all_order_id[] = $row;
    }
  }
  mysqli_free_result($result);
}

$unfinished = false;
if(!empty($all_order_id)){
  foreach($all_order_id as $key => $row) {
    //搜每筆訂單的status是否有未完成
    $oid = $row['OID'];
    $sql = "SELECT `Status` FROM `order` WHERE `OID`= '$oid'";
    $result = mysqli_query($conn, $sql);
    $status = mysqli_fetch_assoc($result)['Status'];
    mysqli_free_result($result);
    if($status == 'Not Finished') {
      $unfinished = true;
    }
  }
}

if($unfinished) {
  echo "尚有包含此餐點的訂單未完成";
} else {
  $sql = "UPDATE `meal` SET `Price` = ?, `Amount` = ? WHERE `PID` = '$pid'";
  $_update_meal = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($_update_meal, "ii", $price, $quantity);
  mysqli_stmt_execute($_update_meal);
  $result = mysqli_stmt_get_result($_update_meal); 
  echo "編輯成功";
}
?>