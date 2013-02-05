/**
* 获取对象坐标
* @param {Object} obj
* @return 坐标数组，["top","left","width","height"]
* @type Array
*/

function getPosition(obj) {
    var top = 0;
    var left = 0;
    var width = obj.offsetWidth;
    var height = obj.offsetHeight;
    while (obj.offsetParent) {
        top += obj.offsetTop;
        left += obj.offsetLeft;
        obj = obj.offsetParent;
    }

    return {"top":top,"left":left,"width":width,"height":height};
}

//交换两个结点的位置: add by pluto
function swapNode(node1,node2)
{
	//获取父结点
	var _parent=node1.parentNode;
	//获取两个结点的相对位置
	var _t1=node1.nextSibling;
	var _t2=node2.nextSibling;
	//将node2插入到原来node1的位置
	if(_t1)_parent.insertBefore(node2,_t1);
	else _parent.appendChild(node2);
	//将node1插入到原来node2的位置
	if(_t2)_parent.insertBefore(node1,_t2);
	else _parent.appendChild(node1);
	
	return;
}



//检查一个字段是否为空
function checkEmpty(obj,msg,minlen,maxlen){
	//alert("checkEmpty " + obj.value + "  " + msg);
	if(obj.value=="" || obj.value==" "){
		alert("请输入【" + msg +"】");
		obj.select();
		obj.focus();
		return false;
	}
	if(arguments.length>1 && obj.value.length<minlen){
		alert(msg+"：长度不能小于"+minlen)
		obj.select();
		obj.focus();
		return false;
	}
	if(arguments.length>2 && obj.value.length>maxlen){
		alert(msg+"：长度不能大于"+maxlen)
		obj.select();
		obj.focus();
		return false;
	}
	return true;
}

//检查一个字段是否为空
function checkEmpty2(obj,msg,minlen,maxlen){
	//alert("checkEmpty " + obj.value + "  " + msg);
	if(obj.value=="" || obj.value==" "){
		alert("请输入【" + msg +"】");
		//obj.select();
		//obj.focus();
		return false;
	}
	if(arguments.length>1 && obj.value.length<minlen){
		alert(msg+"：长度不能小于"+minlen)
		//obj.select();
		//obj.focus();
		return false;
	}
	if(arguments.length>2 && obj.value.length>maxlen){
		alert(msg+"：长度不能大于"+maxlen)
		//obj.select();
		//obj.focus();
		return false;
	}
	return true;
}

//检查一个字符串是否是一个有效的日期
function checkDate(obj, msg, minv, maxv){
	if (obj.value=="" || obj.value==" "){
		alert("请输入【"+msg+"】");
		doDateSelect2(obj);
		obj.focus();
		return false;
	}

	if (!isDateString(obj.value)){
		alert("请输入【"+msg+"】\r\n"+obj.value+" 不是有效的日期或者格式不正确\r\n正确的格式是yyyy-mm-dd或者yyyy/mm/dd");
		doDateSelect2(obj);
		obj.focus();
		return false;
	}
	//alert(msg + "\r\n" + minv + "\r\n" + maxv);
	if(arguments.length>=3 && compareDate(minv, obj.value)){
		alert("请输入【"+msg+"】\r\n"+"日期不能小于 " + minv);
		doDateSelect2(obj);
		obj.focus();
		return false;
	}
	if(arguments.length>=4 && compareDate(obj.value, maxv)){
		alert("请输入【"+msg+"】\r\n"+"日期不能大于 " + maxv);
		doDateSelect2(obj);
		obj.focus();
		return false;
	}
	return true;
}

//检验一个数字是否在指定的范围之内，范围可以不指定，也可以只指定最小值
function checkNumber(obj,msg,minv,maxv){
	//alert("checkNumber  " + obj.value + "  " + msg);
	if(checkEmpty(obj,msg)){
		v = parseFloat(obj.value);
		if(!isFinite(v)){
			alert(msg + " 需要输入有效的数字");
			obj.select();
			obj.focus();
			return false;
		}
		if(arguments.length>2 && v<minv){
			alert(msg + " 不能够小于"+minv+",请重新输入");
			obj.select();
			obj.focus();
			return false;
		}
		if(arguments.length>3 && v>maxv){
			alert(msg + " 不能够大于"+maxv+",请重新输入");
			obj.select();
			obj.focus();
			return false;
		}
		return true;
	}
	return false;
}

function checkNumber3(obj,msg,minv,maxv){
	//alert("checkNumber  " + obj.value + "  " + msg);
	if(checkEmpty2(obj,msg)){
		v = parseFloat(obj.value);
		if(!isFinite(v)){
			alert(msg + " 需要输入有效的数字");
			//obj.select();
			//obj.focus();
			return false;
		}
		if(arguments.length>2 && v<minv){
			alert(msg + " 不能够小于"+minv+",请重新输入");
			//obj.select();
			//obj.focus();
			return false;
		}
		if(arguments.length>3 && v>maxv){
			alert(msg + " 不能够大于"+maxv+",请重新输入");
			//obj.select();
			//obj.focus();
			return false;
		}
		return true;
	}
	return false;
}

//检验一个所输入的字符是否是数字,数字带小数点
function checkNumber2(obj,msg,minv,maxv){
	
	if(obj.value!=""){
		v = parseFloat(obj.value);		
		
		if(!isFinite(v) || !isNumber(obj,msg)){
			alert(msg + " 需要输入有效的数字");
			obj.select();
			obj.focus();
			return false;
		}
		if(arguments.length>2 && v<minv){
			alert(msg + " 不能够小于"+minv+",请重新输入");
			obj.select();
			obj.focus();
			return false;
		}
		if(arguments.length>3 && v>maxv){
			alert(msg + " 不能够大于"+maxv+",请重新输入");
			obj.select();
			obj.focus();
			return false;
		}
		return true;
	}else{
	   obj.value = 0;
        }
	return true;	
}

//检验一个所输入的字符是否是整数,整数不能以0开头
function checkInteger(obj,msg,minv,maxv){
	
	if(obj.value!=""){
		v = parseFloat(obj.value);		
		if(!isFinite(v) || !isInteger(obj,msg)){
			alert(msg + " 需要输入有效的整数");
			obj.select();
			obj.focus();
			return false;
		}
		if(arguments.length>2 && v<minv){
			alert(msg + " 不能够小于"+minv+",请重新输入");
			obj.select();
			obj.focus();
			return false;
		}
		if(arguments.length>3 && v>maxv){
			alert(msg + " 不能够大于"+maxv+",请重新输入");
			obj.select();
			obj.focus();
			return false;
		}
		return true;
	}else{
	    obj.value = 0;
        }
	return true;	
}

//检查一个选择是否为空
function checkSelectEmpty(obj,msg){
	//alert("checkEmpty " + obj.value + "  " + msg);
	if(obj.value=="" || obj.value==" "){
		alert("请选择【" + msg +"】");		
		return false;
	}	
	return true;
}

// 阻止事件冒泡
function cancel_bubble(e)
{
	//如果提供了事件对象，则这是一个非IE浏览器
	if ( e && e.stopPropagation )
	{
		//因此它支持W3C的stopPropagation()方法
		e.stopPropagation();
	} 
	else
	{
		//否则，我们需要使用IE的方式来取消事件冒泡 
		window.event.cancelBubble = true;
		return false;
	}
}


/** 样式相关的函数  **/
function mouseover(obj)
{
	var trObj = document.getElementById(obj.id);
	if(trObj!=null)
	trObj.className = "mouseover";
}

function mouseout(obj, css)
{
	var trObj = document.getElementById(obj.id);
	if(trObj!=null)
	trObj.className = css;
}

function th_over(id)
{
	var tspan = document.getElementById("func_col_"+id);
	if(tspan!=null)
	tspan.style.display = "inline-block";
	
	var tobj = document.getElementById("th_"+id);
	if(tobj!=null)
	tobj.className = "onhover";
}

function th_out(id)
{
	var tspan = document.getElementById("func_col_"+id);
	if(tspan!=null)
	tspan.style.display = "none";
	
	var tobj = document.getElementById("th_"+id);
	if(tobj!=null)
	tobj.className = "default";
}

function showChart(id)
{
	$('#chart').css('display','');
	
	var tarObj = document.getElementById("ChartP_"+id);
	if ( tarObj )
	{
		if ( tarObj.style.display == 'none' )
		{
			tarObj.style.display = 'block';
		}
		else
		{
			tarObj.style.display = 'none';
		}
	}
}

function outSelect( )
{
	var divObj  = document.getElementById( 'filterDiv' );
    divObj.onmousedown = function(){drag(this, document.getElementById('filterDiv_iframe'), event);};
    
    return;
}

function inSelect()
{
	var divObj  = document.getElementById( 'filterDiv' );
    divObj.onmousedown = "";
    
    return;
}

function hidenFilterDiv()
{ 
	if (event.srcElement.tagName == 'DIV' || event.srcElement.tagName == 'SELECT' || event.srcElement.tagName == 'SPAN')
	{
		return;
	}
	else
	{
		var filDiv = document.getElementById('filterDiv');
		filDiv.style.display = 'none';
	}
	
	return;
}

function goThisDay ( deltaDays )
{
	var Form = document.getElementById('form1');
	var timeObj = document.getElementById('stattime');
	var strTime = timeObj.value;
	
	var strTimeArr = strTime.split('-');
	var strY = strTimeArr[0];
	var strM = strTimeArr[1];
	var strD = strTimeArr[2];
	
	var theTime = new Date(Date.UTC(strY, strM-1, strD) + (deltaDays*86400*1000));
	
	var theDate = '';
	theY = theTime.getFullYear() + "-";
	
	theM = theTime.getMonth() + 1;
	if ( theM < 10 )
	{
		theM = '0' + theM;
	}
	theM += "-"; 
	
   	theDay = theTime.getDate();  // 获取日。
   	if ( theDay < 10 )
	{
		theDay = '0' + theDay;
	}
	theDate = theY + theM + theDay;
   	
	timeObj.value = theDate ;
	Form.submit();
}

function showDataFilterDiv( oid, dimId )
{
	var opDIV = document.getElementById(oid);
	
	var e = window.event;
    
    var objSrc = e.srcElement;//event source element
    
    var left = 0;
    var top  = 0;

    while (objSrc.offsetParent)
    {
        left += objSrc.offsetLeft;
        top  += objSrc.offsetTop;
        objSrc = objSrc.offsetParent;
    }

    left += objSrc.offsetLeft;//source element's offsetTop
    top  += objSrc.offsetTop;//source element's offsetLeft
    
    mouseOffsetX = left + e.offsetX;
    mouseOffsetY = top + e.offsetY;
    
    opDIV.style.top = mouseOffsetY;
	opDIV.style.left =  mouseOffsetX + 4;
	
	if ( opDIV.style.display != 'none' )
	{
		opDIV.style.display = 'none';
	}
	else
	{
		opDIV.style.display = 'block';
	}
	
	var opTable = document.getElementById("filterTable");
	for (var i = 0; i < opTable.rows.length;i++)
	{
		opTable.rows[i].style.display = 'none';
	}
	
	var opDIM = document.getElementById("tr" + dimId);
	opDIM.style.display = 'block';
	
	return;
}

/*合并多列相同行的表格
tableID: 要合并的表格的id
参数colNum:要合并的列号集如：0;1  
num: 从第几行开始合并
*/
function doRowSpanTable(tableID,colNum,num)
{
	colNum = colNum == null ? 0 : colNum;
	num = num == null ? 0 : num;
	var tab=document.getElementById(tableID);
	var colNumArray = colNum.split(";");
	for( i = tab.rows.length-1-num;i > 0; i-- )
	{
	  for( j = colNumArray.length-1; j >= 0; j-- )
	  {    
	    if( tab.rows[i].cells[colNumArray[j]] && ( tab.rows[i].cells[colNumArray[j]].innerText == tab.rows[i-1].cells[colNumArray[j]].innerText ) && ((j==0) || (tab.rows[i].cells[colNumArray[j-1]].innerText == tab.rows[i-1].cells[colNumArray[j-1]].innerText )) && ( tab.rows[i].cells[colNumArray[0]].innerText == tab.rows[i-1].cells[colNumArray[0]].innerText ) )
	    {
		  	
	      tab.rows[i-1].cells[colNumArray[j]].rowSpan = tab.rows[i].cells[colNumArray[j]].rowSpan+1;
	      tab.rows[i].deleteCell(colNumArray[j]);
	    }
	   }
	 }
}

function calculateMainPosition()
{
	var h=$(".filter").eq(0).height()+10;//获取数组中第一个.filter过滤器高度+10空隙
	$(".table_wrap").css("padding-top",h);//表格上边距腾出过滤器高度
	/*
	var isIE6 = $.browser.msie && (parseInt($.browser.version) == 6);//jquery判断ie6
	if(isIE6){
		$(window).scroll(function(){//针对IE6模拟fixed效果
			var t=setTimeout("$('.filter').css('top',$(document).scrollTop());",200)
		}); 
	}
	*/
	$(".table_wrap tr").hover(
		function(){
			$(this).addClass("hover");
		},function(){
			$(this).removeClass("hover");
	});
	
}

/** end of 样式相关的函数  **/

/** 数据处理相关的函数 **/
function getExeclfile( url,fileName,tableHId,tableBId,tarDoc)
{
	if ( arguments.length < 4 )
	{
		tableHId = "table_header";
		tableBId = "table_record";
		tarDoc = document;
	}
	else if ( arguments.length < 5 )
	{
		tarDoc = document;
	}
	
	fileName = fileName == null ? "统计分析数据" : fileName;
	
	var baseUrl = "http://"+window.location.hostname+"/infocenter/server/table2excel.php";
	var formObj = tarDoc.createElement("form");
   	tarDoc.body.appendChild(formObj);
	
	if ( url )
	{
		formObj.method = "POST"; 
	   	formObj.action = baseUrl + "?nocheck=yes&filename=" + fileName + "&___url=" + url;
   		formObj.submit();
   		
   		return;
	}
	
	var tableheaderObj = tarDoc.getElementById( tableHId );	
	var tablerecordObj = tarDoc.getElementById( tableBId );  
	
	// 表头数据
	var trArr1 = tableheaderObj.all.tags("tr");
	var headerObj = new Array();
	
	for(i=0;i<trArr1.length;i++)
	{
	       var tmpthArr = trArr1[i].all.tags("th");
	       headerObj[i] = new Array();
	       for(j=0;j<tmpthArr.length;j++)
	       {
	                var thObj = {
                        "colSpan":tmpthArr[j].colSpan,
                        "rowSpan":tmpthArr[j].rowSpan,
                        "title":tmpthArr[j].innerText
                   }
                    headerObj[i][j] = thObj ;
	       }	      
	}
	
    // 记录数据
    var trArr2 = tablerecordObj.all.tags("tr");
    var recordObj = new Array();
    
    // 若有客户端添加的合计行,则减去
    var totalLength = trArr2.length;
    if ( tarDoc.getElementById('trClSum') != null )
    {
    	totalLength -= 1;
    	
    	if ( tarDoc.getElementById('trClAvg') != null )
    	{
    		totalLength -= 1;
    	}
    }
   
    for(i=0;i<totalLength;i++)
    {
           var tmptdArr = trArr2[i].all.tags("td");
           recordObj[i] = new Array();
           for(j=0;j<tmptdArr.length;j++)
           {
                    var tdObj = {
                        "colSpan":tmptdArr[j].colSpan,
                        "rowSpan":tmptdArr[j].rowSpan,
                        "value":tmptdArr[j].innerText
                   }
                    recordObj[i][j] = tdObj ;
           } 
    }
   
   // create form with two hiden input 
   var data = tarDoc.createElement("input");
   data.type = "hidden";
   formObj.appendChild(data);
   data.value = $.toJSON(headerObj);
   data.name = "header"; 
   
   var recFormObj = tarDoc.createElement("input");
   recFormObj.type = "hidden";
   formObj.appendChild(recFormObj);
   recFormObj.value = $.toJSON(recordObj);
   recFormObj.name = "record"; 
   
   formObj.method = "POST"; 
   formObj.action = baseUrl + "?nocheck=no&filename=" + fileName;
   formObj.submit();
   
   return;
}

// 上报用户访问数据(前置条件是载入了jquery)
function reportPageFinish()
{
	try{
		$(document).ready(function(){
			if ( typeof(top.window) == 'object' && top.window.indexObj )
			{
				//上报数据
				if ( top.window.indexObj.__viewBeginTime )
				{
					var timePassed = (new Date()).getTime() - top.window.indexObj.__viewBeginTime;
				}
				else
				{
					var timePassed = 0;
				}
				var menuId = top.window.indexObj.currentMenuId; 
				
				if ( menuId > 0 )
				{
					var rptUrl = "/infocenter/tool/userClickReport.php?_menuId=" + menuId + "&_timePassed=" + timePassed;
					var imgSendObj = new Image();
		 			imgSendObj.src = rptUrl;
				}
				
				// 重置开始时间:用户刷新或查询时现在记录不了开始时间
				top.window.indexObj.__viewBeginTime = null;
			}
		});
	}
	catch (e)
	{
		// do nothing
	}
}

// 日期快速操作

// timeType: 1 日, 3 月, 6 年
// direct: 1 上翻, 2 下翻
function changeDateUnit(timeType, direct) // 上/下翻
{
	if ( arguments.length != 2 )
	{
		return;
	}
	
	var dateUnit = document.getElementById("sDateUnit").value;
	var theInputWrap = null;
	if ( timeType == 3 )
	{
		theInputWrap = $(".DateMonthPicker");
	}
	else
	{
		theInputWrap = $(".DatePicker");
	}
	
	var i = 0;
	if ( theInputWrap && theInputWrap.length > 0 )
	{
		if ( timeType == 1 && theInputWrap.length == 2 && dateUnit == 30 ) // 按日 且 按月 特别处理
		{
			var begDate = theInputWrap[0].value;
			var dateArr = begDate.split("-");
			var theNumMonth = direct == 1 ? (parseInt(dateArr[1],10) - 1) : (parseInt(dateArr[1],10) + 1);
			var theDayObj = new Date(dateArr[0],theNumMonth,0);
			
			var theNumMonth = theDayObj.getMonth() + 1;
			var strMonth = theNumMonth < 10 ? "0" + theNumMonth : theNumMonth;
			var strYear = theDayObj.getFullYear();
			
			theInputWrap[0].value = strYear + "-" + strMonth + "-" + "01";
			theInputWrap[1].value = strYear + "-" + strMonth + "-" + theDayObj.getDate();
		}
		else if ( timeType == 1 && theInputWrap.length == 2 && (dateUnit == 1 || dateUnit == 7 || dateUnit == 14) ) // 按日 且 按7or14天 也特别处理
		{
			var begDate = theInputWrap[0].value;
			var dateArr = begDate.split("-");
			var theOralDay = dateArr[2] ? parseInt(dateArr[2],10) : 1;
			
			var deltaDays = dateUnit - 1 ? (dateUnit - 1) : 1;
			if ( direct == 1 )
			{
				var theBegDayObj = new Date(Date.UTC(dateArr[0],(parseInt(dateArr[1],10) - 1), theOralDay) - (deltaDays*86400*1000));
				
				var theNumMonth = theBegDayObj.getMonth() + 1;
				var strMonth = theNumMonth < 10 ? "0" + theNumMonth : theNumMonth;
				var theNumDay = theBegDayObj.getDate();
				var strDay = theNumDay < 10 ? "0" + theNumDay : theNumDay;
				theInputWrap[0].value = theBegDayObj.getFullYear() + "-" + strMonth + "-" + strDay;
				theInputWrap[1].value = deltaDays == 1 ? theInputWrap[0].value : begDate;
			}
			else
			{
				var theBegDayObj = new Date(Date.UTC(dateArr[0],(parseInt(dateArr[1],10) - 1), theOralDay) + (deltaDays*86400*1000));	
				var theNumMonth = theBegDayObj.getMonth() + 1;
				var strMonth = theNumMonth < 10 ? "0" + theNumMonth : theNumMonth;
				var theNumDay = theBegDayObj.getDate();
				var strDay = theNumDay < 10 ? "0" + theNumDay : theNumDay;
				theInputWrap[0].value = theBegDayObj.getFullYear() + "-" + strMonth + "-" + strDay;
				
				if ( deltaDays == 1 )
				{
					theInputWrap[1].value = theInputWrap[0].value;
				}
				else
				{
					var theEndDayObj = new Date(Date.UTC(dateArr[0],(parseInt(dateArr[1],10) - 1), theOralDay) + (2*deltaDays*86400*1000));	
					theNumMonth = theEndDayObj.getMonth() + 1;
					strMonth = theNumMonth < 10 ? "0" + theNumMonth : theNumMonth;
					theNumDay = theEndDayObj.getDate();
					strDay = theNumDay < 10 ? "0" + theNumDay : theNumDay;
					theInputWrap[1].value = theEndDayObj.getFullYear() + "-" + strMonth + "-" + strDay;
				}
			}
		}
		else
		{
			for(i = 0; i < theInputWrap.length ;i++)
			{
				var thisInputObj = theInputWrap[i];
				var newStrDate = __changeDateUnit(thisInputObj.value,timeType,dateUnit,direct);
				if ( newStrDate )
				{
					thisInputObj.value = newStrDate;
				}
			}
		}
	}
	
	return;
}             

/* strDate 字符串形式的日期(用-分割)
   timeType: 1 日, 3 月, 6 年
   dateUnit: int 时间变动量
   direct: 1 上翻(-), 2 下翻(+)
*/
function __changeDateUnit(strDate,timeType,dateUnit, direct)
{
	var dateArr = strDate.split("-");
	if ( dateArr.length < 2 )
	{
		return;
	}
	
	var theOralDay = dateArr[2] ? parseInt(dateArr[2],10) : 1;
	var theDayObj = new Date(dateArr[0],(parseInt(dateArr[1],10) - 1),theOralDay);
	
	dateUnit = parseInt(dateUnit,10);
	if ( timeType == 3 )
	{
		var theMonth = parseInt(dateArr[1],10) - 1;

		if ( direct == 1 )
		{
			theMonth -= dateUnit;
		}
		else
		{
			theMonth += dateUnit;
		}
		
		theDayObj.setMonth(theMonth);
	}
	else if ( timeType == 6 )
	{
		var theYear = theDayObj.getFullYear();
		if ( direct == 1 )
		{
			theYear -= dateUnit;
		}
		else
		{
			theYear += dateUnit;
		}
		theDayObj.setFullYear(theYear);
	}
	else
	{
		if ( direct == 1 )
		{
			var theDayObj = new Date(Date.UTC(dateArr[0],(parseInt(dateArr[1],10) - 1), theOralDay) - (dateUnit*86400*1000));
		}
		else
		{
			var theDayObj = new Date(Date.UTC(dateArr[0],(parseInt(dateArr[1],10) - 1), theOralDay) + (dateUnit*86400*1000));	
		}
	}
	
	var newStrDayArr = new Array();
	newStrDayArr.push(theDayObj.getFullYear());
	
	var theNumMonth = theDayObj.getMonth() + 1;
	var strMonth = theNumMonth < 10 ? "0" + theNumMonth : theNumMonth;
	newStrDayArr.push(strMonth);
	
	if ( dateArr.length > 2 )
	{
		var theNumDay = theDayObj.getDate();
		var strDay = theNumDay < 10 ? "0" + theNumDay : theNumDay;
		newStrDayArr.push(strDay);
	}
	
	return newStrDayArr.join("-");
}

/****************IIIL中特有的js全局函数*****************************************/

var getArgs = function (list) {
    var lists = list.split(','), args = {};
    for (var key = 0; key < lists.length; key++) {
        var arr = $n(lists[key]);
        if (arr.length == 1) {
            if (arr[0].type == 'checkbox' || arr[0].type == 'radio') {
                if (arr[0].checked == true) {
                    args[lists[key]] = arr[0].value;
                }
            } else {
                args[lists[key]] = arr[0].value;
            }
        } else if (arr.length > 1) {
            for (var i = 0; i < arr.length; i++) {
                if (arr[0].type == 'checkbox') {
                    if (arr[i].checked == true) {
                        args[lists[key] + '[' + i + ']'] = arr[i].value;
                    }
                } else if (arr[0].type == 'radio') {
                    if (arr[i].checked == true) {
                        args[lists[key]] = arr[i].value;
                        break;
                    }
                } else {
                    args[lists[key] + '[' + i + ']'] = arr[i].value;
                }
            }
        }
    }
    var data = [];
    var i = 0;
    for (var key in args) {
        data[i] = encodeURIComponent(key) + '=' + encodeURIComponent(args[key]);
        i++;
    }
    data = data.join("&");
    return data;
}

var loading = function() {
	var loadObj = document.getElementById('loading');
	var loadIframeObj = document.getElementById('loading_iframe');
	
    loadObj.className = 'loading';
    loadIframeObj.className = 'loading_iframe';
    var top = document.documentElement.scrollTop + document.documentElement.clientHeight / 2 - loadObj.offsetHeight / 2;
    var left =document.documentElement.scrollLeft + document.documentElement.clientWidth / 2 - loadObj.offsetWidth / 2;
    //alert(document.documentElement.scrollWidth)
    //alert(document.documentElement.scrollHeight)
    loadIframeObj.style.height = document.body.scrollHeight;
    loadObj.style.top = top;
    loadObj.style.left = left;
    window.onscroll = window.onresize = function() {
        var top = document.documentElement.scrollTop + document.documentElement.clientHeight / 2 - loadObj.offsetHeight / 2;
        var left = document.documentElement.scrollLeft + document.documentElement.clientWidth / 2 - loadObj.offsetWidth / 2;
        loadObj.style.top = top;
        loadObj.style.left = left;
    }
}
var unloading = function() {
	var loadObj = document.getElementById('loading');
	var loadIframeObj = document.getElementById('loading_iframe');
    loadObj.className = 'unloading';
    loadIframeObj.className = 'unloading_iframe';
    window.onscroll = window.onresize = null;
}

var $n = function(name) {
    return document.getElementsByName(name);
}

/**
 * 加载 json 数据
 * @param {String} url 数据来源URL
 * @param {Function} callback 回调方法
 * @param {Function} errcallback 错误回调
 * @param {String} charset 数据源charset
 * @param {String} callbackFunctionName 数据源回调接口
 * @version 1.3
 * @author zishunchen & stonehuang
 */
var loadJsonData = function(url, callback, errcallback, charset, callbackFunctionName) {
    charset = charset ? charset : "gb2312";
    var cFN = callbackFunctionName ? callbackFunctionName : "jsonCallback";
    if (Browser.isIE) {
        var df = document.createDocumentFragment();
        df[cFN] = function (data) {
            s.onreadystatechange = null;
            df = null;
            try {
                if (callback) {
                    callback(data);
                }
            } catch (e) {
                if (e.number == -2146823277) {
                    status = e.message;
                    setTimeout("status=''", 3000);
                    return;
                }
            }
        }

        var s = df.createElement("SCRIPT");
        s.charset = charset;
        df.appendChild(s);

        s.onreadystatechange = function() {
            if (s.readyState == "loaded") {
                s.onreadystatechange = null;
                df = null;
                try {
                    if (errcallback) {
                        errcallback({error:{msg:"服务器繁忙.",type:900}});
                    }
                } catch (e) {
                    if (e.number != -2146823277) {
                        status = e.message;
                        setTimeout("status=''",3000);
                        return;
                    }
                }
            }
        }
        s.src = url;
    } else {
        var i = document.createElement("IFRAME");
        i.style.display = "none";
        i.callback = function (data) {
            callback(data);
            i.callback = null;
            i.src = "about:blank";
            $(i).empty();
            i = null;
        };
        i.errcallback = errcallback;
        i.src = "javascript:\"<script>function " + cFN + "(data){frameElement.callback(data)};<\/script><script src='" + url + "' charset='" + charset + "'><\/script><script>setTimeout('frameElement.errcallback({error:{msg:\\\"服务器繁忙.\\\",type:900}})',0)<\/script>\"";
        document.body.appendChild(i);
    }
}

//add:ownyang 20090828
var tableSort = {};
(function() {
    var oTable = {};
    var cellStatus = {};
    var sortCells = {};
    var limit = 0;
    var _addEvent = function(tableId, cellId) {
        oTable[tableId].rows[0].cells[cellId].onclick = function() {
            tableSort.sort(tableId, cellId);
        }
    }

    var _addStyle = function(tableId, cellId) {
        oTable[tableId].rows[0].cells[cellId].style.cursor = 'pointer';
    }

    var _addTitle = function(tableId, cellId) {
        oTable[tableId].rows[0].cells[cellId].title = '点击排序';
    }

    var _sortTable = function(tableId, cellId) {
        var rows = oTable[tableId].tBodies[0].rows;
        var _rows = [];
        for (var i = 1; i < rows.length; i++) {
            _rows.push(rows[i]);
        }
        var status = -1;
        if (objKeyExists(cellId, cellStatus[tableId])) {
            status = 0 - cellStatus[tableId][cellId];
        }
        cellStatus[tableId][cellId] = status;
        if (status == 1) {
            oTable[tableId].rows[0].cells[cellId].innerHTML += '&nbsp;<span style="font-family:webdings;">5</span>';
            _rows.sort((function(id){
                return function(a, b) {
                    return _sort(a, b, id);
                }
            }(cellId)));
        } else {
            oTable[tableId].rows[0].cells[cellId].innerHTML += '&nbsp;<span style="font-family:webdings;">6</span>';
            _rows.sort((function(id){
                return function(a, b) {
                    return _rsort(a, b, id);
                }
            }(cellId)));
        }
        var oFragment = document.createDocumentFragment();
        for (var i = 0; i < _rows.length; i++) {
            _rows[i].className = i % 2 ? 'odd' : '';
            //_rows[i].cells[0].className =  i % 2 ? 'td_spec2' : 'td_spec1';
            if (limit > 0) {
                if (i >= limit) {
                    _rows[i].style.display = 'none';
                } else {
                    _rows[i].style.display = '';
                }
            }
            oFragment.appendChild(_rows[i]);
        }
        oTable[tableId].tBodies[0].appendChild(oFragment);
    }

    var _cleanStatus = function(tableId, cellId) {
        for(var i = 0; i < sortCells[tableId].length; i++) {
            oTable[tableId].rows[0].cells[sortCells[tableId][i]].innerHTML = oTable[tableId].rows[0].cells[sortCells[tableId][i]].innerHTML.replace(/&nbsp;<span style=\"font-family\: webdings\">[56]<\/span>$/ig, '');
        }
    }

    var _sort = function(a, b, id) {
        var param1 = a.cells[id].innerText;
        var param2 = b.cells[id].innerText;
        param1 = param1.replace(/[^\d.-]/g, '');
        param2 = param2.replace(/[^\d.-]/g, '');
        if (param1 == '-' || param1 == '') {
            return 1;
        }
        if (param2 == '-' || param2 == '') {
            return -1;
        }
        //如果两个参数均为字符串类型
        if (isNaN(param1) && isNaN(param2)){
            return param1.localeCompare(param2);
        }
        //如果参数1为数字，参数2为字符串
        if (!isNaN(param1) && isNaN(param2)){
            return -1;
        }
        //如果参数1为字符串，参数2为数字
        if (isNaN(param1) && !isNaN(param2)){
            return 1;
        }
        //如果两个参数均为数字
        if (!isNaN(param1) && !isNaN(param2)){
            if (Number(param1) > Number(param2)) {
                return 1;
            }
            if (Number(param1) == Number(param2)) {
                return 0;
            }
            if (Number(param1) < Number(param2)) {
                return -1;
            }
        }
        //return a.cells[id].innerText - b.cells[id].innerText;
    }

    var _rsort = function(a, b, id) {
        var param1 = b.cells[id].innerText;
        var param2 = a.cells[id].innerText;
        param1 = param1.replace(/[^\d.-]/g, '');
        param2 = param2.replace(/[^\d.-]/g, '');
        if (param1 == '-' || param1 == '') {
            return -1;
        }
        if (param2 == '-' || param2 == '') {
            return 1;
        }
        //如果两个参数均为字符串类型
        if (isNaN(param1) && isNaN(param2)){
            return param1.localeCompare(param2);
        }
        //如果参数1为数字，参数2为字符串
        if (!isNaN(param1) && isNaN(param2)){
            return -1;
        }
        //如果参数1为字符串，参数2为数字
        if (isNaN(param1) && !isNaN(param2)){
            return 1;
        }
        //如果两个参数均为数字
        if (!isNaN(param1) && !isNaN(param2)){
            if (Number(param1) > Number(param2)) {
                return 1;
            }
            if (Number(param1) == Number(param2)) {
                return 0;
            }
            if (Number(param1) < Number(param2)) {
                return -1;
            }
        }
        //return b.cells[id].innerText - a.cells[id].innerText;
    }

    tableSort = {
        init : function(tableId, cells, rows) {
    	   var tableObj = document.getElementById(tableId);
            oTable[tableId] = tableObj;
            sortCells[tableId] = cells;
            cellStatus[tableId] = {};
            limit = rows;
            for (var i = 0; i < cells.length; i++) {
                _addEvent(tableId, cells[i]);
                _addStyle(tableId, cells[i]);
                _addTitle(tableId, cells[i]);
            }
            if (rows > 0) {
                for (var i = 1; i < tableObj.rows.length; i++) {
                    if (i > rows) {
                        tableObj.rows[i].style.display = 'none';
                    }
                }
            }
        },
        sort : function(tableId, cellId) {
            _cleanStatus(tableId, cellId);
            _sortTable(tableId, cellId);
        }
    }
})();
            
            
/**
 * 检查给定的键名或索引是否存在于数组中
 * @param {Object} key
 * @param {Object} search
 * @author shenkong(shenkong@php.net)
 */
var objKeyExists = function(key, search) {
	if (typeof key != 'number' && typeof key != 'string') {
		return false;
	}
	for (k in search) {
		if (k == key) {
			return true;
		}
	}
	return false;
}
/**
* 显示提示信息
*/
var showMsg=function(msg,timeout)
{
    if(timeout=== undefined || timeout<0)
    {
        timeout=2;
    }
    var init=function(data)
    {
        $('body').append(data);
        var dlg=$('#infoDlg_wrapper');
        showMsg.dlg=dlg;
        dlg.jqm({modal:true});
        $('.close',dlg).click(
    	    function(){
        	    dlg.jqmHide();
    	    }
	    );
        show();
     };
     var show=function()
     {
    	$('.content',showMsg.dlg).html(msg);
        showMsg.dlg.jqmShow();
        if(timeout>0)
        { 
            setTimeout("showMsg.dlg.jqmHide()",timeout*1000);
        }
     };
    if(typeof showMsg.firtTime==='undefined')
    {
        $.get('/infocenter/template/alert.html',null,init);
        showMsg.firtTime=false; 
    }
    else
    {
        show();
    }
	
}

/*****************IIIL中特有的js全局函数****************************************/


/** end of 数据处理相关的函数 **/