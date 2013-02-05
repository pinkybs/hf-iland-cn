
// ---------- begin of index页面函数 ------------ //
var indexObj = new Object();
indexObj.currentMenuId = '';
indexObj.__viewBeginTime = '';

// 在改变iframe:main(tree)的src前,需要调用这个进行一些初始化设置(目前设计三处：1. 点顶部菜单;2. 从左边的树点击;3. 通过index.php Load)
indexObj.changeNav = function(menuId) {
    indexObj.__clickTime=(new Date()).getTime();
    indexObj.currentMenuId = menuId;
    if(menuId<1000000)
    {
        indexObj.treeMenuId=menuId;
       // $('#go_sys_menu').attr('class','item_now');$('#go_sys_menu').siblings().attr('class','item');
    }
    indexObj.__viewBeginTime = (new Date()).getTime();	// 记录访问开始时间
    setTimeout(function(){indexObj.showFunctionMenu()},1200);
}

//add:ownyang 判断是否显示功能性菜单
//根据appId从数据库拉取该应用的appId的一些情况
indexObj.showFunctionMenu = function(menuId) {
	if ( arguments.length == 0 )
	{
		menuId = indexObj.currentMenuId;
	}
    if(menuId<1000000)$('#favoriteElementId').show();
    else $('#favoriteElementId').hide();
    
	$('#warningHintArea').css('display', 'none');
    $.ajax({
        url:'http://'+window.location.hostname+'/infocenter/tool/getApplicationInfo.php',
        type: 'POST',
        data: {_menuId:menuId},
        dataType: 'json',
        timeout: 5000,
        error: function(txt){
            $('#subscribeElementId').css('display', 'none');
            $('#excelElementId').css('display', 'none');
        },
        success: function(txt){
            obj = txt;
            if (obj == null)
            {
            	$('#subscribeElementId').css('display', 'none');
            	$('#excelElementId').css('display', 'none');
            	return;   
            }
                
            if (obj.subscribe_flag == '1') { //显示订阅功能按钮
                $('#subscribeElementId').css('display', '');
            } else { //隐藏订阅功能按钮
                $('#subscribeElementId').css('display', 'none');                
            }
            if (obj.excel_flag == '1') { //显示下载excel的功能按钮
                $('#excelElementId').css('display', '');
            } else {
                $('#excelElementId').css('display', 'none');
            }
            indexObj.currentAppId = obj.app_id; 
            // 告警相关
            if ( obj.warning_flag == '1' )
            {
            	/*
            	var warningInfoArr = obj.warning_info;
            	var len = warningInfoArr.length;
            	var hintHTML = "";
            	for (var i=0; i<len; i++)
            	{
            		var thisSpanArr = new Array();
            		thisSpanArr.push("<DIV>");
            		thisSpanArr.push("【OZ自动告警提示】<label>");
            		thisSpanArr.push(warningInfoArr[i].comment_auther);
            		thisSpanArr.push("(");
            		thisSpanArr.push(warningInfoArr[i].join_time);
            		thisSpanArr.push(")</label>：");
            		thisSpanArr.push(warningInfoArr[i].comment_content);
            		thisSpanArr.push("</DIV>");
            		
            		hintHTML += thisSpanArr.join("");
            	}
            	*/
            	var hintHTML = obj.warning_html;
            	$('#warningHintArea').css('display', '');
            	$('#warningHintArea > span').html(hintHTML);
            }
            setFrameHeight();//重置框架高度
        }
    });
}


indexObj.changeBar = function() {
    
	var sub_w = parseInt($("#content_frame_sub").css("width"));
	if ( sub_w > 0 )
	{
		indexObj.lastWidth = sub_w;
		$("#content_frame_sub").css("width",0);
		$("#content_frame_main").css("margin-left",0);
        $("#switch-bar").css('left',0);
        $("#switch-bar a").css('background-position',"-3px center");
	}
	else
	{
		var thisWidth = indexObj.lastWidth;
		$("#content_frame_sub").css("width",thisWidth);
		$("#content_frame_main").css("margin-left",thisWidth);
        $("#switch-bar").css('left',thisWidth);
        $("#switch-bar a").css('background-position',"-6px center");
	}
	var mainW=$(window).width()-$("#main").offset().left;
	$("#main").css("width",mainW);
}

// ----- 到这 -----

// ---------- end of index页面函数 ------------ //
var menuObj = new Object(); //index里横向菜单相关的操作封装到这个名称空间里
menuObj.changePage = function(id) {
    
	indexObj.changeNav(id);
      
    var treeUrl = "/infocenter/tree.php?menuId=" + id;
    //add:ownyang 20090722 菜单管理的处理。这里硬编码了，不好。玩意menu_id变了，都不知道代码错在哪里。 menu_id == 370 代表菜单管理
    if (id == 370) {
        treeUrl = "/infocenter/subsystem/menumonitor/treeEdit.php";
    }
    $('#tree').attr("src",treeUrl);
     if(id<1000000)
     {
         $('#go_sys_menu').attr('class','item_now');$('#go_sys_menu').siblings().attr('class','item');
     }
    
}

/**
 * 设置顶层菜单项为选中状态
 * @param $li 目标jq对象
 * @param showChild 是否显示子菜单
 */
menuObj.selectNav=function(li,showChild)
{
    if(li.length==0) return;
    li.siblings().children('a').removeClass("current");
    li.children('a').addClass("current");
    var idx=li.parent().children().index(li);//兄弟节点中的索引
    li.children().children().blur();
    var level=li.parent('ul').attr('class').substring('menu_item'.length);
    var childUl;
    if(level==1)//第一级总是打开子菜单
    {
    	childUl=$('#sub_menu > ul').eq(idx);
        childUl.siblings('ul').hide();
    	childUl.show();
    }
    else if(level==2 && showChild)
    {
    	childUl=li.children('ul');
    	childUl.show();
    	li.siblings().children('ul').hide();
        var pUl=li.parent();
    	if(pUl.css('display')=='none')//父菜单是否打开,若没有则开之
    	{
    	    var pUlidx=pUl.parent().children().index(pUl);
    	    menuObj.selectNav($('#menu_main').children('li').eq(pUlidx));
    	}
    }
}

//改变main框架地址
menuObj.setMainSrc = function (src)
{
	$("#main").attr("src", src);
}

//载入收藏相关文件
function onFavoriteClick()
{
    if(window.hasloadFavorite)
    {
        favorite.selectedId='';favorite.createFavoriteDialog();
    }
    else
    {
       $.get('/infocenter/template/favorite.html',
                null,
                function(data)
                {
                    $('#subscribe_wrap').html(data);
                    favorite.createFavoriteDialog();
                },
                ' _default'
        );
    }
    window.hasloadFavorite=true;
    return false;
}

//载入订阅对话框相关文件
function onSubscribeClick()
{
    if(window.hasloadSubscribe)
    {
        subscribe.createSubscribeDialog();
    }
    else
    {
       $.get('/infocenter/template/subscribe.html',
                null,
                function(data)
                {
                    $('#subscribe_wrap').html(data);
                    subscribe.createSubscribeDialog();
                },
                ' _default'
        );
    }
    window.hasloadSubscribe=true;
    return false;
}
// 复制地址
function copyPageUrl()
{
    var subWindow = window.frames["main"];
    if ( subWindow )
    {
        try{
            
            var paraUrl = encodeURIComponent(subWindow.document.location.href);
            if ( typeof(subWindow.getPageUrl) == 'function' )
            {
                paraUrl = subWindow.getPageUrl();
            }
            
            var orgUrl="http://"+window.location.hostname+tree.treeObj_d.aNodes[indexObj.currentMenuId].url;
            var theUrl;
            if(orgUrl==paraUrl)
            {
                theUrl = "http://"+window.location.hostname+"/index.php?menuId=" + indexObj.currentMenuId;
            }
    		else
    		{
    		    
    		    theUrl = "http://"+window.location.hostname+"/index.php?menuId=" + indexObj.currentMenuId + "&url=" + paraUrl;
    		}
    		
            
            if ( window.clipboardData.setData("Text",theUrl) )
            {
                showMsg('已经复制Url到剪贴板中。');
            }
        
        }
        catch(ex)
        {
            showMsg('无法访问剪贴板！请手动复制：'+'<br/><textarea style="margin-top:10px;margin-left:-80px;font-weight:normal;width:300px;height:80px">'+theUrl+'</textarea>',0);
        }
    }
}

// 下载Excel
function downExcelData()
{
    var subWindow = window.frames["main"];

    if ( subWindow )
    {
        if ( typeof(subWindow.getExeclfile) == 'function' )
        {
            subWindow.getExeclfile();
        }
        else
        {
            getExeclfile('统计分析数据','table_header', 'table_record',subWindow.document);
        }
    }
}

//设置iframe 100%高度
function setFrameHeight()
{
    //初始化页面高度100%
	var h=$(window).height()-$("#tree").offset().top;
	$("#content_wrap").css("height",h);
	$("#tree").css("height",h);
    $("#switch-bar").css("height",$(window).height()-$('#switch-bar').offset().top);
	var mainH=$(window).height()-$("#main").offset().top;
	$("#main").css("height",mainH);
	var mainW=$(window).width()-$("#main").offset().left;
	$("#main").css("width",mainW);
}

$(function()
{
     //调整两个iframe的高度
    setFrameHeight();
    $(window).resize(setFrameHeight);

    $('.menu_main1 > li').click(function(){
        menuObj.selectNav($(this),1);
        $.cookie('navId',$(this).attr('id').substr('nav_'.length),3);
    });
    $('.menu_main2 > li').hover(
            function(){
                menuObj.selectNav($(this),true);
            },
            function(){
                $(this).children('ul').hide();
                $(this).children('a').removeClass("current");
            }
    );

    $('.menu_main1 > li,.menu_main2 > li,.menu_main3 > li').click
    (
        function(event)
        {
            var menu_id=$(this).attr('menu_id');
            if(menu_id && menu_id !=-1)menuObj.changePage(menu_id);
            event.stopPropagation();
        }
    );
    $('#sub_menu .menu_main3').bgiframe();

    $("#switch-bar").draggable
    ({
        stop: function(event, ui) {

        sub_w=$("#switch-bar").offset().left;
        $("#content_frame_sub").css("width",sub_w);
		$("#content_frame_main").css("margin-left",sub_w);
		var mainW=$(window).width()-$("#main").offset().left;
	    $("#main").css("width",mainW);
        $("#switch-bar a").blur()
      },
      axis: 'x',
      distance:0,
      iframeFix:true
    });
    $('#go_sys_menu').click(function()
    {
        $(this).attr('class','item_now');$(this).siblings().attr('class','item');
        $('#tree').attr('src','/infocenter/tree.php?menuId='+indexObj.treeMenuId);
        return false;
    });
    $('#go_fav_menu').click(function()
    {
        $(this).attr('class','item_now');$(this).siblings().attr('class','item');
        $('#tree').attr('src','/infocenter/tree.php?treeType=2&menuId='+indexObj.treeMenuId);
        return false;
    })
});
