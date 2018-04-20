function isKC(d) {
    switch (d) {
        case 1: return "现货"; break;
        case 2: return "在途"; break;
        case 3: return "预订"; break;
        case 4: return "已下市"; break;
        case 5: return "缺货"; break;
        case 6: return "预约"; break;
        case 7: return "有货"; break;
    }
}

/*
var ajaxReuestResutl = {
    "Id": "25537_2691",
    "CityId": 2691,
    "PPID": 25537,
    "WebStoreState": 1,
    "ShopStoreState": 1,
    "WebStoreDes": "从<i>麒麟区仓</i>发货,17:00前完成下单，3小时内送达",
    "ShopStores": [{ "Id": 25, "ShopName": "阿诗玛店", "StoreDes": "充足", "url": "http://www.ch999.com/qujing/25.html" },
                                { "Id": 104, "ShopName": "步行街店", "StoreDes": "少量", "url": "http://www.ch999.com/qujing/104.html" },
                                { "Id": 6, "ShopName": "翠峰路店", "StoreDes": "预订", "url": "http://www.ch999.com/qujing/6.html" }],
    "Support39Transfer": false,
    "SupportFreeTransfer": true,
    "SupportFreeMaintenance": true,
    "SupportInstallment": false,
    "supportJiaji": false,
    "supportZiti": false,
    "IsMobile": true,
    "SupportThreeHourReach": true,
    "SupportExpressDelivery": false,
    "ZITI": null
};*/

function stockStatus(cityid, ppid, productid) {
    var hasshop = Cookie.Get("hasshop") == "false" || Cookie.Get("hasshop") == null ? false : true;
    if (!hasshop) { cityid = 990101 }
    $.ajax({
        url: "/ajax.aspx",
        data: { act: "get-kc", ppid: ppid, cityId: cityid },
        dataType: "jsonp",
        success: function (data) {
            //var data = ajaxReuestResutl;
            var webKC = isKC(data.WebStoreState);
            if (data.WebStoreState == 5) {//商品缺货
                $(".detail-buy").html("<a href='javascript:;' class='ban'>缺货</a>")
            } else if (data.WebStoreState == 4) {//商品已下市
                $(".detail-buy").html("<a href='javascript:;' class='ban'>商品已下市</a>")
            } else if (data.WebStoreState == 6) {//商品未到货可提前预约
                $("#buy_btn").addClass("buy-yuding").text("预约购买");
                $("#add-cart").hide();
            } else { //正常状态
                
            }

            $("#lblProductKcState").text(webKC);
            $("#lblProductKcStateShop").text(isKC(data.ShopStoreState));
            var ShopStores = data.ShopStores;
            var html = "", zitiHtml = "";
            if (ShopStores == null || ShopStores.length==0) {
                $("#lblShop").hide();
            } else {
                $("#lblShop").show();
                for (var i = 0; i < ShopStores.length; i++) {
                    var _class = "";
                    var StoreDes = ShopStores[i].StoreDes;
                    if (StoreDes == "少量" || StoreDes == "现货" || StoreDes == "充足") {
                        _class = "kc-chongzu";
                        if (StoreDes == '少量') _class = 'kc-shao';
                    } else if (StoreDes == "预订" || StoreDes == "预约") {
                        _class = "kc-yuding";
                    } else if (StoreDes == "已下市" || StoreDes == "缺货" || StoreDes == "下市") {
                        _class = "kc-quehuo";
                    } else if (StoreDes == "在途") {
                        _class = "kc-zaitu";
                    }
                    html += '<li><a target="_blank" href="' + ShopStores[i].url + '" title="点击查看' + ShopStores[i].ShopName + '详细信息">' + ShopStores[i].ShopName + '</a><i class="' + _class + '">' + StoreDes + '</i></li>';
                }
                $("#lblShopList").html(html);
            }

            if (data.Support39Transfer) { /*三九快递*/
                $("#sjkdId").show();
                if ($("#dnfwId").length > 0) {
                    $("#dnfwId").show();
                }
            }
            if (data.SupportThreeHourReach) { /*3小时极速达*/
                $("#sxsId").show();
            }            
            if (data.supportJiaji) { /*加急配送*/
                $("#jiajiId").show();
            }
            if (data.SupportFreeMaintenance) { /*免费送*/
                $("#myfId").show();
            }
            if (data.SupportExpressDelivery) { /*快递运输*/
                $("#kuaidiId").show();
            }
            if (data.SupportInstallment) { /*分期付款*/
            }

            //if (data.supportZiti) { /*自提点*/
            //    $("#zitid").show();
            //    if (data.ZITI != null) { /*自提点地址*/
            //        var ztItem = data.ZITI, num = ztItem.length;
            //        if (num > 3) { num = 3; }
            //        for (var i = 0; i < num; i++) {
            //            zitiHtml += '<li><h4><a title="查看详情" class="left" target="_blank" href="http://www.ch999.com/zitidian/">' + ztItem[i].Name + '</a>';
            //            zitiHtml += '<a onclick="sendToPhone(\'地址：' + ztItem[i].Adress + '\n电话：' + ztItem[i].Tel1 + '\n联系人：' + ztItem[i].nickContact + '\',' + ztItem[i].Name + ',2)" title="发送自提点信息到手机" class="right icon" href="javascript:;"></a>';
            //            zitiHtml += '</h4><p title="' + ztItem[i].Adress + '">' + ztItem[i].Adress + '</p></li>';
            //        }
            //        if ($("#fujindian li").length < 1) {
            //            $("#fujindian h3").html('附近自提点(<a href="/zitidian/" style="color:#f90;">' + ztItem.length + '个</a>)');
            //            $("#fujindian ul").html(zitiHtml);
            //            $("#fujindian > a").text("更多自提点").attr("href", "/zitidian/");
            //        }
            //    }
            //}
        },

        error: function () {

        }
    });
}
