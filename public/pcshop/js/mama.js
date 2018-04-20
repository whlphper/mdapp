;!function () { function a(a) { return a.replace(t, "").replace(u, ",").replace(v, "").replace(w, "").replace(x, "").split(y) } function b(a) { return "'" + a.replace(/('|\\)/g, "\\$1").replace(/\r/g, "\\r").replace(/\n/g, "\\n") + "'" } function c(c, d) { function e(a) { return m += a.split(/\n/).length - 1, k && (a = a.replace(/\s+/g, " ").replace(/<!--[\w\W]*?-->/g, "")), a && (a = s[1] + b(a) + s[2] + "\n"), a } function f(b) { var c = m; if (j ? b = j(b, d) : g && (b = b.replace(/\n/g, function () { return m++, "$line=" + m + ";" })), 0 === b.indexOf("=")) { var e = l && !/^=[=#]/.test(b); if (b = b.replace(/^=[=#]?|[\s;]*$/g, ""), e) { var f = b.replace(/\s*\([^\)]+\)/, ""); n[f] || /^(include|print)$/.test(f) || (b = "$escape(" + b + ")") } else b = "$string(" + b + ")"; b = s[1] + b + s[2] } return g && (b = "$line=" + c + ";" + b), r(a(b), function (a) { if (a && !p[a]) { var b; b = "print" === a ? u : "include" === a ? v : n[a] ? "$utils." + a : o[a] ? "$helpers." + a : "$data." + a, w += a + "=" + b + ",", p[a] = !0 } }), b + "\n" } var g = d.debug, h = d.openTag, i = d.closeTag, j = d.parser, k = d.compress, l = d.escape, m = 1, p = { $data: 1, $filename: 1, $utils: 1, $helpers: 1, $out: 1, $line: 1 }, q = "".trim, s = q ? ["$out='';", "$out+=", ";", "$out"] : ["$out=[];", "$out.push(", ");", "$out.join('')"], t = q ? "$out+=text;return $out;" : "$out.push(text);", u = "function(){var text=''.concat.apply('',arguments);" + t + "}", v = "function(filename,data){data=data||$data;var text=$utils.$include(filename,data,$filename);" + t + "}", w = "'use strict';var $utils=this,$helpers=$utils.$helpers," + (g ? "$line=0," : ""), x = s[0], y = "return new String(" + s[3] + ");"; r(c.split(h), function (a) { a = a.split(i); var b = a[0], c = a[1]; 1 === a.length ? x += e(b) : (x += f(b), c && (x += e(c))) }); var z = w + x + y; g && (z = "try{" + z + "}catch(e){throw {filename:$filename,name:'Render Error',message:e.message,line:$line,source:" + b(c) + ".split(/\\n/)[$line-1].replace(/^\\s+/,'')};}"); try { var A = new Function("$data", "$filename", z); return A.prototype = n, A } catch (B) { throw B.temp = "function anonymous($data,$filename) {" + z + "}", B } } var d = function (a, b) { return "string" == typeof b ? q(b, { filename: a }) : g(a, b) }; d.version = "3.0.0", d.config = function (a, b) { e[a] = b }; var e = d.defaults = { openTag: "<%", closeTag: "%>", escape: !0, cache: !0, compress: !1, parser: null }, f = d.cache = {}; d.render = function (a, b) { return q(a, b) }; var g = d.renderFile = function (a, b) { var c = d.get(a) || p({ filename: a, name: "Render Error", message: "Template not found" }); return b ? c(b) : c }; d.get = function (a) { var b; if (f[a]) b = f[a]; else if ("object" == typeof document) { var c = document.getElementById(a); if (c) { var d = (c.value || c.innerHTML).replace(/^\s*|\s*$/g, ""); b = q(d, { filename: a }) } } return b }; var h = function (a, b) { return "string" != typeof a && (b = typeof a, "number" === b ? a += "" : a = "function" === b ? h(a.call(a)) : ""), a }, i = { "<": "&#60;", ">": "&#62;", '"': "&#34;", "'": "&#39;", "&": "&#38;" }, j = function (a) { return i[a] }, k = function (a) { return h(a).replace(/&(?![\w#]+;)|[<>"']/g, j) }, l = Array.isArray || function (a) { return "[object Array]" === {}.toString.call(a) }, m = function (a, b) { var c, d; if (l(a)) for (c = 0, d = a.length; d > c; c++) b.call(a, a[c], c, a); else for (c in a) b.call(a, a[c], c) }, n = d.utils = { $helpers: {}, $include: g, $string: h, $escape: k, $each: m }; d.helper = function (a, b) { o[a] = b }; var o = d.helpers = n.$helpers; d.onerror = function (a) { var b = "Template Error\n\n"; for (var c in a) b += "<" + c + ">\n" + a[c] + "\n\n"; "object" == typeof console && console.error(b) }; var p = function (a) { return d.onerror(a), function () { return "{Template Error}" } }, q = d.compile = function (a, b) { function d(c) { try { return new i(c, h) + "" } catch (d) { return b.debug ? p(d)() : (b.debug = !0, q(a, b)(c)) } } b = b || {}; for (var g in e) void 0 === b[g] && (b[g] = e[g]); var h = b.filename; try { var i = c(a, b) } catch (j) { return j.filename = h || "anonymous", j.name = "Syntax Error", p(j) } return d.prototype = i.prototype, d.toString = function () { return i.toString() }, h && b.cache && (f[h] = d), d }, r = n.$each, s =  "break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined", t = /\/\*[\w\W]*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|"(?:[^"\\]|\\[\w\W])*"|'(?:[^'\\]|\\[\w\W])*'|\s*\.\s*[$\w\.]+/g, u = /[^\w$]+/g, v = new RegExp(["\\b" + s.replace(/,/g, "\\b|\\b") + "\\b"].join("|"), "g"), w = /^\d[^,]*|,\d[^,]*/g, x = /^,+|,+$/g, y = /^$|,+/; e.openTag = "{{", e.closeTag = "}}"; var z = function (a, b) { var c = b.split(":"), d = c.shift(), e = c.join(":") || ""; return e && (e = ", " + e), "$helpers." + d + "(" + a + e + ")" }; e.parser = function (a) { a = a.replace(/^\s/, ""); var b = a.split(" "), c = b.shift(), e = b.join(" "); switch (c) { case "if": a = "if(" + e + "){"; break; case "else": b = "if" === b.shift() ? " if(" + b.join(" ") + ")" : "", a = "}else" + b + "{"; break; case "/if": a = "}"; break; case "each": var f = b[0] || "$data", g = b[1] || "as", h = b[2] || "$value", i = b[3] || "$index", j = h + "," + i; "as" !== g && (f = "[]"), a = "$each(" + f + ",function(" + j + "){"; break; case "/each": a = "});"; break; case "echo": a = "print(" + e + ");"; break; case "print": case "include": a = c + "(" + b.join(",") + ");"; break; default: if (/^\s*\|\s*[\w\$]/.test(e)) { var k = !0; 0 === a.indexOf("#") && (a = a.substr(1), k = !1); for (var l = 0, m = a.split("|"), n = m.length, o = m[l++]; n > l; l++) o = z(o, m[l]); a = (k ? "=" : "=#") + o } else a = d.helpers[c] ? "=#" + c + "(" + b.join(",") + ");" : "=" + a } return a }, "function" == typeof define ? define(function () { return d }) : "undefined" != typeof exports ? module.exports = d : this.template = d }();
/*延时加载*/
jQuery.fn.extend({
    delayLoading: function (a) {
        function g(d) { var b, c; if (a.container === undefined || a.container === window) { b = $(window).scrollTop(); c = $(window).height() + $(window).scrollTop() } else { b = $(a.container).offset().top; c = $(a.container).offset().top + $(a.container).height() } return d.offset().top + d.height() + a.beforehand >= b && c >= d.offset().top - a.beforehand } function h(d) {
            var b, c; if (a.container === undefined || a.container === window) { b = $(window).scrollLeft(); c = $(window).width() + $(window).scrollLeft() } else {
                b = $(a.container).offset().left;
                c = $(a.container).offset().left + $(a.container).width()
            } return d.offset().left + d.width() + a.beforehand >= b && c >= d.offset().left - a.beforehand
        } function f() {
            e.filter("img[" + a.imgSrcAttr + "]").each(function (d, b) {
               
                if ($(b).attr(a.imgSrcAttr) !== undefined && $(b).attr(a.imgSrcAttr) !== null && $(b).attr(a.imgSrcAttr) !== "" && g($(b)) && h($(b))) {
                    var c = new Image; c.onload = function () { $(b).attr("src", c.src); a.duration !== 0 && $(b).hide().fadeIn(a.duration); $(b).removeAttr(a.imgSrcAttr); a.success($(b)) }; c.onerror = function () {
                        $(b).attr("src",
                        a.errorImg); $(b).removeAttr(a.imgSrcAttr); a.error($(b))
                    }; c.src = $(b).attr(a.imgSrcAttr)
                }
            })
        } a = jQuery.extend({ defaultImg: "", errorImg: "", imgSrcAttr: "data-url", beforehand: 0, event: "scroll", duration: "normal", container: window, success: function () { }, error: function () { } }, a || {}); if (a.errorImg === undefined || a.errorImg === null || a.errorImg === "") a.errorImg = a.defaultImg; var e = $(this); if (e.attr("src") === undefined || e.attr("src") === null || e.attr("src") === "") e.attr("src", a.defaultImg); f(); $(a.container).bind(a.event, function () { f() })
    }
});

/*页面可视区宽度*/
var win = $(window).width();

$(document).ready(function () {
    /*导航菜单*/
    $(".nav-index li").click(function () {
        $(this).addClass("nav-on").siblings().removeClass("nav-on");
    });
    $(".nav-list strong").hover(function () {
        $(".nav-box").show();
    }, function () {
        $(".nav-box").hide();
    })
    $(".nav-box").hover(function () {
        $(this).show();
    }, function () {
        $(this).fadeOut();
    })

    //导航下拉菜单选项卡
    $(".nav-list-down li").mouseover(function () {
        $(this).addClass("nav-list-down-on").siblings().removeClass("nav-list-down-on");
        $(".nav-list-down-info").eq($(this).index()).show().siblings().hide();
    });

    /*门店*/
    jQuery(".stores").slide({ mainCell: ".stores-box ul", autoPlay: true, effect: "leftLoop", vis: 3, scroll: 1, prevCell: ".stores-prev", nextCell: ".stores-next" });

    /*右侧购物车导航*/
    var cartshow = function () {
        $(".mami-cart").animate({ right: '0' });
        $(".baby-dangan").show(800);
    }
    if (win >= 1270) {
        setTimeout(cartshow, 1000);
    }
    $(".mami-cart-float").hover(function () {
        $(this).find("span").show().stop().animate({ right: '35px', opacity: '1' });
    }, function () {
        $(this).find("span").animate({ right: '60px', opacity: '0' }, function () {
            $(this).hide();
        });
    })
    $(".baby-dangan").hover(function () {
        $(this).animate({ right: '-5px'});
    }, function () {
        $(this).animate({ right: '-18px' })
    });

    /*右侧购物车*/
    $(".mami-cart-btn").click(function (e) {
        $(".baby-dangan").hide();
        cartN.cartLoaded = function(cartInfo) {
            all_jiage(); //统计价格和数量
            $(".mami-cart-box-body h2 a").each(function () {
                $(this).text_length(24); //文字超出显示省略号
            })
            $(".mami-cart-box").stop().show().animate({ width: '280px' }, function () {
                $("#close-cart,.mami-cart-box-view").show();
            });
        }
        cartN.loadCarInfo();
        e.stopPropagation();
    })
    //隐藏右侧购物车
    $(".mami-cart-box").click(function (e) {
        e.stopPropagation();
    })
    $(document).click(function () {
        $("#close-cart,.mami-cart-box-view").fadeOut();
        $(".mami-cart-box").animate({ width: '0' }, function () {
            $(".mami-cart-box").hide();
            $(".baby-dangan").show(500);
        });
    })
    $("#close-cart").click(function () {
        $("#close-cart,.mami-cart-box-view").fadeOut();
        $(".mami-cart-box").animate({ width: '0' }, function () {
            $(".mami-cart-box").hide();
            $(".baby-dangan").show(500);
        });
    })
    //右侧购物车显示滚动条
    $(".mami-cart-box-body").hover(function () {
        $(this).css({ "overflow-y": "auto", "overflow-x": "hidden" });
    }, function () {
        $(this).css("overflow-y", "hidden");
    })

    //右侧购物车删除行
    $(document).on("click", ".cart-delrow", function () {
        console.log(this);
        $(this).parent("li").remove();
        all_jiage();
        e.stopPropagation();
    });

    //右侧购物车商品数量增减
    all_jiage(); //侧栏购物车统计价格和数量
    $(".cart-add").click(function () {
        var single = parseInt($(this).siblings("input").val());
        single = single + 1;
        $(this).siblings("input").val(single);
        all_jiage();
        cart_jiage()
    })
    $(".cart-jian").click(function () {
        var single = parseInt($(this).siblings("input").val());
        if (single > 1) {
            single = single - 1;
        } else {
            single = 1;
        };
        $(this).siblings("input").val(single);
        all_jiage();
        cart_jiage()
    })

    /*列表页导航*/
    $(".list-menu i").each(function () {
        if ($(this).text() === '+') {
            $(this).siblings(".list-menu-down").hide();
        } else {
            $(this).siblings(".list-menu-down").show();
        }
    })
    $(".list-menu i").click(function () {
        if ($(this).text() === '+') {
            $(this).siblings(".list-menu-down").stop().slideDown();
            $(this).text("-");
        } else {
            $(this).siblings(".list-menu-down").slideUp();
            $(this).text("+");
        }
    })

    /*列表页-热卖商品-选项卡*/
    $(".list-tab-btn li").mouseover(function () {
        $(this).addClass("list-tab-on").siblings().removeClass("list-tab-on");
        $(".list-tab-view>ul").eq($(this).index()).fadeIn().siblings().hide();
    });

    /*商品文字数*/
    $(".zx_protit a").each(function () {
        $(this).text_length(13);
    })

    $(".list-tab-view h3 a,.list-hot h3 a,.detail-pinpai-box h3 a,.detail-like h3 a").each(function () {
        $(this).text_length(20);
    })
    $(".select-view h2 a,.mami-cart-box-body h2 a").each(function () {
        $(this).text_length(24);
    })
    $(".list-shop h1 a,.sale-list li h2,.brand-list h2 a").each(function () {
        $(this).text_length(28);
    })
    $(".sale-top li h3 a,.detail-sale h3 a").each(function () {
        $(this).text_length(26);
    })

    /*列表页-广告*/
    jQuery(".list-ad").slide({ mainCell: ".list-ad-bd ul", titCell: ".list-ad-hd li", autoPlay: true, effect: "leftLoop", delayTime: "800", interTime: "4000", titOnClassName: "list-ad-on" });

    /*延时加载*/
    $("img").delayLoading({
        defaultImg: "/Static/images/img_empty.jpg",           // 预加载前显示的图片
        errorImg: "",                        // 读取图片错误时替换图片(默认：与defaultImg一样)
        imgSrcAttr: "data-url",           // 记录图片路径的属性(默认：originalSrc，页面img的src属性也要替换为originalSrc)
        beforehand: 0,                       // 预先提前多少像素加载图片(默认：0)
        event: "scroll",                     // 触发加载图片事件(默认：scroll)
        duration: "slow",                  // 三种预定淡出(入)速度之一的字符串("slow", "normal", or "fast")或表示动画时长的毫秒数值(如：1000),默认:"normal"
        container: window,                   // 对象加载的位置容器(默认：window)
        success: function (imgObj) { },      // 加载图片成功后的回调函数(默认：不执行任何操作)
        error: function (imgObj) { }         // 加载图片失败后的回调函数(默认：不执行任何操作)
    });
});

/*回到顶部*/
function go_top($val) {
    $('html,body').stop().animate({ scrollTop: $val }, 800);
}

/*鼠标滑过延时触发插件*/
(function ($) {
    $.fn.hoverDelay = function (options) {
        var defaults = {
            hoverDuring: 200,
            outDuring: 200,
            hoverEvent: function () {
                $.noop();
            },
            outEvent: function () {
                $.noop();
            }
        };
        var sets = $.extend(defaults, options || {});
        var hoverTimer, outTimer;
        return $(this).each(function () {
            $(this).hover(function () {
                clearTimeout(outTimer);
                hoverTimer = setTimeout(sets.hoverEvent, sets.hoverDuring);
            }, function () {
                clearTimeout(hoverTimer);
                outTimer = setTimeout(sets.outEvent, sets.outDuring);
            });
        });
    }
})(jQuery);
//hoverDuring       鼠标经过的延时时间
//outDuring          鼠标移出的延时时间
//hoverEvent        鼠标经过执行的方法
//outEvent         鼠标移出执行的方法


/*添加收藏*/
function like(ppid, the) {
    var href = window.location.href
    if ($.trim(ppid).length > 0) {
        $.ajax({
            url: '/ajax.aspx',
            dataType: 'json',
            data: { 'act': 'addCollection', 'ppid': ppid },
            type: 'post',
            success: function (txt) {
                var res = { msg: "", callfun: function () { } };
                if (txt.stats == 1) {
                    res.msg = "收藏成功";
                    icon = 6;
                } else if (txt.stats == 2) {
                    res.msg = "没有登录，请先登录";
                    icon = 5;
                    res.callfun = function () {
                        window.location.href = "login.html?url=" + href + "";
                    }
                } else if (txt.stats == 3) {
                    res.msg = "您已收藏过该商品了！";
                    icon = 5;
                } else {
                    res.msg = "收藏失败！";
                    icon = 5;
                };
                layer.msg(res.msg, {
                    icon: icon,
                    time: 1500
                }, res.callfun);
            }
        });
    }
}

//购物车列表-统计价格和数量
function cart_jiage() {
    var check_num = 0;  //选中数量
    var shop_nums = 0;   //商品数量
    var check_money = 0;  //价格
    var check_basketid = '';
    $(".cart-list-bd ul").eq(0).find(".col-sel").each(function () {
        var self = $(this);
        var shop_num = parseFloat(self.siblings(".col-num").find(".cart-input").val());   //商品数量
        var rmb = Number(parseFloat(self.siblings(".col-price").find("b").text() * shop_num).toFixed(2));
        self.siblings(".col-money").find("b").text(rmb.toFixed(2));
        shop_nums += shop_num;
        if ($(this).is(':checked')) {
            check_num += shop_num;
            check_money += rmb;
            check_basketid += $(this).attr('data-baskid') + ",";
        }
    })
    $(".cart-num").text(check_num);
    $(".cart-all").text(check_money.toFixed(2));
    $(".cart-list li").eq(0).find("em").text(check_num);

    $(".cart-list-bd ul").eq(1).each(function () {
        var shixiao = $(this).find("li").length;
        $(".cart-list li").eq(1).find("em").text(shixiao);
    })
    /*全选和反选*/
    if (check_num == shop_nums)
        $('.col-sel-all').prop("checked", true);
    else {
        $('.col-sel-all').removeAttr("checked");
    }


    /*计算保税商品和普通商品的价格*/
    var price = 0, count = 0;
    $('#bspro1 li').each(function () {
        if (check_basketid.indexOf($(this).children("input[name=basketid]").val() + ",") == -1) {
            $(this).hide();
        }
        else {
            price += parseFloat($(this).find("input[name=totalprice]").val());
            count += parseInt($(this).find("input[name=totalcount]").val());
            $(this).show();
        }
    });

    $('#totalbscount').html(count);
    $('#totalbspri').html(price);
    if (count == 0)
        $('#totalbspri').parent().parent().hide();
    else
        $('#totalbspri').parent().parent().show();
    price = 0, count = 0;
    $('#comm li').each(function () {
        if (check_basketid.indexOf($(this).children("input[name=basketid]").val() + ",") == -1) {
            $(this).hide();
        }
        else {
            price += parseFloat($(this).find("input[name=totalprice]").val());
            count += parseInt($(this).find("input[name=totalcount]").val());
            $(this).show();
        }

    });
    $('#totalcommoncount').html(count);
    $('#totalcommonpri').html(price);
    if (count == 0)
        $('#totalcommonpri').parent().parent().hide();
    else
        $('#totalcommonpri').parent().parent().show();
    scroll();
    /*去结算改变*/
    if ($('#bspro1').parent().parent().parent().children("div:visible").length == 1)
        $("#go-Confirm").removeClass("ban").text("去结算");
    else if (($('#bspro1').parent().parent().parent().children("div:visible").length > 1))
        $("#go-Confirm").addClass("ban").text("保税商品需单独结算");
}

//侧边栏购物车-统计价格和数量
function all_jiage() {
    var cart_number = 0;  //数量
    var cart_money = 0;  //价格
    $(".mami-cart .cart-input").each(function () {
        cart_number += Number(parseFloat($(this).val()));
        cart_money += Number(parseFloat($(this).val()) * parseInt($(this).parent().siblings("strong").find(".cart-money").text()));
    })
    $("#cart-number").text(cart_number);
    $("#cart-heji").text(cart_money.toFixed(2));

    $(".mami-cart-btn").find("em").text(cart_number);
    $("#cartcount").text(cart_number);

    //判断购物车是否为空
    if (cart_number == 0) {
        $(".mami-cart-box-body h4").show();
    } else {
        $(".mami-cart-box-body h4").hide();
    }
}

/*详情页图片放大镜*/
(function ($) {
    $.fn.imagezoom = function (options) {
        var settings = {
            xzoom: 310,
            yzoom: 310,
            offset: 10,
            position: "BTR",
            preload: 1
        };
        if (options) {
            $.extend(settings, options);
        }

        var noalt = '';
        var self = this;

        $(this).bind("mouseenter", function (ev) {
            var imageLeft = $(this).offset().left; //元素左边距
            var imageTop = $(this).offset().top; //元素顶边距
            var imageWidth = $(this).get(0).offsetWidth; //图片宽度
            var imageHeight = $(this).get(0).offsetHeight; //图片高度
            var boxLeft = $(this).parent().offset().left; //父框左边距
            var boxTop = $(this).parent().offset().top; //父框顶边距
            var boxWidth = $(this).parent().width(); //父框宽度
            var boxHeight = $(this).parent().height(); //父框高度
            //noalt = $(this).attr("alt"); //图片标题
            
            var bigimage = $(this).attr("big"); //大图地址
            //$(this).attr("alt", ''); //清空图片alt
            if ($("div.zoomDiv").get().length == 0) {
                $(document.body).append("<div class='zoomDiv'><img class='bigimg' src='" + bigimage + "'/></div><div class='zoomMask'><a href='javascript:;' style='display:block;width:100%;height:100%;'></a></div>"); //放大镜框及遮罩
            }
            if (settings.position == "BTR") {
                //如果超过了屏幕宽度 将放大镜放在右边
                if (boxLeft + boxWidth + settings.offset + settings.xzoom > screen.width) {
                    leftpos = boxLeft - settings.offset - settings.xzoom;
                } else {
                    leftpos = boxLeft + boxWidth + settings.offset;
                }
            } else {
                leftpos = imageLeft - settings.xzoom - settings.offset;
                if (leftpos < 0) {
                    leftpos = imageLeft + imageWidth + settings.offset;
                }
            }
            $("div.zoomDiv").css({ top: boxTop, left: leftpos });
            $("div.zoomDiv").width(settings.xzoom);
            $("div.zoomDiv").height(settings.yzoom);
            $("div.zoomDiv").show();

            $(this).css('cursor', 'crosshair');

            $(document.body).mousemove(function (e) {
                mouse = new MouseEvent(e);
                if (mouse.x < imageLeft || mouse.x > imageLeft + imageWidth || mouse.y < imageTop || mouse.y > imageTop + imageHeight) {
                    mouseOutImage();
                    return;
                }

                var bigwidth = $(".bigimg").get(0).offsetWidth;
                var bigheight = $(".bigimg").get(0).offsetHeight;

                var scaley = 'x';
                var scalex = 'y';

                //设置遮罩层尺寸
                if (isNaN(scalex) | isNaN(scaley)) {
                    var scalex = (bigwidth / imageWidth);
                    var scaley = (bigheight / imageHeight);
                    $("div.zoomMask").width((settings.xzoom) / scalex);
                    $("div.zoomMask").height((settings.yzoom) / scaley);
                    $("div.zoomMask").css('visibility', 'visible');
                }

                xpos = mouse.x - $("div.zoomMask").width() / 2;
                ypos = mouse.y - $("div.zoomMask").height() / 2;

                xposs = mouse.x - $("div.zoomMask").width() / 2 - imageLeft;
                yposs = mouse.y - $("div.zoomMask").height() / 2 - imageTop;

                xpos = (mouse.x - $("div.zoomMask").width() / 2 < imageLeft) ? imageLeft : (mouse.x + $("div.zoomMask").width() / 2 > imageWidth + imageLeft) ? (imageWidth + imageLeft - $("div.zoomMask").width()) : xpos;
                ypos = (mouse.y - $("div.zoomMask").height() / 2 < imageTop) ? imageTop : (mouse.y + $("div.zoomMask").height() / 2 > imageHeight + imageTop) ? (imageHeight + imageTop - $("div.zoomMask").height()) : ypos;


                $("div.zoomMask").css({ top: ypos, left: xpos });
                $("div.zoomDiv").get(0).scrollLeft = xposs * scalex;
                $("div.zoomDiv").get(0).scrollTop = yposs * scaley;
            });
        });
        function mouseOutImage() {
            $(self).attr("alt", noalt);
            $(document.body).unbind("mousemove");
            $("div.zoomMask").remove();
            $("div.zoomDiv").remove();
        }

        //预加载
        count = 0;
        if (settings.preload) {
            $('body').append("<div style='display:none;' class='jqPreload" + count + "'></div>");

            $(this).each(function () {

                var imagetopreload = $(this).attr("rel");

                var content = jQuery('div.jqPreload' + count + '').html();

                jQuery('div.jqPreload' + count + '').html(content + '<img src=\"' + imagetopreload + '\">');

            });
        }
    }
})(jQuery);
function MouseEvent(e) {
    this.x = e.pageX;
    this.y = e.pageY;
}

/*提示信息*/
var TipMsg = {
    showTimer: null,
    popUp: function (icon, msg, abort, callback) {/*true或false,"提示",true为有取消按钮*/
        var cName;
        var callback = callback || function () { };
        if (icon == true) { cName = "true" }
        else if (icon == false) { cName = "false" }
        $("body").append('<div id="mark"></div><div id="alert"><h3>Tips<a href="javascript:;" onclick="TipMsg.closePop()"></a></h3><div class="tip_msg"><i class="' + cName + '"></i><div class="msg_con">' + msg + '</div></div><div class="al_btn"><button id="config" style="">确定</button><button onclick="TipMsg.closePop()" id="abort">取消</button></div></div>');
        if (abort == true) { $("#abort").show(); }
        var win = $(window), off = $("#alert");
        off.css({ "left": (win.width() - off.width()) / 2 });
        $("#config").click(function () {
            TipMsg.closePop(callback);
        });
    },
    closePop: function (callback) {
        $("#mark,#alert").remove();
        if (callback) { callback(); }
    },
    position: function (msg, tag, timer, leftplus, topplus, direction) {/*"提示",$(this),2000,向左偏移,向上偏移,方向*/
        clearTimeout(this.showTimer);
        if ($("#tipBox").length == 0) { $("body").append('<div id="tipBox"></div>'); } else { $("#tipBox").show(); }
        var tagOff = tag.offset() || tag.position(), the = $("#tipBox");
        the.html('<div>' + msg + '</div>')
        var h = the.height() + 30;
        var _direction = direction || "up";
        if (leftplus == null) { leftplus = 0 }
        if (topplus == null) { topplus = 0 }
        if (_direction == "up") {
            the.css({ top: tagOff.top - h - 20, left: tagOff.left + leftplus }).removeClass("downTip leftTip rightTip");
            the.fadeIn(300).animate({ top: tagOff.top - h + topplus }, 300);
        } else if (_direction == "down") {
            the.css({ top: tagOff.top + tag.outerHeight() + 10, left: tagOff.left + leftplus }).addClass("downTip");
            the.fadeIn(300).animate({ top: tagOff.top + tag.outerHeight() + topplus }, 300);
        } else if (_direction == "left") {
            the.css({ top: tagOff.top + topplus, left: tagOff.left - tag.outerWidth() - 10 }).addClass("leftTip");
            the.fadeIn(300).animate({ left: tagOff.left - tag.outerWidth() - 10 + leftplus }, 300);
        } else if (_direction == "right") {
            the.css({ top: tagOff.top + topplus, left: tagOff.left + tag.outerWidth() + 10 + leftplus }).addClass("rightTip");
            the.fadeIn(300).animate({ left: tagOff.left + tag.outerWidth() + 10 + leftplus }, 300);
        }
        if (timer != -1) {
            the.hover(function () { clearTimeout(TipMsg.showTimer); }, function () {
                TipMsg.showTimer = setTimeout(function () {
                    the.fadeOut(300, function () { the.remove(); });
                }, timer);
            });
            TipMsg.showTimer = setTimeout(function () {
                the.fadeOut(300, function () { the.remove(); });
            }, timer);
        }
    },
    Dialog: function (icon, msg, timer, callfun) {/*true或false,"提示",时间*/
        var cName = icon == true ? "true" : "false";
        $("body").append('<div id="mark"></div><div id="automsg"><i class="' + cName + '"></i><span>' + msg + '</span></div>');
        var win = $(window), the = $("#automsg");
        the.css({ left: (win.width() - the.width()) / 2, top: (win.height() - the.height()) / 2 });
        var returnfun = { close: function () { $("#mark,#automsg").remove(); } };
        setTimeout(function () { returnfun.close(); if (callfun) callfun(); }, timer);
        return returnfun;
    }
};

/*设置cookie*/
var Cookie = {
    Set: function (name, value, time, path, domain) {
        if (!domain) domain = "mama999.com";
        this.Clear(name, "/", "");
        if (domain) this.Clear(name, "/", domain);
        var expires = new Date(new Date().getTime() + (time || 24) * 3600 * 1000);
        if (time == 0) {
            expires = null;
        }
        document.cookie = name + "=" + encodeURIComponent(value) + ((expires) ? "; expires=" + expires.toGMTString() : "") + ((path) ? "; path=" + path : "; path=/") + ((domain) ? ";domain=" + domain : ";domain=");
    },
    Get: function (name) {
        var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
        if (arr != null) {
            try {
                return decodeURIComponent(arr[2]);
            }
            catch (err) {
                return unescape(arr[2]);
            }
        }
        return null;
    },
    Clear: function (name, path, domain) {
        document.cookie = name + "=" + ((path) ? "; path=" + path : "; path=/") + ((domain) ? "; domain=" + domain : ";domain=") + ";expires=Fri, 02-Jan-1970 00:00:00 GMT";
        document.cookie = name + "=" + ((path) ? "; path=" + path : "; path=/") + ";domain=" + ";expires=Fri, 02-Jan-1970 00:00:00 GMT";
    },
    ISCookie: function () {
        this.Set("ISCookie", "1");
        if (this.Get("ISCookie"))
        { this.Clear("ISCookie"); return true; }
        else
        { return false; }
    }
};

function GetUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    if (window.location.search == '') return null;
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return r[2]; return null; 
}
function GetLoginUrl() {
    var url = GetUrlParam('url');
    if (url == null) url = location.pathname + window.encodeURIComponent(location.search);
    return url;
}
function GoLogin() {
    location.href ='login.html?url='+GetLoginUrl();
}

/*文字超出隐藏*/
(function ($) {
    $.fn.extend({
        //插件名称 -- text_length();
        text_length: function (needLeng) {
            var len = parseInt($(this).text().length);
            if (len > needLeng) {
                var text = $(this).text().substr(0, needLeng) + '...';
                $(this).text(text);
            };
        }
    });
})(jQuery);

/*城市信息*/
var Area = {
    iProvince: 99,
    iCity: 9901,
    iCounty: 990101,
    iProvinceName: "全国",
    iCityName: "全国",
    iCountyName: "全国",
    hasShop: false,
    provinceData: null,
    cityData: null,
    init: function (province, city, county, pname, cname, coname, hasshop) {/*页面载入设置当前城市*/
        this.iProvince = province;
        this.iCity = city;
        this.iCounty = county;
        this.iProvinceName = pname;
        this.iCityName = cname;
        this.iCountyName = coname;
        this.hasShop = hasshop;

        if (this.iProvince == 99 && Cookie.Get("ischecked") == null && Cookie.ISCookie()) {
            this.setCity();
        }
    },
    change: function () {
        if (this.iProvince == 99) {
            this.getProvince();
        } else {
            this.getCityData();
            this.getCounty(this.iCity, this.iCityName);
        }
    },
    getCityData: function () {
        $.ajax({
            "url": '/js/areaall.ashx?pid=' + this.iProvince, dataType: "json", async: false, success: function (rsp) {
                if (rsp.stats == 1) {
                    Area.cityData = rsp.result;
                    Area.hasShop = rsp.result[0].isshop;//现在是默认第一个城市有店就设置为这个省有店，以后可能会出bug，有时间改接口即可。
                } else {
                    return false;
                }
            }
        });
    },
    getProvince: function () {
        if (this.provinceData == null) {
            $.ajax({
                "url": '/js/areaall.ashx', dataType: "json", async: false, success: function (rsp) {
                    if (rsp.stats == 1) {
                        Area.provinceData = rsp.result;
                    }
                }
            });
        }
        this.build(1, this.provinceData);
    },
    getCity: function (pid, pname) {
        this.iProvince = pid;
        this.iProvinceName = pname;/*设置省id,name*/
        this.getCityData();
        this.iCityName = "请选择";
        this.iCountyName = "请选择";

        this.build(2, this.cityData);
    },
    getCounty: function (pid, pname) {
        var countyData;
        var data = this.cityData;
        for (var i = 0; i < data.length; i++) {
            if (data[i].id == pid) {
                countyData = data[i].items;
                this.iCityName = data[i].name;/*设置市name*/
            }
        }
        this.iCity = pid;/*设置市id*/

        this.build(3, countyData);
    },
    build: function (level, data) {
        var func;
        if (level == 1) { func = "getCity" }
        if (level == 2) { func = "getCounty" }
        if (level == 3) { func = "select" }
        var tmplData = { iProvince: this.iProvince, iCity: this.iCity, iCounty: this.iCounty, provincename: this.iProvinceName, cityname: this.iCityName, countyname: this.iCountyName, lv: level, func: func, data: data };
        $(".select-city").html(template("cityTmpl", tmplData));
    },
    select: function (id, name) {/*切换事件*/
        this.iCounty = id;
        this.iCountyName = name;
        if ($(".city-wrap>p").is(":visible")) {
            this.setCity();
        } else {
            AreaCallback && AreaCallback();
        }
    },
    setCity: function () {
        var time = 24 * 365;
        Cookie.Set('pidc', this.iProvince, time);//省id
        Cookie.Set('zidc', this.iCity, time);//市id
        Cookie.Set('didc', this.iCounty, time);//县id
        Cookie.Set('dnamec', this.iCountyName, time);//县/区名
        Cookie.Set('cityid', this.iCounty, time);
        Cookie.Set('hasshop', this.hasShop, time);
        Cookie.Set('ischecked', 1, time);
        window.location.reload();
    },
    quickSet: function (province, city, county, name, hasShop) {
        this.iProvince = province;
        this.iCity = city;
        this.iCounty = county;
        this.iCountyName = name;
        this.hasShop = hasShop;
        this.setCity();
    }
};
function closeCt()
{
    $(".city-wrap").hide();
}

function ZhuiwenDisplay(obj) {
    var hide = $(obj).siblings(".detail-comment-text").css("display");
    if (hide == "block") {
        $(obj).siblings(".detail-comment-text").stop().slideUp();
    } else {
        $(obj).siblings(".detail-comment-text").stop().slideDown();
    }
}


function loginout() {
    $.post('/Ajax.aspx', { 'act': 'logout' }, function (txt) {
        if (txt.stats > 0)
            location.reload();
    },'json');
}

/*发送短信*/
function sendToPhone(sms, ziid, type) {
    $('body').append("<div id='markdiv'></div>")
    if ($("#cend_phone").size() <= 0) {
        $('body').append("<div id='cend_phone'><h3><b>免费短信发送</b><a href='javascript:;' id='closesend'>关闭</a><div class='clear'></div></h3><p><b>手机号码：</b><input name='sendPhoto' type='text' /><span>请输入您要发送的手机号码</span></p><p><b>短信内容：</b><div name='content' style='background: #f5f5f5;height:100px;margin-left: 75px;border: 1px solid #ccc;padding: 2px 6px;margin-right:5px;' disabled='disabled'></div></p><p><b>验证码：</b><input name='inpYZM' type='text' /><img id='vCodeImg' src='answer.htmlVerificateCode.aspx?t='+Math.random()*100' width='80' height='30' style='margin-right:5px;' onclick=\"this.src='answer.htmlVerificateCode.aspx?t='+Math.random()*100\" /><a href='javascript:void(0)' onclick=\"document.getElementById('vCodeImg').src='answer.htmlVerificateCode.aspx?t='+Math.random()*100\">看不清楚？换一张</a></p><button name='sendMSMS'>发送</button></div>");
        $("div[name=content]").html(sms.replace(/\n/g, '<br />'));
        //isLogin($("#cend_phone input[name='sendPhoto']"), null, null);
        $("#cend_phone input[name='sendPhoto']").val();
        $("#cend_phone").css("left", ($(window).width() - 440) * 0.5 + 'px');
        $("#closesend").click(function () { $("#cend_phone").remove(); $("#markdiv").remove(); });
        $("#cend_phone button[name=sendMSMS]").click(function () {
            //手机号
            var txtPhone = $("#cend_phone input[name=sendPhoto]");
            var mobile = $.trim(txtPhone.val());
            var ismobi = /^1[3|4|5|7|8][0-9]\d{4,8}$/.test(mobile);

            if (mobile == "") {
                TipMsg.position("请输入手机号码", txtPhone, 1000);
                return false;
            }
            if (!ismobi || isNaN(mobile) || mobile.length != 11) {
                TipMsg.position("请输入正确的手机号码", txtPhone,1000);
                return false;
            }
            //验证码
            var txtYzm = $("#cend_phone input[name=inpYZM]");
            var yzm = txtYzm.val();
            if ($.trim(yzm) == "") {
                TipMsg.position("验证码不能为空！", txtYzm,1000);
                return false;
            }

            //消息验证
            $.post('/Ajax.aspx', { 'act': 'SendDiMian', 'code': yzm, 'phone': mobile, 'ziid': ziid, "type": type }, function (res) {
                if (res.stats == 1) {
                    TipMsg.position("发送成功！", $("#cend_phone button"),1000);
                    $("#cend_phone button[name=sendMSMS]").prop("disabled", "disabled");
                    $("#cend_phone").delay(2000).fadeOut(1000, function () { $(this).remove(); });
                    $("#markdiv").delay(2000).fadeOut(1000, function () { $(this).remove(); });
                } else if (res.stats == 0) {
                    TipMsg.position(res.result, txtYzm,1000);
                    _timer = 0;
                } else if (res.stats == 3) {
                    TipMsg.position("请先登录！", $("#cend_phone button"), 1000);
                } else {
                    TipMsg.position("发送失败,再试试", $("#cend_phone p div[name=content]"),1000); 
                }
            }, 'json');

        });
        $("#cend_phone input[name=inpYZM]").keydown(function (e) {
            if (e.keyCode == 13) {
                $("#cend_phone button[name=sendMSMS]").trigger("click");
            }
        });
    }
}

/*priceCallback 定义为全局变量， 用来加载批量获取价格成功后 回调*/
var priceCallback;
/*加载页面时 加载价格 leizhou*/
$(function () {
    var ppids = ""//默认是浏览记录
    $(".nowPrice").each(function () {
        ppids += $(this).attr("ppid") + ",";
    });
    if (ppids != "") {
        /*$.ajax({
            url: "/Ajax.aspx", data: { act: "g-price-bat", "ppid": ppids, "pid": Cookie.Get("pidc"), "zid": Cookie.Get("zidc"), "did": Cookie.Get("didc") }, success: function (rsp) {
                $(".nowPrice").each(function () {
                    var ppid = $(this).attr("ppid");
                    for (var j = 0; j < rsp.length; j++) {
                        if (ppid == rsp[j].ppid) {
                            var txtY = $(this).attr("isy")==1?'￥':'';
                            if ($(this).attr("tofixed") == 0) {
                                $(this).html(txtY + rsp[j].price);
                            } else {
                                $(this).html(txtY + parseFloat(rsp[j].price).toFixed(2));
                            }
                        }
                    }
                });
                priceCallback && priceCallback(rsp);
            }, dataType: "jsonp"
        });*/
    }
});

function loadKcArea(pid, the) {
    isSelectCity = 1;
    $.get("/js/oaArea.ashx?pid=" + pid, function (json) {
        if (json.stats == 1) {
            re = json.result;
            var shiStr = "";
            for (var i = 0; i < re.length; i++) {
                var reItem = re[i];
                var zname = reItem.name, zid = reItem.id;
                shiStr += "<a onclick='loadxian(" + zid + ",this)' zname='" + zname + "'>" + zname + "</a>";
            }
            $("#ct_shi").html(shiStr);
        }
        $("#now_shi").trigger("click").find("small").html("请选择");
        $("#now_xian small").html("请选择");
        $("#now_sheng small").html($(the).attr("pname"));
        $("#ct_xian").html("");
    }, 'JSON');
}
function loadxian(sid, the) {
    isSelectCity = 1;
    var xianStr = "";
    for (var i = 0; i < re.length; i++) {
        var reItem = re[i];
        var zname = reItem.name, zid = reItem.id;
        if (zid == sid) {
            for (var j = 0; j < reItem.items.length; j++) {
                var xian = reItem.items[j];
                var xianName;
                xianName = xian.name;
                if (xian.iskc == true) { xianName = xianName + "&nbsp;*" }
                xianStr += "<a pid='" + reItem.pid + "' zid='" + zid + "' did='" + xian.id + "' dname='" + xian.name + "' onclick='AreaMin.AreaClick(this)'>" + xianName + "</a>"
            }
            $("#ct_xian").html(xianStr + "<p style='clear:both;padding-top:15px;margin-left:10px;color:#999'>注：标 * 的为有三九实体门店的地区</p>");
        }
        $("#now_shi small").html($(the).attr("zname"));
    }
    $("#now_xian").trigger("click").find("small").html("请选择");
}

/*URL参数获取*/
function request(paras) {
    var url = location.href;
    var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
    var paraObj = {}
    for (i = 0; j = paraString[i]; i++) {
        paraObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
    }
    var returnValue = paraObj[paras.toLowerCase()];
    if (typeof (returnValue) == "undefined") { return ""; }
    else { return returnValue; }
}

var mobileReg = /^1[3|4|5|7|8][0-9]\d{8}$/;

/*移入a显示b*/
function hoverShow(a, b, cur) {
    var showTimer;
    a.hover(function () {
        $(this).addClass(cur);
        clearInterval(showTimer);
        b.show();
    }, function () {
        showTimer = setInterval(function () {
            b.hide();
            a.removeClass(cur);
        }, 500)
    });
    b.hover(function () { clearInterval(showTimer); }, function () { $(this).hide(); a.removeClass(cur); });
}

/*手机号码验证*/
function IsMobile(mobile) {
    var ismobi = /^1[3|4|5|7|8][0-9]\d{4,8}$/.test(mobile);
    if (ismobi && !isNaN(mobile) && mobile.length == 11) {
        return true;
    }
    return false;
}