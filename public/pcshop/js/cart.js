var loadCarInfoCallback;
var cartN = {
    url: "/Order/cartapi.aspx",
    cartInfo: [],
    memberAllPrice: 0,//总原价
    totalPrice: 0, //总价格
    totalCount: 0, //总数量
    totalYh: 0.0, //总优惠
    totalNum: 0,//购物车中总商品数
    selectedNum: 0,//购物车中选中商品数
    baskIds: "",//选中商品的baskid 英文逗号 隔开
    isSupportJiaji: null,//加急体验卡相关信息
    codes: [],
    points:0,//积分
    haspromotion: false,
    cartLoaded: $.noop,
    addCart: function(id,count) {
        $.post("/order/cartapi.aspx", { "act": "addCartAjax", "ppriceid": id,count:count }, function(rsp) {
            //if (rsp == 1) location.href = "cart.html";
        });
    },
    buyNow: function (id, count) {
        location.href = "/order/cartapi.aspx?act=addCartAjax&ppriceid=" + id + "&count=" + count;
    },
    selectServices: function(obj) {
        var ppriceid = $(obj).attr("data-ppriceid"),
            servicesid = "无",
            baskid = $(obj).attr("data-baskid");
        $(obj).parents("tbody").find("div[libask=li_" + baskid + "]").each(function() {
            servicesids += $(this).attr("data-ids") + ",";
        });
        //添加/修改 三九服务 弹出层
        $.ajax({
            url: cartN.url,
            type: "post",
            dataType: "json",
            async: false,
            cache: false,
            data: { "act": "loadServices", "ppriceid": ppriceid },
            success: function(txt) {
                var tmplData = { servicesid: servicesid, baskid: baskid, result: txt };

                $("#servicesContentN").html(template("servicesTmpl", tmplData));
                //页面层
                layer.open({
                    type: 1,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['620px', '240px'], //宽高
                    title: "请选择您需要的三九服务",
                    content: template("servicesTmpl", tmplData)
                });
            }
        });
    },
    chooseServices: function(obj) {
        $(obj).toggleClass("cur");
        var index = $("#servicesContentN .cur").length, html = "";
        for (i = 0; i < index; i++) {
            html += '<li>' + $("#servicesContentN .cur").eq(i).text() + '</li>';
        }
        $("#yixuan_fuwu ul").html(html);
    },
    saveServices: function(obj, baskid) {
        var servicesid = "";

        var index = $("#servicesContentN .cur").length, html = "";
        for (i = 0; i < index; i++) {
            servicesid += $("#servicesContentN .cur").eq(i).attr("data-ppriceid") + ',';
        }
        $.ajax({
            url: "/cart/CartApi.aspx",
            type: "post",
            dataType: "html",
            async: false,
            cache: false,
            data: { "act": "chooseServicesid", "baskid": baskid, 'servicesid': servicesid },
            success: function(txt) {
                //alert("成功");
                layer.load(1, {
                    shade: [0.1, '#fff'] //0.1透明度的白色背景
                });
                cartN.loadCarInfo();
            }
        });
    },
    savaPremiums: function(obj) {
        //保存赠品
        var PremiumsBox = $(obj).parent().siblings("ul");
        if (PremiumsBox.find("input:checked").length != 1) {
            layer.tips('赠品只能选择一个哦~', $(obj), {
                tips: 1,
                time: 2000
            });
            return false;
        }
        $.ajax({
            url: cartN.url,
            type: "post",
            dataType: "html",
            async: false,
            cache: false,
            data: { "act": "savaPremiums", "baskid": baskid, 'servicesid': servicesid },
            success: function(txt) {
                $(obj).parent().parent().hide();
                alert("成功");
                layer.load(1, {
                    shade: [0.1, '#fff'] //0.1透明度的白色背景
                });
                cartN.loadCarInfo();
            }
        });
    },
    loadCarInfo: function (baskids, paytype_id, promotionId,isservice) {
        var cartOffset = "auto"; 
        layer.load(1, { offset: cartOffset, shade: false });
        $.get("/order/CartApi.aspx", {
            act: 'load', baskids: baskids, paytype_id: paytype_id, promotionId: promotionId, isservice:isservice,t: $.now()
        }, function (result) {
            layer.closeAll();
            cartN.isSupportJiaji = result.setupExp;
            cartN.haspromotion = result.hasPromotion;

            var html = template("cartInfoTmpl", result.carts);
           
            if (result.carts.length < 1&&$(".mami-cart").size()==0) {
                html = '<tbody style="border:0;"><tr><td colspan="10" style="text-align:center;width:1200px;height:300px; background:url(http://imgma.ch999img.com/Static/images/kong.jpg) no-repeat  center"><div class="nothing"><a style="height:200px; display:block;" href="/"></a></div></td></tr></tbody>';
                if (baskids != null)
                    window.location.href = "cart.html";
            }
            //$("#tableCart").empty();
            $("#tableCart").html(html);
            $("#tableCartJifen").html(template("cartJifenInfoTmpl", result.carts));
            if (baskids == null && window.location.href.toLowerCase().indexOf("cart") != -1) {
                var html1 = template("commonpro", result.carts);
                var html2 = template("bspro", result.carts)
                $('#comm').html(html1);
                $('#bspro1').html(html2);
                cart_jiage();
                /*加载去结算*/
                var len = $("#bspro1 li").length;
                var len1 = $("#comm li").length;
                if (len == 0 || len1 == 0) {
                    $("#go-Confirm").removeClass("ban").text("去结算");
                }
                    
            }
            cartN.codes = result.codes;
            cartN.calcTotal();
            cartN.cartLoaded(result);
            cartN.points = result.carts[0].totaljifen;
        }, "json");
    },
    updateCartNum: function(othis, type, id) {
        var $input = $(othis).siblings("input").length > 0 ? $(othis).siblings("input") : $(othis);
        var num = parseInt($input.val());
        var flag = false;
        if (type == "1") { //+
            if (isNaN(num)) {
                flag = true;
                layer.tips('请输入正确的数字', $input, { tips: 1 });
            } else {
                num = num + 1;
            }
        } else if (type == "-1") { //-
            if (isNaN(num)) {
                flag = true;
                layer.tips('请输入正确的数字', $input, { tips: 1 });
            } else {
                num = num - 1;
            }
        } else if (type == "0") { //inputChange
            if (isNaN(num)) {
                flag = true;
                layer.tips('请输入正确的数字', $input, { tips: 1 });
            }
        }
        if (!flag) {
            if (num <= 0) {
                num = 1;
                return;
            }
            $.ajax({
                url: cartN.url,
                type: "post",
                dataType: "html",
                async: false,
                data: { "act": "updateCarNum", 'num': num, 'id': id },
                success: function(result) {
                    if (result == "1") {
                        cartN.loadCarInfo();
                    }
                }
            });
        }
    },
    delCar: function(id) {
        //if (confirm("确定删除？")) {
            $.ajax({
                url: cartN.url,
                type: "post",
                dataType: "html",
                async: false,
                cache: false,
                data: { "act": "delCar", 'id': id },
                success: function(result) {
                    if (result == "1") {
                        cartN.loadCarInfo();
                    }
                }
            });
        //}
    },
    delSelect: function() {
        $("li.c_goods").each(function () {
            var dom = $(this).find("input:checked");
            if (dom.size()>0) $(".col-del", this).trigger("click");
        });
    },
    clearCart: function() {
        if (confirm("确定清除购物车？")) {
            $.ajax({
                url: cartN.url,
                type: "post",
                dataType: "html",
                async: false,
                cache: false,
                data: { "act": "clearCart" },
                success: function(result) {
                    if (result == "1") {
                        cartN.loadCarInfo();
                    }
                }
            });
        }
    },
    selectPro: function (obj) {
        var self = $(obj);
        var _type = self.attr("data-type") || "";
        if (_type == "all") {
            if (!$(".all").is(":checked")) {
                $(".c_goods").removeProp("checked");
            } else {
                $(".c_goods").each(function () {
                    /*$(this).prop("checked", "true");*/
                    cartN.loadCarInfo();
                });
            }
        } else {
            $(".all").removeProp("checked");
        }
        cartN.calcTotal();
    },
    calcTotal: function() {
        var allPrice = 0;
        var selectedNum = 0;
        cartN.baskIds = "";
        cartN.totalPrice = 0;
        $(".c_goods").each(function() {
            if (false&&$(this).parents("tbody").attr("data-use").length > 0) {
                //获取套餐中商品数量及 套餐价格
                //selectedNum += $(this).parents("tbody").find("tr[data-bask_sp]").length * parseInt($(this).attr("data-num"));
                $(this).parents("tbody").find(".inputbox input:checkbox").not(".c_goods").each(function() {
                    selectedNum += parseInt($(this).attr("data-num"));
                    if (cartN.baskIds.indexOf($(this).attr("data-baskid")) == -1) {
                        cartN.baskIds += "," + $(this).attr("data-baskid");
                    }
                });
                cartN.totalPrice += parseFloat($(this).attr("data-price")) * parseInt($(this).attr("data-num"));


            } else if ($(this).parents("tbody").find("tr[data-bask_sp=" + $(this).attr("data-baskid") + "]").length > 0) {

                //获取单个商品数量（包含三九服务）及 价格
                var dom = $(this).parents("tbody").find("tr[data-bask_sp=" + $(this).attr("data-baskid") + "]");
                selectedNum += parseInt($(this).attr("data-num"));

                dom.each(function() {
                    cartN.totalPrice += parseFloat($(this).find("input:checkbox").attr("data-price")) * parseInt($(this).find("input:checkbox").attr("data-num"));
                    if (cartN.baskIds.indexOf($(this).find("input:checkbox").attr("data-baskid")) == -1) {
                        cartN.baskIds += "," + $(this).find("input:checkbox").attr("data-baskid");
                    }
                });

            } else {
                selectedNum += parseInt($(this).attr("data-num"));
                cartN.totalPrice += parseFloat($(this).attr("data-price")) * parseInt($(this).attr("data-num"));
                if (cartN.baskIds.indexOf($(this).attr("data-baskid")) == -1) {
                    cartN.baskIds += "," + $(this).attr("data-baskid");
                }
            }
        });
        cartN.totalNum = 0;
        cartN.memberAllPrice = 0;
        $("li.c_goods").each(function () {
            var dom = $(this);//.find("input:checkbox");
            cartN.totalNum += parseInt(dom.attr("data-num"));
            cartN.memberAllPrice += parseInt(dom.attr("data-memberprice")) * parseInt(dom.attr("data-num"));
        });
        cartN.selectedNum = selectedNum;


        $("#strAllHeadCount").html(cartN.totalNum);
        $("#strFootCount, #strHeadCount").html(cartN.selectedNum);
        if ($("#rightCart").length > 0) { $("#rightCart b").html(cartN.selectedNum); }
        if (cartN.memberAllPrice == cartN.totalPrice.toFixed(2)) $("#totalcartYH").parent().parent().hide(); else { $("#totalcartYH").html("￥" + parseFloat(cartN.memberAllPrice - cartN.totalPrice.toFixed(2)).toFixed(2)); $("#totalcartYH").parent().parent().show(); };
        $("#str_allPrice").html(cartN.totalPrice.toFixed(2));

        loadCarInfoCallback && loadCarInfoCallback();
    },
    selectOfficeService: function (obj) {
        //选择官方服务 
        var baskid = $(obj).attr("data-baskid") || 0, ppriceid = $(obj).attr("data-ids") || 0, fromid = $(obj).attr("data-id") || 0;
        $.post("/cartHandler.aspx", { "act": "pcService", "baskid": baskid, "ppid": ppriceid, "fromid": fromid }, function (d) {
            if (d == 1) {
                cartN.loadCarInfo();
            } else {
                if (baskid == 0) { layer.msg("购买官方服务失败，请重试~", { shift: 6 }); } else { layer.msg("取消购买官方服务失败，请重试~", { shift: 6 }); }                
            }
        });
    },
    submit: function() {
        if (cartN.selectedNum < 1) {
            layer.msg('请选择需要结算的商品', function() {
            });
            return false;
        }
        var baskids = (cartN.baskIds).substring(1, (cartN.baskIds).length).replace(",undefined", "");
        window.location.href = "/cart/cartStep.aspx?baskids=" + baskids;
        /*if (!cartN.haspromotion) 
        else window.location.href = "/cart/cart.aspx";*/
    }
};