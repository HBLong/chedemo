define(['bootstrap'], function($) {
    var sell = {};
    sell.init = function() {
        $(document).delegate(".modal-sell", "click", function() {
            $('#sell-Modal').remove();
            var shift_id = parseInt($(this).data('id'));
            var start_station_id = parseInt($(this).data('start'));
            var end_station_id = parseInt($(this).data('end'));
            var surplus = parseInt($(this).data('surplus'));
            sell.ticket(shift_id, start_station_id, end_station_id, surplus);
        });
    };

    sell.ticket = function(shift_id, start_station_id, end_station_id, surplus) {
        var startDate = $("#startDate").val();
        var html = '<div class="modal fade" id="sell-Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">' +
                '	<div class="modal-dialog modal-lg" role="document">' +
                '		<div class="modal-content">' +
                '			<form class="table-responsive form-inline" method="post" action="" id="form-sell">' +
                '				<div class="modal-header">' +
                '					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '					<h4 class="modal-title" id="myModalLabel">确认购票信息</h4>' +
                '				</div>' +
                '				<div class="modal-body">' +
                '					<div id="sell-loading">加载中...</div>' +
                '					<table id="sell-table" class="table table-bordered hidden">' +
                '						<tr>' +
                '							<th width="150"></th>' +
                '							<td>' +
                '								出发站点：<strong id="sell-start-station"></strong> 目的站点：<strong id="sell-end-station"></strong>' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>余票：</th>' +
                '							<td>' +
                '								<div class="form-group">' +
                '									<span class="form-control-static" id="sell-surplus-votes"></span>' +
                '								</div>' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>票价：</th>' +
                '							<td>' +
                '								￥<strong id="sell-price"></strong>元' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>购票数量：</th>' +
                '							<td>' +
                '								<div class="form-group">' +
                '									<input type="number" id="sell-numbers" name="numbers" value="1" max="' + surplus + '" min="1" class="form-control">' +
                '								</div>' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>座位号：</th>' +
                '							<td>' +
                '								<div class="form-group">' +
                '                                                                   <input type="hidden" name="seat_number" id="hide_seat_number" value="" />' +
                '                                                                   <span class="form-control-static" id="seat_number" style="margin-right:25px;"></span> <a href="javascript:;" class="btn btn-sm btn-success" id="select-seat">选择座位</a>' +
                '								</div>' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>姓名：</th>' +
                '							<td>' +
                '								<div class="form-group">' +
                '									<input type="text" placeholder="购票人姓名" name="name" id="ticket-name" value="" class="form-control">' +
                '								</div>' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>手机号码：</th>' +
                '							<td>' +
                '								<div class="form-group">' +
                '									<input type="text" placeholder="手机号码" name="phone" id="ticket-phone" value="" class="form-control">' +
                '								</div>' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>身份证：</th>' +
                '							<td>' +
                '								<div class="form-group">' +
                '									<input type="text" placeholder="购票人身份证" name="idcard" id="ticket-idcard" value="" class="form-control">' +
                '								</div>' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>备注：</th>' +
                '							<td>' +
                '								<div class="form-group">' +
                '									<textarea name="remarks" id="remarks" class="form-control" cols="90" placeholder="管理员备注"></textarea>' +
                '								</div>' +
                '							</td>' +
                '						</tr>' +
                '						<tr>' +
                '							<th>总价：</th>' +
                '							<td>' +
                '								￥<strong id="sell-total-price"></strong>元' +
                '							</td>' +
                '						</tr>' +
                '					</table>' +
                '				</div>' +
                '				<div class="modal-footer">' +
                '					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
                '					<input type="submit" class="btn btn-primary" id="submit" name="提交" value="提交">' +
                '				</div>' +
                '			</form>' +
                '		</div>' +
                '	</div>' +
                '</div>';
        var surplus_votes = 0;
        var ticket_price = 0;
        //sell-numbers
        require(['validator'], function($) {
            $('#sell-Modal').remove();
            $(document.body).append(html);
            var dialog = $('#sell-Modal');
            $.post(sell_check_url, {shift_id: shift_id, start_station_id: start_station_id, end_station_id: end_station_id, startDate: startDate}, function(res) {
                if (res.type == 'success') {
                    var shift = res.message;
                    dialog.modal('show');
                    $('#sell-start-station').text(shift.start_station.name);
                    $('#sell-end-station').text(shift.end_station.name);
                    $('#sell-price, #sell-total-price').text(shift.ticket_price);
                    $('#sell-surplus-votes').text(shift.surplus_votes);
                    $('#seat_number').text(shift.seat_number);
                    $('#hide_seat_number').val(shift.seat_number);
                    surplus_votes = shift.surplus_votes;
                    ticket_price = shift.ticket_price;
                    $("#sell-loading").addClass("hidden");
                    $("#sell-table").removeClass("hidden");
                } else {
                    dialog.modal('hide');
                    alert(res.message);
                }
            }, "JSON");
            $('#form-sell').bootstrapValidator({
                fields: {
                    numbers: {
                        validators: {
                            notEmpty: {
                                message: '请填写购票数量'
                            },
                            greaterThan: {
                                message: '购票数量最小1张'
                            },
                            lessThan: {
                                message: '购票数量不能超过现有余票数量'
                            },
                            integer: {
                                message: '购票数量输入有误'
                            }
                        }
                    },
                }
            });
            var Validator = $('#form-sell').data('bootstrapValidator');
            $('#form-sell .btn-primary').click(function() {
                Validator.validate();
                if (Validator.isValid()) {
                    var numbers = Number($("#sell-numbers").val());
                    var name = $("#ticket-name").val();
                    var idcard = $("#ticket-idcard").val();
                    var phone = $("#ticket-phone").val();
                    var remarks = $("#remarks").val();
                    var seat_number = $("#hide_seat_number").val();
                    if (isNaN(numbers)) {
                        alert("购票数量有误！");
                        return false;
                    }
                    if (numbers < 1 || numbers > surplus_votes) {
                        alert("购票数量有误！");
                        return false;
                    }
                    var param = {
                        shift_id: shift_id,
                        start_station_id: start_station_id,
                        end_station_id: end_station_id,
                        numbers: numbers, 
                        name: name, 
                        idcard: idcard, 
                        phone: phone, 
                        remarks: remarks, 
                        startDate: startDate,
                        seat_number: seat_number,
                    };
                    $.post(sell_url, param, function(res) {
                        if (res.type != 'success') {
                            util.message(res.message, '', 'error');
                            return false;
                        } else {
                            dialog.modal('hide');
                            var content = '<i class="pull-left fa fa-4x fa-check-circle"></i><div class="pull-left"><p>'+ res.message +'</p></div><div class="clearfix"></div>';
                            var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">确认</button><a class="btn btn-primary" href="'+res.redirect+'" target="_blank">打印</a>';
                            var modalobj = util.dialog('购票成功', content, footer, {'containerName' : 'modal-message'});
                                modalobj.find('.modal-content').addClass('alert alert-success');
                                modalobj.modal('show');
//                            util.message(res.message, '', 'success');
//                            window.open(res.redirect);
                            return false;
                        }
                    }, 'JSON');
                }
            });
            
            $("#sell-numbers").on('change keyup', function(){
                if (isNaN(Number($(this).val())) || Number($(this).val()) < 1) {
                    $(this).val(1);
                }
                if (Number($(this).val()) > surplus_votes) {
                    $(this).val(surplus_votes);
                }
                $("#sell-total-price").text($(this).val() * ticket_price);
            });
            
            $("#select-seat").on("click", function(){
                $.post(select_seat_url, {shift_id: shift_id, start_station_id: start_station_id, end_station_id: end_station_id, startDate: startDate}, function(res) {
                    $("body").append(res);
                    $("#seat_number_box").show();
                }, "html");
            });
            
        });
    };

    return sell;
});
    

$(document).delegate("#confirmSelectSeat", "click", function() {
    var seat_number = '';
    $("#seat_number_list").find(".selected").each(function(){
        seat_number += seat_number == '' ? $(this).data("seat") : "," + $(this).data("seat");
    });
    if(seat_number != '') {
        $("#hide_seat_number").val(seat_number);
        $("#seat_number").text(seat_number);
    }
    $("#seat_number_box").remove();
});

$(document).delegate("#cancelSelectSeat", "click", function() {
    $("#seat_number_box").remove();
});
$(document).delegate(".seat", "click", function() {
    var occupied = $(this).data("occupied");
    if (occupied == 1) {
        return false;
    }
    var number = $("#sell-numbers").val();
    var selected = $("#seat_number_list").find(".selected");
    if (selected.length == number && !$(this).hasClass("selected")) {
        var obj = $("#seat_number_list").find(".selected").eq(0);
        obj.removeClass("selected btn-success").addClass("btn-default");
        obj.find("span").removeClass("hidden");
        obj.find("i").addClass("hidden");
    }
    if ($(this).hasClass("selected")) {
        $(this).removeClass("selected btn-success").addClass("btn-default");
        $(this).find("span").removeClass("hidden");
        $(this).find("i").addClass("hidden");
    }else{
        $(this).addClass("selected btn-success").removeClass("btn-default");
        $(this).find("span").addClass("hidden");
        $(this).find("i").removeClass("hidden");
    }        
});