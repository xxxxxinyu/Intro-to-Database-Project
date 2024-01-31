<?php
require_once("../conn.php");
$pid = $_GET['pid'];

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
  // $delete_meal = "DELETE FROM meal WHERE PID=$delete_pid";
  // $result = mysqli_query($conn, $delete_meal);
  $sql = "UPDATE `meal` SET `Is_Deleted` = 1 WHERE `PID` = '$pid'";
  $result = mysqli_query($conn, $sql);
  echo "刪除成功";
}
?>