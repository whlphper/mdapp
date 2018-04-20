var timer = 500;
var wintip = null;
function trim(str) { return String(str).replace(/^\s+|\s+$/, '') }
function $GET(id) { return document.getElementById(id) || null }
function showtip(msg, obj, opt, time) {
    if (typeof opt == 'object') {
        if (opt['left'] == null) { opt.left = 0 }
        if (opt['top'] == null) { opt.top = 0 }
    } else { opt = { left: 0, top: 0} }
    if (!wintip) { if ($GET('tipdiv')) { wintip = $GET('tipdiv') } }
    if (!wintip) {
        var wintip = document.createElement("span");
        wintip.id = "tipdiv";
        var wintipmsg = document.createElement("span");
        wintipmsg.id = "tipdiv_msg";
        wintip.appendChild(wintipmsg);
        wintip.posoff = { offx: 0, offy: 0 }
        wintip.msgobj = wintipmsg;
        wintip.hide = function () { this.style.display = 'none'; this.msgobj.innerHTML = ''; }
        document.body.appendChild(wintip);
        wintip.hide();
        wintip.position = function (posobj) {
            var pos = { left: parseInt(posobj.offsetLeft), top: parseInt(posobj.offsetTop) }
            while (posobj = posobj.offsetParent) {
                pos.left += posobj.offsetLeft;
                pos.top += posobj.offsetTop
            }
            this.style.left = ((pos.left - 20) + opt.left) + 'px';
            this.style.top = ((pos.top - 36) + opt.top) + 'px';
            return this;
        }
        wintip.posshow = function (time, posobj) {
            this.style.display = document.all ? '' : 'block';
            this.position(posobj);
            clearTimeout(wintip.timer)
            wintip.timer = setTimeout(function () { wintip.hide() }, time);
            return this;
        }
        wintip.setmsg = function (msg) { this.msgobj.innerHTML = msg; return this; }
    }
    wintip.setmsg(msg).posshow(time || 3000, obj);
}
showtip.hide = function () { wintip.hide(); }


function PresJson(jsonData, callfunc) {
    var json = null;
    try { json = eval('(' + jsonData.replace(/\n/g, '') + ')') } catch (e) { alert('解析错误！'); return null; }
    if (typeof callfunc == 'function') { callfunc(json) }
    return json;
}


jQuery.fn.extend({
    serializeData: function (v) {
        var arr = $(this).serializeArray();
        var _jsonData = {};
        for (var i = 0; i < arr.length; i++) {
            var f = arr[i];
            if (_jsonData[f.name]) {
                _jsonData[f.name] += "," + f.value;
            } else {
                _jsonData[f.name] = f.value;
            }
        }
        if (typeof v == 'object') {
            for (var o in v) {
                _jsonData[o] = v[o];
            }
        }
        return _jsonData;
    }

});

/**
 * 打开一个网页对话框
 */
function OpenDialog(url, winBackcall, width, height) {
    if (!width) { width = 620; }
    if (!height) { height = 620; }
    if (typeof winBackcall != 'function') { winBackcall = function () { } }
    if (/MSIE/.test(window.navigator.userAgent)) {
        window.open(url, '', 'modal=yes,width=' + width + ',height=' + height + ',top=200,left=200,resizable=yes,scrollbars=yes');
        this.returnAction = winBackcall;
    } else {
        this.returnAction = winBackcall;
        var value = window.showModalDialog(url, 'mywin', 'dialogWidth:' + width + 'px;dialogHeight:' + height + 'px;')
        if (value != null) {
            winBackcall(value);
        }
    }
}
/**
 * 打开的网页对话框回调函数
 */
function returnValueFun(value) {
    if (window.opener) {
        window.opener.returnAction(value);
        window.close();
    } else {
        parent.window.returnValue = value;
        window.close();
    }
}
/*显示图片*/
function showtippic(tiptarget, path) {
    if (path == undefined)
    { path = ""; }
    path += this.value;
    $(tiptarget).mouseover(function () {
        if (this.value.length < 5) return;
        if (!window.mpic) {
            window.mpic = new Image();
            window.mpic.onload = function () {
                if (this.width > 400) { window.mpic.width = 400; }
            };
            window.mpic.src = path;
            window.mpic.osrc = path;

            $(document.body).append(window.mpic);
        } else {
            if (window.mpic.osrc != path) {
                window.mpic.src = path;
                window.mpic.osrc = path;
            }
        }
        var pos = $(tiptarget).offset();
        $(window.mpic).show().css({ border: '3px solid #ccc', position: 'absolute', left: pos.left + 'px', top: pos.top + 20 + 'px' })
    }).mouseout(function () {
        $(window.mpic).hide();
    })
}

/*
 下载编辑器内的图片
*/
function DownImage(kedit, pictype) {
    var html = kedit.html();
    var imgreg = /(<img)((?:\s+[^>]*?)?(?:\s+src="\s*([^"]+)\s*")(?: [^>]*)?)(\/?>)/ig;
    if (imgreg.test(html)) {
        var imgarr = imgreg.exec(html);
        if (!window.__imgdiv) {
            var imgdiv = document.createElement('div');
            imgdiv.style.display = 'none';
            window.__imgdiv = imgdiv;
            //document.body.appendChild(imgdiv);	
        }
        window.__imgdiv.innerHTML = html;
         imgarr = window.__imgdiv.getElementsByTagName('img');
        var srcarr = [], tmp_src = '';
        for (var i = 0; i < imgarr.length; i++) {
            tmp_src = String(imgarr[i].src);
            if (tmp_src.substring(0, 7) == 'http://' && tmp_src.indexOf('ch999') == -1 && tmp_src.indexOf('localhost') == -1)
                srcarr.push(tmp_src);
        };
        if (srcarr.length == 0) { window.__imgdiv.innerHTML = ''; return; }
        $.post("../DownImage.ashx?pictype="+pictype, { 'downimg': srcarr.join('#') }, function (re) {
            if (re.imgs && re.imgs.length > 0) {
                for (var i = 0; i < re.imgs.length; i++) {
                    var pic = re.imgs[i];
                    html = html.replace(pic.osrc, pic.nsrc);
                }
            }
            kedit.html(html);
            window.__imgdiv.innerHTML = '';
        }, 'json');
    }
}
/* 创建编辑器, 指定图片上传类型*/
function CreateEditor(target,pictype) {
    if (!KindEditor.___isinit) {
        KindEditor.lang({ downimg: '下载网络图片' });
        KindEditor.plugin('downimg', function (K) {
            var self = this, name = 'downimg';
            self.clickToolbar(name, function () {
                DownImage(self, pictype);
            });
        });
        KindEditor.___isinit = true;
    };
    return KindEditor.create(target, {
        allowFileManager: true,
        allowFileManager: true,
        fileManagerJson: "../kindeditor/asp.net/file_manager_json.ashx?pictype="+pictype,
        uploadJson: "../kindeditor/asp.net/upload_json.ashx?pictype="+pictype,
        items: ['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', '|', 'cut', 'copy', 'paste', 'plainpaste', 'wordpaste', '|',
				'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
				'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/', 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor',
				'bold', 'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage', 'flash', 'media', 'insertfile', 'table', 'hr',
				'emoticons', 'baidumap', 'pagebreak', 'anchor', 'link', 'unlink', '|', 'downimg']
    });
}
//创建一个百度文本编辑器
function CreateUEditor(target, pictype)
{
    UE.Editor.prototype._bkGetActionUrl = UE.Editor.prototype.getActionUrl;
    UE.Editor.prototype.getActionUrl = function (action) {
        if (action == 'uploadimage' || action == 'uploadfile') 
            return '../ueditor/net/upload_json.ashx?pictype=' + pictype + '';
        else if (action == 'uploadvideo') 
            return '../ueditor/net/upload_json.ashx?pictype=' + pictype + '&dir=media';
        else if (action == 'listimage' || action == 'listfile') 
            return '../ueditor/file_manager_json.ashx?pictype=' + pictype + '';
        else 
            return this._bkGetActionUrl.call(this, action);
    };
    UE.Editor.prototype.pictype = pictype;
    return UE.getEditor(target, {
        initialFrameWidth: 780,
        initialFrameHeight: 460,
        enableAutoSave: false,
        imagePath: "/",
        imageCompressEnable:false,
        toolbars: [[
           'paragraph', 'fontfamily', 'fontsize', '|', 'insertorderedlist', 'insertunorderedlist', 'indent', 'lineheight', 'superscript', 'subscript', '|',
           'undo', 'redo', '|', 'preview', 'template', 'pasteplain', '|',
            'removeformat', 'selectall', 'formatmatch', 'autotypeset', '|', 'source', 'fullscreen'
        ], [
            'forecolor', 'backcolor', 'bold', 'italic', 'underline', 'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
            'link', 'unlink', 'anchor', 'date', 'time', 'spechars', 'searchreplace', '|',
            'simpleupload', 'insertimage', 'insertvideo', 'attachment', 'inserttable', 'horizontal', 'emotion', 'map', 'pagebreak', 'help'
        ]]
    });
}
function showtippic_(tiptarget,isoriginal) {
    $(tiptarget).mouseover(function () {
        var allPath = "/pic/product/800x800/" + this.value;
        if (isoriginal) allPath = "/pic/product/opic/" + this.value;
        if (allPath.length < 5 || $.trim(this.value) == "") return;
        if (!window.mpic) {
            window.mpic = new Image();
            window.mpic.onload = function () {
                if (this.width > 400) { window.mpic.width = 400; }
            };
            window.mpic.src = allPath;
            window.mpic.osrc = allPath;

            $(document.body).append(window.mpic);
        } else {
            if (window.mpic.osrc != allPath) {
                window.mpic.src = allPath;
                window.mpic.osrc = allPath;
            }
        }
        var pos = $(tiptarget).offset();
        $(window.mpic).show().css({ border: '3px solid #ccc', position: 'absolute', left: pos.left + 'px', top: pos.top + 20 + 'px' })
    }).mouseout(function () {
        $(window.mpic).hide();
    })
}

function showtippic1(tiptarget, path, maxHeight) {    
    $(tiptarget).mouseover(function () {
        var path1;
        if (path == undefined)
        { path1 = ""; }
        path1 =path+ this.value;
        if (this.value.length < 5) return;
        if (!window.mpic) {
            window.mpic = new Image();
            window.mpic.onload = function () {
                if (maxHeight == null)
                { if (this.width > 400) window.mpic.width = 400; }
                else
                    window.mpic.height = maxHeight;
            };
            window.mpic.src = path1;
            window.mpic.osrc = path1;

            $(document.body).append(window.mpic);
        } else {
            if (window.mpic.osrc != path1) {
                window.mpic.src = path1;
                window.mpic.osrc = path1;
            }
        }
        var pos = $(tiptarget).offset();
        $(window.mpic).show().css({ border: '3px solid #ccc', position: 'absolute', left: pos.left + 'px', top: pos.top + 20 + 'px' })
    }).mouseout(function () {
        $(window.mpic).hide();
    })
}

/*设置cookie*/
var Cookie = {
    Set: function (name, value, time, path, domain) {
        this.Clear(name, "/", "");
        this.Clear(name, "/", "mama999.com");
        var expires = new Date(new Date().getTime() + (time || 24) * 3600 * 1000);
        if (time == 0) {
            expires = null;
        }   
        document.cookie = name + "=" + encodeURIComponent(value) + ((expires) ? "; expires=" + expires.toGMTString() : "") + ((path) ? "; path=" + path : "; path=/") + ((domain) ? ";domain=" + domain : ";domain=mama999.com");
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

        document.cookie = name + "=" + ((path) ? "; path=" + path : "; path=/") + ((domain) ? "; domain=" + domain : ";domain=mama999.com") + ";expires=Fri, 02-Jan-1970 00:00:00 GMT";
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

$(function(){
 $("select[title]").each(function(i){
	if( this.title!="")
	this.value=this.title;
 });
});

