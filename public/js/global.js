// toastr 配置
toastr.options = {
    closeButton: false,
    debug: false,
    progressBar: true,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "2000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut"
};
/*
* 全局公共点击事件
* */
function md_btnEvent(_this,callBack)
{
    // 获取表单
    var form = _this.closest("form");
    // 表单ID
    var formId = form.attr("id");
    // 表单地址
    var action = form.attr("action");
    // 验证表单
    $("#"+formId).parsley().validate();
    setTimeout(function(){
        var errLength = form.find(".parsley-error").length;
        if(errLength > 0){
            if(_this.hasClass("disabled")){
                _this.removeClass("disabled");
            }
            toastr.warning('请检查输入项');
        }else{
            // 搜集数据
            var formData = $("#"+formId).serializeArray();
            var formJson = {};
            $.each(formData,function(){
                if(this.value){
                    formJson[this.name] = this.value;
                }
            });
            md_ajax(_this,action,formJson,callBack);
        }
    },200);
}

/*
* 全局公共ajax
* this 当前点击的dom
* action 地址
* formJson  数据
* callBack  只写成功后的回调函数就可以
* type   请求方式  默认POST
* */
function md_ajax(_this,action,formJson,callBack,type)
{
    var type = type ? type : "POST";
    if(_this == '' || !_this){
        _this = false;
    }
    // 发送ajax
    $.ajax({
        url:action,    //请求的url地址
        dataType:"json",   //返回格式为json
        async:true,//请求是否异步，默认为异步，这也是ajax重要特性
        data:formJson,    //参数值
        type:type,   //请求方式
        beforeSend:function(){
            //请求前的处理
            // 禁用按钮
            if(_this){
                _this.addClass("disabled");
            }
        },
        success:function(response){
            //请求成功时处理
            switch(response["code"]){
                case 0:
                    toastr.error(response.msg);
                    break;
                case 1:
                    toastr.success(response.msg);
                    callBack(response);
                    break;
                case 2:
                    toastr.warning('您没有权限访问');
                default:
                    break;
            }
        },
        complete:function(){
            //请求完成的处理
            if(_this){
                _this.removeClass("disabled");
            }
        },
        error:function(){
            //请求出错处理
            toastr.error('404 not found');
        }
    });
}