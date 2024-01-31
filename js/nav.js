function register() {
    $.ajax({
        type: "POST", //傳送方式
        url: "register.php", //傳送目的地
        dataType: "json", //資料格式
        data: { //傳送資料
            name: $("#register-name").val(),
            category: $("#register-category").val(),
            latitude: $("#register-latitude").val(),
            longitude: $("#register-longitude").val()
        },
        success: function(data) {
            if (data.errorMsg) { //如果後端回傳 json 資料有 errorMsg
                window.alert(data.errorMsg); //顯示錯誤訊息
            } else {
                $("#register")[0].reset(); //表單清空
                window.alert("註冊成功");
                window.location.reload();
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

function checkShop() {
    $.ajax({
        url: 'Shop_check.php',  // 判斷欄位是否存在的程式
        type: 'GET',    // 傳遞的方法
        data: {
            shopname: $('#register-name').val()
        },
        error: function(xhr) {          // 設定錯誤訊息
            alert('Ajax request 發生錯誤');
        },
        success: function(response) {   // 將回傳的訊息寫入網頁中
            $('#msg_shopname').html(response);
        }
    });
}

function handle_multi_order(obj, cmd) {
    var oids = [];
    var detail = obj.parentNode.getElementsByTagName('tbody')[0];
    var orders = $(detail).children('tr');
    for(const order of orders) {
        var cbx = $(order).children().first().children()[0];
        if(cbx && cbx.checked) {
            oids.push(cbx.value);
        }
    };
    $.ajax({
        url: 'php/handle_order.php',  // 處理訂單
        type: 'GET',    // 傳遞的方法
        data: {
            oids: JSON.stringify(oids),
            cmd: cmd
        },
        error: function(xhr) {          // 設定錯誤訊息
            alert('Ajax request 發生錯誤');
        },
        success: function(data) {
            window.alert(data);
            window.location.reload();
        }
    });
}

function handle_order(oid, cmd) {
    var oids = [oid];
    $.ajax({
        url: 'php/handle_order.php',  // 處理訂單
        type: 'GET',    // 傳遞的方法
        data: {
            oids: JSON.stringify(oids),
            cmd: cmd
        },
        error: function(xhr) {          // 設定錯誤訊息
            alert('Ajax request 發生錯誤');
        },
        success: function(data) {
            window.alert(data);
            window.location.reload();
        }
    });
}

function check_location() {
    if($('#latitude').val() == '' || $('#longitude').val() == '') {
        alert('欄位不可為空白');
        return false;
    } else if($('#latitude').val() < -90 || $('#latitude').val() > 90 || $('#longitude').val() < -180 || $('#longitude').val() > 180) {
        alert('經緯度超出範圍');
        return false;
    }
    return true;
}

function add_value(uid) {
    $.ajax({
        url: 'php/add_value.php',  // 儲值
        type: 'GET',    // 傳遞的方法
        data: {
            uid: uid,
            value: $('#value').val()
        },
        error: function(xhr) {          // 設定錯誤訊息
            alert('Ajax request 發生錯誤');
        },
        success: function(data) {
            window.alert(data);
            window.location.reload();
        }
    });
}

function edit_meal(pid) {
    var price = $('#edit-price-' + pid).val();
    var quantity = $('#edit-quantity-' + pid).val();
    if(price == '' || quantity == '') {
        alert('欄位不可為空白');
    } else if(price <= 0 || quantity <= 0) {
        alert('價錢及數量請填寫大於零的整數');
    } else {
        $.ajax({
            url: 'php/edit_meal.php',  // 處理訂單
            type: 'GET',    // 傳遞的方法
            data: {
                pid: pid,
                price: price,
                quantity: quantity
            },
            error: function(xhr) {          // 設定錯誤訊息
                alert('Ajax request 發生錯誤');
            },
            success: function(data) {
                window.alert(data);
                window.location.reload();
            }
        });
    }
}

function delete_meal(pid) {
    $.ajax({
        url: 'php/delete_meal.php',  // 處理訂單
        type: 'GET',    // 傳遞的方法
        data: {
            pid: pid,
        },
        error: function(xhr) {          // 設定錯誤訊息
            alert('Ajax request 發生錯誤');
        },
        success: function(data) {
            window.alert(data);
            window.location.reload();
        }
    });
}

$(function() {
    switch_tab();
    selected_option();
    $("#add-meal").on("submit", function(e) {
        e.preventDefault();
        var msg = $('#addmeal-msg');
        if($('#meal-name').val() == '' || $('#meal-price').val() == '' || $('#meal-quantity').val() == '') {
            msg.text('欄位不可為空白');
            // return false;
        } else if($('#myFile').val() == '') {
            msg.text('請上傳圖片');
            // return false;
        } else if($('#meal-price').val() <= 0 || $('#meal-quantity').val() <= 0) {
            msg.text('價錢及數量請填寫大於零的整數');
            // return false;
        } else {
            var formData = new FormData(this);
            formData.append('sid', $("#meal-sid").val());
            formData.append('name', $("#meal-name").val());
            formData.append('price', $("#meal-price").val());
            formData.append('quantity', $("#meal-quantity").val());
            $.ajax({
                url: $(this).attr("action"),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    window.location.reload();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.warn(XMLHttpRequest.responseText);
                    alert(XMLHttpRequest.status);
                    alert(XMLHttpRequest.readyState);
                    alert(textStatus);
                    alert(errorThrown);
                }
            });
        }
    });
});

function switch_tab() {
    var hash = window.location.hash.substring(1);
    if (hash == '') {
        hash = 'home';
    }
    var tab_content = document.getElementById(hash);
    var tab = document.getElementById(hash+'-tab');
    tab.className = 'active';
    tab_content.className += ' in active';
}

function hide_empty_variable() {
    var form = document.getElementById('search');
    var allInputs = form.getElementsByTagName('input');
    var input, i;

    for(i = 0; i<allInputs.length; i++) {
        input = allInputs[i];
        if(input.getAttribute('name') && !input.value) {
            input.setAttribute('name', '');
        }
    }
    var select = form.getElementsByTagName('select')[0];
    if(select.getAttribute('name') && !select.value) {
        select.setAttribute('name', '');
    }
}

function selected_option() {
    const params = new URLSearchParams(window.location.search);
    const actions = params.get("actions");
    var select = document.getElementById('actions');
    var options = select.getElementsByTagName('option');
    for (var i=0; i<options.length; i++) {
        if (options[i].value == actions) {
            options[i].setAttribute('selected', true); 
        }
    }
    var status = params.get("myorder-status");
    select = document.getElementById('myorder-status');
    options = select.getElementsByTagName('option');
    for (var i=0; i<options.length; i++) {
        if (options[i].value == status) {
            options[i].setAttribute('selected', true); 
        }
    }
    status = params.get("shoporder-status");
    select = document.getElementById('shoporder-status');
    options = select.getElementsByTagName('option');
    for (var i=0; i<options.length; i++) {
        if (options[i].value == status) {
            options[i].setAttribute('selected', true); 
        }
    }
}

function order_num(pid, cmd) {
    var span = document.getElementById('order-qty-'+pid);
    var num = parseInt(span.innerHTML, 10);
    if (cmd == 'add') {
        num += 1;
    } else if (cmd == 'del' && num > 0) {
        num -= 1;
    }
    span.innerHTML = num;
}

function show_preview(obj) {
    var order = document.getElementById('order-form');  //提交的form
    var order_content = document.getElementById('order-content');  //預覽顯示
    var order_detail = order.getElementsByClassName('item-detail')[0];  //提交form的商品input
    var detail = obj.parentNode.parentNode.getElementsByTagName('tbody')[0];  //商品一覽

    const type = obj.parentNode.getElementsByTagName('select')[0];  //外送/自取
    var delivery = Number(detail.getAttribute('delivery'));  //運費
    if(type.value == 'Pick-up') {
        delivery = 0;
    }
    const items = detail.getElementsByTagName('tr');
    var order_content_html = '';
    var order_detail_html = '';
    var subtotal = 0;
    var is_order = false;
    var count = 0;
    for (var i=0; i<items.length; i++) {
        const item = items[i];
        const pid = item.getAttribute('pid');
        const quantity = item.children[5].getElementsByTagName('span')[0].innerHTML;
        if (quantity != '0') {
            is_order = true;
            const img = item.children[1].outerHTML;
            const meal_name = item.children[2].outerHTML;
            const price = item.children[3];
            subtotal += Number(price.innerHTML) * Number(quantity);
            order_content_html += '<tr>' + img + meal_name + price.outerHTML + '<td>' + quantity + '</td></tr>';
            order_detail_html += '<input name="' + count + '" value="' + pid + ' ' + quantity + '">';
            count++;
        }
    }
    if (is_order) {
        $('#order').modal('show');
    } else {
        alert('請至少訂購一項');
    }
    order_content.innerHTML = order_content_html;
    order_detail.innerHTML = order_detail_html;
    document.getElementById('detail-subtotal').innerHTML = subtotal;
    document.getElementById('detail-delivery-fee').innerHTML = delivery;
    document.getElementById('detail-total').innerHTML = subtotal + delivery;
    
    order["type"].value = type.value;
    order["sid"].value = detail.getAttribute('sid');
    order["delivery-fee"].value = delivery;
}