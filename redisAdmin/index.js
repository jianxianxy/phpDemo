var DB = 0;
$(function () {
    $("#db_list dd").on("click", function () {
        var sel = $(this).attr("data");
        DB = sel;
    });
    $("#mainTab").on("click",function(){
        $("#redisval").val("");
    });
});
function ajaxQuery(query) {
    $.post('./Redis.php', query, function (ret) {
        $("#redisval").val(ret);
    });
}
//获取字符串
function getString() {
    var key = $("#keyString").val();
    if (key == '') {
        $("#keyString").prop("placeholder", "请输入Key值");
    }
    var query = {"db": DB, "type": "string", "key": key};
    ajaxQuery(query);
}
//获取Hash
function getHash() {
    var key = $("#keyHash").val();
    if (key == '') {
        $("#keyHash").prop("placeholder", "请输入Key值");
    }
    var index = $("#indexHash").val();
    var query = {"db": DB, "type": "hash", "key": key, "index": index};
    ajaxQuery(query);
}
//获取List
function getList() {
    var key = $("#keyList").val();
    if (key == '') {
        $("#keyList").prop("placeholder", "请输入Key值");
    }
    var indexStart = $("#indexListStart").val();
    var indexEnd = $("#indexListEnd").val();
    var query = {"db": DB, "type": "list", "key": key, "start": indexStart, "end": indexEnd};
    ajaxQuery(query);
}
//获取Set
function getSet() {
    var key = $("#keySet").val();
    if (key == '') {
        $("#keySet").prop("placeholder", "请输入Key值");
    }
    var query = {"db": DB, "type": "set", "key": key};
    ajaxQuery(query);
}
//获取ZSet
function getZSet() {
    var key = $("#keyZSet").val();
    if (key == '') {
        $("#keyZSet").prop("placeholder", "请输入Key值");
    }
    var indexStart = $("#indexZSetStart").val();
    var indexEnd = $("#indexZSettEnd").val();
    var query = {"db": DB, "type": "zset", "key": key, "start": indexStart, "end": indexEnd};
    ajaxQuery(query);
}