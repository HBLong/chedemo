$("#addShiftStation").click(function(){
    var template = $("#ShiftStationTemplate").html().replace(/{{index}}/g, shift_station_index);
    $(this).parent().before(template);
    $('.clockpicker' + shift_station_index).clockpicker(option);
    shift_station_index++;
});
$("#addShiftEndStation").click(function(){
    var template = $("#ShiftEndStationTemplate").html().replace(/{{index}}/g, shift_end_station_index);
    $(this).parent().before(template);
    shift_end_station_index++;
});

$(document).delegate(".fr_area", "change", function() {
    var _this = $(this), id = _this.data('id');
    if (!id) {
        getStation(_this);
        return ;
    }
    _this.parents(".selectList").find(".fr_district").html('<option value="0">'+ _this.parents(".selectList").find(".fr_district").children('option').eq(0).html()+'</option>');
    var val = _this.val();
    if (val == 0) {
        _this.parents(".selectList").find("."+id).html('<option value="0">'+_this.parents(".selectList").find("."+id).children('option').eq(0).html()+'</option>');
        getStation(_this);
        return false;
    }
    $.post(get_area_url, {pid:val, act:'getarea'}, function (res){
        if (res) {
            var html = '<option value="0">'+_this.parents(".selectList").find("."+id).children('option').eq(0).html()+'</option>';
            $.each(res, function(i, _item) {
                html += '<option value="'+_item.id+'">'+_item.name+'</option>';
            });
            _this.parents(".selectList").find("."+id).html(html);
            getStation(_this);
        }
    }, 'JSON');
});
function getStation(obj) {
    var province_id = obj.parents(".selectList").find(".fr_province").val();
    var city_id = obj.parents(".selectList").find(".fr_city").val();
    var district_id = obj.parents(".selectList").find(".fr_district").val();
    var data = {province_id:province_id, city_id:city_id, district_id:district_id};
    $.post(get_station_url, data, function (res){
        var station = obj.parents(".selectList").find(".station");
        var html = '<option value="0">'+station.children('option').eq(0).html()+'</option>';
        if (res) {
            $.each(res, function(i, _item) {
                html += '<option value="'+_item.id+'">'+_item.name+'</option>';
            });
        }
        station.html(html);
    }, 'JSON');
}
$(document).delegate(".ShiftStationRemove", "click", function() {
    var id = $(this).data('id'), type = $(this).data('type'), confirm = false;
    if (id > 0) {
        confirm = true;
    }
    if (confirm) {
        if (window.confirm("确定删除？")) {
            if (type == 'start') {
                var delete_start_station = $("#delete_shift_station").val();
                delete_start_station = delete_start_station == '' ? id : delete_start_station + ',' + id;
                $("#delete_shift_station").val(delete_start_station);
            }else{
                var delete_end_station = $("#delete_shift_end_station").val();
                delete_end_station = delete_end_station == '' ? id : delete_end_station + ',' + id;
                $("#delete_shift_end_station").val(delete_end_station);
            }
            $(this).parent().remove();
        }
    }else{
        $(this).parent().remove();  
    }  
});