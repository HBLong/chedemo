function setCookie(c_name, value, expiredays) {
    var exdate = new Date()
    exdate.setDate(exdate.getDate() + expiredays)
    document.cookie=c_name+ "=" + escape(value) + ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start!=-1) { 
            c_start = c_start + c_name.length+1 
            c_end = document.cookie.indexOf(";",c_start)
            if (c_end == -1) c_end = document.cookie.length
            return unescape(document.cookie.substring(c_start, c_end))
        } 
    }
    return "";
}
var NEW_ORDER_INTERVAL = 60000;
/*
 * 检查订单
 */
function checkOrder() {
    $.get(check_order_url, '', checkOrderResponse, "JSON");
}

/* *
 * 处理检查订单的反馈信息
 */
function checkOrderResponse(result) {
    setTimeout(checkOrder, NEW_ORDER_INTERVAL);
    //出错屏蔽
    if (result.error != 0 || (result.new_orders == 0 && result.new_paid == 0)) {
        return;
    }
    $("#spanNewOrder").text(result.new_orders);
    $("#spanNewPaid").text(result.new_paid);
    require([__JS__ + '/layer/layer.js'], function(layer) {
        layer.open({
            type: 1,
            title: "新订单提醒",
            closeBtn: 2, //不显示关闭按钮
            shade: 0,
            area: ['250px', '150px'],
            offset: 'rb', //右下角弹出
            time: 5000, //2秒后自动关闭
            shift: 6,
            content: $("#popCheckOrder")
        });
    });
}


document.getCookie = function(sName) {
    // cookies are separated by semicolons
    var aCookie = document.cookie.split("; ");
    for (var i = 0; i < aCookie.length; i++) {
        // a name/value pair (a crumb) is separated by an equal sign
        var aCrumb = aCookie[i].split("=");
        if (sName == aCrumb[0]) {
            return decodeURIComponent(aCrumb[1]);
        }
    }
    // a cookie with the requested name does not exist
    return null;
}

document.setCookie = function(sName, sValue, sExpires) {
    var sCookie = sName + "=" + encodeURIComponent(sValue);
    if (sExpires != null) {
        sCookie += "; expires=" + sExpires;
    }
    document.cookie = sCookie;
}

document.removeCookie = function(sName, sValue) {
    document.cookie = sName + "=; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}
