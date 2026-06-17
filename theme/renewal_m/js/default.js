// MOUSE on/ff
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function MM_showHideLayers() { //v3.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
// 레이어에 사용
function Layers_findObj(n, d) { //v4.0
	var p,i,x;  if(!d) d=document; if((p=n.indexOf('?'))>0&&parent.frames.length) {
	  d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
	if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=Layers_findObj(n,d.layers[i].document);
	if(!x && document.getElementById) x=document.getElementById(n); return x;
}
function Layers_showHideLayers() { //v3.0
	var i,p,v,obj,args=Layers_showHideLayers.arguments;
	for (i=0; i<(args.length-2); i+=3) if ((obj=Layers_findObj(args[i]))!=null) { v=args[i+2];
	  if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
	  obj.visibility=v; }
}


function Layers_showHideLayersEvent2() { //v3.0
	var i,p,v,obj,args=Layers_showHideLayersEvent.arguments;
	for (i=0; i<(args.length-2); i+=3) if ((obj=Layers_findObj(args[i]))!=null) v=args[i+2];

	if(obj.style.visibility == 'hidden') {
		obj.style.visibility = 'show';
	} else {
		obj.style.visibility = 'hidden';
	}

	obj.style.visibility = v;

}

function Layers_showHideLayersEvent(_d) {
  var _x = document.getElementById(_d);
  var _plus = "/images/x_p.gif";
  var _minus = "/images/x_m.gif";

	if (_x != null){

	  _x.style.visibility=_x.style.visibility=="hidden"?"visible":"hidden";
	  _x.style.overflow=_x.style.overflow=="hidden"?"visible":"hidden";
		
		if(_x.style.visibility=="hidden") {
			if ( document.all.plusminus )
			{
				document.all.plusminus.src = _plus;
			}
		} else {
			if ( document.all.plusminus )
			{
				document.all.plusminus.src = _minus;
			}
		}

	}
}


// 윈도우 오픈
function openWin(URL,name,si)
  {
   window.open (URL,name,si);
  }


//탭액션
function tab_action(obj1, obj2) { //obj1:tab구분자, obj2:선택되는tab

		for(i=0;i<=(eval(obj1).length-1);i++){
			if(eval(obj1)[obj2-1] == eval(obj1)[i]){
				eval(obj1)[i].style.display="block";
			}
			else{
				eval(obj1)[i].style.display="none";
			}
		}
	}


//리스트&뷰_수강후기
var main_cnt =10 //주 메뉴 갯수

function showhide(num, p_totcount){ 
	for (i=1; i<=p_totcount; i++){
		menu=eval("document.all.block"+i+".style"); 
		if (num==i ) {
			if (menu.display=="block"){
				menu.display="none"; 
			}
			else{
				menu.display="block"; 
			}
		}
		else { 
			menu.display="none"; 
		}
	} 
}


//리스트&뷰_강의소개안의 수강후기
var main_cnt =5 //주 메뉴 갯수

function showhide_1(num, p_totcount){ 
	for (i=1; i<=p_totcount; i++){
		menu=eval("document.all.black_info"+i+".style"); 
		if (num==i ) {
			if (menu.display=="block"){
				menu.display="none"; 
			}
			else{
				menu.display="block"; 
			}
		}
		else { 
			menu.display="none"; 
		}
	} 
}

function showhide_2(num, p_totcount){ 
	for (i=1; i<=p_totcount; i++){
		menu=eval("document.all.black_info2"+i+".style"); 
		if (num==i ) {
			if (menu.display=="block"){
				menu.display="none"; 
			}
			else{
				menu.display="block"; 
			}
		}
		else { 
			menu.display="none"; 
		}
	} 
}


// 텍스트 스크롤
var sRepeat=null;
function doScrollerIE(dir, src, amount) {
	if (amount==null) amount=10;
	if (dir=="up")
		document.all[src].scrollTop-=amount;
	else if ( dir == "left")
	{
		document.all[src].scrollLeft-=amount;
	}else if ( dir == "right" )
	{
		document.all[src].scrollLeft+=amount;
	}else
		document.all[src].scrollTop+=amount;
	if (sRepeat==null)
		sRepeat = setInterval("doScrollerIE('" + dir + "','" + src + "'," + amount + ")",100);
	return false
}


function doScrollerIE2(dir, src, amount , interval ) {
	if (amount==null) amount=10;
	if (dir=="up")
		document.all[src].scrollTop-=amount;
	else if ( dir == "left")
	{
		document.all[src].scrollLeft-=amount;
	}else if ( dir == "right" )
	{
		document.all[src].scrollLeft+=amount;
	}else
		document.all[src].scrollTop+=amount;
	if (sRepeat==null)
		sRepeat = setInterval("doScrollerIE('" + dir + "','" + src + "'," + amount + ")",interval);
	return false
}


window.document.onmouseout = new Function("clearInterval(sRepeat);sRepeat=null");
window.document.ondragstart = new Function("return false");


// 텍스트 흐름
function start(){
 if (document.all)  toeic_main.start();
}
function stop(){
 if (document.all)  toeic_main.stop();
}


//버튼으로 업다운 제어
function PdButton() {

this.version = "0.2";
this.name = "PdButton";
this.item = new Array();
this.itemcount = 0;
this.height = 100;
this.width = 100;
this.i=0;

this.add = function () {
	var text = arguments[0];

	this.item[this.itemcount] = text;
	this.itemcount ++;
};

this.start = function (layer_name) {
	if (layer_name != null)	{
		document.getElementById(layer_name).innerHTML = this.str_ret();
	}
	else {
		this.display();
	}
};

this.display = function () {
	document.write('<div id="'+this.name+'" style="height:'+this.height+'; width:'+this.width+'; position:relative; overflow:hidden; ">');
	for(var i = 0; i < this.itemcount; i++) {
			document.write('<div id="'+this.name+'item'+i+'"style="left:'+(this.width*i)+'px; width:'+this.width+'; position:absolute; top:0px; ">');
			document.write(this.item[i]);
			document.write('</div>');
	}
	document.write('</div>');
};

this.unext = function () {
	for (i = 0; i < this.itemcount; i++) {
		obj = document.getElementById(this.name+'item'+i).style;
		if ( parseInt(obj.left) < 1 ) { 
			width = this.width + parseInt(obj.left);
			break;
		}
	}
	for (i = 0; i < this.itemcount; i++) {
		obj = document.getElementById(this.name+'item'+i).style;
		if ( parseInt(obj.left) < 1 ) { 
			obj.left = this.width * (this.itemcount-1);
		} else {
			obj.left = parseInt(obj.left) - width;
		}
	}
}

this.uprev = function () {	
	for (i = 0; i < this.itemcount; i++) {
		obj = document.getElementById(this.name+'item'+i).style;
		if ( parseInt(obj.left) < 1 ) { 
			width = parseInt(obj.left) * (-1);
			break;
		}
	}
	if ( width == 0 ) {
		total_width = this.width * (this.itemcount-1);
		for (i = 0; i < this.itemcount; i++) {
			obj = document.getElementById(this.name+'item'+i).style;
			if ( parseInt(obj.left) + 1 > total_width ) { 
				obj.left = 0;
			} else {
				obj.left = parseInt(obj.left) + this.width;
			}
		}
	} else {
		for (i = 0; i < this.itemcount; i++) {
			obj = document.getElementById(this.name+'item'+i).style;
			if ( parseInt(obj.left) < 1 ) { 
				obj.left = 0;
			} else {
				obj.left = parseInt(obj.left) + width;
			}
		}
	}
}

}

//현재진행중인 e4u 마스터수강증 
function Click()
  {
	var targetid, srcElement, targetElement;
	srcElement= window.event.srcElement;

	if(srcElement.className=="menu")
	{
	   targetid= "menu_" + srcElement.id;
	   targetElement = document.all(targetid);
   
	   if(targetElement.style.display=="none")
	   {
		 targetElement.style.display="";
	   }
	   else
	   {
		 targetElement.style.display="none";
	   }
	}
  }



String.prototype.trim = function(){ return this.replace(/(^\s*)|(\s*$)/g, "");} 
String.prototype.stripHTML = function(){ return this.replace( /[<][^>]*[>]/gi, "");} 

function fnchangeImage(filename) { MainTitleImg.src = filename; }


function num_check() {
	//숫자만 입력받기.
	if (navigator.userAgent.indexOf("MSIE") != -1) {
		var keyCode = window.event.keyCode;
		if ( (keyCode < 48) || (keyCode > 57) ){
			event.returnValue=false;
		}
	}
	return;
}
function auto_comma(val) {
	if (navigator.userAgent.indexOf("MSIE") != -1) {
		var keyCode = window.event.keyCode;
		if ( ((keyCode>=48) && (keyCode <= 105)) || (keyCode==8) || (keyCode==13) || (keyCode==35) || (keyCode==46) ) {
			//0(48)~숫자키패드9(105), enter(13), bakspace(8), delete(46), end(35) key 일 때만 처리한다.
			var str = "" + get_number(val.value); //숫자만 가져온다
			if ( (str != null) && (str != "") && (str != "0") ) {
				val.value = add_comma(str); //콤마삽입
			} else {
				val.value = "0";
			}
		}
	}
	return;
}
function add_comma(val) {
	var num = val.toString();
	if(num.length <= 3) return num;
	var loop = Math.ceil(num.length / 3);
	var offset = num.length % 3;
	if(offset==0) offset = 3;
	var str = num.substring(0, offset);
	for(i=1;i<loop;i++) {
		str += "," + num.substring(offset, offset+3);
		offset += 3;
	}
	return str;
}
function get_number(val) {
	var str = ""+val;
	var temp = "";
	var num = "";
	for(var i=0; i<str.length; i++) {
		temp = str.charAt(i);
		if (temp >= "0" && temp <= "9") {
			num += temp;
		}
	}
	if ( (num != null) && (num != "") && (num != "0") ) {
		return parseInt(num,10); //십진수로 변환하여 리턴
	} else {
		return "0";
	}
} 


function getCookie(name)  
{  
		var nameOfCookie = name + "=";  
		var x = 0;  
		while ( x <= document.cookie.length )  
		{  
				var y = (x+nameOfCookie.length);  
				if ( document.cookie.substring( x, y ) == nameOfCookie ) {  
						if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 )
  
								endOfCookie = document.cookie.length;  
						return unescape( document.cookie.substring( y, endOfCookie )
 );  
				}  
				x = document.cookie.indexOf( " ", x ) + 1;  
				if ( x == 0 )  
						break;  
		}  
		return "";  
}

// #### iFrame 사이즈 가변적으로 변경처리하는 코드 (정효태, 2007.05.04) ####
function resize_iFrame(obj, minHeight) {
 minHeight = minHeight || 10;

 try {
	var getHeightByElement = function(body) {
	 var last = body.lastChild;
	 try {
		while (last && last.nodeType != 1 || !last.offsetTop) last = last.previousSibling;
		return last.offsetTop+last.offsetHeight;
	 } catch(e) {
		return 0;
	 }			 
	}
		
	var doc = obj.contentDocument || obj.contentWindow.document;
	if (doc.location.href == 'about:blank') {
	 obj.style.height = minHeight+'px';
	 return;
	}
	
	//var h = Math.max(doc.body.scrollHeight,getHeightByElement(doc.body));
	//var h = doc.body.scrollHeight;
	if (/MSIE/.test(navigator.userAgent)) {
	 var h = doc.body.scrollHeight;
	} else {
	 var s = doc.body.appendChild(document.createElement('DIV'))
	 s.style.clear = 'both';

	 var h = s.offsetTop;
	 s.parentNode.removeChild(s);
	}
	
	//if (/MSIE/.test(navigator.userAgent)) h += doc.body.offsetHeight - doc.body.clientHeight;
	if (h < minHeight) h = minHeight;
 
	obj.style.height = h + 'px';
	if (typeof resizeIfr.check == 'undefined') resizeIfr.check = 0;
	if (typeof obj._check == 'undefined') obj._check = 0;

//  if (obj._check < 5) {
//   obj._check++;
	 setTimeout(function(){ resizeIfr(obj,minHeight) }, 200); // check 5 times for IE bug
//  } else {
	 //obj._check = 0;
//  } 
 } catch (e) { 
	//alert(e);
 } 
}



function fnTailKeyWord(section_index, Cate_num) {
	location.href="/product/list.asp?Cate_num="+Cate_num+"&Section_index="+section_index;

}

function fnTailKeyWord_phone(section_index, Cate_num) {
	location.href="/product/list_phone.asp?Cate_num="+Cate_num+"&Section_index="+section_index;

}


function iframeSize(frame_name) 
{
	var dbody = document.body;
	var body_height = dbody.scrollHeight + (dbody.offsetHeight-dbody.clientHeight);
	var body_width = dbody.scrollWidth + (dbody.offsetWidth-dbody.clientWidth);


	if (parent.document)
	{
		if ( parent.document.all(frame_name) )
		{

			var p_height = parent.document.all(frame_name).style.height.replace(/px/g,"") ==  "" ? 0 : parseInt(parent.document.all(frame_name).style.height.replace(/px/g,""))
			if (dbody.scrollHeight == p_height) 
			{
				return false;
			}
			setTimeout("iframeSize('"+frame_name+"')", 300);

			parent.document.all(frame_name).style.height = p_height < body_height ? body_height : p_height;
		}
	}

}

function iframeSize2() 
{
	var frame_name = window.name
	if (window.name!="")
	{
		var dbody = document.body;
		var body_height = dbody.scrollHeight + (dbody.offsetHeight-dbody.clientHeight);
		var body_width = dbody.scrollWidth + (dbody.offsetWidth-dbody.clientWidth);

		
		if (parent.document)
		{
			if ( parent.document.all(frame_name) )
			{

				var p_height = parent.document.all(frame_name).style.height.replace(/px/g,"") ==  "" ? 0 : parseInt(parent.document.all(frame_name).style.height.replace(/px/g,""))
				if (dbody.scrollHeight == p_height) 
				{
					return false;
				}
				setTimeout("iframeSize('"+frame_name+"')", 300);

				parent.document.all(frame_name).style.height = body_height;
			}
		}
/*
		if (parent.name!="")
		{
			if (parent.iframeSize2)
			{
				parent.iframeSize2();
			}
		}
*/

	}

}