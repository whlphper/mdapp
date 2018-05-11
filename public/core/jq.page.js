$(document).ready(function(){
    var pageSize = 100;
    // 开始渲染表格
    $("body").find('table').each(function(){
        var tableUrl = $(this).attr("data-url");
        var obj = $(this);
        mdAjax(false,tableUrl,{},function(res){
            layer.closeAll('loading');
            jqPageRender(obj,res.data);
            obj.parent("div").find('.pagination').pagination(res.total,{
                callback: function(page){
                    InitTable(obj,page,tableUrl);
                },
                display_msg: true,
                setPageNo: true,
                items_per_page:100
            });
        });
    });

    // 表格搜索
    $("body").find(".confirm").click(function () {
        var searchForm = $(this).closest("form");
        // 表单ID
        var formId = searchForm.attr("id");
        // 要搜索的表单
        var table = searchForm.attr("data-table");
        var dataJson = getFormJson(formId);
        var tableUrl = $("#"+table).attr("data-url");
        var obj = $("#"+table);
        InitTable(obj,0,tableUrl,dataJson);

    });

    // 表格搜索重置
    $("body").find(".clear").click(function () {
        var searchForm = $(this).closest("form");
        // 表单ID
        var formId = searchForm.attr("id");
        $("#"+formId)[0].reset();
        var searchForm = $(this).closest("form");
        // 要搜索的表单
        var table = searchForm.attr("data-table");
        var tableUrl = $("#"+table).attr("data-url");
        var obj = $("#"+table);
        InitTable(obj,0,tableUrl,[]);
    });

    // 批量删除
    $("body").find(".delMore").click(function () {
        // 获取表头第一列的字段名称
        var table = $(this).parent("div").parent("div").find("table");
        var tableId = table.attr("id");
        var obj = $("#"+tableId);
        var tableUrl = table.attr("data-url");
        var fieldName = table.find("thead").find("th").eq(0).attr("data-key");
        // 获取选中的列
        var checked = [];
        table.find("tbody").find("tr").each(function (i) {
            if($(this).find("input[type=checkbox]").is(":checked'")){
                checked.push($(this).find("input[type=checkbox]").val());
            }
        });
        if(checked.length == 0){
            layer.msg('请选中要删除的数据',{icon:0});
            return;
        }
        //询问框
        var delUrl = $(this).attr("data-url");
        delUrl = delUrl + '?field='+fieldName+'&ids='+checked.join(",");
        layer.confirm('确定批量删除数据吗？', {
            btn: ['删除','取消'] //按钮
        }, function(){
            mdAjax($(this),delUrl,{},function (res) {
                InitTable(obj,0,tableUrl);
            },'get');
        });
    });

    // 添加数据
    $("body").find(".addData").click(function () {
        layer.load(2, {
            shade: [0.2, '#fff'] //0.1透明度的白色背景
        });
        var url = $(this).attr("data-url");
        window.parent.$("#iframe").attr("src",url);
        layer.closeAll('loading');
    })
});
var pageSize = 100;
//请求数据
function InitTable(obj,pageIndex,tableUrl,extra) {
    var extra = extra ? extra :false;
    var data = {};
    if(extra){
        data = extra;
    }
    var tableUrl = obj.attr("data-url");
    data.pageIndex = pageIndex;
    data.pageSize = pageSize;
    mdAjax(false,tableUrl,data,function (res) {
        layer.closeAll('loading');
        jqPageRender(obj,res.data);
        if(extra){
            obj.parent("div").find('.pagination').pagination(res.total,{
                callback: function(page){
                    InitTable(obj,page,tableUrl);
                },
                display_msg: true,
                setPageNo: true,
                items_per_page:100
            });
        }
    },'get');
}

function jqPageRender(obj,data)
{
    obj.find("tbody").html('');
    var allData = [];
    // 获取需要数据中的哪些字段，循环表格head来找出
    for(var j=0;j<data.length;j++){
        var trHtml = '<tr>';
        obj.find("thead").find("th").each(function(){
            var field = $(this).attr("data-field");
            var keyF = $(this).attr("data-key");
            var actionFunc = $(this).attr("data-action");
            if(actionFunc && actionFunc != undefined && actionFunc != ''){
                var curAction = renderAction(actionFunc,j,data);
                trHtml= trHtml + '<td style="padding-left: 15px;">'+curAction+'</td>';
            }else{
                if(field == 'checkbox' ){
                    trHtml = trHtml + '<td><input type="checkbox" value="'+data[j][keyF]+'"></td>';
                }else{
                    if(data[j][field] == null || data[j][field] == ''){
                        trHtml= trHtml + '<td style="padding-left: 15px;">-</td>';
                    }else{
                        trHtml= trHtml + '<td style="padding-left: 15px;">'+data[j][field]+'</td>';
                    }
                }
            }
        });
        trHtml = trHtml + '</tr>';
        allData.push(trHtml);
    }
    obj.find("tbody").append(allData.join(' '));
}

// 表格渲染操作选项中间件
function renderAction(name,index,data){
    var funcBB = eval(name,index,data);
    var html = funcBB(index,data);
    return html;
}

// 获取表单数据
function getFormJson(formId,isGET) {
    var isGET = isGET ? true :false;
    // 搜索参数搜集
    var formData = $("#" + formId).serializeArray();
    if(isGET){
        var param = '';
        $.each(formData, function() {
            if(this.value != ''){
                if (param == '') {
                    param = '?'+this.name+'='+this.value;
                } else {
                    param = param + '&'+this.name+'='+this.value;
                }
            }
        });
        return param;
    }else{
        var o = {};
        $.each(formData, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [ o[this.name] ];
                }
                o[this.name].push(this.value || '');
            } else {
                if(this.value && this.value != ''){
                    o[this.name] = this.value || '';
                }
            }
        });
        return o;
    }
}
