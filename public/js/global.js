// 提示信息
function mdNotify(title, msg, type, url, icon, from, align, animIn, animOut) {
    $.growl({
        icon: icon,
        title: title,
        message: msg,
        url: url
    }, {
        element: 'body',
        type: type,
        allow_dismiss: true,
        placement: {
            from: from,
            align: align
        },
        offset: {
            x: 20,
            y: 85
        },
        spacing: 10,
        z_index: 1031,
        delay: 2500,
        timer: 1000,
        url_target: '_blank',
        mouse_over: false,
        animate: {
            enter: animIn,
            exit: animOut
        },
        icon_type: 'class',
        template: '<div data-growl="container" class="alert" role="alert">' +
        '<button type="button" class="close" data-growl="dismiss">' +
        '<span aria-hidden="true">&times;</span>' +
        '<span class="sr-only">Close</span>' +
        '</button>' +
        '<span data-growl="icon"></span>' +
        '<span data-growl="title"></span>' +
        '<span data-growl="message"></span>' +
        '<a href="#" data-growl="url"></a>' +
        '</div>'
    });
};

// 全局公共点击事件-Form
function mdBtnEvent(e, callBack) {
    // 获取表单
    var form = e.closest("form");
    // 表单ID
    var formId = form.attr("id");
    // 表单地址
    var action = form.attr("action");
    // 验证表单
    /*$("#" + formId).parsley();
    var errLength = form.find(".has-error").length;
    if (errLength > 0) {
        if (e.attr("disabled") == 'disabled') {
            e.removeAttr("disabled");
        }
        mdNotify('','请检查输入项','warning');
    } else {*/
    // 搜集数据
    var formData = $("#" + formId).serializeArray();
    var formJson = {};
    $.each(formData, function () {
        if (this.value) {
            formJson[this.name] = this.value;
        }
    });
    // 当点击提交的时候,再次遍历一个次表单数据
    // 差异数组
    var diffOldField = {};
    var diffNewField = {};
    var oldObj = $.parseJSON(sessionStorage.getItem("oldFormData"));
    // 筛选变化的字段
    for (oldField in oldObj) {
        if (oldObj[oldField] != formJson[oldField]) {
            diffOldField[oldField] = oldObj[oldField];
            diffNewField[oldField] = formJson[oldField];
        }
    }
    console.log(diffOldField)
    console.log(diffNewField)
    formJson.old = diffOldField;
    formJson.new = diffNewField;
    mdAjax(e, action, formJson, callBack);
    //}
}

/**
 * 获取表单原始数据
 * @param  formId 表单ID
 * @return old-array  new-array
 * */
function getOldFormData(formId) {
    // 如果页面加载完毕,先搜集下初始化的数据,也就是旧的数据
    var oldData = $("#" + formId).serializeArray();
    //console.log(oldData)
    var oldObj = {};
    $.each(oldData, function () {
        oldObj[this.name] = this.value;
    });
    sessionStorage.setItem('oldFormData',JSON.stringify(oldObj));
    //console.log(oldObj)
}

/*
* 全局公共ajax
* this 当前点击的dom
* action 地址
* formJson  数据
* callBack  只写成功后的回调函数就可以
* type   请求方式  默认POST
* */
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
        },
        success: function (response) {
            if (resType == 'html') {
                callBack(response);
                return;
            }
            //请求成功时处理
            switch (response["code"]) {
                case 0:
                    mdNotify('', response.msg, 'danger');
                    break;
                case 1:
                    mdNotify('', response.msg, 'success');
                    setTimeout(function () {
                        callBack(response)
                    }, 1500);
                    break;
                case 2:
                    mdNotify('', '您没有权限访问', 'danger');
                    sdefault:
                        break;
            }
        },
        complete: function () {
            //请求完成的处理
            if (_this) {
                _this.removeAttr("disabled");
            }
        },
        error: function () {
            //请求出错处理
            mdNotify('NetError', '404 not found', 'danger');
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
function mdConfirmModal(size, title, content, action) {
    var size = size ? size : 'modal-sm';
    var title = title ? title : '';
    var content = content ? content : '';
    var action = action ? action : [];
    // 功能按钮的array
    var actFirst = {};
    actFirst.name = '测试一';
    actFirst.class = 'btn-primary';
    actFirst.func = 'funcOne()';
    actFirst.icon = 'icon-flag';
    //action.push(actFirst);
    var actFirst2 = {};
    actFirst2.name = '测试222';
    actFirst2.class = 'btn-success';
    actFirst2.func = 'funcTwo()';
    actFirst2.icon = 'icon-flag2';
    //action.push(actFirst2);
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
        $("body").append(modalMain + modalAction + modalFooter);
    }
    $('#mdConfirmModal').modal('show');
}

/*
 * 页面窗口
*/
function mdFormModal(size, title, content, action) {
    var size = size ? size : 'modal-sm';
    var title = title ? title : '';
    var content = content ? content : '';
    var action = action ? action : [];
    // 功能按钮的array
    var actFirst = {};
    actFirst.name = '测试一';
    actFirst.class = 'btn-primary';
    actFirst.func = 'funcOne()';
    actFirst.icon = 'icon-flag';
    action.push(actFirst);
    var actFirst2 = {};
    actFirst2.name = '测试222';
    actFirst2.class = 'btn-success';
    actFirst2.func = 'funcTwo()';
    actFirst2.icon = 'icon-flag2';
    action.push(actFirst2);
    var modalMain = '<div class="modal fade" id="mdFormModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">\
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
    if ($("body").find("#mdFormModal").length > 0) {
        $("#mdFormModal").remove();
    }
    $("body").append(modalMain + modalAction + modalFooter);
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