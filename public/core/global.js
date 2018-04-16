/**
 * 更换iframe
 * @param obj
 */
function mdChaneIrame(obj, isWelcome) {
    var isWelcome = isWelcome ? isWelcome : false;
    if (isWelcome) {
        breadHtml = '<li><a href="#"><i class="fa fa-dashboard"></i>系统首页</a></li>';
        $(".breadcrumb").html(breadHtml);
        return
    }
    var url = obj.attr("data-href");
    session("curIframe", url);
    var curName = obj.attr("data-name");

    // 获取面包屑
    var breadList = breadcrumb(obj);
    var breadHtml = '';
    var length = breadList.length;
    for (var i = 0; i < breadList.length; i++) {
        if (breadList[i] == '') {
            breadList.splice(i, 1);
        }
    }
    for (var i = 0; i < breadList.length; i++) {
        breadHtml = breadHtml + '<li><a href="#"><i class="fa fa-dashboard"></i>' + breadList[i] + ' </a></li>';
    }
    breadHtml = breadHtml + '<li><a href="#"><i class="fa fa-dashboard"></i>' + curName + ' </a></li>';
    $(".breadcrumb").html(breadHtml);
    $(".LRADMS_iframe").attr("src", url);
    if (/(iPhone|iPad|iPod|iOS|Android)/i.test(navigator.userAgent)) { //移动端
        $(".sidebar-toggle").click();
    }
}

function breadcrumb(obj, result) {
    var result = result ? result : [];
    var bread = [];
    // 获取父级li
    var parent = obj.parent("li").parent("ul").parent("li");
    var parentBread = parent.find("a").eq(0).find("span").text();
    bread.push(parentBread);
    result = $.merge(result, bread);
    // 如果还存在父节点
    if (parent.parent("ul").length > 0) {
        breadcrumb(parent.parent("ul").eq(0), result);
    }
    return result;
}

/**
 * 递归形成菜单树
 * @param res
 * @param html
 * @returns HTML
 */
function listMenu(res, html) {
    var html = html ? html : '';
    for (var i = 0; i < res.length; i++) {
        if (res[i].subMenus != undefined) {
            html = html + '<li class="treeview"><a href="#">\
			<i class="' + res[i].flag + '"></i> <span>' + res[i].name + '</span>\
				<i class="fa fa-angle-left pull-right"></i></a><ul class="treeview-menu">';
            html = listMenu(res[i].subMenus, html);
            html = html + '</li></ul>';
        } else {
            html = html + '<li class="treeview"><a style="cursor: pointer;" onclick="mdChaneIrame($(this))" class="mdMenuJump" data-name="' + res[i].name + '" data-href="' + res[i].url + '">\
			<i class="' + res[i].flag + '"></i> <span>' + res[i].name + '</span></a></li>';
        }
    }
    return html;
}
/**
 * 点击事件
 * @param e
 * @param callBack
 */
function mdBtnEvent(e, callBack) {
    // 获取表单
    var form = e.closest("form");
    // 表单ID
    var formId = form.attr("id");
    // 表单地址
    var action = form.attr("action");
    // 验证表单
    $('#' + formId).validate();
    var flag = $('#' + formId).validate().form();
    if (!flag) {
        layer.msg('请检查表单数据', {icon: 0});
        return;
    }
    // 搜集数据
    var formData = $("#" + formId).serializeArray();
    var formJson = {};
    $.each(formData, function () {
        if (formJson[this.name] !== undefined) {
            if (!formJson[this.name].push) {
                formJson[this.name] = [formJson[this.name]];
            }
            formJson[this.name].push(this.value || '');
        } else {
            formJson[this.name] = this.value || '';
        }
    });
    for (tmp in formJson) {
        if ($.isArray(formJson[tmp])) {
            formJson[tmp] = formJson[tmp].join(",");
        }
    }
    // 当点击提交的时候,再次遍历一个次表单数据
    // 差异数组
    var diffOldField = {};
    var diffNewField = {};
    var oldObj = $.parseJSON(sessionStorage.getItem("oldFormData"));

    // 筛选变化的字段
    if (oldObj && oldObj != undefined && oldObj != '' && oldObj.id != undefined) {
        for (newField in formJson) {
            if (oldObj[newField] != formJson[newField]) {
                diffOldField[newField] = oldObj[newField];
                diffNewField[newField] = formJson[newField];
            }
        }
        formJson.old = diffOldField;
        formJson.new = diffNewField;
    }
    mdAjax(e, action, formJson, callBack);
    //}
}

/**
 * 获取表单原始数据
 * @param formId
 * @return old-array  new-array
 */
function getOldFormData(formId) {
    // 如果页面加载完毕,先搜集下初始化的数据,也就是旧的数据
    var oldData = $("#" + formId).serializeArray();
    //console.log(oldData)
    var oldObj = {};
    $.each(oldData, function () {
        oldObj[this.name] = this.value;
    });
    sessionStorage.setItem('oldFormData', JSON.stringify(oldObj));
}

/**
 * 全局公共ajax
 * @param _this    当前点击的dom
 * @param action   地址
 * @param formJson 数据
 * @param callBack 只写成功后的回调函数就可以
 * @param type     请求方式  默认POST
 * @param resType  返回数据格式 默认JSON
 */
function mdAjax(_this, action, formJson, callBack, type, resType) {
    var type = type ? type : "POST";
    var resType = resType ? resType : "json";
    if (_this == '' || !_this) {
        _this = false;
    }
    // 发送ajax
    $.ajax({
        url: action,    //请求的url地址
        dataType: resType,   //返回格式为json
        async: true,//请求是否异步，默认为异步，这也是ajax重要特性
        data: formJson,    //参数值
        type: type,   //请求方式
        beforeSend: function () {
            //请求前的处理
            // 禁用按钮
            if (_this) {
                _this.attr("disabled", "disabled")
            }
            var layerLoadding = layer.load(2, {
                shade: [0.2, '#fff'] //0.1透明度的白色背景
            });
        },
        success: function (response) {
            if (resType == 'html') {
                callBack(response);
                return;
            }
            //请求成功时处理
            switch (response["code"]) {
                case 0:
                    layer.msg(response.msg, {icon: 2});
                    layer.closeAll("loading");
                    break;
                case 1:
                    if (response.msg != '' && response.msg != undefined) {
                        layer.msg(response.msg, {icon: 1});
                    }
                    if (response["data"]) {
                        session("oldFormData", JSON.stringify(response["data"]));
                    }
                    callBack(response);
                    break;
                case 2:
                    layer.msg(response.msg, {icon: 3});
                    break;
                default:
                    layer.closeAll("loading");
                    console.log(response);
                    callBack(response);
                    break;
            }
        },
        complete: function () {
            //layer.closeAll("loading");
            //请求完成的处理
            if (_this) {
                _this.removeAttr("disabled");
            }
        },
        error: function () {
            layer.closeAll("loading");
            //请求出错处理
            layer.msg('系统出错,请重试', {icon: 2});
        }
    });
}

/*
 * 询问窗口
 * @param size    窗口的大小 modal-lg(da) modal-sm(xiao)
 * @param title   窗口标题
 * @param content 窗口内容
 * @param action  功能按钮   array('actName'=>'','actFunc'=>'','actIcon'=>'')
 */
function mdConfirmModal(size, title, content, url, table) {
    var size = size ? size : 'modal-sm';
    var title = title ? title : '';
    var content = content ? content : '';
    var action = [];
    // 功能按钮的array
    var actFirst = {};
    actFirst.name = '取消';
    actFirst.class = 'btn-default';
    actFirst.func = 'cancel()';
    actFirst.icon = 'icon-flag';
    action.push(actFirst);
    var actFirst2 = {};
    actFirst2.name = '确定';
    actFirst2.class = 'btn-success';
    actFirst2.func = 'baseDelete($(this),\'' + url + '\',\'' + table + '\')';
    actFirst2.icon = 'icon-flag2';
    action.push(actFirst2);
    var modalMain = '<div class="modal fade" id="mdConfirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
	    <div class="modal-dialog ' + size + '">\
	        <div class="modal-content">\
	            <div class="modal-header">\
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
	                <h4 class="modal-title" id="myModalLabel">' + title + '</h4>\
	            </div>\
	            <div class="modal-body">\
	            	' + content + '\
	            </div>\
	            <div class="modal-footer">\
	                <button type="button" class="btn btn-sdefault" data-dismiss="modal">关闭</button>\
	                \
	            ';
    var modalAction = '';
    for (var i = 0; i < action.length; i++) {
        modalAction = modalAction + '<button onclick="' + action[i].func + '" type="button" class="btn ' + action[i].class + '"><i class="' + action[i].icon + '">&nbsp;&nbsp;</i>' + action[i].name + '</button>';
    }
    var modalFooter = '</div>\
	        </div>\
	    </div>\
	</div>';
    // 只有一个确认弹窗在body
    if ($("body").find("#mdConfirmModal").length > 0) {
        $("#mdConfirmModal").remove();
    }
    $("body").append(modalMain + modalAction + modalFooter);
    $('#mdConfirmModal').modal('show');
}

function baseDelete(obj, url, tableId) {
    mdAjax(obj, url, {}, function (res) {
        $("#" + tableId).bootstrapTable('refresh');
        $("#mdConfirmModal").modal('hide');
        layer.closeAll("loading");
    }, 'GET');
}

/*
 * 页面窗口
 */
function mdFormModal(html) {
    // 只有一个确认弹窗在body
    if ($("body").find("#mdFormModal").length > 0) {
        $("#mdFormModal").remove();
    }
    sessionStorage.removeItem('oldFormData');
    $("body").append(html);
    $('#mdFormModal').modal('show');
}

/*
 * 获取BootstrapeTable选中行
 * @param  tableId  表格ID
 * @return 选中的ID数组
 */
function mdGetTableChecked(tableID) {
    var checkArr = $("#" + tableID).bootstrapTable('getSelections');
    if (checkArr.length == 0) {
        return false;
    }
    var idStr = new Array();
    for (i = 0; i < checkArr.length; i++) {
        idStr.push(checkArr[i].id);
    }
    return idStr;
}

/*
 * 数据很多的select/checkbox/radio循环
 * 假如说把不经常变动的数据表存起来放在静态JS文件中
 * @param table     对应数据表生成的JS变量名称
 * @param type      checkbox/radio/select
 * @param filedName 表单的name名称
 * @param code      在此表中的标识
 * @param sdefault   默认值
 */
function mdInitData(table, type, filedName, code, sdefault) {
    var html = '';
    switch (type) {
        case 'select':
            for (var i = 0; i < table.length; i++) {
                if (table[i].code && table[i].code.indexOf(code, 0) === 0 && table[i].level == 2) {
                    if (table[i].code == sdefault) {
                        html = html + '<option value="' + table[i].code + '" selected>' + table[i].name + '</option>';
                    } else {
                        html = html + '<option value="' + table[i].code + '">' + table[i].name + '</option>';
                    }
                }
            }
            break;
        case 'checkbox':
            for (var i = 0; i < table.length; i++) {
                if (table[i].code && table[i].code.indexOf(code, 0) === 0 && table[i].level == 2) {
                    if (sdefault.includes(table[i].code)) {
                        html += ['<div class="checkbox-custom checkbox-primary checkbox-inline">',
                            '				                                        <input type="checkbox" name="' + filedName + '" id="' + table[i].code + '" value="' + table[i].code + '" checked>',
                            '				                                        <label for="' + table[i].code + '">' + table[i].name + '</label>',
                            '				                                    </div>'].join("");
                    } else {
                        html += ['<div class="checkbox-custom checkbox-primary checkbox-inline">',
                            '				                                        <input type="checkbox" name="' + filedName + '" id="' + table[i].code + '" value="' + table[i].code + '" >',
                            '				                                        <label for="' + table[i].code + '">' + table[i].name + '</label>',
                            '				                                    </div>'].join("");

                    }
                }
            }
            break;
        case 'radio':
            for (var i = 0; i < table.length; i++) {
                if (table[i].code && table[i].code.indexOf(code, 0) === 0 && table[i].level == 2) {
                    if (table[i].code == ssdefault) {
                        html = html + '<div class="radio-custom radio-primary radio-inline"><input id="' + table[i].code + '"type="radio" name="' + fname + '" value="' + table[i].code + '" checked><label for="' + table[i].code + '">' + table[i].name + '</label></div>';
                    } else {
                        html = html + '<div class="radio-custom radio-primary radio-inline"><input id="' + table[i].code + '" type="radio" name="' + fname + '" value="' + table[i].code + '" ><label for="' + table[i].code + '">' + table[i].name + '</label></div>';
                    }
                }
            }
            break;
    }
}

/*
 * @ 判断一个值是否在一个数组
 * @param search 要搜索的字符
 * @param array  所在的数组
 */
function in_array(search, array) {
    for (var i in array) {
        if (array[i] == search) {
            return true;
        }
    }
    return false;
}


/*
 * @ 产生随机数函数
 * @param n 随机数长度
 */
function RndNum(n) {
    var rnd = "";
    for (var i = 0; i < n; i++)
        rnd += Math.floor(Math.random() * 10);
    return rnd;
}

// 用户登陆
function mdLogin(obj) {
    mdBtnEvent(obj, function (res) {
        location.href = res["data"]["url"];
    });
}

/**
 * session 操作
 * @param name
 * @param value
 */
function session(name, value) {
    if (value == '' || !value || value == undefined) {
        return sessionStorage.getItem(name);
    } else {
        sessionStorage.setItem(name, value);
    }
}

function assign(data, formId) {
    $("#" + formId).find(":input").each(function () {
        var inputType = $(this).attr("type");
        var inputName = $(this).attr("name");
        var inputVal = $(this).val();
        switch (inputType) {
            case 'text':
            case 'number':
            case 'url':
            case 'hidden':
            case 'date':
                $(this).val(data[inputName]);
                break;
            case 'checkbox':
                inputAssign(formId, 'checkbox', data[inputName], inputName[data[inputName]]);
                break;
            case 'select':
                $(this).find("option").each(function () {
                    if ($(this).val() == data[inputName]) {
                        $(this).attr("selected", true);
                    } else {
                        $(this).attr("selected", false);
                    }
                });
                break;
            case 'radio':
                    inputAssign(formId, 'radio', data[inputName], inputName, data[inputName]);
                break;
        }
    });
    $("#"+formId).find('input').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green',
        increaseArea: '30%', // optional
        labelHover: false
    });
}

function initDataList(url, dom, table, condition, field, sort, pField, cField) {
    var data = {};
    data.table = table;
    data.condition = condition;
    data.field = field;
    data.order = sort;
    data.pField = pField;
    data.cField = cField;
    mdAjax(false, url, data, function (res) {
        $("." + dom).html(res);
        //layer.closeAll("loading");
    }, 'POST', 'html');
}

function refreshTable(refreshTable) {
    $("#" + refreshTable).bootstrapTable('refresh');
}

// allExplain  所有标识信息
function inputAssign(formId, type, code, name, initial) {
    var workTree = [];
    if (code == '1' || code == '0') {
        var obj = {};
        obj.code = '0';
        obj.name = '否';
        workTree.push(obj);
        var obj2 = {};
        obj2.code = '1';
        obj2.name = '是';
        workTree.push(obj2);
    } else {
        for (var i = 0; i < allExplain.length; i++) {
            if (allExplain[i].sign == code) {
                workTree = allExplain[i].node;
            }
        }
        if (workTree.length == 0) {
            layer.msg("node不存在", {icon: 0});
        }
    }
    var html = '';
    switch (type) {
        case 'checkbox':
            var initial = initial ? initial : [];
            for (var i = 0; i < workTree.length; i++) {
                if ($.inArray(workTree[i].code, initial) !== -1) {
                    html = html + '<label class="checkbox-inline"><input type="checkbox" name="' + name + '" value="' + workTree[i].code + '" checked>' + workTree[i].name + '</label>';
                } else {
                    html = html + '<label class="checkbox-inline"><input type="checkbox" name="' + name + '" value="' + workTree[i].code + '" >' + workTree[i].name + '</label>';
                }
            }
            break;
        case 'radio':
            var initial = initial ? initial : '';
            for (var i = 0; i < workTree.length; i++) {
                if (workTree[i].code == initial) {
                    html = html + '<label class="radio-inline"><input type="radio" name="' + name + '" value="' + workTree[i].code + '" checked>' + workTree[i].name + '</label>';
                } else {
                    html = html + '<label class="radio-inline"><input type="radio" name="' + name + '" value="' + workTree[i].code + '" >' + workTree[i].name + '</label>';
                }
            }
            break;
        case 'select':
            var initial = initial ? initial : '';
            for (var i = 0; i < workTree.length; i++) {
                if (workTree[i].code == initial) {
                    html = html + '<option value="' + workTree[i].code + '" selected>' + workTree[i].name + '</option>';
                } else {
                    html = html + '<option value="' + workTree[i].code + '" >' + workTree[i].name + '</option>';
                }
            }
            break;
    }
    var parent = $("#" + formId).find('input[name=' + name + ']').parent("div");
    $("#" + formId).find('input[name=' + name + ']').remove();
    parent.html(html);
}
// 获取icon 并添加至input
function getCurIcon(obj) {
    var flag = obj.find("i").attr("class");
    $("#flaginput").val(flag);
    $(".showIconList").find("i").attr('class', flag);
    obj.parent('div').css('display','none');
}

function initMenu()
{
    // 初始化菜单
    var initMenu = $("#initMenu").val();
    mdAjax(false,initMenu,{},function(res){
        var menu = listMenu(res);
        menu = '<li class="header">功能列表</li>'+menu;
        $("#mdMenuListBae").html(menu);
        mdChaneIrame(false,true);
    });
    // 获取当前浏览器高度 自适应iframe
    var h = document.documentElement.clientHeight || document.body.clientHeight;
    $(".LRADMS_iframe").css("height",h+'px');
}

