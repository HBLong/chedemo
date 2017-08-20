var _obj = {};

/*页面需要一个class="overlay"的div*/
_obj.overlay = {
    show: function() {
        $(document.body).append('<div class="overlay"></div>');
        $(".overlay").attr("style", "background: #000;filter: alpha(opacity = 30);filter: alpha(opacity = 30);opacity: 0.3;" +
                "position: absolute;top: 0px;left: 0px;"
                + "width: 100%;height: 100%;z-index: 1000;");
        //计算弹出层
        var height = $(document.body).height();
        //浏览器可视高度
        var clientHeight = $(window).height();
        if (height < clientHeight) {
            height = clientHeight;
        }
        $(".overlay").css("height", height + "px");
        //让滚动条消失
        /*
         $("html").css({
         "height": "100%",
         "overflow": "hidden"
         });
         */
        $(".overlay").css("display", "block");
    },
    hide: function() {
        //让滚动条显示
        /*
         $("html").css({
         "overflow": "auto"
         });
         */
        $(".overlay").remove();
    },
    loadingShow: function() {
        _obj.overlay.show();
        //$(document.body).append('<div id="_loading_div" style="position:fixed;z-index:1000;width:32px;height:32px;background-image:url(/image/loading_tool.gif);"></div>');
        $(document.body).append('<div class="ui_content" id="_loading_div" style="position:fixed;z-index:1000;width: 200px; height: 74px;"><div class="beinloading">正在加载...</div></div>');
        var left = ($(document).width() - 200) / 2;
        var top = ($(window).height() - 74) / 2;
        $("#_loading_div").css("top", top + "px");
        $("#_loading_div").css("left", left + "px");
    },
    loadingHide: function() {
        $("#_loading_div").remove();
        _obj.overlay.hide();
    }
}
_obj.openDialog = {
    dialogHtml: function(code, desc) {
        return '<div class="_warm_prompt" style="width: 420px;position:fixed;box-shadow: 0 0 5px #aaa;z-index:1000;display: none;">' +
                '<div class="ui_title_wrap">' +
                '<div class="ui_title">' +
                '<div class="ui_title_text">' +
                '<span class="ui_title_icon"></span>温馨提示' +
                '</div>' +
                '<div class="ui_btn_wrap">' +
                '<a class="ui_btn_close" onclick="_obj.openDialog.hide();">关闭</a>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="wp_bd">' +
                '<i class="' + (code == 0 ? "i_right" : "i_error") + '"></i>' +
                '<span style="text-align:center;">' + desc + '</span>' +
                '</div>' +
                '</div>';
    },
    dialogNoCloseHtml: function(code, desc) {
        return '<div class="_warm_prompt" style="width: 420px;position:fixed;box-shadow: 0 0 5px #aaa;z-index:1000;display: none;">' +
                '<div class="ui_title_wrap">' +
                '<div class="ui_title">' +
                '<div class="ui_title_text">' +
                '<span class="ui_title_icon"></span>温馨提示' +
                '</div>' +
                '<div class="ui_btn_wrap"></div>' +
                '</div>' +
                '</div>' +
                '<div class="wp_bd">' +
                '<i class="' + (code == 0 ? "i_right" : "i_error") + '"></i>' +
                '<span style="text-align:center;">' + desc + '</span>' +
                '</div>' +
                '</div>';
    },
    showNoClose: function(code, charNum, desc) {
        _obj.overlay.show();
        if (isNaN(charNum) || charNum < 9) {
            charNum = 9;
        }
        //计算字体宽度
        var ctxWidth = charNum * 16 + 45 + 200;
        $(document.body).append(_obj.openDialog.dialogNoCloseHtml(code, desc));
        $("._warm_prompt").css("width", ctxWidth + "px");
        _obj.openDialog.scrollOpt();
        $("._warm_prompt").css("display", "block");
    },
    show: function(code, charNum, desc) {
        _obj.overlay.show();
        if (isNaN(charNum) || charNum < 9) {
            charNum = 9;
        }
        //计算字体宽度
        var ctxWidth = charNum * 16 + 45 + 200;
        $(document.body).append(_obj.openDialog.dialogHtml(code, desc));
        $("._warm_prompt").css("width", ctxWidth + "px");
        _obj.openDialog.scrollOpt();
        $("._warm_prompt").css("display", "block");
    },
    hide: function() {
        _obj.overlay.hide();
        $("._warm_prompt").remove();
    },
    scrollOpt: function() {
        var left = ($(document).width() - $("._warm_prompt").width()) / 2;
        var top = ($(window).height() - $("._warm_prompt").height()) / 2;
        $("._warm_prompt").css("top", top + "px");
        $("._warm_prompt").css("left", left + "px");
    }
}
_obj.paging = {
    pageIndex: 1,
    pageCount: 1,
    search: function(n) {//n == pageIndex
        isNaN(n) && (n = 1), n <= 0 && (n = 1);
        var r = $("#isSearch").val();
        r == "true" ? searchLine(1) :($(".content").each(function(t) {
            $(this).css("display", "none"), n == t + 1 && $(this).css("display", "block");
        }), _obj.paging.changeShowPage({
            pageIndex:n,
            pageCount:_obj.paging.pageCount
        }));
    },
    //改变显示
    changeShowPage: function(pageData) {
        _obj.paging.pageIndex = pageData.pageIndex;
        _obj.paging.pageCount = pageData.pageCount;
        var pageCount = parseInt(pageData.pageCount);
        var index = parseInt(pageData.pageIndex);
        var html = '<a href="javascript:_obj.paging.changePage(1);">1</a>';
        if (pageCount >= 7) {
            if (index == 3) {
                html += '<a href="javascript:_obj.paging.changePage(2);">2</a>';
                html += '<a href="javascript:_obj.paging.changePage(3);">3</a>';
                html += '<a href="javascript:_obj.paging.changePage(4);">4</a>';
                html += '<a href="javascript:_obj.paging.changePage(5);">5</a>';
                html += '<a href="javascript:void(0);">...</a>';
            } else if (index > 3 && index < (pageCount - 2)) {
                html += '<a href="javascript:void(0);">...</a>';
                for (var i = (index - 1); i <= (index + 1); i++) {
                    html += '<a href="javascript:_obj.paging.changePage(' + i + ');">' + i + '</a>';
                }
                html += '<a href="javascript:void(0);">...</a>';
            } else if (index == (pageCount - 2)) {
                html += '<a href="javascript:void(0);">...</a>';
                for (var i = (pageCount - 4); i < pageCount; i++) {
                    html += '<a href="javascript:_obj.paging.changePage(' + i + ');">' + i + '</a>';
                }
            } else {
                html += '<a href="javascript:_obj.paging.changePage(2);">2</a>';
                html += '<a href="javascript:_obj.paging.changePage(3);">3</a>';
                html += '<a href="javascript:void(0);">...</a>';
                for (var i = (pageCount - 2); i < pageCount; i++) {
                    html += '<a href="javascript:_obj.paging.changePage(' + i + ');">' + i + '</a>';
                }
            }
            html += '<a href="javascript:_obj.paging.changePage(' + pageCount + ');" >' + pageCount + '</a>';
        } else {
            for (var i = 2; i <= pageCount; i++) {
                html += '<a href="javascript:_obj.paging.changePage(' + i + ');">' + i + '</a>';
            }
        }
        var pageHtml = '<a href="javascript:_obj.paging.previousPage();" class="up-page">上一页</a>';
        pageHtml += html;
        pageHtml += '<a href="javascript:_obj.paging.nextPage();" class="down-page">下一页</a>';
        pageHtml += '<span>共<strong>' + pageCount + '</strong>页 跳转到 <input type="text" id="txtGoto" onkeyup="if(event.keyCode==13){_obj.paging.changePage(this.value);}" value="' + index + '" class="txt"> 页 <input type="button" onClick="_obj.paging.changePage($(\'#txtGoto\').val());" class="btn-bg"></span>';
        $(".m-page-box .m-page").html(pageHtml);
        //页面效果
        $(".m-page-box .m-page a").each(function() {
            $(this).removeClass("cur");
            var pageNum = $(this).html();
            if (index == pageNum) {
                $(this).addClass("cur");
            }
        });
    },
    //改变页数
    changePage: function(index) {
        //判断是否数字
        if (isNaN(index)) {
            return;
        }
        index = parseInt(index);
        if (index < 1 || index > _obj.paging.pageCount) {
            return;
        }
        //已经是当前页了
        if (index == _obj.paging.pageIndex) {
            return;
        }
        _obj.paging.pageIndex = index;
        _obj.paging.search(index);
    },
    nextPage: function() {
        if (_obj.paging.pageIndex >= _obj.paging.pageCount) {
            return;
        }
        var pageIndex = _obj.paging.pageIndex + 1;
        _obj.paging.changePage(pageIndex);
    },
    previousPage: function() {
        if (_obj.paging.pageIndex <= 1) {
            return;
        }
        var pageIndex = _obj.paging.pageIndex - 1;
        _obj.paging.changePage(pageIndex);
    }
}
_obj.cookie = {
    get: function(cookieName) {
        var ckArray = document.cookie.split(";");
        for (var i = 0; i < ckArray.length; i++) {
            var keyVal = $.trim(ckArray[i]).split("=");
            if (keyVal[0] == cookieName) {
                return decodeURIComponent(keyVal[1]);
            }
        }
        return "";
    }
}
_obj.validation = {
        email: function(str) {
            var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
            return reg.test(str);
        },
        isDate8: function(sDate) {
            if (!/^[0-9]{8}$/.test(sDate)) {
                return false;
            }
            var year, month, day;
            year = sDate.substring(0, 4);
            month = sDate.substring(4, 6);
            day = sDate.substring(6, 8);
            var iaMonthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
            if (year < 1700 || year > 2500)
                return false
            if (((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0))
                iaMonthDays[1] = 29;
            if (month < 1 || month > 12)
                return false
            if (day < 1 || day > iaMonthDays[month - 1])
                return false
            return true
        },
        userName: function(inputobj) {
            var classstrok = "txtinput e_ok";
            var classstrlefterror = "div[class='e_error_notice left']";
            var classstrerror = "txtinput e_error";
            inputobj.attr("class", classstrok);
            inputobj.parent().find(classstrlefterror).eq(0).hide();
            var regChinese = /[\u4e00-\u9fa5]/;
            if ($.trim($(inputobj).val()).length == 0) {
                inputobj.parent().find(classstrlefterror).eq(0).show();
                inputobj.attr("class", classstrerror);
                inputobj.parent().find(classstrlefterror).find(".content").eq(0)
                        .text("不能为空！");
                return false;
            } else if ($.trim($(inputobj).val()).length < 2
                    || $.trim($(inputobj).val()) > 30
                    || !regChinese.test($.trim($(inputobj).val()))) {
                inputobj.parent().find(classstrlefterror).eq(0).show();
                inputobj.attr("class", classstrerror);
                inputobj.parent().find(classstrlefterror).find(".content").eq(0)
                        .text("必须是2-15个汉字");
                return false;
            }
            return true;
        },
        certificateNo: function(inputobj, widClass) {
            var classstrok = "txtinput e_ok";
            var classstrlefterror = "div[class='e_error_notice left']";
            var classstrerror = "txtinput e_error";
            inputobj.attr("class", classstrok);
            inputobj.parent().find(classstrlefterror).eq(0).hide();
            if ($.trim($(inputobj).val()).length == 0) {
                inputobj.parent().find(classstrlefterror).eq(0).show();
                inputobj.attr("class", classstrerror);
                inputobj.parent().find(classstrlefterror).find(".content").eq(0)
                        .text("不能为空！");
                return false;
                // 保险要判断年龄
            } else if (_obj.validation.isIdCardNo($.trim($(inputobj).val()))) {
                return true;
            } else {
                inputobj.parent().find(classstrlefterror).eq(0).show();
                inputobj.attr("class", classstrerror);
                inputobj.parent().find(classstrlefterror).find(".content").eq(0)
                        .text("身份证格式有问题！");
                return false;
            }
            return true;
        },
        isIdCardNo: function(num) {
            var factorArr = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5,
                    8, 4, 2, 1);
            var parityBit = new Array("1", "0", "X", "9", "8", "7", "6", "5", "4",
                    "3", "2", "x");
            var varArray = new Array();
            var intValue;
            var lngProduct = 0;
            var intCheckDigit;
            var intStrLen = num.length;
            var idNumber = num;
            if ((intStrLen != 15) && (intStrLen != 18)) {
                return false;
            }
            for (i = 0; i < intStrLen; i++) {
                varArray[i] = idNumber.charAt(i);
                if ((varArray[i] < '0' || varArray[i] > '9') && (i != 17)) {
                    return false;
                } else if (i < 17) {
                    varArray[i] = varArray[i] * factorArr[i];
                }
            }
            if (intStrLen == 18) {
                var date8 = idNumber.substring(6, 14);
                if (_obj.validation.isDate8(date8) == false) {
                    return false;
                }
                for (i = 0; i < 17; i++) {
                    lngProduct = lngProduct + varArray[i];
                }
                intCheckDigit = parityBit[lngProduct % 11];
                try {
                    if (varArray[17].toUpperCase() != intCheckDigit.toUpperCase()) {
                        return false;
                    }
                } catch (e) {
                    return false;
                }
            } else { // length is 15
                var date6 = idNumber.substring(6, 12);
                if (isDate6(date6) == false) {
                    return false;
                }
            }
            return true;
        },
        phone: function(inputobj) {
            var classstrok = "txtinput e_ok";
            var classstrlefterror = "div[class='e_error_notice left']";
            var classstrerror = "txtinput e_cha";
            inputobj.attr("class", classstrok);
            inputobj.parent().find(classstrlefterror).eq(0).hide();
            var re = /^(1+\d{10})$/;
            if ($.trim($(inputobj).val()).length == 0) {
                inputobj.parent().find(classstrlefterror).eq(0).show();
                inputobj.attr("class", classstrerror);
                inputobj.parent().find(classstrlefterror).find(".content").eq(0)
                        .text("不能为空！");
                return false;
            } else if (!re.test($.trim($(inputobj).val()))) {
                inputobj.parent().find(classstrlefterror).eq(0).show();
                inputobj.attr("class", classstrerror);
                inputobj.parent().find(classstrlefterror).find(".content").eq(0)
                        .text("手机号格式有问题！");
                return false;
            }
            return true;
        }
    }