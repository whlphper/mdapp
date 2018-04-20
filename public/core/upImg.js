window.addEventListener("onorientationchange" in window ? "orientationchange" : "resize", ___resize, false);
function ___resize(){
    var psd_w = 750,	psd_h = 1206,//设计稿尺寸
        max_w = 480,	min_w = 320,//最大、最小屏幕尺寸范围
        win_w = $(window).width(),	win_h = $(window).height(),//浏览器尺寸
        psd_sp = psd_w / psd_h,	win_sp = win_w / win_h;

    if(win_sp > psd_sp){
        // win_w *= psd_sp / win_sp;
    }

    win_w = win_w > max_w ? max_w : win_w < min_w ? min_w : win_w;

    $("html").css({
        "fontSize" : win_w / psd_w * 100 + "px"
    });
}
___resize();

var Brand = {
    toggleVideo:function (obj) {
        var video = $('#brand-video');
        var _this = $(obj);
        if(!_this.hasClass('active')){
            video.get(0).play();
            _this.addClass('active');
        }else{
            video.get(0).pause();
            _this.removeClass('active');
        }
    },
    videoEnd:function (obj) {
        var _this = $(obj);
        _this.next().removeClass('active');
    }
};

var Mall = {
    swiperInit:function () {
        var swiper = new Swiper('#mall-banner', {
            pagination: {
                el: '.swiper-pagination'
            },
            loop:true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false
            }
        });
    },
    init:function () {
        this.swiperInit();
    }
};

var Good = {
    changeBannerNumber:function () {
        var box =$('#mall-banner'),
            myNumber = box.next();
        var myIndex = box.find('.swiper-slide-active').attr('data-index');
        myNumber.find('i').text(myIndex);
    },
    swiperInit:function () {
        var swiper = new Swiper('#mall-banner', {
            pagination: {
                el: '.swiper-pagination'
            },
            loop:true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false
            },
            on: {
                slideChangeTransitionEnd: function(){
                    Good.changeBannerNumber();
                }
            }
        });
    },
    init:function () {
        this.swiperInit();
    }
};


var Upload = {
    removeImg:function (obj) {
        $(obj).parent('.img').remove();
    },
    uploadImage:function (obj) {
        var files = obj.files;
        for(var i=0;i<files.length;i++){
            var callback = function (res) {
                var html = "<div class='img'><img src='"+res+"'> <span class='close' onclick='Upload.removeImg(this);'></span></div>";
                $(obj).parent().before(html);
            };
            var base64 = this.cutImageBase64(obj.files[i],375,.6,callback);
        }
    },
    cutImageBase64:function (m_this,wid,quality,callback) {
        var file = m_this;
        var URL = window.URL || window.webkitURL;
        var blob = URL.createObjectURL(file);
        var base64;
        var img = new Image();

        var size = file.size/1000;
        if(size>2000){
            quality = 0.6
        }else if(size<2000&&size>500){
            quality = 0.8
        }

        img.src = blob;
        img.onload = function () {
            var that = this;
            //生成比例
            var w = that.width,
                h = that.height,
                scale = w / h;
            w = wid || w;
            h = w / scale;

            //生成canvas
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            $(canvas).attr({
                width: w,
                height: h
            });
            ctx.drawImage(that, 0, 0, w, h);

            // 生成base64
            base64 = canvas.toDataURL('image/jpeg', quality || 0.6);

            callback(base64);
        }
    }
};

var Order = {
    changeActive:function (obj,id) {
        $(obj).siblings().removeClass('active');
        $(obj).addClass('active');
        $("#refundtype").val(id);
    }
};

var Qa = {
    toggleQa:function (obj) {
        var _this = $(obj);
        var h = _this.attr('data-height');
        if(_this.hasClass('active')){
            _this.animate({
                'height':.9*50+'px'
            },300).removeClass('active');
        }else{
            _this.animate({
                'height':h*50+'px'
            },300).addClass('active');

        }
    },
    init:function () {
        var list = $('.item');
        for(var i=0;i<list.length;i++){
            (function () {
                var k =i;
                var h = $(list[k]).find('.answer').outerHeight()/50;
                h = h + 0.9;
                $(list[k]).attr('data-height',h);
            })();
        }
    }
};


