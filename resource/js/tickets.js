function searchLine(n) {
    var r = $("#hideStartCityName").val();
    if (r == null || r == "") {
        return;
    }
    var i = $("#hideEndCityName").val();
    if (i == null || i == "") {
        return;
    }
    var s = $("#hideStartDate").val(), u = $("#hideOrderBy").val(), es = $("#hideEndStationId").val(), ss = $("#hideStartStationId").val(), l = getTimeCondition(), c = getStationCondition();
    $("#calendar_ul ul li").unbind("click"),
            $("#localSearchNoResult").css("display", "none"),
            $("#timeOutResult").css("display", "none"), searchStart(), clearStation(),
            $.ajax({
                url: query_url,
                type: "POST",
                timeout: 2e4,
                data: {
                    startCityName: r,
                    endCityName: i,
                    startDate: s,
                    end_station_id: es,
                    start_station_id: ss,
                    pageIndex: n,
                    order: u,
                    timeInterval: l,
                    stationName: c
                },
                success: function(r) {
                    $("#isSearch").val("false"), $("#content").html(r), fr.dataHtml = $(".lineitem").html(),
                            $("#calendar_ul ul li").unbind("click").on("click", function() {
                        selectDate($(this).attr("title"));
                    }), searchEnd(), initStation(), boundEvent();
                    var i = $("#pageCount").val();
                    typeof i == "undefined" && (i = 1), _obj.paging.changeShowPage({
                        pageIndex: n,
                        pageCount: i
                    });
                    var s = $("#dataCount").val();
                    typeof s == "undefined" && (s = 0), $("#scheduleCount").html(s), moreBox();
                },
                error: function() {
                    searchEnd(), $("#calendar_ul ul li").unbind("click").on("click", function() {
                        selectDate($(this).attr("title"));
                    }), $("#content").css("display", "none"), $("#timeOutResult").css("display", "block");
                }
            });
}
function initStation() {
    var t = $("#stationList").val();
    typeof t == "undefined" ? t = 0 : t = parseInt(t), t > 9 ? ($("#moreStation").attr("class", "control up"),
            $("#moreStation").css("display", "block"), $(".morebox").css("height", "60px"),
            $("#moreStation").unbind("click").on("click", function() {
        moreStation();
    })) : $("#moreStation").css("display", "none"), $(".morebox ul").html($("#station_list").html());
}
function moreStation() {
    var t = $("#stationList").val();
    typeof t == "undefined" ? t = 0 : t = parseInt(t);
    if (t <= 9)
        return;
    var n = Math.ceil((t - 1) / 4) * 30, r = $("#moreStation").attr("class");
    r.indexOf("up") >= 0 ? ($("#moreStation").attr("class", "control down"), $(".morebox").css("height", n + "px")) : ($("#moreStation").attr("class", "control up"),
            $(".morebox").css("height", "60px"));
}
function searchStart() {
    loadingShow(), $("#content").css("display", "none");
}

function loadingHide() {
    $("#loadingImage").css("display", "none");
}
function loadingShow() {
    $("#loadingImage").css("display", "block");
}

function clearStation() {
    $("#moreStation").css("display", "none"), $(".morebox ul").html("");
}
function searchEnd() {
    loadingHide(), $("#content").css("display", "block");
}
function selectDate(n) {
    var r = 0, i = $("#preDate").html();
    typeof i == "undefined" ? i = 11 : i = parseInt(i), $("#calendar_ul ul li").each(function() {
        var t = $(this).attr("title");
        r++;
        if (t == n)
            return !1;
    }), r <= i && $("#calendar_ul ul li").each(function() {
        var s = $(this).attr("title");
        s == n ? ($(this).addClass("current"), $("#hideStartDate, #startDate").val(n), $("#isSearch").val("true"),
                r < i && (clearSearchCondition(),
                        _obj.paging.search())) : $(this).removeClass("current");
    });
    if (r > 11) {
        var s = -100 * (r - 11);
        $("#calendar_ul ul").css("left", s + "px");
    }
    r >= i && (i > 8 && $(".icon_cal_left").removeClass("disable"), clearSearchCondition(),
            _obj.paging.search());
}

function boundEvent() {
    var t = $("#hideStartCityName").val(), 
        r = $("#hideEndCityName").val(), 
        i = $("#hideStartDate").val();
    t != null && t != "" && r != null && r != "" && $(".btn_y80").unbind("click").on("click", function() {
        var s = $(this).attr("datatype");
        s == "1" && a.booking.userOrderCheck($(this).attr("dataId"));
    }), 
    //searchPrice(), 
    $("#startTimeOrderBy").unbind("click").on("click", function() {
        startTime();
    }), 
    $("#ticketPriceOrderBy").unbind("click").on("click", function() {
        ticketPrice();
    });
}

function timeBox() {
    $(".searchselect .timebox .no_lim div").unbind("click").on("click", function() {
        $(this).attr("class").indexOf("on") < 0 && ($(this).addClass("on"), $(".searchselect .timebox .icbox").each(function() {
            $(this).removeClass("ckbox");
        }), fr.execute());
    }), $(".searchselect .timebox .icbox").unbind("click").on("click", function() {
        var t = $(this).attr("class");
        if (t.indexOf("ckbox") >= 0) {
            $(this).removeClass("ckbox");
            var n = 0;
            $(".searchselect .timebox .icbox").each(function() {
                if ($(this).attr("class").indexOf("ckbox") >= 0)
                    return n++, !1;
            }), n == 0 && $(".searchselect .timebox .no_lim div").addClass("on");
        } else
            $(this).addClass("ckbox"), $(".searchselect .timebox .no_lim div").removeClass("on");
        fr.execute();
    });
}
function moreBox() {
    $(".searchselect .morebox .no_lim div").unbind("click").on("click", function() {
        $(this).attr("class").indexOf("on") < 0 && ($(this).addClass("on"), $(".searchselect .morebox .icbox").each(function() {
            $(this).removeClass("ckbox");
        }), fr.execute());
    }), $(".searchselect .morebox .icbox").unbind("click").on("click", function() {
        var t = $(this).attr("class");
        if (t.indexOf("ckbox") >= 0) {
            $(this).removeClass("ckbox");
            var n = 0;
            $(".searchselect .morebox .icbox").each(function() {
                if ($(this).attr("class").indexOf("ckbox") >= 0)
                    return n++, !1;
            }), n == 0 && ($(".searchselect .morebox .no_lim div").addClass("on"));
        } else
            $(this).addClass("ckbox"), $(".searchselect .morebox .no_lim div").removeClass("on");
        fr.execute();
    });
}
function getTimeCondition() {
    var t = "";
    return $(".searchselect .timebox .icbox").each(function() {
        var n = $(this).attr("class");
        n.indexOf("ckbox") >= 0 && (t += $(this).attr("datatype") + ",");
    }), t;
}
function getStationCondition() {
    var t = "";
    return $(".searchselect .morebox .icbox").each(function() {
        var n = $(this).attr("class");
        n.indexOf("ckbox") >= 0 && (t += $(this).attr("datatype") + ",");
    }), t;
}

function clearSearchCondition() {
    $(".searchselect .timebox .icbox").each(function() {
        var t = $(this).attr("class");
        t.indexOf("ckbox") >= 0 && $(this).removeClass("ckbox");
    }), $(".searchselect .morebox .icbox").each(function() {
        var t = $(this).attr("class");
        t.indexOf("ckbox") >= 0 && $(this).removeClass("ckbox");
    }), $("#hideOrderBy").val("");
}
function searchPrice() {
    $(".sprice").unbind("click").on("click", function() {
        var n = $(this), r = n.attr("dataId");
        _obj.overlay.loadingShow(), $.ajax({
            url: o + "/index/remainTicket.html",
            type: "POST",
            data: {
                scheduleId: r
            },
            success: function(t) {
                var r = $.parseJSON(t);
                if (r.rsCode == "0") {
                    var i = 0;
                    try {
                        i = parseFloat(r.ctx.fullPrice).toFixed(1);
                    } catch (s) {
                        i = r.ctx.fullPrice;
                    }
                    n.parent().html('<span class="price"><em>¥</em>' + i + "</span>");
                } else
                    n.parent().parent().find(".buy-btn").attr("class", "btn_g80 buy-btn"), n.parent().html('<span class="price"><em>¥</em>--</span>');
            },
            complete: function() {
                _obj.overlay.loadingHide();
            }
        });
    });
}

function startTime() {
    fr.execute(1);
}
function ticketPrice() {
    fr.execute(2);
}
var fr = {
    dataHtml: "",
    getSelectedTime: function() {
        var t = "";
        return $(".searchselect .timebox .icbox").each(function() {
            var n = $(this).attr("class");
            n.indexOf("ckbox") >= 0 && (t += $(this).attr("datatype") + ",");
        }), t;
    },
    getSelectedStation: function() {
        var t = "";
        return $(".searchselect .morebox .icbox").each(function() {
            var n = $(this).attr("class");
            n.indexOf("ckbox") >= 0 && (t += $(this).attr("datatype") + ",");
        }), t;
    },
    receiveDataArray: function() {
        var t = fr.dataHtml, n = $(t).find(".time"), r = $(t).find(".startStation"), i = $(t).find(".price"), s = new Array();
        for (var o = 0; o < n.length; o++)
            s[o] = $(n[o]).html() + "_" + $(r[o]).html() + "_" + $(i[o]).find("span").html() + "_" + o;
        return s;
    },
    orderBy: {
        startTime: function(t) {
            var n = $("#startTimeOrderBy").attr("class");
            n.indexOf("i1") >= 0 ? ($("#startTimeOrderBy").removeClass("i1"), $("#startTimeOrderBy").addClass("i2")) : ($("#startTimeOrderBy").removeClass("i2"),
                    $("#startTimeOrderBy").addClass("i1"));
            if (t.length <= 1)
                return t;
            var r = new Array();
            for (var i = 0; i < t.length; i++) {
                var s = t[i].split("_");
                r[i] = s[0] + "_" + i + "_" + s[3];
            }
            r = r.sort();
            var o = new Array();
            if (n.indexOf("i1") >= 0)
                for (var i = 0; i < r.length; i++) {
                    var u = parseInt(r[i].split("_")[1]);
                    o[i] = t[u];
                }
            else {
                var a = 0;
                for (var i = r.length - 1; i >= 0; i--) {
                    var u = parseInt(r[i].split("_")[1]);
                    o[a] = t[u], a++;
                }
            }
            return o;
        },
        ticketPrice: function(t) {
            var n = $("#ticketPriceOrderBy").attr("class");
            n.indexOf("i1") >= 0 ? ($("#ticketPriceOrderBy").removeClass("i1"), $("#ticketPriceOrderBy").addClass("i2")) : ($("#ticketPriceOrderBy").removeClass("i2"),
                    $("#ticketPriceOrderBy").addClass("i1"));
            if (t.length <= 1)
                return t;
            var r = new Array(), i = new Array();
            for (var s = 0; s < t.length; s++) {
                var o = t[s].split("_");
                try {
                    price = parseFloat(o[2]) * 1e4 + "_" + s + "_" + o[3], i[s] = price;
                } catch (u) {
                    i[s] = s + "_" + s + "_" + o[3];
                }
            }
            i = i.sort();
            var a = 0, f = 0, l = "";
            if (n.indexOf("i1") >= 0)
                for (var s = 0; s < i.length; s++)
                    l = i[s].split("_"),
                            a = parseInt(l[1]), r[f] = t[a], f++;
            else
                for (var s = i.length - 1; s >= 0; s--)
                    l = i[s].split("_"),
                            a = parseInt(l[1]), r[f] = t[a], f++;
            return r;
        }
    },
    filterData: function() {
        var t = fr.receiveDataArray(), n = fr.getSelectedTime();
        n != null && n != "" && (t = $.grep(t, function(e, t) {
            var r = e.split("_"), i = n.split(",");
            for (var s = 0; s < i.length; s++) {
                if (i[s] == "")
                    continue;
                var o = i[s].split("~");
                if (r[0] >= o[0] && r[0] < o[1])
                    return !0;
            }
        }));
        var r = fr.getSelectedStation();
        return r != null && r != "" && (t = $.grep(t, function(e, t) {
            var n = e.split("_"), i = r.split(",");
            for (var s = 0; s < i.length; s++) {
                if (i[s] == "")
                    continue;
                if (n[1] == i[s])
                    return !0;
            }
        })), t;
    },
    execute: function(n) {
        var r = fr.filterData();
        n == 1 ? r = fr.orderBy.startTime(r) : n == 2 && (r = fr.orderBy.ticketPrice(r)), r.length <= 0 ? ($("#content").css("display", "none"),
                $("#localSearchNoResult").css("display", "block")) : ($("#content").css("display", "block"),
                $("#localSearchNoResult").css("display", "none"));
        var i = "", s = $(fr.dataHtml).find(".box");
        for (var o = 0; o < r.length; o++) {
            if (o == 0 || o % 10 == 0)
                pageIndex = Math.ceil((o + 1) / 10), pageIndex == 1 ? i += '<div class="content" index="' + pageIndex + '" style="display:block">' : i += '<div class="content" index="' + pageIndex + '" style="display:none">';
            var u = parseInt(r[o].split("_")[3]);
            i += $(s[u]).prop("outerHTML");
            if ((o + 1) % 10 == 0 || o == r.length - 1)
                i += "</div>";
        }
        $(".lineitem .content").remove(), $(".lineitem").append(i);
        var l = Math.ceil(r.length / 10);
        _obj.paging.changeShowPage({
            pageIndex: 1,
            pageCount: l
        }), $("#scheduleCount").html(r.length), boundEvent();
    }
}