//链接配置
var CONN = 0;
//DB选择
var DB = 0;
$(function () {
    $("#db_list dd").on("click", function () {
        var sel = $(this).attr("data");
        DB = sel;
    });
	$(".layui-layout-left li").on("click",function(){
		var con = $(this).attr("data");
        CONN = con;	
	});
    $("#mainTab").on("click",function(){
        $("#redisval").val("");
    });
});


function ajaxQuery(query) {
	query.db = DB;
	query.conn = CONN;
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
    var query = {"type": "string", "key": key};
    ajaxQuery(query);
}
//获取Hash
function getHash() {
    var key = $("#keyHash").val();
    if (key == '') {
        $("#keyHash").prop("placeholder", "请输入Key值");
    }
    var index = $("#indexHash").val();
    var query = {"type": "hash", "key": key, "index": index};
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
    var query = {"type": "list", "key": key, "start": indexStart, "end": indexEnd};
    ajaxQuery(query);
}
//获取Set
function getSet() {
    var key = $("#keySet").val();
    if (key == '') {
        $("#keySet").prop("placeholder", "请输入Key值");
    }
    var query = {"type": "set", "key": key};
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
    var query = {"type": "zset", "key": key, "start": indexStart, "end": indexEnd};
    ajaxQuery(query);
}
//Hash常用
function getHashList(){
    layer.open({
        type: 1,
        area: ['500px', '400px'],
        closeBtn: 1,        
        shadeClose: true,
        title:"匹配",
        btn: ['匹配', '取消'],
        content:$("#keyMap"),
        yes: function(){            
            $("#keyMap").hide();
        },
        cancel:function(){
            $("#keyMap").hide();
        }
    });    
}