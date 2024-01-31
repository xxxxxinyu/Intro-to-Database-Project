<?php
require_once("../conn.php");

$oids = json_decode($_GET['oids']);
$cmd = $_GET['cmd'];
$err = false;

if(count($oids) == 0) {
    echo '請至少勾選一項';
} else {
    if($cmd == 'finish') {
        foreach($oids as $oid) {
            $sql = "SELECT * FROM `order` WHERE `OID`= '$oid'";
            $result = mysqli_query($conn, $sql);
            $data = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            ///////訂單資訊
            $status = $data['Status'];
            ////////
            if($status == 'Finished') {
                $err = true;
            }
            else if($status == 'Cancelled') {
                $err = true;
            }
            else if($status == 'Not Finished') {
                $sql = "UPDATE `order` SET `Status` = 'Finished', `Finish_time` = CURRENT_TIMESTAMP WHERE `OID`= '$oid'";
                $result = mysqli_query($conn, $sql);
            }
        }
        if($err) {
            $msg = '部分訂單無法完成';
        } else {
            $msg = '訂單完成';
        }
    }
    else if($cmd == 'cancel') {
        foreach($oids as $oid) {
            $sql = "SELECT * FROM `order` WHERE `OID`= '$oid'";
            $result = mysqli_query($conn, $sql);
            $data = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            ///////訂單資訊
            $sid = $data["SID"];
            $uid = $data['UID'];
            $status = $data['Status'];
            $subtotal = $data['Subtotal'];
            $delivery_fee = $data['Delivery_fee'];
            $total = $subtotal + $delivery_fee;
            $payback = $total * -1;
            ////////
            if($status == 'Finished') {
                $err = true;
            }
            else if($status == 'Cancelled') {
                $err = true;
            }
            else if($status == 'Not Finished') {
                $sql = "SELECT * FROM `shop` WHERE `SID` = $sid";
                $result = mysqli_query($conn, $sql);
                $data = mysqli_fetch_assoc($result);
                mysqli_free_result($result);
                ////////交易店家資訊
                $shop_uid = $data['UID'];
                $shop_name = $data['Name'];
                ////////

                $sql = "SELECT `Balance`, `Account` FROM `user` WHERE `UID` = '$uid'";
                $result = mysqli_query($conn, $sql);
                $data = mysqli_fetch_assoc($result);
                mysqli_free_result($result);
                ////////顧客資訊
                $balance = $data['Balance'] + $total;
                $account = $data['Account'];
                ////////

                //訂單狀態更新
                $sql = "UPDATE `order` SET `Status` = 'Cancelled', `Finish_time` = CURRENT_TIMESTAMP WHERE `OID` = $oid";
                $result = mysqli_query($conn, $sql);
                //顧客退錢
                $sql = "UPDATE `user` SET `Balance` = '$balance'  WHERE `UID` = '$uid'";
                $result = mysqli_query($conn, $sql);
                //店家扣錢
                $sql = "SELECT `Balance` FROM `user` WHERE `UID` = '$shop_uid'";
                $result = mysqli_query($conn, $sql);
                $balance = mysqli_fetch_assoc($result)['Balance'] + $payback;
                mysqli_free_result($result);
                $sql = "UPDATE `user` SET `Balance` = '$balance' WHERE `UID` = '$shop_uid'";
                $result = mysqli_query($conn, $sql);
                //顧客交易紀錄
                $sql = "INSERT INTO `transaction` (`UID`, `Amount`, `Behavior`, `Trader`) VALUES('$uid', '$total', 'Receive', '$shop_name')";
                mysqli_query($conn, $sql);
                //店家交易紀錄
                $sql = "INSERT INTO `transaction` (`UID`, `Amount`, `Behavior`, `Trader`) VALUES('$shop_uid', '$payback', 'Payment', '$account')";
                mysqli_query($conn, $sql);
                //商品數量
                $sql = "SELECT `PID`, `Quantity` FROM `order_item` WHERE `OID` = '$oid'";
                $result = mysqli_query($conn, $sql);
                $data = array();
                if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    while ($_i = mysqli_fetch_assoc($result)) {
                    $data[] = $_i;
                    }
                }
                mysqli_free_result($result);
                }

                if(!empty($data)){ 
                    foreach($data as $item) {
                        $item_quantity = $item['Quantity'];
                        $pid = $item['PID'];
                        $sql = "SELECT `Amount` FROM `meal` WHERE `PID` = '$pid'";
                        $result = mysqli_query($conn, $sql);
                        $quantity = mysqli_fetch_assoc($result)['Amount'];
                        mysqli_free_result($result);

                        $total_quantity = $item_quantity + $quantity;
                        $sql = "UPDATE `meal` SET `Amount` = '$total_quantity' WHERE `PID` = '$pid'";
                        mysqli_query($conn, $sql);
                    }
                }
            }
        }
        if($err) {
            $msg = '部分訂單無法取消';
        } else {
            $msg = '訂單成功取消';
        }
    }
    echo $msg;
}
?>