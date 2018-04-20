var WAQOO={};
String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g,"")};
WAQOO.$=function(s){return(typeof s=='object')?s:document.getElementById(s)};
WAQOO.PosOffset=function(ele){var x=y=0,w=ele.offsetWidth,h=ele.offsetHeight;do{y+=ele.offsetTop||0;x+=ele.offsetLeft||0;ele=ele.offsetParent}while(ele);return {left:x,top:y,width:w,height:h}};
function SuggestSearch(searchInputobj){
	if(!WAQOO.$(searchInputobj)) return;
	var sugg = document.createElement("div");
		sugg.id="suggview";
		sugg.className='suggview';
		sugg.style.display="none";
		sugg.innerHTML='<div id="suggdata"></div><div id="subbenddiv"><span id="sugginfo"></span><span id="suggclose"></span></div>';
		document.body.appendChild(sugg);
	
var WAQ={
	inputObj:null,
	index:-1,
	Data:new Array,
	items:new Array,
	ResultCall:function(jsonData){
			if(jsonData.result==0 || jsonData.info.length==0){return;}
			this.Data = jsonData.info;
			this.index = -1;
			var d=jsonData.info;
			var resulit = '<ul>';
        if (jsonData.type == 1)
            for (var i = 0; i < d.length; i++) { resulit += '<li id="dli' + i + '"><span>' + d[i].name + '</span><em>￥' + d[i].price + '</em></li>'; }
        if (jsonData.type == 2)//显示个人搜索记录
            for (var i = 0; i < d.length; i++) { resulit += '<li id="dli' + i + '"><span>' + d[i].name + '</span><span index="'+i+'" otherid="' + d[i].Id+ '" style="display:none;float:right;">删除</span></li>'; }
			resulit+='</ul>';										
			WAQOO.$("suggdata").innerHTML = resulit;
			WAQOO.$("sugginfo").innerHTML = jsonData.info;
			this.items=WAQOO.$("suggdata").getElementsByTagName("li");
			this.Align();
			this.Show();
			this.Select();
		},
	
	Select:function(){
		var li = this.items;
		for (var n = 0; n < li.length; n++) {
		        li[n].index = n;
				li[n].onmouseover = function () {
				    this.className = 'move';
				    this.children[1].style.display = 'block';
				};
				li[n].onmouseout = function () {
				    this.className = '';
				    this.children[1].style.display = 'none';
				}
				li[n].onclick = function () { WAQ.SetKey(this.index); WAQ.subForm(); }
				li[n].children[1].onclick = function () {
				    var id = this.attributes.otherid.value;
				    var span_index = this.attributes.index.value;
				    $.post('/ajax.aspx?act=searchdel', { 'id': id },
                        function (data) {
                            switch (data.status) {
                                case 1: li[span_index].style.display = 'none'; break;
                                default: break;
                            }
                        }, 'json');
				    event.stopPropagation();
				};
		}
	},

	setCss:function(ind,n){
			var li = this.items;
			for(i=0;i<li.length;i++){li[i].className="";}
			li[ind].className="move";
		},
		
	Keyboard:function(k){
		if(this.items.length==0){return;}
		k=k||event; k=k.keyCode;
		if(k==38||k==40){
			var index = this.index;			
			if(k==38){index--;if(index<0){index=this.items.length-1}}
			if(k==40){index++;if(index>=this.items.length){index=0}}			
			this.index = index;
			this.setCss(index);
			this.SetKey(index);	
		};		
		if(k==13){this.SetKey(this.index);this.subForm()};
	},
	
	Align:function(){
		var	POS=WAQOO.PosOffset(WAQOO.$(this.inputObj));
		var suggview = WAQOO.$("suggview")
			suggview.style.left=POS.left+'px';
			suggview.style.top=(POS.top+POS.height-1)+'px';
			suggview.style.width=(POS.width-2)+'px';
		},
	
	SetKey: function (index) { this.inputObj.value = this.Data[index].name; $(this.inputObj).next().val(this.Data[index].productId);},
	Hide:function(){WAQOO.$("suggview").style.display='none';},
	Show:function(){WAQOO.$("suggview").style.display='block';document.onclick=function(){WAQ.Hide()}},
	subForm:function(){this.Hide(); this.inputObj.form.submit();},
	ResultSearch:function(ajax){WAQ.ResultCall(ajax)}
}

function checkSpecialCharacter(str)          
{      
    var SPECIAL_STR ="`~!@#$%^&*()-+=[]{}'\"<>?\/\\:;";   
    for(i=0;i<str.length;i++){ 
    if (SPECIAL_STR.indexOf(str.charAt(i)) !=-1) {   
		str=str.replace(str.charAt(i),'');
    }}
    return str;
}

WAQOO.$(searchInputobj).onkeyup=function(e){
	var keywd=checkSpecialCharacter(this.value.trim());
    if(keywd==null){return ;}
	//if (keywd == '' || keywd == null || keywd.length < 1) { return; }
	WAQ.inputObj = this; e=e||event; var k=e.keyCode;
	if(k==37||k==38||k==39||k==40||k==13){}
	else{
	    $.post('/ajax.aspx?act=ajaxsearch', { 'w': keywd }, WAQ.ResultSearch, 'json');
	}	
};
WAQOO.$(searchInputobj).onkeydown=function(e){WAQ.Keyboard(e);}
WAQOO.$(searchInputobj).onclick = WAQOO.$(searchInputobj).onkeyup;

}

