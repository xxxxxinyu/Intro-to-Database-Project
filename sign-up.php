<?php
    require_once("conn.php");
    @$f_name = $_POST["fname"];
    @$l_name = $_POST["lname"];
    @$account = $_POST["account"];
    @$phone_num = $_POST["phonenumber"];
    @$password = $_POST["password"];
    $hash_passsword = hash("sha256", $password);
    @$re_password = $_POST["re-password"];
    @$latitude = $_POST["latitude"];
    @$longitude = $_POST["longitude"];
    $sql = "INSERT INTO `user` (`Account`, `Password`, `First_name`, `Last_name`, `Location`, `Phone_number`) VALUES (?, ?, ?, ?, ST_GeomFromText('POINT(" .$longitude. " " .$latitude. ")', 0), ?)";
    $_sql = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($_sql, "sssss", $account, $hash_passsword, $f_name, $l_name, $phone_num);
    if(mysqli_stmt_execute($_sql)){
        echo "<script>alert('註冊成功')</script>";
        header("refresh:1; url=index.html");
        exit();
    }
    else{
        echo "Error creating table: " . mysqli_error($conn);
    }
?>