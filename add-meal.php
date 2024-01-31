<?php
require_once("conn.php");
if ($_SERVER['REQUEST_METHOD'] == "POST") { //如果是 POST 請求
    $sid = $_POST["sid"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];
    $file = fopen($_FILES["myFile"]["tmp_name"], "rb");
    $fileContents = fread($file, filesize($_FILES["myFile"]["tmp_name"])); 
    fclose($file);
    $fileContents = base64_encode($fileContents);
    
    $add_meal = "INSERT INTO `meal` (`Name`, `Price`, `Amount`, `Image`, `SID`) VALUES(?, '$price', '$quantity', '$fileContents', '$sid')";
    $stmt = mysqli_prepare($conn, $add_meal);
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    echo json_encode(array(
        'res' => 1
    ));
}
?>

