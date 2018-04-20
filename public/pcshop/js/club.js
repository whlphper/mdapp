/*显示积分相关*/
function loadJifen() {
    $.get('club.htmlajax.aspx?Act=GET&m=' + Math.random(), function (re) {
        if (!re.islogin) { return false };
        $('.mylogindiv').hide();
        $('.myczzdiv').show();
        $('#uname').html(re.uname);
        $('.myclass').html(re.cname).parent().show();
        $('.mypoint,#jifen_a').html(re.myjifen);
        $('.utip').html(re.utip);
        $('.mycode').attr('href', 'member_index.html').html(re.myyhj);
        if (re.myyhj == 0)
            $('.mycode2').parent().html('优惠券过期作废，请于过期前使用');
        else
            $('.mycode2').html(re.myyhj2);
        for (var i = 0; i < re.mytq.length; i++) {
            $('#viptq li[tqid=' + re.mytq[i].id + ']').addClass('vip-tequan-on');
        }
    }, 'json');
}

function gologin() {  
    location.href = 'login.html?url=' + GetLoginUrl();
}
function duihuanSub(id, point, the) {
    $.get('club.htmlajax.aspx?act=CHECKPOINT&id=' + id + '&m=' + Math.random(), function (re) {
        if (!re.islogin) {
            TipMsg.position('<div style="line-height:22px;font-size:14px"><p>亲，请先登录在兑换！</p><p>现在就去<a href="#" onclick="gologin()" style="color:#32b4e8">登录&gt;&gt;</a></p>', $(the), 3000);
            return false;
        } else {
            if (re.stats == 1) { window.location.href = '/order/confirm.aspx?promotionid=E'+id; }
            else if (re.stats == 0) {
                TipMsg.position('很抱歉，您的积分不够兑换该商品！', $(the), 3000);
            } else if (re.stats == -1) {
                TipMsg.position('很抱歉，兑换商品库存不足或已被兑换完！', $(the), 3000);
            } else if (re.stats == -2) {
                TipMsg.position("很抱歉，该商品只有" + re.cname.replace('会员', '及以上会员') + "专享", $(the), 5000);
            } else if (re.stats == -3) {
                TipMsg.position('很抱歉，无法兑换商品！', $(the), 3000);
            }
        }
    }, 'json');
    return false;
}

function showDuihuan2(the, key, money, jifen) {
    $(".dhquan").show().css({ left: ($(window).width() - 300) / 2, top: ($(window).height() - 120) / 2 });
    $("body").append('<div id="markdiv"></div>');
    $("#dhtip").html('您将兑换<b>' + money + '</b>元优惠券，这将花费您<span>' + jifen + '</span>积分，确定兑换吗？');
    $("#subDh").attr("qid", key);
}
function subDh(the) {
    var isclick = $(the).attr('isclick');
    if (isclick == 1) return;
    $(the).attr('isclick', 1);
    var qid = $(the).attr("qid");
    $.get("club.htmlajax.aspx?act=QUAN&qid=" + qid + "&t=" + $.now() + "", function (d) {
        $(the).attr('isclick', 0);
        if (d.islogin == true) {
            if (d.stats == 1) {
                closeDuihuan();
                var call=TipMsg.Dialog(true, "恭喜你，兑换成功，你可以到会员中心<a href='member_index.htmlredpaper.aspx'>查看</a>兑换到的优惠码", 2000);
                setTimeout(function () { window.location.reload(); }, 1000);
            } else {
                $('.dhquan').hide();
                TipMsg.Dialog(true, "很抱歉，兑换失败~<p>请检查您的积分是否满足兑换条件。</p>", 4000, function () { $('#markdiv').remove() });
            }
        } else {
            TipMsg.Dialog(true, "亲，登录才能兑换哦~", 1500);
        }
    }, "json")
}
function closeDuihuan() {
    $(".dhquan").hide();
    $("#markdiv").remove();
}