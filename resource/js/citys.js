;
(function($) {
    $.fn.citys = function(options) {
        var opts = $.extend({}, $.fn.citys.defaults, options);
        return this.each(function() {
            var $this = $(this);
            var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
            //绑定事件
            $("#startCityName").bind("focus", function() {
                var t = $("#startCityName").attr("title");
                if ($("#startCityName").val() == t) {
                    $("#startCityName").val("");
                }
            });


            //绑定事件
            $("#endCityName").bind("focus", function() {
                var t = $("#endCityName").attr("title");
                if ($("#endCityName").val() == t) {
                    $("#endCityName").val("");
                }
                if (o.endCityResult.length == 0) {
                    var startCityValue = $("#startCityName").val();
                    if ($.trim(startCityValue).length > 0) {
                        ComboView.endCity.reqData(startCityValue);
                    }
                }
            });

            $("#startCityName").bind("keyup", function(e) {
                if (e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) {

                } else {
                    ComboView.startCity.show();
                }
                if (e.keyCode == 13) {
                    if (ComboView.isHidden()) {
                        return;
                    }
                    var v = ComboView.getSelectedValue();
                    $("#endCityName").attr("inputIsSubmit", "1");
                    $("#endCityName").val(v);
                    ComboView.startCity.hide();
                    ComboView.endCity.reqData(v);
                    //清空目的地城市
                    $("#endCityName").val($("#endCityName").attr("title"));
                } else {
                    ComboView.seletedEvent(e);
                }
                e.stopPropagation();
            });

            $("#endCityName").bind("keyup", function(e) {
                var value = $("#endCityName").val();
                if (value == null || value == "") {
                    ComboView.endCity.hide();
                    return;
                }
                if (e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) {
                } else {
                    ComboView.endCity.show(value);
                }
                if (e.keyCode == 13) {
                    if (ComboView.isHidden()) {
                        return;
                    }
                    var v = ComboView.getSelectedValue();
                    $("#endCityName").attr("inputIsSubmit", "1");
                    $("#endCityName").val(v);
                    ComboView.endCity.hide();
                } else {
                    ComboView.seletedEvent(e);
                }
                e.stopPropagation();
            });
            $(document).bind("click", function(e) {
                var t = e.target;
                if (!ComboView.isHidden()) {
                    var t = $("#cityToolBar").attr("cityType");
                    var v = ComboView.getSelectedValue();
                    if (t == "start") {
                        ComboView.endCity.reqData(v);
                        $("#startCityName").attr("inputIsSubmit", "1");
                        $("#startCityName").val(v);
                        ComboView.startCity.hide();
                        $("#endCityName").val($("#endCityName").attr("title"));
                    }
                    else if (t == "end") {
                        $("#endCityName").attr("inputIsSubmit", "1");
                        $("#endCityName").val(v);
                        ComboView.endCity.hide();
                    } else {
                    }
                }
            });
            var ComboView = {
                searchCitys: function(py) {
                    console.log(o.url);
                },
                isHidden: function() {
                    if ($("#cityToolBar").css("display") == "none") {
                        return true;
                    } else {
                        return false;
                    }
                },
                insertHtml: function(arr, v) {
                    if (arr.length > 0) {
                        $("#cityToolBar").html(_.template($('#endCitys-template').html(), {data: arr}));
                    } else {
                        $("#cityToolBar").html("<p class='snodate'>没有找到：“<span style='color:#ff7600;font-family:Arial'>" + v + "</span>”的匹配结果</p>");
                    }
                },
                hover: function() {
                    $("#cityToolBar li").hover(function() {
                        $("#cityToolBar li").each(function() {
                            $(this).removeClass("hover");
                        });
                        $(this).addClass("hover");
                    });
                },
                seletedEvent: function(e) {
                    /*up*/
                    if (e.keyCode == 38) {
                        var selectedIndex = 0;
                        $("#cityToolBar ul li").each(function(index) {
                            var cla = $(this).attr("class");
                            if (cla == "hover" && index > 0) {
                                selectedIndex = index - 1;
                            }
                            $(this).removeClass("hover");
                        });
                        $($("#cityToolBar ul li")[selectedIndex]).addClass("hover");
                    }
                    /*down*/
                    else if (e.keyCode == 40) {
                        var selectedIndex = 0;
                        var liLength = $("#cityToolBar ul li").length;
                        $("#cityToolBar ul li").each(function(index) {
                            var cla = $(this).attr("class");
                            if (cla == "hover" && index < (liLength - 1)) {
                                selectedIndex = index + 1;
                            }
                            $(this).removeClass("hover");
                        });
                        $($("#cityToolBar ul li")[selectedIndex]).addClass("hover");
                    }
                },
                startCity: {
                    show: function() {
                        $("#cityToolBar").css("left", $("#startCityName").offset().left + "px");
                        $("#cityToolBar").css("top", $("#startCityName").offset().top + 35 + "px");
                        var v = ComboView.getInputValue($("#startCityName").val());
                        if (v == null || v == "") {
                            $("#cityToolBar").hide();
                            return;
                        }

                        //过滤符合条件的数据
                        var arr = _.filter(d, function(a) {
                            return  a.simplepy.toLowerCase().indexOf(v.toLowerCase()) == 0
                                    || a.pinyin.toLowerCase().indexOf(v.toLowerCase()) == 0
                                    || a.name.toLowerCase().indexOf(v.toLowerCase()) == 0;
                        });
                        ComboView.insertHtml(arr, v);
                        $("#cityToolBar li").bind("click", function(e) {
                            ComboView.endCity.reqData($(this).attr("title"));
                            $("#startCityName").attr("inputIsSubmit", "1");
                            $("#startCityName").val($(this).attr("title"));
                            $("#cityToolBar").hide();
                            $("#endCityName").val($("#endCityName").attr("title"));
                            e.stopPropagation();
                        });
                        ComboView.hover();
                        $("#cityToolBar").attr("cityType", "start");
                        $("#cityToolBar").show();
                    },
                    hide: function() {
                        $("#cityToolBar").attr("cityType", "");
                        $("#cityToolBar").html("");
                        $("#cityToolBar").hide();
                    }
                },
                endCity: {
                    reqData: function(c) {
                        if (c == null || c == "") {
                            return;
                        }
                        $.ajax({
                            url: o.url,
                            type: 'POST',
                            data: {'startCityName': c},
                            dataType: 'json',
                            success: function(json) {
                                o.endCityResult = json;
                            }
                        });
                    },
                    show: function(cityName) {
                        if (cityName == null || cityName == "") {
                            return;
                        }
                        var value = ComboView.getInputValue($("#endCityName").val());
                        if (value == null || value == "") {
                            $("#cityToolBar").hide();
                            return;
                        }

                        var arr = o.endCityResult;
                        if (arr.length > 0) {
                            //过滤符合条件的数据
                            var data = _.filter(arr, function(a) {
                                return  a.simplepy.toLowerCase().indexOf(value.toLowerCase()) == 0 || a.pinyin.toLowerCase().indexOf(value.toLowerCase()) == 0 || a.name.toLowerCase().indexOf(value.toLowerCase()) == 0;
                            });
                            ComboView.insertHtml(data, value);
                            $("#cityToolBar").css("left", $("#endCityName").offset().left + "px");
                            $("#cityToolBar").css("top", $("#endCityName").offset().top + 35 + "px");
                            $("#cityToolBar li").bind("click", function() {
                                $("#endCityName").attr("inputIsSubmit", "1");
                                $("#endCityName").val($(this).attr("title"));
                                $("#cityToolBar").hide();
                            });
                            ComboView.hover();
                            $("#cityToolBar").attr("cityType", "end");
                            $("#cityToolBar").show();
                        }
                    },
                    hide: function() {
                        $("#cityToolBar").attr("cityType", "");
                        $("#cityToolBar").html("");
                        $("#cityToolBar").hide();
                    }

                },
                getSelectedValue: function() {
                    var i = 0;
                    $("#cityToolBar ul li").each(function(index) {
                        var cla = $(this).attr("class");
                        if (cla == "hover") {
                            i = index;
                            return false;
                        }
                    });
                    var v = $($("#cityToolBar li")[i]).attr("title");
                    return v;
                },
                //针对qq输入法优化
                getInputValue: function(v) {
                    if (v.indexOf("'") >= 0) {
                        v = v.replace("'", "");
                    }
                    return v;
                }
            };

        });
    };
    $.fn.citys.defaults = {
        url: '222',
        endCityResult: []
    };
})(jQuery);
