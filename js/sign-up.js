function check_select(obj) {
    var msg = $(obj).parent().find('span');
    if($(obj).val() == '') {
        msg.text('欄位不可為空白');
        return false;
    }
    if(obj.id == 're-password') {
        if($('#re-password').val() != $('#password').val()) {
            msg.text('請輸入相同密碼');
            return false;
        } else {
            msg.text('');
        }
    } else if(obj.id == 'password') {
        if($('#password').val().match(/[^a-zA-Z0-9]+/)) {
            msg.text('只能輸入英數');
            return false;
        } else {
            msg.text('');
        }
        var re_msg = $('#re-password').parent().find('span');
        if($('#re-password').val() != $('#password').val() && $('#re-password').val() != '') {
            re_msg.text('請輸入相同密碼');
            return false;
        } else {
            re_msg.text('');
        }
    } else if(obj.id == 'Account') {
        if($('#Account').val().match(/[^a-zA-Z0-9]+/)) {
            msg.text('只能輸入英數');
            return false;
        }
    } else if(obj.id == 'phonenumber') {
        if(!$('#phonenumber').val().match(/^\d{10}$/)) {
            msg.text('請輸入十位數字');
            return false;
        } else {
            msg.text('');
        }
    } else if(obj.id == 'latitude') {
        if($('#latitude').val() > 90 ||$('#latitude').val() < -90 ) {
            msg.text('請輸入正確範圍');
            return false;
        } else {
            msg.text('');
        }
    } else if(obj.id == 'first-name' || obj.id == 'last-name') {
        if($(obj).val().match(/[^a-zA-Z]+/)) {
            msg.text('只能輸入英文');
            return false;
        } else {
            msg.text('');
        }
    } else if(obj.id == 'longitude') {
        if($('#longitude').val() > 180 ||$('#longitude').val() < -180 ) {
            msg.text('請輸入正確範圍');
            return false;
        } else {
            msg.text('');
        }
    }
    return true;
}

function checkAcc() {
    if(check_select($('#Account')[0])) {
        $.ajax({
            url: 'Account_check.php',  // 判斷欄位是否存在的程式
            type: 'GET',    // 傳遞的方法
            dataType: "json", //資料格式
            data: {
                username: $('#Account').val()
            },
            error: function(xhr) {          // 設定錯誤訊息
                alert('Ajax request 發生錯誤');
            },
            success: function(data) {   // 將回傳的訊息寫入網頁中
                if (data.errorMsg) { //如果後端回傳 json 資料有 errorMsg
                    $('#msg_username').html(data.errorMsg);
                    console.log('Account: false');
                    return false;
                } else {
                    $('#msg_username').html(data.res);
                    console.log('Account: true');
                    return true;
                }
            }
        });
    }
}

function check_submit(obj) {
    console.log('check_submit called');
    var inputs = $('input', $(obj));
    var isCorrect = true;
    inputs.each(function(){
        if(!check_select(this)){
            isCorrect = false;
        }
    });
    if(checkAcc()) {
        isCorrect = false;
        console.log('checkAcc false');
    }
    console.log(isCorrect);
    return isCorrect;
}