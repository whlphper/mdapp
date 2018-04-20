/* stats >> 0:结束，1:等待，2:进行中，3:数量不足 */
(function ($) {
    $.fn.timePlay = function (options) {
        var defVal = {
            stats: -1,
            time: 100,
            sAttr: 'stime',
            eAttr: 'etime',
            etpl: '<span>距离时间</span>&nbsp;<b>%D%</b>天<b>%H%</b>小时<b>%M%</b>分<b>%S%</b>秒',
            //stpl: '<span>剩余时间</span>&nbsp;<b>%D%</b>天<b>%H%</b>小时<b>%M%</b>分<b>%S%</b>秒',
            stpl: '<em>%D%</em> 天 <em>%H%</em> 时 <em>%M%</em> 分 <em>%S%</em> 秒',
            otpl: '<span>活动已经结束</span>',
            callFun: function () { }
        };

        var s = $.extend(defVal, options);
        var vthis = $(this);

        vthis.each(function () {
            var the = $(this);
            var stime = parseInt(the.attr(s.sAttr)) * 1000;
            var etime = parseInt(the.attr(s.eAttr)) * 1000;
            var showTime = function (stats, showTpl) {
                var nowTime = new Date().getTime();
                if (etime >= nowTime) {
                    the.second = etime - nowTime;
                }
                var totalSecond = the.second -= s.time;
                totalSecond = totalSecond / 1000;
                var D = Math.floor(totalSecond / (24 * 3600));
                var H = Math.floor((totalSecond / 3600) % 24);
                var M = Math.floor((totalSecond / 60) % 60);
                var S = (the.second % 60000 * 0.001).toFixed(1);
                if (s.time == 1000) { S = Math.floor(S) };
                if (S < 10) { S = '0' + S; }

                the.html(showTpl.replace(/%D%/, D < 10 ? "0" + "" + D + "" : D).replace(/%H%/, H < 10 ? "0" + "" + H + "" : H).replace(/%M%/, M < 10 ? "0" + "" + M + "" : M).replace(/%S%/,S));
                if (stats == 0) {
                    return;
                } else if (stats == 1 && totalSecond <= 0) {
                    stats = 2;
                    the.second = etime;
                    showTpl = s.stpl;
                } else if ((stats == 3 || stats == 2) && totalSecond <= 0) {
                    stats = 0;
                    the.second = 0
                    showTpl = s.otpl;
                };

                s.callFun(stats, the.second);
                setTimeout(function () { showTime(stats, showTpl) }, s.time);
            };
            the.second = 0;
            if (stime > 0) {
                the.second = stime;
                showTime(1, s.etpl);
            } else if (etime > 0) {
                the.second = etime;
                showTime(s.stats, s.stpl);
            } else {
                showTime(0, s.otpl);
            }
        });
    }
})(jQuery);



