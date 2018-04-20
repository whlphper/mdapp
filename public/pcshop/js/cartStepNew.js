var vcity = {
    11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古",
    21: "辽宁", 22: "吉林", 23: "黑龙江", 31: "上海", 32: "江苏",
    33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南",
    42: "湖北", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆",
    51: "四川", 52: "贵州", 53: "云南", 54: "西藏", 61: "陕西", 62: "甘肃",
    63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外"
};

checkCard = function (obj) {
    var card = $.trim(obj.val());
    if (card.length < 1) {
        layer.tips("请输入身份证号，身份证号不能为空", obj);
        obj.focus();
        return false;
    }
    //校验长度，类型
    if (isCardNo(card) === false) {
        layer.tips("您输入的身份证号码不正确，请重新输入", obj);
        obj.focus();
        return false;
    }

    //检查省份
    if (checkProvince(card) === false) {
        layer.tips("您输入的身份证号码不正确，请重新输入", obj);
        obj.focus();
        return false;
    }

    //校验生日
    if (checkBirthday(card) === false) {
        layer.tips("您输入的身份证号码不正确，请重新输入", obj);
        obj.focus();
        return false;
    }

    //检验位的检测
    if (checkParity(card) === false) {
        layer.tips("您输入的身份证号码不正确，请重新输入", obj);
        obj.focus();
        return false;
    }
    return true;
};


//检查号码是否符合规范，包括长度，类型
isCardNo = function (card) {
    //身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X
    var reg = /(^\d{15}$)|(^\d{17}(\d|X)$)/;
    if (reg.test(card) === false) {
        return false;
    }

    return true;
};

//取身份证前两位,校验省份
checkProvince = function (card) {
    var province = card.substr(0, 2);
    if (vcity[province] == undefined) {
        return false;
    }
    return true;
};

//检查生日是否正确
checkBirthday = function (card) {
    var len = card.length;
    //身份证15位时，次序为省（3位）市（3位）年（2位）月（2位）日（2位）校验位（3位），皆为数字
    if (len == '15') {
        var re_fifteen = /^(\d{6})(\d{2})(\d{2})(\d{2})(\d{3})$/;
        var arr_data = card.match(re_fifteen);
        var year = arr_data[2];
        var month = arr_data[3];
        var day = arr_data[4];
        var birthday = new Date('19' + year + '/' + month + '/' + day);
        return verifyBirthday('19' + year, month, day, birthday);
    }
    //身份证18位时，次序为省（3位）市（3位）年（4位）月（2位）日（2位）校验位（4位），校验位末尾可能为X
    if (len == '18') {
        var re_eighteen = /^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/;
        var arr_data = card.match(re_eighteen);
        var year = arr_data[2];
        var month = arr_data[3];
        var day = arr_data[4];
        var birthday = new Date(year + '/' + month + '/' + day);
        return verifyBirthday(year, month, day, birthday);
    }
    return false;
};

//校验日期
verifyBirthday = function (year, month, day, birthday) {
    var now = new Date();
    var now_year = now.getFullYear();
    //年月日是否合理
    if (birthday.getFullYear() == year && (birthday.getMonth() + 1) == month && birthday.getDate() == day) {
        //判断年份的范围（3岁到100岁之间)
        var time = now_year - year;
        if (time >= 3 && time <= 100) {
            return true;
        }
        return false;
    }
    return false;
};

//校验位的检测
checkParity = function (card) {
    //15位转18位
    card = changeFivteenToEighteen(card);
    var len = card.length;
    if (len == '18') {
        var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        var cardTemp = 0, i, valnum;
        for (i = 0; i < 17; i++) {
            cardTemp += card.substr(i, 1) * arrInt[i];
        }
        valnum = arrCh[cardTemp % 11];
        if (valnum == card.substr(17, 1)) {
            return true;
        }
        return false;
    }
    return false;
};

//15位转18位身份证号
changeFivteenToEighteen = function (card) {
    if (card.length == '15') {
        var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        var cardTemp = 0, i;
        card = card.substr(0, 6) + '19' + card.substr(6, card.length - 6);
        for (i = 0; i < 17; i++) {
            cardTemp += card.substr(i, 1) * arrInt[i];
        }
        card += arrCh[cardTemp % 11];
        return card;
    }
    return card;
};
var cartStep = {
    payID: "1",/*支付方式ID*/
    pickupID: "",/*取货方式ID 4为送货上门 1为门店交易 3为自提点交易*/
    toAddrID: "",/*收货地址ID*/
    zitiID: "",/*自提点ID*/
    shopID: "",/*门店ID*/
    coupons: '', /*优惠券*/
    couponsPrice:0,/*优惠码 或 其他 优惠金额*/
    productIds: 0,/*结算商品ID*/
    isGetJiaji: 0,/*是否领取加急体验卡 1为已领 否则 未领*/
    JiajiCount: 0,/*加急体验卡剩余张数*/
    JiajiPrice: 0,/*加急体验卡优惠金额*/
    JiajiId: 0,/*加急体验卡ID*/
    needMobile:"",/*如果是门店交易 或者  自提点交易，用来存储用户电话号码*/
    Points: 0,/*积分*/
    PointsPrice:0,/*积分抵扣金额*/
    Balances: 0,/*余额*/
    BalancesPrice: 0,/*余额抵扣金额*/
    feePrice:0,/*运费*/
    cartTimer: null,
    promotionId: '',/*促销Id*/
    //isservice:'',/*是否是保税商品*/
    data: {},/*用于存放需要提交的字段*/
    Lianghao:null,/*用于存放需要提交的身份证信息相关字段*/
    initCartStep: function () {
        $(".c_addr_box").hover(function () {
            clearTimeout(cartStep.cartTimer);
            $(this).addClass("c_addr_cur").css({ zIndex: 10000 });
            var pos = $(this).offset();
            $(".city-wrap").show().css({ left: pos.left, top: pos.top + 24, position: "absolute" }).attr("data-type", $(this).attr("data-type"));
            $(".city-wrap>p").hide();
            Area.change();
        }, function () {
            cartStep.cartTimer = setTimeout(function () {
                $(".c_addr_box").removeClass("c_addr_cur");
                $(".city-wrap").hide().removeAttr("data-type");
                $(".city-wrap>p").show();
            }, 300);
        });
        $(".city-wrap").hover(function () { clearTimeout(cartStep.cartTimer); }, function () {
            if (Cookie.Get("ischecked") == "1") {
                $(".city-wrap").hide().removeAttr("data-type");
                $(".city-wrap>p").show();
            }
            $(".c_addr_box").removeClass("c_addr_cur");
        });
        cartStep.initToAddrList();        
        loadCarInfoCallback = function () {
            $("#code_list").html(template("codeTmpl", cartN.codes));
            cartStep.calcPrice();

            if (cartN.isSupportJiaji != null) {
                //"setupExp":{"usedCount":0,"privilegeCount":0,"Id":0,"Geted":false}
                var leftCount = cartN.isSupportJiaji.privilegeCount - cartN.isSupportJiaji.usedCount;
                var Geted = cartN.isSupportJiaji.Geted ? 1 : 0;/*是否领取加急体验卡 1为已领 否则 未领*/
                var jiajiInfo = "", HasMobile = cartN.isSupportJiaji.HasMobile;/*有没有大件*/

                if (HasMobile) {
                    cartStep.isGetJiaji = Geted;
                    cartStep.JiajiCount = leftCount;/*加急体验卡剩余张数*/
                    cartStep.JiajiId = cartN.isSupportJiaji.Id;/*加急体验卡ID*/

                    //if (!Geted) {
                    //    jiajiInfo = leftCount > 0 ? "您可以领取<em class=\"red\">" + leftCount + "</em>张体验卡。<a href=\"/vipclub/privilege/11.html\" target=\"_blank\">[点我领取]</a>" : "会员升级后，可领取免费体验特权。<a href=\"/vipclub/privilege/11.html\" target=\"_blank\">[特权详情]</a>";
                    //} else {
                    //    jiajiInfo = leftCount > 0 ? "您拥有<span class=\"red\">" + leftCount + "</span>张免费体验卡，可勾选前面选择框使用。" : "您的加急配送体验卡已用完";
                    //}
                    //$("#jiajiTiYanBtn").siblings(".jiaji-info").html(jiajiInfo);
                }

            }

            //if (cartStep.isservice > 0) {
            $.post("/order/cartapi.aspx", { act: 'checkProduct', baskits: cartStep.productIds }, function (rsp) {
                    if (rsp == "0") {
                        layer.open({
                            content: '购物车中包含保税商品，需要完善资料！',
                            btn: ["好的"],
                            yes: function () {
                                location.href = 'member_index.htmlrealname.aspx';
                                setTimeout(function () {
                                    location.href = 'member_index.htmlrealname.aspx';
                                }, 3000);
                            },
                            closeBtn: false
                        });
                    }
                }, "text");
            };
        //}
        cartN.loadCarInfo(cartStep.productIds, cartStep.pickupID, cartStep.promotionId);
        cartStep.loadPayList();
        
    },
    setPickupID: function (obj, id) {
        /*选择取货方式*/
        var self = $(obj);
        self.addClass("cur").siblings(".cur").removeClass("cur");
        this.pickupID = id;
        if (id == 4) {
            $(".c_delivery").removeClass("hide");
            $(".c_shop,.c_ziti").addClass("hide");
            if ($(".c_delivery_list .cur").length > 0) {
                cartStep.loadPayList($(".c_delivery_list .cur").attr("data-payid"));
            }
        } else if (id == 3) {
            $(".c_ziti").removeClass("hide");
            $(".c_shop,.c_delivery").addClass("hide");
            if ($(".c_ziti .default_shop .cur").length < 1) {
                $(".c_ziti .default_shop .link").trigger("click");
            } else {
                cartStep.loadPayList('1,2,3');
            }
        }else{
            $(".c_shop").removeClass("hide");
            $(".c_delivery,.c_ziti").addClass("hide");
            if ($(".cart-style .cart-style-sel").length < 1) {
                $(".cart-style .cart-style-sel").trigger("click");
            } else {
                cartStep.loadPayList('1,2,3,7');
            }
        }

        cartStep.calcPrice();
    },
    setToAddr: function (obj, id) {
        /*选择收货地址*/
        var self = $(obj).parent();
        self.addClass("cur").siblings(".cur").removeClass("cur");
        this.toAddrID = id;
        //if (self.attr("data-ps") == "1") {
        if (self.attr("data-did") == "2679" || self.attr("data-did") == "2680" || self.attr("data-did") == "2681" || self.attr("data-did") == "2682" || self.attr("data-did") == "2683") {
            $("#jiajidiv").show();
            //$("#ToAddrTime").show();
            $("#jiajidiv").parent().show();
        } else {
            $("#ToAddrTime,#jiajidiv").hide();
            $("#jiajidiv").parent().hide();

        }
        cartStep.loadPayList(self.attr("data-payid"));

        cartStep.calcPrice();
    },
    getToAddrTime: function (obj) {
        /*获取商品送达时间 */
        var self = $(obj).parent();
        self.addClass("cur").siblings(".cur").removeClass("cur");
        this.toAddrID = id;
        if (self.attr("data-did") == "2679" || self.attr("data-did") == "2680" || self.attr("data-did") == "2681" || self.attr("data-did") == "2682" || self.attr("data-did") == "2683") {
            $("#jiajidiv").show();
        } else {
            $("#jiajidiv").hide();
        }
        cartStep.loadPayList(self.attr("data-payid"));
    },
    initToAddrList: function () {
        $.ajax({
            url: "/order/cartapi.aspx",
            dataType: 'json',
            data: { 'act': 'loadAllAddr'},
            type: 'post',
            cache: false,
            async: false,
            success: function (rsp) {
                $("#address").html(template("addrsTmpl", rsp.addInfo));
                //$(".cart-address").before(template("addrsTmpl", rsp.addInfo));
                //$(".cart-addr-box .cart-addr-box-on") && $(".cart-addr-box .cart-addr-box-on").trigger("click");
                //$(".cart-addr-tab .cart-addr-tab-on").trigger("click");
            }
        });
    },
    saveToAddr: function (id) {
        var editAddrData = { act: "addOrEdit" };
        editAddrData.txtReceiveName = $.trim($("#receiveName").val());
        editAddrData.txtAddress = $.trim($("#txtAddress").val());
        //editAddrData.txtPostCode = $.trim($("#txtPostCode").val());
        editAddrData.txtPhone = $.trim($("#txtPhone").val());
        editAddrData.txtMobile = $.trim($("#txtMobile").val());
        editAddrData.isdefault = $.trim($("input[name=isdefault]").val());

        //editAddrData.ddlProvince = $("#setAddr").attr("data-pid") || 3363;
        //editAddrData.ddlCity = $("#setAddr").attr("data-zid") || 3364;
        //editAddrData.ddlArea = $("#setAddr").attr("data-did") || 3365;


        editAddrData.ddlProvince = $("#ddlProvince").find("option:selected").val() || 99;
        editAddrData.ddlCity = $("#ddlCity").find("option:selected").val() || 9901;
        editAddrData.ddlArea = $("#ddlArea").find("option:selected").val() || 990101;

        editAddrData.isdefault = 0;
        editAddrData.xzdz = (id == 0) ? "addNew" : id;

        var ismobi = /^1[3|4|5|7|8][0-9]\d{8}$/.test(editAddrData.txtMobile);
        var isPostCode = /^\d{6}$/.test(editAddrData.txtPostCode);
        var isTel = /^\d{11,12}$/.test(editAddrData.txtPhone);

        if ($("input[name=isdefault]").is(":checked")) {
            editAddrData.isdefault = 1;
        }

        if (editAddrData.txtReceiveName.length < 1) {
            TipMsg.position("请填写收货人姓名！", $("#receiveName"), 20000);
            $("#receiveName").focus();
            return false;
        }
        if (editAddrData.ddlArea == 990101) {
            var obj = $("#ddlProvince"); var msg = '';
            if (editAddrData.ddlProvince == 0) {
                msg = "请选择省份";
            }
            else if (editAddrData.ddlProvince != 0 && editAddrData.ddlCity == 0) {
                obj = $("#ddlCity");
                msg = "请选择城市";
            }
            TipMsg.position(msg, obj, 2000);
            return false;
        }
        if (editAddrData.txtAddress.length < 1 || editAddrData.txtAddress.length>30) {
            TipMsg.position("请填写详细收货地址，地址长度在20个字符内！", $("#txtAddress"), 2000);
            $("#txtAddress").focus();
            return false;
        }
        //if (editAddrData.txtPostCode.length != 0 && !isPostCode) {
        //    TipMsg.position("请正确填写邮编！", $("#txtPostCode"), 2000);
        //    $("#txtPostCode").focus();
        //    return false;
        //}
        //if (editAddrData.txtPhone.length < 1 && editAddrData.txtMobile.length < 1) {
        //    TipMsg.position("固话与手机至少填写一项！", $("#txtPhone"), 2000);
        //    $("#txtPhone").focus();
        //    return false;
        //}
        //if (editAddrData.txtPhone.length > 0 && !isTel) {
        //    TipMsg.position("您的电话号码填写有误！", $("#txtPhone"), 2000);
        //    $("#txtPhone").focus();
        //    return false;
        //}
        if (editAddrData.txtPhone.length < 1 && editAddrData.txtMobile.length < 1) {
            TipMsg.position("请填写手机号码！", $("#txtMobile"), 2000);
            $("#txtMobile").focus();
            return false;
        }
        if (editAddrData.txtMobile.length > 0 && !ismobi) {
            TipMsg.position("您的手机号码填写有误！", $("#txtMobile"), 2000);
            $("#txtMobile").focus();
            return false;
        }

        $.post("/order/cartapi.aspx", editAddrData, function (d) {
            if (d.stats == 1) {
                cartStep.initToAddrList();
                cartStep.closeEditDelivery();
                layer.closeAll();
            } else {
                TipMsg.position(d.result, $("#editAddrBox button"), 2000);
            }
        }, "json");

    },
    editToAddr: function (obj) {
        var self = $(obj).parents("li");
        var setting = {};

        setting.id = self.attr("data-id");
        setting.pid = self.attr("data-pid");
        setting.zid = self.attr("data-zid");
        setting.did = self.attr("data-did");
        setting.pName = self.attr("data-pname");
        setting.zName = self.attr("data-zname");
        setting.dName = self.attr("data-dname");
        setting.addr = self.attr("data-addr");
        setting.userName = self.attr("data-uname");
        setting.postCode = self.attr("data-postcode");
        setting.phone = self.attr("data-phone");
        setting.mobile = self.attr("data-mobile");
        setting.isdefault = self.attr("data-isdefault");

        cartStep.initEditDelivery(setting);
    },
    delAddress: function (id) {
        if (confirm("您确定要删除吗？")) {
            $.post("/order/cartapi.aspx", { act:'delAddr', 'addId': id }, function (res) {
                if (res.stats == '1') {
                    $(".c_delivery_list li[data-id=" + id + "]").remove();
                }
            }, 'json');
        }
    },
    setDefaultAddr: function (obj, id) {
        //opeType请求类型（1：修改，2：删除，3：设为默认地址，4：保存添加地址信息,5：保存修改地址信息）
        $.post("/order/cartapi.aspx?act=SetDefaultAddr", { 'addId': id }, function (res) {
                if (res.status == 1) {
                    cartStep.initToAddrList();
                }
                else {
                    TipMsg.position(res.result, $(obj), 2000);
                }
            }, 'json');

    },
    initEditDelivery: function (setting) {
        var defaults = {
            id: 0,
            pid: Area.iProvince || "99",
            zid: Area.iCity || "9901",
            did: Area.iCounty || "990101",
            pName: Area.iProvinceName || "全国",
            zName: Area.iCityName || "全国",
            dName: Area.iCountyName || "全国",
            userName: "",
            mobile: "",
            addr: "",
            phone: "",
            postCode: "",
            isdefault:0
        };
        var config = $.extend(defaults, setting || {});

        this.initSelectArea(config);
        var title;
        var btn;
        if (config.id != 0) {
            // $("#editAddrBox h2 span").text("修改收货地址");
            title = "添加收货地址";
            btn = "保存新地址";
        } else {
            //$("#editAddrBox h2 span").text("新增收货地址");
            title = "修改收货地址";
            btn = "保存收货地址";
        }

        $("#receiveName").val(config.userName);
        //$("#setAddr span").html(config.pName + "/" + config.zName + "/" + config.dName).parent().attr({
        //    "data-pid": Area.iProvince,
        //    "data-zid": Area.iCity,
        //    "data-did": Area.iCounty
        //});

        city.set(config.pid, config.zid, config.did);



        $("#txtAddress").val(config.addr);
        //$("#txtPostCode").val(config.postCode);
        $("#txtPhone").val(config.phone);
        $("#txtMobile").val(config.mobile);
        if (config.isdefault == 0) { $("input[name=isdefault]").removeProp("checked"); } else { $("input[name=isdefault]").prop("checked", "checked"); }

        //$("#editAddrBox button").attr("onclick", "cartStep.saveToAddr(" + config.id + ")");
        //$("#editAddrBox").show();
        //if ($("#markdiv").length < 1) {
        //    $("body").append('<div id="markdiv" class="markdiv"></div>')
        //}
        //$("#markdiv").show();

        layer.open({
            type: 1,
            title: title,
            //scrollbar: false,
            btn: btn,
            area: ['550px', '340px'], //宽高
            content: $("#editAddrBox"),
            yes: function (index) {
                //cartStep.saveToAddr($edit);
                cartStep.saveToAddr(config.id);
            }
        });
    },
    closeEditDelivery: function () {
        $("#editAddrBox").hide();
        $("#markdiv").remove();
    },
    initSelectArea: function (setting) {
        var defaults = {
            id: 0,
            pid: Area.iProvince || "99",
            zid: Area.iCity || "9901",
            did: Area.iCounty || "990101",
            pName: Area.iProvinceName||"全国",
            zName: Area.iCityName || "全国",
            dName: Area.iCountyName || "全国",
            hasShop:false
        };
        var config = $.extend(defaults, setting || {});

        Area.init(config.pid, config.zid, config.did, config.pName, config.zName, config.dName);
    },
    cutoverShop: function (obj) {
        //切换门店、自提点信息
        var self = $(obj);
        var html = $("#mama-shop");//self.parents(".default_shop").siblings(".cutover_shop");
        var title = "";
        if (cartStep.pickupID == 1) {
            cartStep.shopID = 0;/*门店ID*/

            cartStep.getShopList();
            title = "请选择门店";
        } else if (cartStep.pickupID == 3) {
            cartStep.zitiID = 0;/*自提点ID*/
            cartStep.getZitiList();
            title = "请选择自提点";
        }
        //self.parent().hide().siblings(".cutover_shop").show();
        layer.open({
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['945px', '540px'],  //宽高
            content: html,
            title: title,
            btn: ['确定'],
            success: function () {
                $(document).on("click","#shopList li",function () {
                    $(this).addClass("cart-sel-shop-on").siblings().removeClass("cart-sel-shop-on");
                    $("#shop_select").html($(this).find("h4").find("span").html());
                    $("#shop_select").attr("area", $(this).find("input[name=dpid]").val());
                });
            },
            yes: function () {
                cartStep.setShop(this, 'shop');
            }
        });
    },
    selectShop: function (obj,id) {
        /*选择门店*/
        var self = $(obj).parent();
        self.addClass("cur").siblings(".cur").removeClass("cur");
        cartStep.shopID = id;
        cartStep.loadPayList(self.attr("data-payid"));
    },
    selectZiti: function (obj, id) {
        /*选择自提点*/
        var self = $(obj).parent();
        self.addClass("cur").siblings(".cur").removeClass("cur");
        cartStep.zitiID = id;
        cartStep.loadPayList(self.attr("data-payid"));
    },
    setShop: function (obj, _type) {
        var dom = $("#" + _type + "List .cart-sel-shop-on");
        if (dom.length < 1) { layer.open({content:'亲，别急，还没有选择门店~',icon:5}); return false; }
        var html = "";
        if (_type == 'shop') {
            //html = '<div class="cart-style-sel" class="cur" onclick="cartStep.cutoverShop(this)" href="javascript:;">' + dom.attr("data-tit") + '</div><strong> 地址：' + (dom.attr("data-o").indexOf("（") != -1 ? dom.attr("data-o").substring(0, dom.attr("data-o").indexOf("（")) : dom.attr("data-o")) + ' <a href="javascript:;" class="link" onclick="cartStep.cutoverShop(this)">修改</a></strong>';
            html = '<div class="cart-style-shop"><h4>' + dom.attr("data-tit") + '<a href="javascript:cartStep.cutoverShop(this);">更换门店</a></h4><p>地址：' + (dom.attr("data-o").indexOf("（") != -1 ? dom.attr("data-o").substring(0, dom.attr("data-o").indexOf("（")) : dom.attr("data-o")) + ' </p> <p>销售热线：0871-68393939</p><p>工作时间：周一至周日:9:00-21:301 </p></div>';
        } else {
            html = '<a onclick="cartStep.cutoverShop(this)" href="javascript:;" class="cur">' + dom.attr("data-tit") + '</a> ' + dom.attr("data-o") + '<a href="javascript:;" class="link" onclick="cartStep.cutoverShop(this)">修改</a>';

        }
        html = "<h1>选择门店</h1>" + html;
        //self.parents(".cutover_shop").hide().siblings(".default_shop").html(html).show();
        layer.closeAll();
        //$(".c_" + _type + " .default_shop").html(html);
        $("#shop_select").html(html);
        cartStep.calcPrice();
    },
    loadPayList: function (payId) {
        var payIds = payId || "";
        if(payIds==""){
            switch (cartStep.pickupID) {
                case "3":
                    payIds = '1,2';
                    break;
                case "1":
                    payIds = '1,2,3,7';
                    break;
                case "4":
                    payIds = ($(".c_delivery_list .cur").length > 0) ? $(".c_delivery_list .cur").attr("data-payid") : '1,3';
                    break;
                default:
                    payIds = "1,3";
                    break;
            }
        }
        var payArr = payIds.split(",");
        $(".c_pay_list li").addClass("no").removeClass("cur");
        for (var i = 0; i < payArr.length; i++) {
            $(".c_pay_list li[data-id=" + payArr[i] + "]").removeClass("no");
        }
        if ($(".c_pay_list li[data-id=" + cartStep.payID + "]").hasClass("no")) {
            cartStep.payID = 1;
        }
        $(".c_pay_list li[data-id=" + cartStep.payID + "]").addClass("cur");
    },
    setPayId: function (obj, id) {
        /*选择支付方式*/
        var self = $(obj);
        if (!self.hasClass("no")) {
            self.addClass("cur").siblings(".cur").removeClass("cur");
            cartStep.payID = id;
        }
        session('curOrderPayType',id);
        //cartStep.calcPrice();
    },
    isHasShop: function (zid, did) {
        var HasShop = false;
        for (var i = 0; i < Area.cityData.length; i++) {
            var cities = Area.cityData[i];
            if (cities.id == zid && cities.isshop) {
                for (var j = 0; j < cities.items.length; j++) {
                    var items = cities.items[j];
                    if (items.id == did && items.iskc) {
                        HasShop = true;
                    }
                }
            }
        }
        return HasShop;
    },
    getShopList: function () {
        $.get("/order/cartapi.aspx?act=GetShops&zid=" + Area.iCounty, {}, function (shops) { //Area.iCounty
            if (shops.length > 0) {
                $("#shopList").html(template("shopTmpl", shops));
            } else {
                $("#shopList").html('<p class="loading1">该地区暂无门店，支持全场免邮配送！</p>')
            }
        }, "json");
        $(".c_addr_box[data-type=c_shop]").attr({
            "data-pid": Area.iProvince,
            "data-zid": Area.iCity,
            "data-did": Area.iCounty
        }).find("span").html(Area.iProvinceName + "/" + Area.iCityName + "/" + Area.iCountyName);
        $(".city-wrap").hide().removeAttr("data-type");
    },
    getZitiList: function () {

        $.post("/order/cartapi.aspx", { act: "getzitibyCityidSearch", did: Area.iCounty }, function (rsp) {
            if (rsp.stats == 1) {
                var result = rsp.result;
                var html = [];
                for (var i = 0; i < result.length; i++) {
                    var item = result[i];
                    var iName = item.name;
                    html.push("<li data-payid=\"1,2,3\" data-o=\"地址：", item.address, " 电话：", item.tel1, " 联系人：", item.nickcontact, " 工作时间：", item.hours, "\" data-id=\"", item.id, "\" data-tit=\"", (iName.length > 16 ? iName.substring(0, 16) + "..." : iName), "\" data-type=\"zitidian\"><p><a href=\"javascript:sendToPhone('",
                    Area.iCountyName, " ", item.name, "自提点\\n地址：", item.address, "\\n电话：", item.tel1, "\\n联系人：", item.nickcontact, "\\n工作时间：", item.hours, "',", item.id, ",2);\">发送到手机</a></p><div onclick=\"cartStep.selectZiti(this,", item.id, ")\" class=\"c_d_box\"><h4 class=\"tit\">", (iName.length > 10 ? iName.substring(0, 10) + "..." : iName), "</h4>");
                    html.push("<p>地址：", item.address, "</p><p>自提点电话：", item.tel1, "</p>");
                    html.push("<p>联系人：", item.nickContact, "</p>");
                    html.push("<p>工作时间：", item.hours, "</p>");
                    html.push("<p>周边信息：", item.zhoubian, "</p></div></li>");
                }
                $("#zitiList").html(html.join(""));
            } else {
                $("#zitiList").html('<li class="loading1 red">该地区暂无自提点，支持全场免邮配送！</li>');
            }
        }, "json");

        $(".c_addr_box[data-type=c_ziti]").attr({
            "data-pid": Area.iProvince,
            "data-zid": Area.iCity,
            "data-did": Area.iCounty
        }).find("span").html(Area.iProvinceName + "/" + Area.iCityName + "/" + Area.iCountyName);
        $(".city-wrap").hide().removeAttr("data-type");
    },
    selectCoupon: function (obj) {
        var price = 0;
        var self = $(obj);
        var box=self.parent();
        var fold = self.attr("data-fold");

        cartStep.coupons = "";
        box.parent().removeClass("cur_cart");
        if (self.hasClass("other")) {
            self.siblings(".cur").removeClass("cur");
            $("#sel-code").html("输入优惠码");
            $("#inputCoupon").show();
        } else {
            $("#inputCoupon").hide();
            if (self.hasClass("cur")) {
                self.removeClass("cur");
            } else {
                //<%--data-fold 0为不可叠加 1为可叠加--%>
                if (fold == 0) {
                    self.addClass("cur").siblings(".cur").removeClass("cur");
                } else {
                    self.addClass("cur").siblings(".cur[data-fold=0]").removeClass("cur");
                }
            }
        }

        if (box.find(".cur").length > 0) {
            box.find(".cur").each(function () {
                price += parseInt($(this).attr("data-price"));
                cartStep.coupons += (cartStep.coupons==""?"":",") + $(this).attr("data-code");
            });
            $("#couponBox .num").text(box.find(".cur").length);
            $("#couponBox .price").text(price);
            cartStep.couponsPrice=price;
            //box.siblings("span").html("您已选择" + box.find(".cur").length + "张优惠码，可优惠" + price + "元");
            $("#sel-code").html("已选择 " + box.find(".cur").length + " 张优惠码");
        } else {
            if (!self.hasClass("other")) {
                box.siblings("span").html("请选择优惠码");
            }
            $("#couponBox .num").text(0);
            $("#couponBox .price").text(0);
            cartStep.couponsPrice=0;
        }

        cartStep.calcPrice();


    },
    customCoupon: function (obj) {
        var self = $(obj);
        var coupon = self.siblings("input");
        if ($.trim(coupon.val()).length < 2) {
            TipMsg.position("请输入您的优惠码", coupon, 2000);
            return false;
        }
        self.attr("disabled", "disabled");
        $.ajax({
            url: "cartApi.aspx",
            dataType: 'json',
            cache: false,
            async: false,
            data: { 'act': 'ValidateTicket', 'newTicket': $.trim(coupon.val()), baskids: cartStep.productIds,promotionId:cartStep.promotionId },
            success: function (txt) {
                self.removeAttr("disabled");
                if (txt.stats == 1) {
                    var dom = $('<li onclick="cartStep.selectCoupon(this)" data-price="' + txt.total + '" data-code="' + $.trim(coupon.val()) + '" data-fold="' + (txt.limit2 ? "1" : "0") + '">' + txt.total + '元优惠码【' + (txt.limit2 ? "可叠加" : "不可叠加") + '】</li>');
                    coupon.val('');
                    $("#code_list .other").before(dom);
                    dom.trigger("click");
                } else {
                    TipMsg.position(txt.result, self, 2000);
                    return false;
                }
            }
        });

        cartStep.calcPrice();
    },
    selectOrderBalances: function (obj) {
        //使用余额
        var self = $(obj);
        var maxBalances = self.attr("data-maxye") || 0;
        var Balances = parseFloat($.trim($("#Balances").val()));
        if (Points > maxBalances) {
            layer.tips("很抱歉，您当前的可用余额为：" + maxBalances, self, {
                tips: 1
            });
        } else if (Balances > 0) {
            cartStep.Balances = Balances;
            cartStep.BalancesPrice = Balances;

            self.parents(".jf_box").hide().siblings(".jf_box1").show();
            self.parents(".form").find(".j_balances").text(cartStep.BalancesPrice.toFixed(2));

        } else {
            cartStep.Balances = 0;
            cartStep.BalancesPrice = 0;
        }

        cartStep.calcPrice();

    },
    selectOrderPoints: function (obj) {
        //使用积分
        var self = $(obj);
        var maxJF = parseInt($("#avaliableIntegral").text()) || 0;
        var Points = parseFloat($.trim($("#Points").val())) || 0;
        if (Points <= 0) {
            layer.tips("请输入您想使用的积分", self.siblings("input"), {
                tips: 1
            });
            return false;
        }
        if (Points > maxJF) {
            layer.tips("很抱歉，您当前的可用积分为：" + maxJF, self.siblings("input"), {
                tips: 1
            });
        }else if (Points > 0) {
            cartStep.Points = Points;
            cartStep.PointsPrice = parseFloat(Points / 100);

            self.parents(".jf_box").hide().siblings(".jf_box1").show();


            self.parents(".form").find(".j_use_jf").text(Points);
            self.parents(".form").find(".j_use_jf_p").text(cartStep.PointsPrice.toFixed(2));

        } else {
            cartStep.Points = 0;
            cartStep.PointsPrice = 0;
        }

        cartStep.calcPrice();
    },
    cancelBalances: function () {
        //取消余额
        var self = $(obj);
        self.parents(".jf_box1").hide().siblings(".jf_box").show();
        $("#Balances").val("");
        self.parents(".form").find(".j_balances").text("0");
        cartStep.Points = 0;
        cartStep.PointsPrice = 0;

        cartStep.calcPrice();
    },
    cancelPoints: function (obj) {
        //取消积分

        var self = $(obj);
        self.parents(".jf_box1").hide().siblings(".jf_box").show();
        $("#Points").val("");
        self.parents(".form").find(".j_use_jf").text("0");
        self.parents(".form").find(".j_use_jf_p").text("0.00");
        cartStep.Points = 0;
        cartStep.PointsPrice = 0;

        cartStep.calcPrice();
    },
    setFeePrice: function (obj) {
        //设置运费

        cartStep.calcPrice();
    },
    calcPrice: function () {
        //$("#totalMemberPrice").html("￥" +  parseFloat(cartN.memberAllPrice).toFixed(2));

        if (cartStep.coupons.length < 1) {
            cartStep.couponsPrice = cartN.memberAllPrice - cartN.totalPrice;
        }

        if ($(".cart-jiaji input").is(":checked") && $(".cart-jiaji input").is(":visible")) {
            if (cartStep.isGetJiaji == 1 && cartStep.JiajiCount > 0) {
                //isGetJiaji: 0,/*是否领取加急体验卡 1为已领 否则 未领*/
                //JiajiCount: 0,/*加急体验卡剩余张数*/
                //JiajiPrice: 0,/*加急体验卡优惠金额*/
                //JiajiId: 0,/*加急体验卡ID*/
                cartStep.JiajiPrice = 20;
            } else {
                cartStep.JiajiPrice = 0;
            }

            cartStep.feePrice = 20;
        } else {
            cartStep.feePrice = 0;
            cartStep.JiajiPrice = 0;
        }


        $("#fee").html("￥" + parseFloat(cartStep.feePrice).toFixed(2));
        $("#totalYH").html("￥" +parseFloat(cartStep.couponsPrice + cartStep.JiajiPrice).toFixed(2));
        if (cartStep.PointsPrice > 0) {
            $("#totalJF").html("￥" + parseFloat(cartStep.PointsPrice).toFixed(2)).parent().show();
        } else {
            $("#totalJF").parent().hide();
        }

        if (cartStep.BalancesPrice > 0) {
            $("#totalBalances").html("￥" + parseFloat(cartStep.BalancesPrice).toFixed(2)).parent().show();
        } else {
            $("#totalBalances").parent().hide();
        }

        var str_allPrice = parseInt(cartN.memberAllPrice) + cartStep.feePrice - cartStep.couponsPrice - cartStep.PointsPrice - cartStep.BalancesPrice - cartStep.JiajiPrice;

        //$("#str_allPrice").html("￥" +parseFloat(str_allPrice).toFixed(2));
    },
    showUploadCard: function (_type) {
        if (_type == undefined || _type == null || $.trim(_type).length < 1) {
            $("#showUpdate") && $("#showUpdate").show();
            if ($("#markdiv").length < 1) {
                $("body").append('<div id="markdiv" class="markdiv"></div>')
            }
            $("#markdiv").show();
        } else {
            $("#showUpdate").hide(); $("#markdiv").remove();
        }
    },
    submitCard: function (obj) {

        var numberId = $("#showUpdate").attr("numberId");
        var number = $("#showUpdate").attr("number");
        var userName = $("#userName");
        var cardNumber = $("#cardNumber");
        if ($.trim(userName.val()).length < 1) {
            layer.tips("请填写户主姓名！", userName);
            return;
        }
        var cardFlag = checkCard($("#cardNumber"));
        if (!cardFlag) {
            layer.tips("请正确填写身份证号码", $("#cardNumber"));
            return false;
        }
        if (pic1 == "" || pic2 == "") {
            layer.tips("请上传身份证照片！", $("#pic1").parent());
            return;
        }

        cartStep.Lianghao = {
            userName: userName.val(),
            cardNumber: cardNumber.val(),
            pic1: pic1,
            pic2: pic2
        };
        cartStep.submit(obj);
    },
    
    delImg: function (obj) {
        var dom = $(obj).parent();
        dom.hide();
        dom(obj).parent().siblings("input").show();
        dom(obj).parent().siblings(".imgTip").hide();
    },
    needMobileBlur: function (obj) {
        var needMobile = $.trim($(obj).val());
        if (!IsMobile(needMobile)) {
            $(obj).focus();
            layer.tips("请正确填写，手机号码将作为取机凭证哟！", $(obj), { tips: 1 });
            return false;
        }
        cartStep.needMobile = needMobile;
    },
    submit: function (obj) {
        var self = $(obj);
        
        if (cartStep.pickupID == 4 && cartStep.toAddrID == "") {
            layer.tips("请选择收货地址", self, { tips: 1 });
            return false;
        }

        if (cartStep.pickupID == 1 && cartStep.shopID == "") {
            layer.tips("请选择取货门店", self, { tips: 1 });
            return false;
        }

        if (cartStep.pickupID == 3 && cartStep.zitiID == "") {
            layer.tips("请选择自提点", self, { tips: 1 });
            return false;
        }

        if ((cartStep.pickupID == 1 || cartStep.pickupID == 3) && !IsMobile(cartStep.needMobile)) {
            $(window).scrollTop($("input[name=zt_phone]:visible").offset().top-20);
            layer.tips("请正确填写，手机号码将作为取机凭证哟！", $("input[name=zt_phone]:visible"), { tips: 1 });
            return false;
            
        }
        if (cartStep.payID == "") {
            layer.tips("请选择付款方式", self, {tips: 1});
            return false;
        }
      
        var couponsOtherSev='';
        if($(".o_service input:checkbox:checked").length>0){
            $(".o_service input:checkbox:checked").each(function(){
                couponsOtherSev += $(this).val() + "；";
            });
        }

        var isRush = false;
        if ($("#jiajiTiYanBtn input").is(":checked") && $("#jiajiTiYanBtn input").is(":visible")) {
            isRush = true;
        }
        //去结算
        var data = {
            pickupID: isRush ? "5" : cartStep.pickupID,/*取货方式ID 4为配送 1为门店 3为自提*/
            payID: cartStep.payID,/*支付方式ID*/
            toAddrID: cartStep.toAddrID,/*收货地址ID*/
            zitiID: cartStep.zitiID,/*自提点ID*/
            shopID: cartStep.shopID,/*门店ID*/
            coupons: cartStep.coupons, /*优惠券*/
            productIds: cartStep.productIds, /*结算商品ID*/
            Points: cartN.points,/*积分*/
            Balance: cartStep.Balance,/*余额*/
            remarks: $.trim($("#remarks").val()),/*备注*/
            act: 'submit',
            promotionId:cartStep.promotionId,
            couponsOtherSev: couponsOtherSev,
            //isservice:cartStep.isservice,
            mobile:cartStep.needMobile/*自提点、门店交易时使用的电话号码*/
        };
        cartStep.data = $.extend(data, cartStep.Lianghao || {});
        $(obj).attr("disabled", "disabled");
        $.post("/order/cartApi.aspx", data, function (rsp) {
            $(obj).removeAttr("disabled");
            if (rsp.stats == 1) {
                window.location.href = '/order/pay.aspx?orderNum=' + rsp.result;
            } else {
                layer.tips(rsp.result, self);
            }
        }, "json");
        //console.log(cartStep.data);

    }
};
