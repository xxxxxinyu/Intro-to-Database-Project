function login() {
    $.ajax({
        type: "POST", //傳送方式
        url: "login.php", //傳送目的地
        dataType: "json", //資料格式
        data: { //傳送資料
            Account: $("#Account").val(),
            password: $("#password").val()
        },
        success: function(data) {
            if (data.errorMsg) { //如果後端回傳 json 資料有 errorMsg
                window.alert(data.errorMsg); //顯示錯誤訊息
            } else {
                window.location = "./nav.php"; //成功登入就跳轉
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.warn(XMLHttpRequest.responseText);
            alert(XMLHttpRequest.status);
            alert(XMLHttpRequest.readyState);
            alert(textStatus);
            alert(errorThrown);
        }
    })
}