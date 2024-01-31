<?php
  require_once('conn.php');
  session_start();
  $log = isset($_SESSION['UID']) ? '<a class="navbar-brand" href="logout.php">Log out</a>' : '<a class="navbar-brand" href="index.html">Log in</a>';
  
  if(!isset($_SESSION['UID'])) {  //使用者資料
    header("Location: index.html");
    exit;
  } else {
    $uid = $_SESSION['UID'];  //uid
    $user_data = array();
    $user = "SELECT `UID`, `Account`, `First_name`, `Last_name`, `Identity`, `Phone_number`, AsText(Location) AS `Location` , `Balance` FROM `user` WHERE `UID` = '$uid'"; //這邊要撈資料庫
    $result = mysqli_query($conn, $user);
    $user_data = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    $pattern = "/^POINT\(([0-9.-]+)\s([0-9.-]+)\)$/";
    preg_match($pattern, $user_data["Location"], $matches);
    $user_latitude = $matches[2];
    $user_longitude = $matches[1];
    $user_account = $user_data["Account"];
  

  if(isset($_POST['editLocation'])){  //編輯user location
    $edit_uid = $_POST['uid'];
    $edit_longitude = $_POST['longitude'];
    $edit_latitude = $_POST['latitude'];

    $update_location = "UPDATE `user` SET `Location` = ST_GeomFromText('POINT(" .$edit_longitude. " " .$edit_latitude. ")', 0) WHERE `UID` = ?";
    $update = mysqli_prepare($conn, $update_location);
    mysqli_stmt_bind_param($update, "s", $edit_uid);
    mysqli_stmt_execute($update);
    $result = mysqli_stmt_get_result($update);
    header("Location: nav.php");
    exit;
  }
  // if(!empty($_POST['addValue'])){  //儲值
  //   $add_value = $_POST['value'];
  //   date_default_timezone_set('Asia/Hong_Kong');
    
  //   if(preg_match("/^[1-9][0-9]*$/", $add_value)){
  //     $add_uid = $_POST['uid'];
  //     $add_time = date("Y/m/d H:i:s", time());
  //     $uacc = $user_data['Account'];
  //     $ubal = $user_data['Balance'] + $add_value;
  //     $update_value = "UPDATE `user` SET `Balance` = '$ubal' WHERE `UID` = '$add_uid'";
  //     $add_transaction = "INSERT INTO `transaction` (`UID`, `Amount`, `Time`, `Behavior`, `Trader`) VALUES('$add_uid', '$add_value', '$add_time', 'Recharge', '$uacc')";
  //     $result = mysqli_query($conn, $update_value);
  //     $result1 = mysqli_query($conn, $add_transaction);
  //     echo "<script>alert('加值成功');</script>";
  //     echo "<script>window.location.href = 'nav.php'</script>";
  //   }
  //   else{
  //     echo "<script>alert('非正整數');</script>";
  //     echo "<script>window.location.href = 'nav.php'</script>";
  //   }
  // }

  // if(isset($_POST['editMeal'])){  //編輯商品
  //   $edit_pid = $_POST['pid'];
  //   $edit_price = $_POST['price'];
  //   $edit_quantity = $_POST['quantity'];

  //   //確定沒在未完成訂單中
  //   //搜尋所有包含此商品的訂單id(含取消及已完成)
  //   $all_order_id = array();
  //   $sql = "SELECT `OID` FROM `order_item` WHERE `PID`= '$edit_pid'";
  //   $result = mysqli_query($conn, $sql);
  //   if ($result) {
  //     if (mysqli_num_rows($result)>0) {
  //       while ($row = mysqli_fetch_assoc($result)) {
  //         $all_order_id[] = $row;
  //       }
  //     }
  //     mysqli_free_result($result);
  //   }
  //   $unfinished = false;
  //   if(!empty($all_order_id)){
  //     foreach($all_order_id as $key => $row) {
  //       //搜每筆訂單的status是否有未完成
  //       $oid = $row['OID'];
  //       $sql = "SELECT `Status` FROM `order` WHERE `OID`= '$oid'";
  //       $result = mysqli_query($conn, $sql);
  //       $status = mysqli_fetch_assoc($result)['Status'];
  //       mysqli_free_result($result);
  //       if($status == 'Not Finished') {
  //         $unfinished = true;
  //       }
  //     }
  //   }

  //   if($unfinished) {
  //     echo "<script>alert('尚有包含此餐點的訂單未完成');</script>";
  //   } else {
  //     $update_meal = "UPDATE `meal` SET `Price` = ?, `Amount` = ? WHERE `PID` = '$edit_pid'";
  //     $_update_meal = mysqli_prepare($conn, $update_meal);
  //     mysqli_stmt_bind_param($_update_meal, "ii", $edit_price, $edit_quantity);
  //     mysqli_stmt_execute($_update_meal);
  //     $result = mysqli_stmt_get_result($_update_meal); 
  //     echo "<script>alert('編輯成功');</script>";
  //   }
  //   echo "<script>window.location.href = 'nav.php#shop'</script>";
  // }

  // if(isset($_POST['deleteMeal'])){  //刪除商品
  //   $delete_pid = $_POST['delete_id'];
  //   //確定沒在訂單中
  //   $all_order_id = array();
  //   $sql = "SELECT `OID` FROM `order_item` WHERE `PID`= '$delete_pid'";
  //   $result = mysqli_query($conn, $sql);
  //   if ($result) {
  //     if (mysqli_num_rows($result)>0) {
  //       while ($row = mysqli_fetch_assoc($result)) {
  //         $all_order_id[] = $row;
  //       }
  //     }
  //     mysqli_free_result($result);
  //   }
  //   $unfinished = false;
  //   if(!empty($all_order_id)){
  //     foreach($all_order_id as $key => $row) {
  //       //搜每筆訂單的status是否有未完成
  //       $oid = $row['OID'];
  //       $sql = "SELECT `Status` FROM `order` WHERE `OID`= '$oid'";
  //       $result = mysqli_query($conn, $sql);
  //       $status = mysqli_fetch_assoc($result)['Status'];
  //       mysqli_free_result($result);
  //       if($status == 'Not Finished') {
  //         $unfinished = true;
  //       }
  //     }
  //   }

  //   if($unfinished) {
  //     echo "<script>alert('尚有包含此餐點的訂單未完成');</script>";
  //   } else {
  //     // $delete_meal = "DELETE FROM meal WHERE PID=$delete_pid";
  //     // $result = mysqli_query($conn, $delete_meal);
  //     $sql = "UPDATE `meal` SET `Is_Deleted` = 1 WHERE `PID` = '$delete_pid'";
  //     $result = mysqli_query($conn, $sql);
  //     echo "<script>alert('刪除成功');</script>";
  //   }
  //   echo "<script>window.location.href = 'nav.php#shop'</script>";
  // }

  if(isset($_POST['order'])){  //下訂單
    $type = $_POST['type'];
    $order_sid = $_POST['sid'];
    $order_uid = $_POST['uid'];
    $delivery_fee = $_POST['delivery-fee'];
    $subtotal = 0;

    $sql = "SELECT `Balance` FROM `user` WHERE `UID` = '$uid'";
    $result = mysqli_query($conn, $sql);
    $balance = mysqli_fetch_assoc($result)['Balance'];
    mysqli_free_result($result);

    $i = 0;
    $error_insufficient = array();
    $error_unexist = false;
    while(true) {
      if(isset($_POST[$i])) {
        $item = explode(" ",$_POST[$i]);
        $sql = "SELECT `Name`, `Amount`, `Price` FROM `meal` WHERE `PID` = '$item[0]' AND `Is_Deleted` = 0";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) != 0) { //商品存在
          $item_detail = mysqli_fetch_assoc($result);
          mysqli_free_result($result);
          if($item_detail['Amount'] < $item[1]) {
            array_push($error_insufficient, $item_detail['Name']);
          }
          $subtotal += (int)$item[1] * $item_detail['Price'];
        } else { //商品不存在
          $error_unexist = true;
        }
        $i++;
      } else {
        break;
      }
    }

    $total = $subtotal + (int)$delivery_fee;
    if($balance < $total) {
      echo "<script>alert('餘額不足');</script>";
    }
    else if(count($error_insufficient) > 0 || $error_unexist) {
      $err_msg = '';
      if(count($error_insufficient) > 0) {
        $err_msg = $err_msg . '商品數量不足：\\n';
        foreach($error_insufficient as $value) {
          $err_msg =  $err_msg . "  " . $value . "\\n";
        }
      }
      if($error_unexist) {
        $err_msg =  $err_msg . "部分商品不存在\\n";
      }
      echo "<script>alert('" . $err_msg . "');</script>";
    } else {
      //更新顧客錢包
      $balance -= $total; 
      $sql = "UPDATE `user` SET `Balance` = '$balance' WHERE `UID` = '$order_uid'";
      $result = mysqli_query($conn, $sql);

      //更新店家錢包
      $sql = "SELECT `UID`, `Name` FROM `shop` WHERE `SID` = '$order_sid'"; 
      $result = mysqli_query($conn, $sql);
      $shop_user_data = mysqli_fetch_assoc($result);
      $shop_uid = $shop_user_data['UID'];
      $shop_name = $shop_user_data['Name'];
      mysqli_free_result($result);
      $sql = "SELECT `Balance` FROM `user` WHERE `UID` = '$shop_uid'";
      $result = mysqli_query($conn, $sql);
      $shop_balance = mysqli_fetch_assoc($result)['Balance'];
      mysqli_free_result($result);
      $shop_balance += $total;
      $sql = "UPDATE `user` SET `Balance` = '$shop_balance' WHERE `UID` = '$shop_uid'";
      $result = mysqli_query($conn, $sql);

      //新增顧客交易紀錄
      $minus_total = $total * -1;
      $sql = "INSERT INTO `transaction` (`UID`, `Amount`, `Behavior`, `Trader`) VALUES('$order_uid', '$minus_total', 'Payment', '$shop_name')";
      $result = mysqli_query($conn, $sql);

      //新增店家交易紀錄
      $sql = "INSERT INTO `transaction` (`UID`, `Amount`, `Behavior`, `Trader`) VALUES('$shop_uid', '$total', 'Receive', '$user_account')";
      $result = mysqli_query($conn, $sql);

      //新增訂單
      $sql = "INSERT INTO `order` (`Type`, `SID`, `UID`, `Subtotal`, `Delivery_fee`) VALUES('$type', '$order_sid', '$order_uid', '$subtotal', '$delivery_fee')";
      $result = mysqli_query($conn, $sql);
      $last_oid = mysqli_insert_id($conn);
      $i = 0;
      while(true) { 
        if(isset($_POST[$i])) {
          $item = explode(" ",$_POST[$i]);
          //新增訂單詳細
          $sql = "SELECT `Price` FROM `meal` WHERE `PID` = '$item[0]'";
          $result = mysqli_query($conn, $sql);
          $price = mysqli_fetch_assoc($result)['Price'];
          mysqli_free_result($result);
          $sql = "INSERT INTO `order_item` (`OID`, `PID`, `Quantity`, `Price`) VALUES('$last_oid', '$item[0]', '$item[1]', '$price')";
          $result = mysqli_query($conn, $sql);
          //更新商品數量
          $sql = "SELECT `Amount` FROM `meal` WHERE `PID` = '$item[0]'";
          $result = mysqli_query($conn, $sql);
          $amount = mysqli_fetch_assoc($result)['Amount'];
          $amount -= $item[1];
          mysqli_free_result($result);
          $sql = "UPDATE `meal` SET `Amount` = '$amount' WHERE `PID` = '$item[0]'";
          $result = mysqli_query($conn, $sql);
          $i++;
        } else {
          break;
        }
      }
      echo "<script>alert('訂購成功');</script>";
    }
    echo "<script>window.location.href = 'nav.php'</script>";
  }

  // if(isset($_POST['finishOrder'])) { //完成訂單
  //   $oid = $_POST['oid'];
  //   $sql = "SELECT * FROM `order` WHERE `OID`= '$oid'";
  //   $result = mysqli_query($conn, $sql);
  //   $data = mysqli_fetch_assoc($result);
  //   mysqli_free_result($result);

  //   $status = $data['Status'];
  //   if($status == 'Finished') {
  //     echo "<script>alert('訂單已被完成');</script>";
  //   }
  //   else if($status == 'Cancelled') {
  //     echo "<script>alert('訂單已被取消');</script>";
  //   }
  //   else if($status == 'Not Finished') {
  //     $sql = "UPDATE `order` SET `Status` = 'Finished', `Finish_time` = CURRENT_TIMESTAMP WHERE `OID`= '$oid'";
  //     $result = mysqli_query($conn, $sql);
  //   }
  //   echo "<script>window.location.href = 'nav.php#shop-order'</script>";
  // }
  // if(isset($_POST['deleteOrder'])){  //取消訂單
  //   $delete_oid = $_POST['delete_oid'];
  //   $sql = "SELECT * FROM `order` WHERE `OID`= '$delete_oid'";
  //   $result = mysqli_query($conn, $sql);
  //   $data = mysqli_fetch_assoc($result);
  //   mysqli_free_result($result);
  //   $delete_sid = $data["SID"];
  //   $delete_uid = $data['UID'];
  //   $status = $data['Status'];
  //   if($status == 'Finished') {
  //     echo "<script>alert('訂單已被完成');</script>";
  //   }
  //   else if($status == 'Cancelled') {
  //     echo "<script>alert('訂單已被取消');</script>";

  //   }
  //   else if($status == 'Not Finished') {
  //     $sql = "SELECT * FROM `shop` WHERE `SID` = $delete_sid";
  //     $result = mysqli_query($conn, $sql);
  //     $shopd = mysqli_fetch_assoc($result);
  //     $shop_uid = $shopd['UID'];
  //     $shop_name = $shopd['Name'];
  //     mysqli_free_result($result);
  //     # 訂單狀態更新
  //     $delete_order = "UPDATE `order` SET `Status` = 'Cancelled', `Finish_time` = CURRENT_TIMESTAMP WHERE `OID` = $delete_oid";
  //     $r = mysqli_query($conn, $delete_order);
  //     # 退錢給顧客
  //     $sql = "SELECT `Balance`, `Account` FROM `user` WHERE `UID` = '$uid'";
  //     $result = mysqli_query($conn, $sql);
  //     $userd = mysqli_fetch_assoc($result);
  //     $balance = $userd['Balance'];
  //     $account = $userd['Account'];
  //     mysqli_free_result($result);
  //     $money = $balance + $data['Subtotal'] + $data['Delivery_fee'];
  //     $add_user = "UPDATE `user` SET `Balance` = '$money'  WHERE `UID` = '$uid'";
  //     mysqli_query($conn, $add_user);
  //     # 使用者交易紀錄
  //     $total = $data['Subtotal'] + $data['Delivery_fee'];
  //     $sql = "INSERT INTO `transaction` (`UID`, `Amount`, `Behavior`, `Trader`) VALUES('$uid', '$total', 'Receive', '$shop_name')";
  //     mysqli_query($conn, $sql);
  //     # 店家扣錢
  //     $payback = $total * -1;
  //     $sql = "SELECT `Balance` FROM `user` WHERE `UID` = '$shop_uid'";
  //     $result = mysqli_query($conn, $sql);
  //     $shopbalance = mysqli_fetch_assoc($result)['Balance'];
  //     mysqli_free_result($result);
  //     $shopmoney = $shopbalance + $payback;
  //     $sub_shop = "UPDATE `user` SET `Balance` = '$shopmoney' WHERE `UID` = '$shop_uid'";
  //     mysqli_query($conn, $sub_shop);
  //     # 店家交易紀錄
  //     $sql = "INSERT INTO `transaction` (`UID`, `Amount`, `Behavior`, `Trader`) VALUES('$shop_uid', '$payback', 'Payment', '$account')";
  //     mysqli_query($conn, $sql);
  //     # 商品數量
  //     $sql = "SELECT `PID`, `Quantity` FROM `order_item` WHERE `OID` = '$delete_oid'";
  //     $result = mysqli_query($conn, $sql);
  //     $item_data = array();
  //     if ($result) {
  //       if (mysqli_num_rows($result)>0) {
  //         while ($item_row = mysqli_fetch_assoc($result)) {
  //           $item_data[] = $item_row;
  //         }
  //       }
  //       mysqli_free_result($result);
  //     }
  //     if(!empty($item_data)){ 
  //       foreach($item_data as $key => $row) {
  //         $item_quantity = $row['Quantity'];
  //         $item_id = $row['PID'];
  //         $sql = "SELECT `Amount` FROM `meal` WHERE `PID` = '$item_id'";
  //         $result = mysqli_query($conn, $sql);
  //         $quantity = mysqli_fetch_assoc($result)['Amount'];
  //         mysqli_free_result($result);
  //         $total_quantity = $item_quantity + $quantity;
  //         $sql = "UPDATE `meal` SET `Amount` = '$total_quantity' WHERE `PID` = '$item_id'";
  //         mysqli_query($conn, $sql);
  //       }
  //     }
    
  //   }
  // }
?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/main.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <title>foodNYCU</title>
</head>

<body>
  <nav class="navbar navbar-inverse" style="border-radius: 0;">
    <div style="display: flex; justify-content: space-between; padding: 0 50px;">
      <a class="navbar-brand" href="#">food NYCU</a>
      <?php echo $log; ?>
    </div>
  </nav>

  <div class="container">
    <ul class="nav nav-tabs">
      <li id="home-tab"><a href="#home">Home</a></li>
      <li id="shop-tab"><a href="#shop">Shop</a></li>
      <li id="my-order-tab"><a href="#my-order">My Order</a></li>
      <li id="shop-order-tab"><a href="#shop-order">Shop Order</a></li>
      <li id="transaction-record-tab"><a href="#transaction-record">Transaction Record</a></li>
    </ul>

    <div class="tab-content">
      <div id="home" class="tab-pane fade">
        <h3>Profile</h3>
        <?php
          if(isset($_SESSION['UID'])) {
        ?>
        <div class="row">
          <div class="col-xs-12">
            Account: <?php echo $user_data["Account"]; ?>, Name: <?php echo $user_data["First_name"] . ' ' . $user_data["Last_name"]; ?>, <?php echo $user_data["Identity"]; ?>, PhoneNumber: <?php echo $user_data["Phone_number"]; ?>,  Location: <?php echo $user_longitude . ', ' . $user_latitude; ?>
            
            <button type="button " style="margin-left: 5px;" class=" btn btn-info " data-toggle="modal"
            data-target="#location">Edit location</button>
            <div class="modal fade" id="location"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit location</h4>
                  </div>

                  <form method="POST" action="" onsubmit="return check_location();">  <!--編輯地點-->
                    <input type="hidden" name="uid" value="<?php print $uid; ?>">
                    <div class="modal-body">
                      <label class="control-label " for="latitude">latitude</label>
                      <input name="latitude" type="number" step="any" class="form-control" id="latitude" placeholder="enter latitude">
                      <br>
                      <label class="control-label " for="longitude">longitude</label>
                      <input name="longitude" type="number" step="any" class="form-control" id="longitude" placeholder="enter longitude">
                    </div>
                    <div class="modal-footer">
                      <input name="editLocation" type="submit" class="btn btn-default" value="Edit">
                    </div>
                  </form>

                </div>
              </div>
            </div>

            Walletbalance: <?php echo $user_data["Balance"]?>
            <!-- Modal -->
            <button type="button " style="margin-left: 5px;" class="btn btn-info" data-toggle="modal"
              data-target="#myModal">Recharge</button>
            <div class="modal fade" id="myModal"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add value</h4>
                  </div>
            
                  <form method="POST" action="">
                    <div class="modal-body">
                      <input name="value" type="text" class="form-control" id="value" placeholder="enter add value">
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-default" onclick="add_value(<?php print $uid; ?>)">Add</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
        
        
        <h3>Search</h3>
        <div class=" row  col-xs-8">
          <!--搜尋-->
          <form class="form-horizontal" action="" method="GET" id="search" onsubmit="hide_empty_variable()">
            <div class="form-group">
              <label class="control-label col-sm-1" for="Shop">Shop</label>
              <div class="col-sm-5">
                <input name="name" type="text" class="form-control" placeholder="Enter Shop name">
              </div>
              <label class="control-label col-sm-1" for="distance">Distance</label>
              <div class="col-sm-5">
                <select name="dis" class="form-control" id="sel1">
                  <option value=""></option>
                  <option value="near">Near</option>
                  <option value="medium">Medium </option>
                  <option value="far">Far</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-sm-1" for="Price">Price</label>
              <div class="col-sm-2">
                <input name="price-low" type="text" class="form-control">
              </div>
              <label class="control-label col-sm-1" for="~">~</label>
              <div class="col-sm-2">
                <input name="price-high" type="text" class="form-control">
              </div>
              <label class="control-label col-sm-1" for="Meal">Meal</label>
              <div class="col-sm-5">
                <input name="meal" type="text" class="form-control" placeholder="Enter Meal">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-1" for="category">Category</label>
                <div class="col-sm-5">
                  <input name="category" type="text" class="form-control" placeholder="Enter shop category">
                </div>
                <button name="search" type="submit" style="margin-left: 18px;"class="btn btn-primary">Search</button>
            </div>
          </form>
          
        </div>
        <?php 
          $arrow = '<span class="arrow asc"></span>';
          $err_msg = '查無資料';
          $is_valid = true;
          $desc_r = -1;
          $order = isset($_GET["order"]) ? $_GET["order"] : 'distant';
          if($order != 'Name' && $order != 'distant' && $order != 'Type') {
            $order = 'distant';
            $is_valid = false;
            $err_msg = "請選擇有效排序";
          }
          $order_sql = "ORDER BY " . $order;
          if(isset($_GET["desc"])) {
            if($_GET["desc"] == -1) {
              $order_sql = $order_sql . " DESC";
              $arrow = '<span class="arrow dsc"></span>';
              $desc_r = 1;
            } else if($_GET["desc"] != 1) {
              $is_valid = false;
              $err_msg = "請選擇有效排序";
            }
          }
          $query = $_GET;
          unset($query['desc']);
          $query['order'] = 'Name';
          $name_href = http_build_query($query);
          $query['order'] = 'Type';
          $type_href = http_build_query($query);
          $query['order'] = 'distant';
          $dis_href = http_build_query($query);
        ?>
        <div class="row">
          <div class="col-xs-8">
            <table class="table" style=" margin-top: 15px;">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col"><a href=<?php echo $_SERVER['PHP_SELF'] . "?" . $name_href . "&desc=". $desc_r; ?>>Shop name<?php if($order == 'Name'){echo $arrow;} ?></a></th>
                  <th scope="col"><a href=<?php echo $_SERVER['PHP_SELF'] . "?" . $type_href . "&desc=". $desc_r; ?>>Shop category<?php if($order == 'Type'){echo $arrow;} ?></a></th>
                  <th scope="col"><a href=<?php echo $_SERVER['PHP_SELF'] . "?" . $dis_href . "&desc=". $desc_r; ?>>Distance<?php if($order == 'distant'){echo $arrow;} ?></a></th>
                </tr>
              </thead>

              <tbody>
                <?php
                  $shop_data = array();
                  //商店名搜尋
                  if(isset($_GET["search"])) {
                    if(!empty($_GET["name"]) || !empty($_GET["dis"]) || !empty($_GET["price-low"]) || !empty($_GET["price-high"]) || !empty($_GET["meal"]) || !empty($_GET["category"])) {
                      if(!empty($_GET["name"])){
                        $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS distant, `Type` FROM `shop` WHERE `Name` like ? " . $order_sql; //這邊要撈資料庫
                        $_sql = mysqli_prepare($conn, $sql);
                        $name = "%".$_GET["name"]."%";
                        mysqli_stmt_bind_param($_sql, "s", $name );
                      }
    
                      //距離搜尋  
                      else if (!empty($_GET["dis"])){
                        $dis = $_GET["dis"];
                        switch($dis){
                          case "near":
                            $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS `distant`, `Type` FROM `shop` WHERE (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) < 3 " . $order_sql;
                            break;
                          case "medium":
                            $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS distant, `Type` FROM `shop` WHERE (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) >= 3 AND (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) <= 10 " . $order_sql;
                            break;
                          case "far":
                            $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS distant, `Type` FROM `shop` WHERE (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) > 10 " . $order_sql;
                            break;
                          default:
                            $err_msg = '請輸入有效範圍';
                            $is_valid = false;
                            break;
                        }
                        $_sql = $is_valid ? mysqli_prepare($conn, $sql) : NULL;
                      }
    
                      //價格區間搜尋(低高)
                      else if(!empty($_GET["price-low"]) && !empty($_GET["price-high"])){
                        if(is_numeric($_GET["price-low"]) && is_numeric($_GET["price-high"])) {
                          $low = $_GET["price-low"];
                          $high = $_GET["price-high"];
                          $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS distant, `Type` FROM `shop` WHERE `SID` IN (SELECT `SID` FROM `meal` WHERE `Price` >= ? && `Price` <= ? GROUP BY `SID`) " . $order_sql;
                          $_sql = mysqli_prepare($conn, $sql);
                          mysqli_stmt_bind_param($_sql, "ii", $low, $high);
                        } else {
                          $err_msg = '請輸入有效範圍';
                          $is_valid = false;
                        }
                      }
                      
                      //價格區間搜尋(低)
                      else if(!empty($_GET["price-low"])){
                        if(is_numeric($_GET["price-low"])) {
                          $low = $_GET["price-low"];
                          $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS distant, `Type` FROM `shop` WHERE `SID` IN (SELECT `SID` FROM `meal` WHERE `Price` >= ?) " . $order_sql;
                          $_sql = mysqli_prepare($conn, $sql);
                          $is_valid = mysqli_stmt_bind_param($_sql, "i", $low);
                        } else {
                          $err_msg = '請輸入有效範圍';
                          $is_valid = false;
                        }
                      }
    
                      //價格區間搜尋(高)
                      else if(!empty($_GET["price-high"])){
                        if(is_numeric($_GET["price-high"])) {
                          $high = $_GET["price-high"];
                          $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS distant, `Type` FROM `shop` WHERE `SID` IN (SELECT `SID` FROM `meal` WHERE `Price` <= ?) " . $order_sql;
                          $_sql = mysqli_prepare($conn, $sql);
                          $is_valid = mysqli_stmt_bind_param($_sql, "i", $high);
                        } else {
                          $err_msg = '請輸入有效範圍';
                          $is_valid = false;
                        }
                      }
    
                      //餐點名稱搜尋
                      else if(!empty($_GET["meal"])){
                        $meal = $_GET["meal"];
                        $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS distant, `Type` FROM `shop` WHERE `SID` IN (SELECT `SID` FROM `meal` WHERE `Is_Deleted` = 0 AND `Name` like ?) " . $order_sql;
                        //  AND `Is_Deleted` = 0 
                        $_meal = "%".$_GET["meal"]."%";
                        $_sql = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($_sql, "s", $_meal);
                      }
    
                      //店家類型搜尋
                      else if(!empty($_GET["category"])){
                        $category = $_GET["category"];
                        $_category = "%".$_GET["category"]."%";
                        $sql = "SELECT `SID`, `Name`, (ST_Distance_Sphere(ST_GeomFromText('POINT(" .$user_longitude. " " .$user_latitude. ")', 0), `Location`)/1000) AS distant, `Type` FROM `shop` WHERE `Type` like ? " . $order_sql;
                        $_sql = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($_sql, "s", $_category);
                      }
                      
                      if($is_valid) {
                        mysqli_stmt_execute($_sql);
                        $result = mysqli_stmt_get_result($_sql);
                        if($result){
                          if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_assoc($result)){
                              $shop_data[] = $row;
                            }
                          }

                          $limit = 5;
                          $total_rows = mysqli_num_rows($result);
                          $pages = ceil($total_rows/$limit);
                          mysqli_free_result($result);
                          $page = !empty($_GET['page']) ? $_GET['page'] : 1;
                          $offset = ($page-1) * $limit;
                          $end = min($total_rows, $offset + $limit);
                        }
                      }
                    }
                    else{
                      echo "請至少輸入一項";
                    }

                    if(!empty($shop_data)){ //列出搜尋結果
                      for ($i = $offset; $i < $end; $i++) {
                        echo '<tr>';
                        echo '<th scope="row">'. ($i +1) . '</th>';
                        echo '<td>' . $shop_data[$i]['Name'] . '</td>';
                        echo '<td>' . $shop_data[$i]['Type'] . '</td>';
                        if ($shop_data[$i]['distant'] < 3) echo '<td> near ' . round($shop_data[$i]['distant'], 2) . ' km</td>';
                        else if (3 <= $shop_data[$i]['distant'] && $shop_data[$i]['distant'] <= 10) echo '<td> medium ' . round($shop_data[$i]['distant'], 2) . ' km</td>';
                        else if ($shop_data[$i]['distant'] > 10) echo '<td> far ' . round($shop_data[$i]['distant'], 2) . ' km</td>';
                        echo '<td> </td>';
                        echo '<td>  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#search-' . $shop_data[$i]['SID'] . '">Open menu</button></td>';
                        echo '</tr>';
                      };
                    } else {
                        echo "<tr><td>". $err_msg . "</td></tr>";
                    }
                  }
                ?>
              </tbody>
            </table>
            <?php
              if(!empty($shop_data)){ 
                for($i=1; $i<=$pages; $i++) {
                  $query = $_GET;
                  $query['page'] = $i;
                  $query_result = http_build_query($query);
                  echo "<a href=". $_SERVER['PHP_SELF'] . "?" . $query_result . ">" . $i . "</a> ";
                }
              }
            ?>
            <?php 
              if(!empty($shop_data)){ //列出搜尋結果
                for ($i = $offset; $i < $end; $i++) {
                  $search_sid = $shop_data[$i]['SID'];
                  $delivery_fee = $shop_data[$i]['distant'] * 10;
                  $delivery_fee = $delivery_fee > 10 ? round($delivery_fee) : 10;
            ?>
                  <div class="modal fade" id="search-<?php echo $search_sid; ?>" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title">menu</h4>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-xs-12">
                              <table class="table" style=" margin-top: 15px;">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Picture</th>
                                    <th scope="col">meal name</th>
                                    <th scope="col">price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Order check</th>
                                  </tr>
                                </thead>
                                <tbody sid="<?php echo $search_sid; ?>" delivery="<?php echo $delivery_fee; ?>">
                                  <!--列出商品清單-->
                                  <?php 
                                    $meal_data = array();
                                    $shop_id = $shop_data[$i]['SID'];
                                    $meal = "SELECT * FROM `meal` WHERE `SID`= '$shop_id' "; //這邊要撈資料庫
                                    $result = mysqli_query($conn, $meal);
                                    if ($result) {
                                      if (mysqli_num_rows($result)>0) {
                                        while ($meal_row = mysqli_fetch_assoc($result)) {
                                          $meal_data[] = $meal_row;
                                        }
                                      }
                                      mysqli_free_result($result);
                                    }
                                    if(!empty($meal_data)){ //列出搜尋結果
                                      $_i = 1;
                                      foreach($meal_data as $meal_key => $meal_row) {
                                        if ($meal_row['Is_Deleted'] == 0) {
                                        ?>
                                        <tr pid="<?php echo $meal_row['PID']; ?>">
                                          <th scope="row"><?php echo $_i; ?></th>
                                          <td><img width="100" src="data:image/jpeg;base64,<?php echo $meal_row['Image']; ?>"></td>
                                          <td><?php echo $meal_row['Name']; ?></td>
                                          <td><?php echo $meal_row['Price']; ?></td>
                                          <td><?php echo $meal_row['Amount']; ?></td>
                                          <td width="110">
                                            <button class="btn qtyminus" onclick="order_num(<?php echo $meal_row['PID']; ?>, 'del')">-</button>
                                            <span id="order-qty-<?php echo $meal_row['PID']; ?>">0</span>
                                            <button class="btn qtyplus" onclick="order_num(<?php echo $meal_row['PID']; ?>, 'add')">+</button>
                                          </td>
                                        </tr>
                                  <?php
                                        $_i++;
                                        }
                                      }
                                    } else {
                                      echo "<tr><td>無品項</td></tr>";
                                    }
                                  ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <select name="type" class="form-control" style="width: 150px; display: inline;">
                            <option value="Delivery">Delivery</option>
                            <option value="Pick-up">Pick-up</option>
                          </select>
                          <button type="button" class="btn btn-default" onclick="show_preview(this)">Calculate the price</button>
                        </div>
                      </div>
                    </div>
                  </div>
              <?php
                }
              ?>
                  <div class="modal fade" id="order" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                          <!-- Modal content-->
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title">order</h4>
                            </div>
                            <div class="modal-body">
                              <!-- 品項 -->
                              <div class="row">
                                <div class="col-xs-12">
                                  <table class="table" style="margin-top: 15px;">
                                    <thead>
                                      <tr>
                                        <th scope="col">Picture</th>
                                        <th scope="col">meal name</th>
                                        <th scope="col">price</th>
                                        <th scope="col">Order Quantity</th>
                                      </tr>
                                    </thead>

                                    <tbody id="order-content"></tbody>
                                  </table>
                                </div>
                              </div>
                            </div>

                            <form id="order-form" class="modal-footer" method="POST" action="">
                              Subtotal $<span id="detail-subtotal"></span><br>
                              Delivery fee $<span id="detail-delivery-fee"></span><br>
                              Total price $<span id="detail-total"></span><br>
                                <div class="order-detail" hidden>
                                  <input name="type">
                                  <input name="sid">
                                  <input name="delivery-fee">
                                  <input name="uid" value="<?php echo $uid; ?>">
                                </div>
                                <div class="item-detail" hidden></div>
                                <input type="submit" class="btn btn-default" value="order" name="order">
                            </form>

                          </div>
                        </div>
                  </div>
            <?php
              }
            ?>
          </div>
        </div>
        <?php
          } else {
            echo '<h4>未登入</h4>';
          }
        ?>
      </div>



      <!--SHOP-->
      <div id="shop" class="tab-pane fade">
        <h3> Start a business </h3>
        <?php
          if(isset($_SESSION['UID'])) { //確定有沒有註冊過店家
            $shop_of_user = "SELECT `SID`, `Name`, `Type`, AsText(Location) AS `Location` FROM `shop` WHERE `UID` = '$uid'"; //這邊要撈資料庫
            $result = mysqli_query($conn, $shop_of_user);
            if(mysqli_num_rows($result) != 0) {
              $user_shop_data = mysqli_fetch_assoc($result);
              mysqli_free_result($result);
              $user_shop_name = $user_shop_data["Name"];
              $user_shop_type = $user_shop_data["Type"];
              preg_match($pattern, $user_shop_data["Location"], $matches);
              $user_shop_latitude = $matches[1];
              $user_shop_longitude = $matches[2];
              $disabled = 'disabled';
            } else {
              $user_shop_name = 'McDonald\'s';
              $user_shop_type = 'Fast Food';
              $user_shop_latitude = '24.78472733371133';
              $user_shop_longitude = '121.00028167648875';
              $disabled = '';
            }
        ?>
        <!--註冊店家-->
          <form class="form-group" id="register">
            <div class="row">
              <div class="col-xs-2">
                <label for="register-name">Shop name</label>
                <input class="form-control" id="register-name" placeholder="<?php echo $user_shop_name; ?>" type="text" onkeyup="checkShop()" <?php echo $disabled;?>>
              </div>
              <div class="col-xs-2">
                <label for="register-category">Shop category</label>
                <input class="form-control" id="register-category" placeholder="<?php echo $user_shop_type; ?>" type="text" <?php echo $disabled;?>>
              </div>
              <div class="col-xs-2">
                <label for="register-latitude">Latitude</label>
                <input class="form-control" id="register-latitude" placeholder="<?php echo $user_shop_latitude; ?>" type="number" step="any" <?php echo $disabled;?>>
              </div>
              <div class="col-xs-2">
                <label for="register-longitude">Longitude</label>
                <input class="form-control" id="register-longitude" placeholder="<?php echo $user_shop_longitude; ?>" type="number" step="any" <?php echo $disabled;?>>
              </div>
            </div>
            <!--註冊店家按鈕-->
            <div class="row" style="margin-top: 25px;">
              <div class="col-xs-3">
                <button onclick="register()" type="button" class="btn btn-primary" <?php echo $disabled;?>>register</button>
              </div>
              <span id="msg_shopname"></span>
            </div>
          </form>
          <?php
            if(!empty($user_shop_data)) {
              $sid = $user_shop_data["SID"];
              $user_meal_data = array();
              $meal_of_user = "SELECT * FROM `meal` WHERE `SID` = '$sid'";
              $result = mysqli_query($conn, $meal_of_user);
              if ($result) {
                if (mysqli_num_rows($result)>0) {
                  while ($user_meal_row = mysqli_fetch_assoc($result)) {
                    $user_meal_data[] = $user_meal_row;
                  }
                }
                mysqli_free_result($result);
              }
          ?>
            <hr>
            <h3>ADD</h3>
            <!--新增商品-->
            <form class="form-group" id="add-meal" action="add-meal.php" enctype='multipart/form-data'>
              <input type="hidden" id="meal-sid" name="sid" value="<?php print $sid; ?>">
              <div class="row">
                <div class="col-xs-6">
                  <label for="meal-name">meal name</label>
                  <input class="form-control" id="meal-name" type="text" name="name">
                </div>
              </div>
              <div class="row" style=" margin-top: 15px;">
                <div class="col-xs-3">
                  <label for="meal-price">price</label>
                  <input class="form-control" id="meal-price" type="number" name="price">
                </div>
                <div class="col-xs-3">
                  <label for="meal-quantity">quantity</label>
                  <input class="form-control" id="meal-quantity" type="number" name="quantity">
                </div>
              </div>

              <div class="row" style=" margin-top: 25px;">
                <div class="col-xs-3">
                  <label for="myFile">上傳圖片</label>
                  <input id="myFile" type="file" name="myFile" multiple class="file-loading" accept="image/jpeg">
                </div>
                <div class="col-xs-2">
                  <input style=" margin-top: 15px;" type="submit" class="btn btn-primary" value="Add">
                </div>
                <div class="col-xs-3" style="text-align: left; vertical-align: middle; height: 50px; padding-top: 25px;">
                  <span id="addmeal-msg"></span>
              </div>
              </div>
            </form>

            <div class="row">
              <div class="col-xs-8">
                <table class="table" style=" margin-top: 15px;">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Picture</th>
                      <th scope="col">meal name</th>
                      <th scope="col">price</th>
                      <th scope="col">Quantity</th>
                      <th scope="col">Edit</th>
                      <th scope="col">Delete</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      if(!empty($user_meal_data)){
                        $i = 1;
                        foreach($user_meal_data as $user_meal_key => $user_meal_row) {
                          $pid = $user_meal_row['PID'];
                          if($user_meal_row['Is_Deleted'] == 0){
                    ?>
                    
                    <tr>
                      <th scope="row"><?php echo $i; ?></th>
                      <td><img src="data:image/jpeg;base64,<?php echo $user_meal_row['Image']; ?>" width="100" alt="<?php echo $user_meal_row['Name']; ?>"></td>
                      <td><?php echo $user_meal_row['Name']; ?></td>
                      <td><?php echo $user_meal_row['Price']; ?></td>
                      <td><?php echo $user_meal_row['Amount']; ?></td>
                      <td>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#<?php echo $pid; ?>-1">
                          Edit
                        </button>
                      </td>
                      <!-- Modal -->
                      <div class="modal fade" id="<?php echo $pid; ?>-1" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="staticBackdropLabel"><?php echo $user_meal_row['Name']; ?> Edit</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <!--編輯商品-->
                            <form method="POST" action="" id="edit-meal">
                            <input type="hidden" name="pid" value="<?php print $pid; ?>">
                              <div class="modal-body">
                                <div class="row" >
                                  <div class="col-xs-6">
                                    <label>price
                                      <input name="price" class="form-control" type="number" id="edit-price-<?php print $pid; ?>">
                                    </label>
                                  </div>
                                  <div class="col-xs-6">
                                    <label>quantity
                                      <input name="quantity" class="form-control" type="number" id="edit-quantity-<?php print $pid; ?>">
                                    </label>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button class="btn btn-secondary" onclick="edit_meal(<?php print $pid; ?>)">Edit</button>
                              </div>
                            </form>

                          </div>
                        </div>
                      </div>
                      <td>
                        <!--刪除商品-->
                        <button class="btn btn-danger" onclick="delete_meal(<?php print $pid; ?>)">Delete</button>
                      </td>
                    </tr>
                    <?php 
                          $i++;
                          }
                        }
                      } else {
                        echo '<tr><td>無品項</td></tr>';
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
        <?php
            }
          } else {
            echo '<h4>未登入</h4>';
          }
        ?>
      </div>




      <!-- my order -->
      <div id="my-order" class="tab-pane fade">
        <h3>My Order</h3>
        <div class="row col-xs-12">
          <form class="form-horizontal" action="" method="GET">
            <label class="control-label col-xs-1" for="status">Status</label>
            <div class="col-xs-3">
              <select name="myorder-status" class="form-control" id="myorder-status" onchange="submit();">
                <option value="all">All</option>
                <option value="not-finished">Not Finished</option>
                <option value="finished">Finished</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </form>
          <button type="button" class="btn btn-danger" onclick="handle_multi_order(this, 'cancel')">Cancel selected orders</button>
          <div class="row">
            <div class="col-xs-12">
              <table class="table" style="margin-top: 15px;">
                <thead>
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">Order ID</th>
                    <th scope="col">Status</th>
                    <th scope="col">Start</th>
                    <th scope="col">End</th>
                    <th scope="col">Shop Name</th>
                    <th scope="col">Total Price</th>
                    <th scope="col">Order Details</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>

                <tbody>
                  <?php 
                    $my_order_data = array();
                    if (!empty($_GET["myorder-status"])){
                      $status = $_GET["myorder-status"];
                      switch($status){
                        case "all":
                          $sql = "SELECT * FROM `order` WHERE `UID`= '$uid' ";
                          break;
                        case "not-finished":
                          $sql = "SELECT * FROM `order` WHERE (`UID`= '$uid') AND (`Status` =  'Not Finished')";
                          break;
                        case "finished":
                          $sql = "SELECT * FROM `order` WHERE (`UID`= '$uid') AND (`Status` =  'Finished')";
                          break;
                        case "cancelled":
                          $sql = "SELECT * FROM `order` WHERE (`UID`= '$uid') AND (`Status` =  'Cancelled')";
                          break;
                        default:
                          $sql = "SELECT * FROM `order` WHERE `UID`= '$uid' ";
                          break;
                      }
                    } else {
                      $sql = "SELECT * FROM `order` WHERE `UID`= '$uid' ";
                    }
                    $result = mysqli_query($conn, $sql);
                    if ($result) {
                      if (mysqli_num_rows($result)>0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                          $my_order_data[] = $row;
                        }
                      }
                      mysqli_free_result($result);
                    }
                    if(!empty($my_order_data)){ //列出搜尋結果
                      foreach($my_order_data as $key => $row) {
                        $subtotal = $row['Subtotal'];
                        $delivery_fee = $row['Delivery_fee'];
                        $total_price = $row['Subtotal'] + $row['Delivery_fee'];
                        $status = $row['Status'];
                        $my_order_sid = $row['SID'];  //搜店家
                        $my_order_shop = "SELECT `Name` FROM `shop` WHERE `SID` = '$my_order_sid'"; //這邊要撈資料庫
                        $result = mysqli_query($conn, $my_order_shop);
                        $my_order_shop_data = mysqli_fetch_assoc($result);
                        mysqli_free_result($result);

                        $my_order_id = 'my-order-' . $row['OID'];
                        
                        $oid = $row['OID']; //搜品項
                        $my_order_item_data = array();
                        $my_order_item = "SELECT * FROM `order_item` WHERE `OID`= '$oid' ";
                        $result = mysqli_query($conn, $my_order_item);
                        while ($_i = mysqli_fetch_assoc($result)) {
                          $my_order_item_data[] = $_i;
                        }
                        mysqli_free_result($result);
                  ?>
                  <tr>
                    <td>
                      <?php
                        if($status == 'Not Finished') {
                          echo '<input type="checkbox" id="' . $my_order_id . '" value="' . $oid . '">';
                        }
                      ?>
                    </td>
                    <th scope="row"><?php echo ($key +1); ?></th>
                    <td><?php echo $row['Status']; ?></td>
                    <td><?php echo $row['Create_time']; ?></td>
                    <td><?php echo $row['Finish_time']; ?></td>
                    <td><?php echo $my_order_shop_data['Name']; ?></td>
                    <td><?php echo $total_price; ?></td>
                    <td>
                      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#<?php echo 'tab-' . $my_order_id; ?>">
                        Order Details
                      </button>

                      <!--modal-->
                      <div class="modal fade" id="tab-<?php echo $my_order_id; ?>" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                          <!-- Modal content-->
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title">order</h4>
                            </div>
                            <div class="modal-body">
                              <!-- 品項 -->
                              <div class="row">
                                <div class="col-xs-12">
                                  <table class="table" style="margin-top: 15px;">
                                    <thead>
                                      <tr>
                                        <th scope="col">Picture</th>
                                        <th scope="col">meal name</th>
                                        <th scope="col">price</th>
                                        <th scope="col">Order Quantity</th>
                                      </tr>
                                    </thead>

                                    <tbody>
                                      <?php
                                        foreach($my_order_item_data as $key => $item) {
                                          $pid = $item['PID'];  //搜品項詳細
                                          $sql = "SELECT `Name`,  `Price`, `Image`, `Is_Deleted` FROM `meal` WHERE `PID` = '$pid'"; //這邊要撈資料庫
                                          $result = mysqli_query($conn, $sql);
                                          $detail = mysqli_fetch_assoc($result);
                                          mysqli_free_result($result);
                                          echo '<tr>';
                                          echo '<td><img src="data:image/jpeg;base64,' . $detail['Image'] . '" width="100" alt="' . $detail['Name'] . '"></td>';
                                          $delete = $detail['Is_Deleted'] ? ' (已下架)' : '';
                                          echo '<td>' . $detail['Name'] . $delete . '</td>';
                                          echo '<td>' . $item['Price'] . '</td>';
                                          echo '<td>' . $item['Quantity'] . '</td>';
                                          echo '</tr>';
                                        }
                                      ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>

                            <div class="modal-footer">
                              Subtotal $<?php echo $subtotal; ?><br>
                              Delivery fee $<?php echo $delivery_fee; ?><br>
                              Total price $<?php echo $total_price; ?>
                            </div>

                          </div>
                        </div>
                      </div>
                      <!--modal end-->
                    </td>
                    
                    <td>
                      <?php
                        if($row['Status'] == 'Not Finished') {
                          echo '<button type="button" class="btn btn-danger" onclick="handle_order(' . $oid . ', `cancel`)">Cancel</button>';
                          // echo '<form method="POST" action="">';
                          // echo '<input type="hidden" name="delete_oid" value="' . $oid . '">';
                          // echo '<input type="submit" class="btn btn-danger" name="deleteOrder" value="Cancel">';
                          // echo '</form>';
                        }
                      ?>
                      <!--刪除訂單-->
                      
                    </td>
                  </tr>

                  <?php
                      }
                    } else {
                      echo "<tr><td>無訂單紀錄</td></tr>";
                    }
                  ?>
                </tbody>
              </table>
              
            </div>
          </div>
        </div>
      </div>



      <!--shop order-->
      <div id="shop-order" class="tab-pane fade">
        <h3>Shop Order</h3>
        <div class="row col-xs-12">
          <form class="form-horizontal" action="" method="GET">
            <label class="control-label col-xs-1" for="status">Status</label>
            <div class="col-xs-3">
              <select name="shoporder-status" class="form-control" id="shoporder-status" onchange="submit();">
                <option value="all">All</option>
                <option value="not-finished">Not Finished</option>
                <option value="finished">Finished</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </form>
          <button type="button" class="btn btn-success" onclick="handle_multi_order(this, 'finish')">Finish selected orders</button>
          <button type="button" class="btn btn-danger" onclick="handle_multi_order(this, 'cancel')">Cancel selected orders</button>
          <div class="row">
            <div class="col-xs-12">
              <table class="table" style="margin-top: 15px;">
                <thead>
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">Order ID</th>
                    <th scope="col">Status</th>
                    <th scope="col">Start</th>
                    <th scope="col">End</th>
                    <th scope="col">User Name</th>
                    <th scope="col">Total Price</th>
                    <th scope="col">Order Details</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>

                <tbody>
                  <?php 
                    $shop_order_data = array();
                    if (!empty($_GET["shoporder-status"])){
                      $actions = $_GET["shoporder-status"];
                      switch($actions){
                        case "all":
                          $sql = "SELECT * FROM `order` WHERE `SID`= '$sid' ";
                          break;
                        case "not-finished":
                          $sql = "SELECT * FROM `order` WHERE (`SID`= '$sid') AND (`Status` =  'Not Finished')";
                          break;
                        case "finished":
                          $sql = "SELECT * FROM `order` WHERE (`SID`= '$sid') AND (`Status` =  'Finished')";
                          break;
                        case "cancelled":
                          $sql = "SELECT * FROM `order` WHERE (`SID`= '$sid') AND (`Status` =  'Cancelled')";
                          break;
                        default:
                          $sql = "SELECT * FROM `order` WHERE `SID`= '$sid' ";
                          break;
                      }
                    } else {
                      $sql = "SELECT * FROM `order` WHERE `SID`= '$sid' ";
                    }
                    $result = mysqli_query($conn, $sql);
                    if ($result) {
                      if (mysqli_num_rows($result)>0) {
                        while ($_i = mysqli_fetch_assoc($result)) {
                          $shop_order_data[] = $_i;
                        }
                      }
                      mysqli_free_result($result);
                    }
                    if(!empty($shop_order_data)){ //列出搜尋結果
                      foreach($shop_order_data as $key => $row) {
                        $subtotal = $row['Subtotal'];
                        $delivery_fee = $row['Delivery_fee'];
                        $total_price = $row['Subtotal'] + $row['Delivery_fee'];

                        $shop_order_uid = $row['UID'];
                        $sql = "SELECT `First_name`, `Last_name` FROM `user` WHERE `UID` = '$shop_order_uid'"; //這邊要撈資料庫
                        $result = mysqli_query($conn, $sql);
                        $shop_order_user_data = mysqli_fetch_assoc($result);
                        mysqli_free_result($result);

                        $shop_order_id = 'shop-order-' . $row['OID'];
                        
                        $oid = $row['OID']; //搜品項
                        $shop_order_item_data = array();
                        $sql = "SELECT * FROM `order_item` WHERE `OID`= '$oid' ";
                        $result = mysqli_query($conn, $sql);
                        while ($_i = mysqli_fetch_assoc($result)) {
                          $shop_order_item_data[] = $_i;
                        }
                        mysqli_free_result($result);
                  ?>
                  <tr>
                    <td>
                      <?php
                        if($row['Status'] == 'Not Finished') {
                          echo '<input type="checkbox" id="' . $shop_order_id . '" value="' . $oid . '">';
                        }
                      ?>
                    </td>
                    <th scope="row"><?php echo ($key +1); ?></th>
                    <td><?php echo $row['Status']; ?></td>
                    <td><?php echo $row['Create_time']; ?></td>
                    <td><?php echo $row['Finish_time']; ?></td>
                    <td><?php echo $shop_order_user_data['First_name'] . ' ' . $shop_order_user_data['Last_name']; ?></td>
                    <td><?php echo $total_price; ?></td>
                    <td>
                      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#<?php echo 'tab-' . $shop_order_id; ?>">
                        Order Details
                      </button>

                      <!--modal-->
                      <div class="modal fade" id="tab-<?php echo $shop_order_id; ?>" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                          <!-- Modal content-->
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title">order</h4>
                            </div>
                            <div class="modal-body">
                              <!-- 品項 -->
                              <div class="row">
                                <div class="col-xs-12">
                                  <table class="table" style="margin-top: 15px;">
                                    <thead>
                                      <tr>
                                        <th scope="col">Picture</th>
                                        <th scope="col">meal name</th>
                                        <th scope="col">price</th>
                                        <th scope="col">Order Quantity</th>
                                      </tr>
                                    </thead>

                                    <tbody>
                                      <?php
                                        foreach($shop_order_item_data as $key => $item) {
                                          $pid = $item['PID'];  //搜品項詳細
                                          $sql = "SELECT `Name`,  `Price`, `Image`, `Is_Deleted` FROM `meal` WHERE `PID` = '$pid'"; //這邊要撈資料庫
                                          $result = mysqli_query($conn, $sql);
                                          $detail = mysqli_fetch_assoc($result);
                                          mysqli_free_result($result);
                                          echo '<tr>';
                                          echo '<td><img src="data:image/jpeg;base64,' . $detail['Image'] . '" width="100" alt="' . $detail['Name'] . '"></td>';
                                          $delete = $detail['Is_Deleted'] ? ' (已下架)' : '';
                                          echo '<td>' . $detail['Name'] . $delete . '</td>';
                                          echo '<td>' . $item['Price'] . '</td>';
                                          echo '<td>' . $item['Quantity'] . '</td>';
                                          echo '</tr>';
                                        }
                                      ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>

                            <div class="modal-footer">
                              Subtotal $<?php echo $subtotal; ?><br>
                              Delivery fee $<?php echo $delivery_fee; ?><br>
                              Total price $<?php echo $total_price; ?>
                            </div>

                          </div>
                        </div>
                      </div>
                      <!--modal end-->
                    </td>
                    
                    <td>
                      <?php
                        if($row['Status'] == 'Not Finished') {
                          // echo '<form method="POST" action="" style="display: inline;">';
                          // echo '<input type="hidden" name="oid" value="' . $oid . '">';
                          // echo '<input type="submit" class="btn btn-success" name="finishOrder" value="Done">';
                          // echo '</form>';
                          echo '<button type="button" class="btn btn-success" onclick="handle_order(' . $oid . ', `finish`)">Done</button>';
                          echo '<button type="button" class="btn btn-danger" onclick="handle_order(' . $oid . ', `cancel`)">Cancel</button>';
                          // echo '<form method="POST" action="" style="display: inline;">';
                          // echo '<input type="hidden" name="delete_oid" value="' . $oid . '">';
                          // echo '<input type="submit" class="btn btn-danger" name="deleteOrder" value="Cancel">';
                          // echo '</form>';

                        }
                      ?>
                    </td>
                  </tr>

                  <?php
                      }
                    } else {
                      echo "<tr><td>無訂單紀錄</td></tr>";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>



      <!--交易紀錄-->
      <div id="transaction-record" class="tab-pane fade">
        
        <h3>Transaction Record</h3>
        <div class="row col-xs-12">
          <form class="form-horizontal" action="" method="GET">
            <label class="control-label col-sm-1" for="actions">Action</label>
            <div class="col-sm-3">
              <select name="actions" class="form-control" id="actions" onchange="submit();">
                <option value="All">All</option>
                <option value="Payment">Payment</option>
                <option value="Receive">Receive</option>
                <option value="Recharge">Recharge</option>
              </select>
            </div>
          </form>

          <div class="row">
            <div class="col-xs-8">
              <table class="table" style="margin-top: 15px;">
                <thead>
                  <tr>
                    <th scope="col">Record ID</th>
                    <th scope="col">Action</th>
                    <th scope="col">Time</th>
                    <th scope="col">Trader</th>
                    <th scope="col">Amount Change</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <?php
                      $transaction_data = array();
                      if (!empty($_GET["actions"])){
                        $actions = $_GET["actions"];
                        switch($actions){
                          case "All":
                            $transaction = "SELECT * FROM `transaction` WHERE `UID`= '$uid' ";
                            break;
                          case "Payment":
                            $transaction = "SELECT * FROM `transaction` WHERE (`UID`= '$uid') AND (`Behavior` =  'Payment')";
                            break;
                          case "Receive":
                            $transaction = "SELECT * FROM `transaction` WHERE (`UID`= '$uid') AND (`Behavior` =  'Receive')";
                            break;
                          case "Recharge":
                            $transaction = "SELECT * FROM `transaction` WHERE (`UID`= '$uid') AND (`Behavior` =  'Recharge')";
                            break;
                          default:
                            $transaction = "SELECT * FROM `transaction` WHERE `UID`= '$uid' ";
                            break;
                        }
                      } else {
                        $transaction = "SELECT * FROM `transaction` WHERE `UID`= '$uid' ";
                      }
                      $result = mysqli_query($conn, $transaction);
                      if ($result) {
                        if (mysqli_num_rows($result)>0) {
                        while ($trans_row = mysqli_fetch_assoc($result)) {
                            $transaction_data[] = $trans_row;
                        }
                        }
                        mysqli_free_result($result);
                      }
                      
                      if(!empty($transaction_data)){ 
                        foreach($transaction_data as $trans_key => $trans_row) {  
                          echo '<tr>';  
                          echo '<th scope="row">'. ($trans_key+1) . '</th>';
                          echo '<td>' . $trans_row['Behavior'] . '</td>';
                          echo '<td>' . $trans_row['Time'] . '</td>';
                          echo '<td>' . $trans_row['Trader'] . '</td>';
                          echo '<td>' . $trans_row['Amount'] . '</td>';
                          echo '<td> </td>';
                          echo '</tr>';
                        }
                      } else {
                        echo "<tr><td>無交易紀錄</td></tr>";
                      }
                    ?>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function () {
      $(".nav-tabs a").click(function () {
        $(this).tab('show');
      });
    });
  </script>
  <script src="js/nav.js"></script>
  <style>
    .arrow {
		  display: inline-block;
		  vertical-align: middle;
		  width: 0;
		  height: 0;
		  margin-left: 5px;
		  opacity: 0.66;
		}
		
		.arrow.asc {
		  border-left: 4px solid transparent;
		  border-right: 4px solid transparent;
		  border-bottom: 4px solid black;
		}

		.arrow.dsc {
		  border-left: 4px solid transparent;
		  border-right: 4px solid transparent;
		  border-top: 4px solid black;
		}
  </style>
</body>

</html>
<?php
  }
  mysqli_close($conn);
?>