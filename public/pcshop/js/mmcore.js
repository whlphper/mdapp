var keyValue = "";
$(function () {
    GetChuShi();
    $("#btnts").click(function () {
        var c = $("#btnts").offset();
        var d = $(window).scrollTop();
        var e = c.left - 400;
        var f = $(window).height() - $("#blk9").height() + d;
        $("#blk9").show().css({
            top: f,
            left: e
        })
    });
    $("#btnAddTS").click(function () {
        var e = $.trim($("#txtPhone_").val());
        var d = /^1[3|4|5|7|8][0-9]\d{4,8}$/.test(e);
        if (!d || isNaN(e) || e.length != 11) {
            showtip("请输入正确的手机号码", $GET("txtPhone_"), {
                top: 8
            });
            return false
        }
        var c = $.trim($("#txtEmail_").val()).toLowerCase();
        $("#txtEmail_").val(c);
        if (c.length < 2) {
            showtip("请输入您常用的邮箱", $GET("txtEmail_"), {
                top: 8
            });
            return false
        }
        var f = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        if (!f.test(c)) {
            showtip("请输入正确的邮箱", $GET("txtEmail_"), {
                top: 8
            });
            return false
        }
        if ($.trim($("#txtUserName_").val()) == "") {
            showtip("请输入您的称呼", $GET("txtUserName_"), {
                top: 8
            });
            return false
        }
        if ($.trim($("#txtTS_").val()) == "") {
            showtip("投诉建议不能为空", $GET("txtTS_"), {
                top: 8
            });
            return false
        } else {
            $.ajax({
                url: "/handler/cartHandler.ashx",
                type: "post",
                dataType: "html",
                cache: false,
                data: {
                    operation: "addts",
                    mobile: e,
                    email: c,
                    uName: function () {
                        return $("#txtUserName_").val()
                    },
                    content: function () {
                        return $("#txtTS_").val()
                    }
                },
                success: function (g) {
                    if (g == "1") {
                        showtip("提交成功！", $GET("btnts"), {
                            top: 8
                        });
                        $("#txtTS_").val("")
                    }
                    $("#blk9").hide()
                }
            })
        }
    });
    var b = 1;
    if (window.location.hash) {
        b = window.location.hash.replace("#", "")
    }
    $("#slides").slides({
        preload: true,
        preloadImage: "img/loading.gif",
        generatePagination: true,
        play: 5000,
        pause: 2500,
        hoverPause: true,
        start: b,
        animationComplete: function (c) { }
    });
    function a() {
        if ($(this).attr("id") != "") { $(this).addClass("current").siblings().removeClass("current").parent().siblings().hide().siblings("." + $(this).attr("id")).show() }
    }
    $(".tabt span").click(a);
    
    $(".menu>li").hover(function () {
        $(this).css({
            "background-color": "#fff4f4",
            "border-top": "1px solid #f34761",
            "width": "238px",
            "height": "58px",
            "border-bottom": "1px solid #f34761"
        }).children("ul").css("background", "#fff4f4 url(/images/menu_menu_bg" + $(this).index() + ".png) no-repeat -1px -7px").show()
    },
    function () {
        $(this).css({
            "background-color": "",
            "border-top": "",
            "width": "238px",
            "height": "58px",
            "border-bottom": ""
        }).children("ul").hide()
    });
    $(".menu li ul li").hover(function () {
        $(this).css("background", "")
    },
    function () {
        $(this).css("background", "")
    });
    $(".showmenu").hide();
    $(".menu_show").hover(function () {
        $(".showmenu").show()
    },
    function () {
        $(".showmenu").hide()
    });
    $(".btn1").click(function () {
        $(".fenlei").slideToggle();
        $(this).toggleClass("up")
    });
    $('#keyword').blur(function () {
        if ($('#aresults').attr('cid') == 0) {
            $('#aresults').hide();
        }
    });
    $("#aresults").remove();
    var txt = document.createElement("div");
    var l = $('.search_box').offset();
    var t = l.top + 28;

    txt.innerHTML = '<div class="ac_results" id="aresults" cid="0" style="position: absolute; width: 397px; top: ' + t + 'px; left: ' + l.left + 'px; display: none;"></div>';
    
    document.body.appendChild(txt);
    $("#aresults").mouseout(function () {
        $(this).attr("cid", "0");
    });
    document.getElementById("keyword").onkeyup = function (e) {
        if (this.value == '') {
            $('#aresults').show();
            if ($('#aresults').html() == "") {
                $.ajax({
                    url: "/Handler/cartHandler.ashx?operation=SearchList", data: { 'q': '' }, dataType: "json", type: "post", success: function (txt) {
                        if (txt.result == 0) {
                            $('.ac_results').css('border','0');
                        }
                        else {
                            var result = '';
                            result += '<ul>';

                            if (txt.info != null && txt.info.length > 0) {
                                for (var i = 0; i < txt.info.length; i++) {

                                    if (i % 2 == 0) {
                                        result += '<li class="ac_even"  onmouseover="SearchHistory(this)"><span onclick="tiao(this)">' + txt.info[i].Name + '</span><a style="display:none;color:#00CACA" onclick="delSearchHistory(' + txt.info[i].proid + ')">删除</a></li>';
                                    }
                                    else {
                                        result += '<li class="ac_odd"   onmouseover="SearchHistory(this)"><span onclick="tiao(this)">' + txt.info[i].Name + '</span><a style="display:none;color:#00CACA" onclick="delSearchHistory(' + txt.info[i].proid + ')">删除</a></li>';
                                    }

                                }
                                
                            }
  
                            result += '</ul>';
                            $('.ac_results').css('border', '1px solid #cccccc');
                            $('#aresults').html(result);
                        }
                        //$('body').html(htm);

                    }
                });
            }
        }
        else {
            $('#aresults').hide();
            $("#keyword").autocomplete("/Handler/cartHandler.ashx?operation=SearchList", {
                delay: 10,
                width: 397,
                srcollHeight: 200,
                max: 10,
                dataType: "json",
                minChars: 1,
                scroll: false,
                matchContains: true,
                matchSubset: false,
                autoFill: false,
                cacheLength: 0,
                parse: function (c) {
                    var f = [];
                    var e = c.info;
                    if (c.result == 1) {
                        for (var d = 0; d < e.length; d++) {
                            f[f.length] = {
                                data: e[d],
                                value: e[d].price,
                                result: e[d].Name
                            }
                        }
                    }
                    return f
                },
                selectFirst: false,
                formatItem: function (c) {
                    return "<span>" + c.Name + "</span><em>￥" + c.price + "</em>"
                }
            }).result(function (c, e, d) {
                window.location.href = "/goods" + e.proid + ".html"
            })
        }
    }
    document.getElementById("keyword").onclick = document.getElementById("keyword").onkeyup;

});


function SearchHistory(obj) {
    $(obj).addClass("ac_over").siblings('li').removeClass("ac_over");
    $(obj).children("a").show().parent().siblings("li").children("a").hide();
    $('#aresults').attr('cid', parseInt($(obj).index()) + 1);
}
function tiao(obj) {
    var vl = escape($(obj).html());
    window.location.href = "/SearchProduct.aspx?keyword="+vl;
}
function delSearchHistory(obj) {
    $.ajax({
        url: '/Handler/cartHandler.ashx?operation=delSearchHistory', type: 'post', data: { 'id': obj }, dataType: 'text', success: function (tx) {
            if (tx > 0) {
                $.ajax({
                    url: "/Handler/cartHandler.ashx?operation=SearchList", data: { 'q': '' }, dataType: "json", type: "post", success: function (txt) {
                        if (txt.result == 0) {
                            $('.ac_results').css('border', '0');
                            $('.ac_results').html('');
                        }
                        else {
                            var result = '';
                            result += '<ul>';
                            if (txt.info != null && txt.info.length > 0) {
                                for (var i = 0; i < txt.info.length; i++) {
                                    if (i % 2 == 0) {

                                        result += '<li class="ac_even"  onmouseover="SearchHistory(this)"><span onclick="tiao(this)">' + txt.info[i].Name + '</span><a style="display:none;color:#00CACA" onclick="delSearchHistory(' + txt.info[i].proid + ')">删除</a></li>';
                                    }
                                    else {
                                        result += '<li class="ac_odd"  onmouseover="SearchHistory(this)"><span onclick="tiao(this)">' + txt.info[i].Name + '</span><a style="display:none;color:#00CACA" onclick="delSearchHistory(' + txt.info[i].proid + ')">删除</a></li>';
                                    }

                                }
                            }
                            result += '</ul>';
                            $('.ac_results').css('border', '1px solid #cccccc');
                            $('#aresults').html(result);
                            //$('body').html(htm);
                        }
                    }
                });
            }
        }
        });
}
function loadCartNum() {
    $.ajax({
        url: "/handler/cartHandler.ashx",
        type: "post",
        cache: false,
        dataType: "html",
        data: {
            operation: "loadCarNum"
        },
        success: function (a) {
            $("#buyCount").html(a)
        }
    })
}


function GetChuShi() {
    $.ajax({
        url: "/handler/cartHandler.ashx",
        type: "post",
        cache: false,
        dataType: "json",
        data: {
            operation: "GetChuShi"
        },
        success: function (a) {
            if (a.stats == 1) {
                $("#buyCount").html(a.loadCarNum);
                $("#logState").html(a.checkLogState);
                keyValue = a;
                $("#keyword").attr("placeholder", a.loadKey);
            }
        }
    });
}
function buybyList(a, b, c) {
    var da={
        operation: "addCart",
        productInfo: a,
        count: "1"
    };
    if (c == 1) {
        da = {
            operation: "addCart",
            productInfo: a,
            count: "1",
            type:"2"
        }
    }
    $.ajax({
        url: "/handler/cartHandler.ashx",
        type: "post",
        dataType: "html",
        cache: false,
        data: da,
        success: function (c) {
            loadCartNum();
            if (b == "1") {
                loadCarInfo()
            } else {
                if (b == "0") {
                    window.location.href = "/car.aspx"
                }
            }
        }
    })
    }

function buybyList1(a, b,c, obj) {
    var da = {
        operation: "addCart",
        productInfo: a,
        count: "1"
    };
    if (c == 1) {
        da = {
            operation: "addCart",
            productInfo: a,
            count: "1",
            type: "2"
        }
    }
        $.ajax({
            url: "/handler/cartHandler.ashx",
            type: "post",
            dataType: "html",
            cache: false,
            data: da,
            success: function (c) {
                loadCartNum();
                if (b == "1") {
                    loadCarInfo()
                } else {
                    if (c == "1" || c == "3") {
                        if (b == "0") {
                            window.location.href = "/car.aspx"
                        }
                    }
                    else if (c == "2") {
                        showtip('只能选择一件赠品(购物车已有赠品)！', $GET(obj));
                    }
                    else if (c == "4") {
                        showtip('购物车商品总金额小于需求价格！', $GET(obj));
                    }
                    else {
                        showtip('赠品不能单独购买！', $GET(obj));
                    }
                }
            }
        })
    }
function buyByListJF(id, othis, productid, ppid, usescoreprice, userscore) {
    if (usescoreprice == 0) {
        $.ajax({
            url: '/handler/cartHandler.ashx',
            type: 'post',
            dataType: 'json',
            cache: false,
            data: { 'operation': 'addCartJF', 'id': id, 'count': '1' },
            success: function (txt) {
                if (txt.result == "1") {
                    loadCartNum();
                    window.location.href = "/car.aspx?category=jifen";
                }
                else {
                    showtip(txt.msg, $(othis).get(0), { top: 8, left: 0 })
                }
            }
        });
    }
    else {
        $.ajax({
            url: '?',
            type: 'post',
            dataType: 'json',
            cache: false,
            data: { 'act': 'GetJ' },
            success: function (te) {
                if (te.islogin == '') {
                    showtip("您当前未登录,无法兑换", $(othis).get(0), { top: 8, left: 0 })
                }
                else {
                    if (parseInt(te.jifen) - parseInt(userscore) > 0) {
                        $.ajax({
                            url: '/handler/cartHandler.ashx',
                            type: 'post',
                            dataType: 'html',
                            cache: false,
                            data: { 'operation': 'addCart', 'productInfo': productid + ',' + ppid, 'count': '1', 'type': '1' },
                            success: function (txt) {
                                window.location.href = "/car.aspx";
                            }
                        });
                    }
                    else {
                        showtip("您当前的积分不足，无法兑换", $(othis).get(0), { top: 8, left: 0 })
                    }
                }
            }
        });
    }
}
function collect(a, b) {
    $.ajax({
        url: "/handler/cartHandler.ashx",
        type: "post",
        cache: false,
        dataType: "html",
        data: {
            operation: "collect",
            id: a
        },
        success: function (c) {
            loadCartNum();
            showtip(c, $(b).get(0), {
                top: 8,
                left: 0
            })
        }
    })
}
function checkLogState() {
    $.ajax({
        url: "/handler/cartHandler.ashx",
        type: "post",
        dataType: "html",
        cache: false,
        data: {
            operation: "checkLogState"
        },
        success: function (a) {
            $("#logState").html(a)
        }
    })
}
function logout() {
    $.ajax({
        url: "/handler/cartHandler.ashx",
        type: "post",
        dataType: "html",
        cache: false,
        data: {
            operation: "logout"
        },
        success: function (a) {
            $("#logState").html(a);
            window.location.href = "/index.aspx"
        }
    })
}
function loadKey() {
    $.ajax({
        url: "/handler/cartHandler.ashx",
        type: "post",
        dataType: "html",
        cache: false,
        data: {
            operation: "loadKey"
        },
        success: function (a) {
            keyValue = a;
            $("#keyword").attr("placeholder", a);
        }
    })
}
function beforeSubmit() {
    if ($("#keyword").val() == "输入您想查找的内容") {
        $("#searchKey").val("")
    } else {
        if ($('#keyword').val()!='')
            $("#searchKey").val(escape($('#keyword').val()))
        else
            $("#searchKey").val(escape($('#keyword').attr("placeholder")))
    }
}
var userMenuTimeOut = null;
jQuery(function (a) {
    if (a(".slide-pic").length > 0) {
        var g = {
            interval: 5000,
            fadeInTime: 300,
            fadeOutTime: 200
        };
        var f = a("ul.slide-txt li");
        var b = a("ul.slide-pic li");
        var c = f.length;
        var d = 0;
        var e = null;
        var l = function () {
            window.clearInterval(e)
        };
        var k = function (m) {
            if (m) {
                d = m.current || 0
            } else {
                d = (d >= (c - 1)) ? 0 : (++d)
            }
            b.filter(":visible").fadeOut(g.fadeOutTime,
            function () {
                b.eq(d).fadeIn(g.fadeInTime);
                b.removeClass("cur").eq(d).addClass("cur")
            });
            f.removeClass("cur").eq(d).addClass("cur")
        };
        var h = function () {
            l();
            e = window.setInterval(function () {
                k()
            },
            g.interval)
        };
        var j = function (o, n) {
            l();
            var m = a.inArray(o, n);
            k({
                current: m
            })
        };
        f.hover(function () {
            if (a(this).attr("class") != "cur") {
                j(this, f)
            } else {
                l()
            }
        },
        h);
        h()
    }
}); (function (a) {
    a.fn.extend({
        autocomplete: function (d, c) {
            var b = typeof d == "string";
            c = a.extend({},
            a.Autocompleter.defaults, {
                url: b ? d : null,
                data: b ? null : d,
                delay: b ? a.Autocompleter.defaults.delay : 10,
                max: c && !c.scroll ? 10 : 150
            },
            c);
            c.highlight = c.highlight ||
            function (e) {
                return e
            };
            c.formatMatch = c.formatMatch || c.formatItem;
            return this.each(function () {
                new a.Autocompleter(this, c)
            })
        },
        result: function (b) {
            return this.bind("result", b)
        },
        search: function (b) {
            return this.trigger("search", [b])
        },
        flushCache: function () {
            return this.trigger("flushCache")
        },
        setOptions: function (b) {
            return this.trigger("setOptions", [b])
        },
        unautocomplete: function () {
            return this.trigger("unautocomplete")
        }
    });
    a.Autocompleter = function (k, p) {
        var l = {
            UP: 38,
            DOWN: 40,
            DEL: 46,
            TAB: 9,
            RETURN: 13,
            ESC: 27,
            COMMA: 188,
            PAGEUP: 33,
            PAGEDOWN: 34,
            BACKSPACE: 8
        };
        var b = a(k).attr("autocomplete", "off").addClass(p.inputClass);
        var x;
        var r = "";
        var e = a.Autocompleter.Cache(p);
        var g = 0;
        var m;
        var f = {
            mouseDownOnSelect: false
        };
        var u = a.Autocompleter.Select(p, k, v, f);
        var d;
        a.browser.opera && a(k.form).bind("submit.autocomplete",
        function () {
            if (d) {
                d = false;
                return false
            }
        });
        b.bind((a.browser.opera ? "keypress" : "keydown") + ".autocomplete",
        function (z) {
            g = 1;
            m = z.keyCode;
            switch (z.keyCode) {
                case l.UP:
                    z.preventDefault();
                    if (u.visible()) {
                        u.prev()
                    } else {
                        o(0, true)
                    }
                    break;
                case l.DOWN:
                    z.preventDefault();
                    if (u.visible()) {
                        u.next()
                    } else {
                        o(0, true)
                    }
                    break;
                case l.PAGEUP:
                    z.preventDefault();
                    if (u.visible()) {
                        u.pageUp()
                    } else {
                        o(0, true)
                    }
                    break;
                case l.PAGEDOWN:
                    z.preventDefault();
                    if (u.visible()) {
                        u.pageDown()
                    } else {
                        o(0, true)
                    }
                    break;
                case p.multiple && a.trim(p.multipleSeparator) == "," && l.COMMA: case l.TAB:
                case l.RETURN:
                    if (v()) {
                        z.preventDefault();
                        d = true;
                        return false
                    }
                    break;
                case l.ESC:
                    u.hide();
                    break;
                default:
                    clearTimeout(x);
                    x = setTimeout(o, p.delay);
                    break
            }
        }).focus(function () {
            g++
        }).blur(function () {
            g = 0;
            if (!f.mouseDownOnSelect) {
                h()
            }
        }).click(function () {
            if (g++ > 1 && !u.visible()) {
                o(0, true)
            }
        }).bind("search",
        function () {
            var A = (arguments.length > 1) ? arguments[1] : null;
            function z(D, B) {
                var E;
                if (B && B.length) {
                    for (var C = 0; C < B.length; C++) {
                        if (B[C].result.toLowerCase() == D.toLowerCase()) {
                            E = B[C];
                            break
                        }
                    }
                }
                if (typeof A == "function") {
                    A(E)
                } else {
                    b.trigger("result", E && [E.data, E.value])
                }
            }
            a.each(y(b.val()),
            function (B, C) {
                t(C, z, z)
            })
        }).bind("flushCache",
        function () {
            e.flush()
        }).bind("setOptions",
        function () {
            a.extend(p, arguments[1]);
            if ("data" in arguments[1]) {
                e.populate()
            }
        }).bind("unautocomplete",
        function () {
            u.unbind();
            b.unbind();
            a(k.form).unbind(".autocomplete")
        });
        function v() {
            var B = u.selected();
            if (!B) {
                return false
            }
            var D = B.result;
            r = D;
            if (p.multiple) {
                var F = y(b.val());
                if (F.length > 1) {
                    var C = p.multipleSeparator.length;
                    var z = a(k).selection().start;
                    var E, A = 0;
                    a.each(F,
                    function (G, H) {
                        A += H.length;
                        if (z <= A) {
                            E = G;
                            return false
                        }
                        A += C
                    });
                    F[E] = D;
                    D = F.join(p.multipleSeparator)
                }
                D += p.multipleSeparator
            }
            b.val(D);
            j();
            b.trigger("result", [B.data, B.value]);
            return true
        }
        function o(z, B) {
            if (m == l.DEL) {
                u.hide();
                return
            }
            var A = b.val();
            if (!B && A == r) {
                return
            }
            r = A;
            A = n(A);
            if (A.length >= p.minChars) {
                b.addClass(p.loadingClass);
                if (!p.matchCase) {
                    A = A.toLowerCase()
                }
                t(A, s, j)
            } else {
                w();
                u.hide()
            }
        }
        function y(z) {
            if (!z) {
                return [""]
            }
            if (!p.multiple) {
                return [a.trim(z)]
            }
            return a.map(z.split(p.multipleSeparator),
            function (A) {
                return a.trim(z).length ? a.trim(A) : null
            })
        }
        function n(A) {
            if (!p.multiple) {
                return A
            }
            var B = y(A);
            if (B.length == 1) {
                return B[0]
            }
            var z = a(k).selection().start;
            if (z == A.length) {
                B = y(A)
            } else {
                B = y(A.replace(A.substring(z), ""))
            }
            return B[B.length - 1]
        }
        function c(z, A) {
            if (p.autoFill && (n(b.val()).toLowerCase() == z.toLowerCase()) && m != l.BACKSPACE) {
                b.val(b.val() + A.substring(n(r).length));
                a(k).selection(r.length, r.length + A.length)
            }
        }
        function h() {
            clearTimeout(x);
            x = setTimeout(j, 200)
        }
        function j() {
            var z = u.visible();
            u.hide();
            clearTimeout(x);
            w();
            if (p.mustMatch) {
                b.search(function (A) {
                    if (!A) {
                        if (p.multiple) {
                            var B = y(b.val()).slice(0, -1);
                            b.val(B.join(p.multipleSeparator) + (B.length ? p.multipleSeparator : ""))
                        } else {
                            b.val("");
                            b.trigger("result", null)
                        }
                    }
                })
            }
        }
        function s(A, z) {
            if (z && z.length && g) {
                w();
                u.display(z, A);
                c(A, z[0].value);
                u.show()
            } else {
                j()
            }
        }
        function t(D, C, B) {
            if (!p.matchCase) {
                D = D.toLowerCase()
            }
            var z = e.load(D);
            if (z && z.length) {
                C(D, z)
            } else {
                if ((typeof p.url == "string") && (p.url.length > 0)) {
                    var A = {
                        timestamp: +new Date()
                    };
                    a.each(p.extraParams,
                    function (E, F) {
                        A[E] = typeof F == "function" ? F() : F
                    });
                    a.ajax({
                        mode: "abort",
                        port: "autocomplete" + k.name,
                        dataType: p.dataType,
                        url: p.url,
                        data: a.extend({
                            q: n(D),
                            limit: p.max
                        },
                        A),
                        success: function (E) {
                            var F = p.parse && p.parse(E) || q(E);
                            e.add(D, F);
                            C(D, F)
                        }
                    })
                } else {
                    u.emptyList();
                    B(D)
                }
            }
        }
        function q(z) {
            var B = [];
            var D = z.split("\n");
            for (var A = 0; A < D.length; A++) {
                var C = a.trim(D[A]);
                if (C) {
                    C = C.split("|");
                    B[B.length] = {
                        data: C,
                        value: C[0],
                        result: p.formatResult && p.formatResult(C, C[0]) || C[0]
                    }
                }
            }
            return B
        }
        function w() {
            b.removeClass(p.loadingClass)
        }
    };
    a.Autocompleter.defaults = {
        inputClass: "ac_input",
        resultsClass: "ac_results",
        loadingClass: "ac_loading",
        minChars: 1,
        delay: 400,
        matchCase: false,
        matchSubset: true,
        matchContains: false,
        cacheLength: 10,
        max: 100,
        mustMatch: false,
        extraParams: {},
        selectFirst: true,
        formatItem: function (b) {
            return b[0]
        },
        formatMatch: null,
        autoFill: false,
        width: 0,
        multiple: false,
        multipleSeparator: ", ",
        highlight: function (c, b) {
            return c.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + b.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>")
        },
        scroll: true,
        scrollHeight: 180
    };
    a.Autocompleter.Cache = function (g) {
        var c = {};
        var e = 0;
        function f(k, l) {
            if (!g.matchCase) {
                k = k.toLowerCase()
            }
            var j = k.indexOf(l);
            if (g.matchContains == "word") {
                j = k.toLowerCase().search("\\b" + l.toLowerCase())
            }
            if (j == -1) {
                return false
            }
            return j == 0 || g.matchContains
        }
        function b(j, k) {
            if (e > g.cacheLength) {
                d()
            }
            if (!c[j]) {
                e++
            }
            c[j] = k
        }
        function h() {
            if (!g.data) {
                return false
            }
            var p = {},
            l = 0;
            if (!g.url) {
                g.cacheLength = 1
            }
            p[""] = [];
            for (var k = 0,
            m = g.data.length; k < m; k++) {
                var n = g.data[k];
                n = (typeof n == "string") ? [n] : n;
                var q = g.formatMatch(n, k + 1, g.data.length);
                if (q === false) {
                    continue
                }
                var j = q.charAt(0).toLowerCase();
                if (!p[j]) {
                    p[j] = []
                }
                var o = {
                    value: q,
                    data: n,
                    result: g.formatResult && g.formatResult(n) || q
                };
                p[j].push(o);
                if (l++ < g.max) {
                    p[""].push(o)
                }
            }
            a.each(p,
            function (r, s) {
                g.cacheLength++;
                b(r, s)
            })
        }
        setTimeout(h, 25);
        function d() {
            c = {};
            e = 0
        }
        return {
            flush: d,
            add: b,
            populate: h,
            load: function (o) {
                if (!g.cacheLength || !e) {
                    return null
                }
                if (!g.url && g.matchContains) {
                    var l = [];
                    for (var n in c) {
                        if (n.length > 0) {
                            var j = c[n];
                            a.each(j,
                            function (k, p) {
                                if (f(p.value, o)) {
                                    l.push(p)
                                }
                            })
                        }
                    }
                    return l
                } else {
                    if (c[o]) {
                        return c[o]
                    } else {
                        if (g.matchSubset) {
                            for (var m = o.length - 1; m >= g.minChars; m--) {
                                var j = c[o.substr(0, m)];
                                if (j) {
                                    var l = [];
                                    a.each(j,
                                    function (k, p) {
                                        if (f(p.value, o)) {
                                            l[l.length] = p
                                        }
                                    });
                                    return l
                                }
                            }
                        }
                    }
                }
                return null
            }
        }
    };
    a.Autocompleter.Select = function (q, j, r, d) {
        var c = {
            ACTIVE: "ac_over"
        };
        var m, b = -1,
        e, t = "",
        p = true,
        f, l;
        function h() {
            if (!p) {
                return
            }
            f = a("<div/>").hide().addClass(q.resultsClass).css("position", "absolute").appendTo(document.body);
            l = a("<ul/>").appendTo(f).mouseover(function (u) {
                if (s(u).nodeName && s(u).nodeName.toUpperCase() == "LI") {
                    b = a("li", l).removeClass(c.ACTIVE).index(s(u));
                    a(s(u)).addClass(c.ACTIVE)
                }
            }).click(function (u) {
                a(s(u)).addClass(c.ACTIVE);
                r();
                j.focus();
                return false
            }).mousedown(function () {
                d.mouseDownOnSelect = true
            }).mouseup(function () {
                d.mouseDownOnSelect = false
            });
            if (q.width > 0) {
                f.css("width", q.width)
            }
            p = false
        }
        function s(v) {
            var u = v.target;
            while (u && u.tagName != "LI") {
                u = u.parentNode
            }
            if (!u) {
                return []
            }
            return u
        }
        function o(w) {
            m.slice(b, b + 1).removeClass(c.ACTIVE);
            n(w);
            var u = m.slice(b, b + 1).addClass(c.ACTIVE);
            if (q.scroll) {
                var v = 0;
                m.slice(0, b).each(function () {
                    v += this.offsetHeight
                });
                if ((v + u[0].offsetHeight - l.scrollTop()) > l[0].clientHeight) {
                    l.scrollTop(v + u[0].offsetHeight - l.innerHeight())
                } else {
                    if (v < l.scrollTop()) {
                        l.scrollTop(v)
                    }
                }
            }
        }
        function n(u) {
            b += u;
            if (b < 0) {
                b = m.size() - 1
            } else {
                if (b >= m.size()) {
                    b = 0
                }
            }
        }
        function k(u) {
            return q.max && q.max < u ? q.max : u
        }
        function g() {
            l.empty();
            var x = k(e.length);
            for (var v = 0; v < x; v++) {
                if (!e[v]) {
                    continue
                }
                var u = q.formatItem(e[v].data, v + 1, x, e[v].value, t);
                if (u === false) {
                    continue
                }
                var w = a("<li/>").html(q.highlight(u, t)).addClass(v % 2 == 0 ? "ac_even" : "ac_odd").appendTo(l)[0];
                a.data(w, "ac_data", e[v])
            }
            m = l.find("li");
            if (q.selectFirst) {
                m.slice(0, 1).addClass(c.ACTIVE);
                b = 0
            }
            if (a.fn.bgiframe) {
                l.bgiframe()
            }
        }
        return {
            display: function (u, v) {
                h();
                e = u;
                t = v;
                g()
            },
            next: function () {
                o(1)
            },
            prev: function () {
                o(-1)
            },
            pageUp: function () {
                if (b != 0 && b - 8 < 0) {
                    o(-b)
                } else {
                    o(-8)
                }
            },
            pageDown: function () {
                if (b != m.size() - 1 && b + 8 > m.size()) {
                    o(m.size() - 1 - b)
                } else {
                    o(8)
                }
            },
            hide: function () {
                f && f.hide();
                m && m.removeClass(c.ACTIVE);
                b = -1
            },
            visible: function () {
                return f && f.is(":visible")
            },
            current: function () {
                return this.visible() && (m.filter("." + c.ACTIVE)[0] || q.selectFirst && m[0])
            },
            show: function () {
                var v = a(j).offset();
                f.css({
                    width: typeof q.width == "string" || q.width > 0 ? q.width : a(j).width(),
                    top: v.top + j.offsetHeight,
                    left: v.left
                }).show();
                if (q.scroll) {
                    l.scrollTop(0);
                    l.css({
                        maxHeight: q.scrollHeight,
                        overflow: "auto"
                    });
                    if (a.browser.msie && typeof document.body.style.maxHeight === "undefined") {
                        var u = 0;
                        m.each(function () {
                            u += this.offsetHeight
                        });
                        var w = u > q.scrollHeight;
                        l.css("height", w ? q.scrollHeight : u);
                        if (!w) {
                            m.width(l.width() - parseInt(m.css("padding-left")) - parseInt(m.css("padding-right")))
                        }
                    }
                }
            },
            selected: function () {
                var u = m && m.filter("." + c.ACTIVE).removeClass(c.ACTIVE);
                return u && u.length && a.data(u[0], "ac_data")
            },
            emptyList: function () {
                l && l.empty()
            },
            unbind: function () {
                f && f.remove()
            }
        }
    };
    a.fn.selection = function (g, c) {
        if (g !== undefined) {
            return this.each(function () {
                if (this.createTextRange) {
                    var k = this.createTextRange();
                    if (c === undefined || g == c) {
                        k.move("character", g);
                        k.select()
                    } else {
                        k.collapse(true);
                        k.moveStart("character", g);
                        k.moveEnd("character", c);
                        k.select()
                    }
                } else {
                    if (this.setSelectionRange) {
                        this.setSelectionRange(g, c)
                    } else {
                        if (this.selectionStart) {
                            this.selectionStart = g;
                            this.selectionEnd = c
                        }
                    }
                }
            })
        }
        var d = this[0];
        if (d.createTextRange) {
            var f = document.selection.createRange(),
            e = d.value,
            h = "<->",
            j = f.text.length;
            f.text = h;
            var b = d.value.indexOf(h);
            d.value = e;
            this.selection(b, b + j);
            return {
                start: b,
                end: b + j
            }
        } else {
            if (d.selectionStart !== undefined) {
                return {
                    start: d.selectionStart,
                    end: d.selectionEnd
                }
            }
        }
    }
})(jQuery); (function (a) {
    a.fn.slides = function (b) {
        b = a.extend({},
        a.fn.slides.option, b);
        return this.each(function () {
            a("." + b.container, a(this)).children().wrapAll('<div class="slides_control"/>');
            var k = a(this),
            f = a(".slides_control", k),
            w = f.children().size(),
            x = f.children().outerWidth(),
            l = f.children().outerHeight(),
            u = b.start - 1,
            j = b.effect.indexOf(",") < 0 ? b.effect : b.effect.replace(" ", "").split(",")[0],
            q = b.effect.indexOf(",") < 0 ? j : b.effect.replace(" ", "").split(",")[1],
            o = 0,
            t = 0,
            p = 0,
            g = 0,
            n,
            c,
            e,
            s,
            h;
            if (w < 2) {
                return
            }
            if (u < 0) {
                u = 0
            }
            if (u > w) {
                u = w - 1
            }
            if (b.start) {
                g = u
            }
            if (b.randomize) {
                f.randomize()
            }
            a("." + b.container, k).css({
                overflow: "hidden",
                position: "relative"
            });
            f.css({
                position: "relative",
                width: (x * 3),
                height: l,
                left: -x
            });
            f.children().css({
                position: "absolute",
                top: 0,
                left: x,
                zIndex: 0,
                display: "none"
            });
            if (b.autoHeight) {
                f.animate({
                    height: f.children(":eq(" + u + ")").outerHeight()
                },
                b.autoHeightSpeed)
            }
            if (b.preload && f.children()[0].tagName == "IMG") {
                k.css({
                    background: "url(" + b.preloadImage + ") no-repeat 50% 50%"
                });
                var m = a("img:eq(" + u + ")", k).attr("src") + "?" + (new Date()).getTime();
                a("img:eq(" + u + ")", k).attr("src", m).load(function () {
                    a(this).fadeIn(b.fadeSpeed,
                    function () {
                        a(this).css({
                            zIndex: 5
                        });
                        k.css({
                            background: ""
                        });
                        n = true
                    })
                })
            } else {
                f.children(":eq(" + u + ")").fadeIn(b.fadeSpeed,
                function () {
                    n = true
                })
            }
            if (b.bigTarget) {
                f.children().css({
                    cursor: "pointer"
                });
                f.children().click(function () {
                    d("next", j);
                    return false
                })
            }
            if (b.hoverPause && b.play) {
                f.children().bind("mouseover",
                function () {
                    v()
                });
                f.children().bind("mouseleave",
                function () {
                    r()
                })
            }
            if (b.generateNextPrev) {
                a("." + b.container, k).after('<a href="#" class="' + b.prev + '">Prev</a>');
                a("." + b.prev, k).after('<a href="#" class="' + b.next + '">Next</a>')
            }
            a("." + b.next, k).click(function (y) {
                y.preventDefault();
                if (b.play) {
                    r()
                }
                d("next", j)
            });
            a("." + b.prev, k).click(function (y) {
                y.preventDefault();
                if (b.play) {
                    r()
                }
                d("prev", j)
            });
            if (b.generatePagination) {
                k.append("<ul class=" + b.paginationClass + "></ul>");
                f.children().each(function () {
                    a("." + b.paginationClass, k).append('<li><a href="#' + p + '">' + (p + 1) + "</a></li>");
                    p++
                })
            } else {
                a("." + b.paginationClass + " li a", k).each(function () {
                    a(this).attr("href", "#" + p);
                    p++
                })
            }
            a("." + b.paginationClass + " li a[href=#" + u + "]", k).parent().addClass("current");
            a("." + b.paginationClass + " li a", k).click(function () {
                if (b.play) {
                    r()
                }
                e = a(this).attr("href").replace("#", "");
                if (g != e) {
                    d("pagination", q, e)
                }
                return false
            });
            a("a.link", k).click(function () {
                if (b.play) {
                    r()
                }
                e = a(this).attr("href").replace("#", "") - 1;
                if (g != e) {
                    d("pagination", q, e)
                }
                return false
            });
            if (b.play) {
                playInterval = setInterval(function () {
                    d("next", j)
                },
                b.play);
                k.data("interval", playInterval)
            }
            function v() {
                clearInterval(k.data("interval"))
            }
            function r() {
                if (b.pause) {
                    clearTimeout(k.data("pause"));
                    clearInterval(k.data("interval"));
                    pauseTimeout = setTimeout(function () {
                        clearTimeout(k.data("pause"));
                        playInterval = setInterval(function () {
                            d("next", j)
                        },
                        b.play);
                        k.data("interval", playInterval)
                    },
                    b.pause);
                    k.data("pause", pauseTimeout)
                } else {
                    v()
                }
            }
            function d(z, A, y) {
                if (!c && n) {
                    c = true;
                    switch (z) {
                        case "next":
                            t = g;
                            o = g + 1;
                            o = w === o ? 0 : o;
                            s = x * 2;
                            z = -x * 2;
                            g = o;
                            break;
                        case "prev":
                            t = g;
                            o = g - 1;
                            o = o === -1 ? w - 1 : o;
                            s = 0;
                            z = 0;
                            g = o;
                            break;
                        case "pagination":
                            o = parseInt(y, 10);
                            t = a("." + b.paginationClass + " li.current a", k).attr("href").replace("#", "");
                            if (o > t) {
                                s = x * 2;
                                z = -x * 2
                            } else {
                                s = 0;
                                z = 0
                            }
                            g = o;
                            break
                    }
                    if (A === "fade") {
                        b.animationStart();
                        if (b.crossfade) {
                            f.children(":eq(" + o + ")", k).css({
                                zIndex: 10
                            }).fadeIn(b.fadeSpeed,
                            function () {
                                f.children(":eq(" + t + ")", k).css({
                                    display: "none",
                                    zIndex: 0
                                });
                                a(this).css({
                                    zIndex: 0
                                });
                                b.animationComplete(o + 1);
                                c = false
                            })
                        } else {
                            b.animationStart();
                            f.children(":eq(" + t + ")", k).fadeOut(b.fadeSpeed,
                            function () {
                                if (b.autoHeight) {
                                    f.animate({
                                        height: f.children(":eq(" + o + ")", k).outerHeight()
                                    },
                                    b.autoHeightSpeed,
                                    function () {
                                        f.children(":eq(" + o + ")", k).fadeIn(b.fadeSpeed)
                                    })
                                } else {
                                    f.children(":eq(" + o + ")", k).fadeIn(b.fadeSpeed,
                                    function () {
                                        if (a.browser.msie) {
                                            a(this).get(0).style.removeAttribute("filter")
                                        }
                                    })
                                }
                                b.animationComplete(o + 1);
                                c = false
                            })
                        }
                    } else {
                        f.children(":eq(" + o + ")").css({
                            left: s,
                            display: "block"
                        });
                        if (b.autoHeight) {
                            b.animationStart();
                            f.animate({
                                left: z,
                                height: f.children(":eq(" + o + ")").outerHeight()
                            },
                            b.slideSpeed,
                            function () {
                                f.css({
                                    left: -x
                                });
                                f.children(":eq(" + o + ")").css({
                                    left: x,
                                    zIndex: 5
                                });
                                f.children(":eq(" + t + ")").css({
                                    left: x,
                                    display: "none",
                                    zIndex: 0
                                });
                                b.animationComplete(o + 1);
                                c = false
                            })
                        } else {
                            b.animationStart();
                            f.animate({
                                left: z
                            },
                            b.slideSpeed,
                            function () {
                                f.css({
                                    left: -x
                                });
                                f.children(":eq(" + o + ")").css({
                                    left: x,
                                    zIndex: 5
                                });
                                f.children(":eq(" + t + ")").css({
                                    left: x,
                                    display: "none",
                                    zIndex: 0
                                });
                                b.animationComplete(o + 1);
                                c = false
                            })
                        }
                    }
                    if (b.pagination) {
                        a("." + b.paginationClass + " li.current", k).removeClass("current");
                        a("." + b.paginationClass + " li a[href=#" + o + "]", k).parent().addClass("current")
                    }
                }
            }
        })
    };
    a.fn.slides.option = {
        preload: false,
        preloadImage: "/img/loading.gif",
        container: "slides_container",
        generateNextPrev: false,
        next: "next",
        prev: "prev",
        pagination: true,
        generatePagination: true,
        paginationClass: "pagination",
        fadeSpeed: 350,
        slideSpeed: 350,
        start: 1,
        effect: "slide",
        crossfade: false,
        randomize: false,
        play: 0,
        pause: 0,
        hoverPause: false,
        autoHeight: false,
        autoHeightSpeed: 350,
        bigTarget: false,
        animationStart: function () { },
        animationComplete: function () { }
    };
    a.fn.randomize = function (b) {
        function c() {
            return (Math.round(Math.random()) - 0.5)
        }
        return (a(this).each(function () {
            var e = a(this);
            var d = e.children();
            var f = d.length;
            if (f > 1) {
                d.hide();
                var g = [];
                for (i = 0; i < f; i++) {
                    g[g.length] = i
                }
                g = g.sort(c);
                a.each(g,
                function (m, n) {
                    var h = d.eq(n);
                    var l = h.clone(true);
                    l.show().appendTo(e);
                    if (b !== undefined) {
                        b(h, l)
                    }
                    h.remove()
                })
            }
        }))
    }
})(jQuery); (function (a, e, c, d) {
    var b = a(e);
    a.fn.lazyload = function (h) {
        var g = this;
        var f;
        var j = {
            threshold: 0,
            failure_limit: 0,
            event: "scroll",
            effect: "show",
            container: e,
            data_attribute: "original",
            skip_invisible: true,
            appear: null,
            load: null
        };
        function k() {
            var l = 0;
            g.each(function () {
                var m = a(this);
                if (j.skip_invisible && !m.is(":visible")) {
                    return
                }
                if (a.abovethetop(this, j) || a.leftofbegin(this, j)) { } else {
                    if (!a.belowthefold(this, j) && !a.rightoffold(this, j)) {
                        m.trigger("appear");
                        l = 0
                    } else {
                        if (++l > j.failure_limit) {
                            return false
                        }
                    }
                }
            })
        }
        if (h) {
            if (d !== h.failurelimit) {
                h.failure_limit = h.failurelimit;
                delete h.failurelimit
            }
            if (d !== h.effectspeed) {
                h.effect_speed = h.effectspeed;
                delete h.effectspeed
            }
            a.extend(j, h)
        }
        f = (j.container === d || j.container === e) ? b : a(j.container);
        if (0 === j.event.indexOf("scroll")) {
            f.bind(j.event,
            function (l) {
                return k()
            })
        }
        this.each(function () {
            var m = this;
            var l = a(m);
            m.loaded = false;
            l.one("appear",
            function () {
                if (!this.loaded) {
                    if (j.appear) {
                        var n = g.length;
                        j.appear.call(m, n, j)
                    }
                    a("<img />").bind("load",
                    function () {
                        l.hide().attr("src", l.data(j.data_attribute))[j.effect](j.effect_speed);
                        m.loaded = true;
                        var p = a.grep(g,
                        function (q) {
                            return !q.loaded
                        });
                        g = a(p);
                        if (j.load) {
                            var o = g.length;
                            j.load.call(m, o, j)
                        }
                    }).attr("src", l.data(j.data_attribute))
                }
            });
            if (0 !== j.event.indexOf("scroll")) {
                l.bind(j.event,
                function (n) {
                    if (!m.loaded) {
                        l.trigger("appear")
                    }
                })
            }
        });
        b.bind("resize",
        function (l) {
            k()
        });
        if ((/iphone|ipod|ipad.*os 5/gi).test(navigator.appVersion)) {
            b.bind("pageshow",
            function (l) {
                if (l.originalEvent.persisted) {
                    g.each(function () {
                        a(this).trigger("appear")
                    })
                }
            })
        }
        a(e).load(function () {
            k()
        });
        return this
    };
    a.belowthefold = function (f, h) {
        var g;
        if (h.container === d || h.container === e) {
            g = b.height() + b.scrollTop()
        } else {
            g = a(h.container).offset().top + a(h.container).height()
        }
        return g <= a(f).offset().top - h.threshold
    };
    a.rightoffold = function (f, h) {
        var g;
        if (h.container === d || h.container === e) {
            g = b.width() + b.scrollLeft()
        } else {
            g = a(h.container).offset().left + a(h.container).width()
        }
        return g <= a(f).offset().left - h.threshold
    };
    a.abovethetop = function (f, h) {
        var g;
        if (h.container === d || h.container === e) {
            g = b.scrollTop()
        } else {
            g = a(h.container).offset().top
        }
        return g >= a(f).offset().top + h.threshold + a(f).height()
    };
    a.leftofbegin = function (f, h) {
        var g;
        if (h.container === d || h.container === e) {
            g = b.scrollLeft()
        } else {
            g = a(h.container).offset().left
        }
        return g >= a(f).offset().left + h.threshold + a(f).width()
    };
    a.inviewport = function (f, g) {
        return !a.rightoffold(f, g) && !a.leftofbegin(f, g) && !a.belowthefold(f, g) && !a.abovethetop(f, g)
    };
    a.extend(a.expr[":"], {
        "below-the-fold": function (f) {
            return a.belowthefold(f, {
                threshold: 0
            })
        },
        "above-the-top": function (f) {
            return !a.belowthefold(f, {
                threshold: 0
            })
        },
        "right-of-screen": function (f) {
            return a.rightoffold(f, {
                threshold: 0
            })
        },
        "left-of-screen": function (f) {
            return !a.rightoffold(f, {
                threshold: 0
            })
        },
        "in-viewport": function (f) {
            return a.inviewport(f, {
                threshold: 0
            })
        },
        "above-the-fold": function (f) {
            return !a.belowthefold(f, {
                threshold: 0
            })
        },
        "right-of-fold": function (f) {
            return a.rightoffold(f, {
                threshold: 0
            })
        },
        "left-of-fold": function (f) {
            return !a.rightoffold(f, {
                threshold: 0
            })
        }
    })
})(jQuery, window, document);
var timer = 500;
var wintip = null;
function trim(a) {
    return String(a).replace(/^\s+|\s+$/, "")
}
function $GET(a) {
    return document.getElementById(a) || null
}
function showtip(a, b, c, d) {
    if (typeof c == "object") {
        if (c.left == null) {
            c.left = 0
        }
        if (c.top == null) {
            c.top = 0
        }
    } else {
        c = {
            left: 0,
            top: 0
        }
    }
    if (!e) {
        if ($GET("tipdiv")) {
            e = $GET("tipdiv")
        }
    }
    if (!e) {
        var e = document.createElement("span");
        e.id = "tipdiv";
        var f = document.createElement("span");
        f.id = "tipdiv_msg";
        e.appendChild(f);
        e.posoff = {
            offx: 0,
            offy: 0
        };
        e.msgobj = f;
        e.hide = function () {
            this.style.display = "none";
            this.msgobj.innerHTML = ""
        };
        document.body.appendChild(e);
        e.hide();
        e.position = function (h) {
            var g = {
                left: parseInt(h.offsetLeft),
                top: parseInt(h.offsetTop)
            };
            while (h = h.offsetParent) {
                g.left += h.offsetLeft;
                g.top += h.offsetTop
            }
            this.style.left = ((g.left - 20) + c.left) + "px";
            this.style.top = ((g.top - 36) + c.top) + "px";
            return this
        };
        e.posshow = function (h, g) {
            this.style.display = document.all ? "" : "block";
            this.position(g);
            clearTimeout(e.timer);
            e.timer = setTimeout(function () {
                e.hide()
            },
            h);
            return this
        };
        e.setmsg = function (g) {
            this.msgobj.innerHTML = g;
            return this
        }
    }
    e.setmsg(a).posshow(d || 3000, b)
}
showtip.hide = function () {
    wintip.hide()
};
function PresJson(jsonData, callfunc) {
    var json = null;
    try {
        json = eval("(" + jsonData.replace(/\n/g, "") + ")")
    } catch (e) {
        alert("解析错误！");
        return null
    }
    if (typeof callfunc == "function") {
        callfunc(json)
    }
    return json
}
$(document).ready(function () { });
function addCart(c) {
    var da = {
        operation: "addCart",
        productInfo: function () {
            return $("#HproductInfo").val()
        },
        count: function () {
            return $("#buyNum").val()
        }
    };
    if (c == 1) {
        da = {
            operation: "addCart",
            productInfo: function () {
                return $("#HproductInfo").val()
            },
            count: function () {
                return $("#buyNum").val()
            },
            type:"2"
        }
    }
    if ($("#isChange").val() == "1") {
        showtip("换购商品，不能单独购买", $GET("buy_"), {
            top: 8,
            left: 0
        })
    } else {
        if (isNaN($("#buyNum").val())) {
            showtip("请输入正确的数字", $GET("buyNum"), {
                top: 8,
                left: 0
            })
        } else {
            $.ajax({
                url: "/handler/cartHandler.ashx",
                type: "post",
                dataType: "html",
                data: da,
                success: function (a) {
                    window.location.href = "/car.aspx"
                }
            })
        }
    }
}
function buyCombine(c) {
    var a = false;
    var inde = new Array();
    $("input[name=match]").each(function (i) {
        if ($(this).get(0).checked == true) {
            var b = $(this).val();
            buybyList(b, "",c);
            inde.push(i);
        }
    });
    if (inde.length == $('input[name=match]:checked').length && inde.length!=0) {
        a = true;
    }
    if (!a) {
        showtip("请选择您所需要搭配的商品", $GET("buyCom"), {
            top: 8,
            left: 0
        })
    } else {
        buybyList($("#HproductInfo").val(), "0",c)
    }
};