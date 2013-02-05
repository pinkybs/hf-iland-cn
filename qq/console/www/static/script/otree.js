/*--------------------------------------------------|
| oTree 2.05 | www.destroydrop.com/javascript/tree/ |
|---------------------------------------------------|
| Copyright (c) 2002-2003 Geir Landr          |
|                                                   |
| This script can be used freely as long as all     |
| copyright messages are intact.                    |
|                                                   |
| Updated: 17.04.2003
| Updated: 2009-12-16 by qijunhuang@tencent.com     |
|--------------------------------------------------*/

// Node object
function Node(id, pid, name, url, title, target, icon, iconOpen, open, dataUrl,extData) {
	this.id = id;
	this.pid = pid;
	this.name = name;
	this.url = url;
	this.title = title;
	this.target = target;
	this.icon = icon;
	this.iconOpen = iconOpen;
	this.extData=extData;//扩展数据
	this._io = open || false;//是否打开
	this._is = open || false; // 是否选中
	this._ls = false;//是否是最后一个节点
	this._hc = false;//是否拥有子节点
	this._hlc = false; //子节点是否载入到dom中
	this._hlu = false;//dataUrl是否载入
	this._hl  =false;//自己是否载入
	this.dataUrl = dataUrl || false; // AJAX 数据源URL add by oking 2009/10/19
};
// Tree object
function oTree(objName,rId) {
    //预留
	this.config = {
		target					: null,
		folderLinks			: true,
		useSelection		: true,
		useCookies			: true,
		useLines				: true,
		useIcons				: false,
		useStatusText		: false,
		closeSameLevel	: false,
		inOrder					: false
	}

	this.obj = objName;
	this.rootId=rId==undefined?0:rId;
	this.aNodes = [];
	this.selectedNode = this.getCookie('so' + this.obj);
	this.onClick=function(id){};
};

// Adds a new node to the node array
oTree.prototype.add = function(id, pid, name, url, title, target, icon, iconOpen, open, dataUrl,extData) {

	this.aNodes[id] = $.extend(this.aNodes[id],new Node(id, pid, name, url, title, target, icon, iconOpen, open, dataUrl,extData));
	if(_empty(this.aNodes[pid])) { this.aNodes[pid]={};}
	if(_empty(this.aNodes[pid]['_c'])){this.aNodes[pid]['_c']=[];}
	this.aNodes[pid]['_c'].push(this.aNodes[id]);
    
};

function _empty(o)
{
    return o==undefined || typeof o === 'undefined' || o===false || (typeof o.length==='undefined' && o.length==0);
}

oTree.prototype.click = function(id) {
    var node=this.aNodes[id];
    !node._io?this.open(id):this.close(id);
    this.select(id);
    this.setStatusCookie(node._io,id);
    this.onClick(id);
};

oTree.prototype.toTree=function(wrap)
{
    wrap.append(this.nodeChildren(this.rootId));
//    var sid=this.getCookie('so' + this.obj);
//    if(sid) this.selectTo(sid,true);
//    var arr=this.getCookie('co' + this.obj).split(',');
//    for(id in arr) this.openTo(id,true);
}
//载入到aNodes中
oTree.prototype.loadUrl=function(id)
{
    var node=this.aNodes[id];
    if(_empty(node.dataUrl) || node._hlu || node._hlc) return;
    var thisObj = this;
	// 返回 json , 1分钟超时
	$.ajax(
	{type:"POST",url:node.dataUrl,dataType:"json",timeout:60000,async:false,
		success: function(data){
			for(i = 0; i < data.length; i++) {
				// 如果不指定pid就是当前结点下
				if(data[i].pid == -1) {
					var tpid = node.id;
				}else {
					var tpid = data[i].pid;
				}
				// id|name|url|title|target|icon|iconOpen|open
				thisObj.add(data[i].id, tpid, data[i].name, data[i].url, data[i].title, data[i].target, data[i].icon, data[i].iconOpen, data[i].open);					
			}
		},
		error: function(XMLHttpRequest, textStatus, thrownError) {
			alert('请求发生错误，请刷新后重试，如果持续出现这提示请联系管理员！');
		}
    });
    node._hlu=true;
}
//生成子节点dom
oTree.prototype.nodeChildren=function(id)
{
    var node=this.aNodes[id];
    if(_empty(node._c) && _empty(node._hc) || node._hlc) return;
    var wrap=$('<ul/>');
	this.loadUrl(id);
    var cdom={};
    if(_empty(node._c)) return;
    for(var i=0;i<node._c.length;i++) 
    {
        cdom=this.node(node._c[i].id);
        if(node._c[i]._io) this.open(node._c[i].id,cdom);
        wrap.append(cdom);
    }
    node._hlc=true;
    return wrap;
}
// 生成节点dom
oTree.prototype.node = function(id) 
{
    var node=this.aNodes[id];
    if(node._hl) return;
    this.setCS(id);
    var nodeLi=$('<li id="'+this.obj+node.id+'" />');
    var clkStr=' onclick="'+ this.obj+'.click('+node.id+')" ';
	var icon=$('<img class="icon" '+' onclick="'+ this.obj+'.toggle('+node.id+')" '+'src="' + STATIC_URL + '/image/infocenter/s.gif" />');
	var iconClass='';
	//一共六种状态
	if(node._hc)
	{
	    if(node._io) iconClass=node._ls?'lastOpenNode':'openNode';
	    else iconClass=node._ls?'lastCloseNode':'closeNode';
	}
	else
	{
	    iconClass=node._ls?'lastNode':'nisiNode';
	}
	icon.addClass(iconClass);
	if(!node._ls) nodeLi.addClass('line');
	if(node.url)
	{
    	var nodeA=$('<a '+clkStr+'class="cNode"  />');
    	nodeA.attr('title',node.title);
    	nodeA.attr('target',node.target);
    	nodeA.html(node.name);
    	nodeA.attr('href',node.url);
    	nodeLi.append(nodeA);
    	
	}else 
	{
	    var nodeSpan=$('<span '+clkStr+' class="cNode" />');
	    nodeLi.append(nodeSpan.html(node.name));
	}
	nodeLi.prepend(icon);
	node._hl=true;
	if(node._io) this.open(id,nodeLi);
	if(node._is) this.select(id,nodeLi);
	return nodeLi;
};

// Checks if a node has any children and if it is the last sibling
oTree.prototype.setCS = function(id) {
    var node=this.aNodes[id];
	if (!_empty(node._c) || !_empty(node.dataUrl)) node._hc = true;
	var _cs=this.aNodes[node.pid]._c;
	if(_cs[_cs.length-1].id==id) 
	node._ls = true;
	if(this.isOpen(id) && node._hc) node._io=true;
	if(node.id==this.selectedNode) node._is=true;
};

oTree.prototype.selectTo=function(id,did)
{
    //if(_empty(this.aNodes[id].id)) return;
    this.openTo(id,did);
    this.select(id);
    
};
oTree.prototype.select=function(id,dom)
{
    if(_empty(dom)) dom=$('#' + this.obj + id);
    if(_empty(dom)) return;
    if(this.selectedNode) $('#' + this.obj + this.selectedNode).children('.cNode').removeClass('selected');
	dom.children('.cNode').addClass('selected');
	this.selectedNode=id;
    this.setCookie('so' + this.obj, id);
};

oTree.prototype.toggle = function(id) 
{
    var node=this.aNodes[id];
    node._io?this.closeTo(id):this.openTo(id);
    this.select(id);
}

oTree.prototype.close = function(id) 
{
    var node=this.aNodes[id];
    var pNodeWrap = $('#' + this.obj + node.id);
    pNodeWrap.children('ul').hide();
	var icon=pNodeWrap.children('.icon');
	node._ls?icon.removeClass('lastOpenNode'):icon.removeClass('openNode');
	node._ls?icon.addClass('lastCloseNode'):icon.addClass('closeNode');
	node._io=false;

}
//打开节点，节点必须存在dom中
oTree.prototype.open = function(id,dom) 
{  
    var node=this.aNodes[id];
   if(_empty(node._hc) && _empty(node.dataUrl)) return;//没有子节点
   if(!dom) dom= $('#' + this.obj + node.id);
   if(_empty(dom)) return;//找不到此节点
   if(!node._hlc)//子节点未载入
   {
       dom.append(this.nodeChildren(id));
   }
   dom.children('ul').show();
   var icon=dom.children('.icon');
   node._ls?icon.removeClass('lastCloseNode'):icon.removeClass('closeNode');
   node._ls?icon.addClass('lastOpenNode'):icon.addClass('openNode');
   node._io=true;
   
}
oTree.prototype.getPids=function(id)
{
    var node=this.aNodes[id];
    if(_empty(node) || _empty(node.pid)) return [];
    var pid=node.pid;
    var pids=[];
    while(pid!=this.rootId)
    {
        pids.unshift(pid);
        pid=this.aNodes[pid].pid;
    }
    return pids;
}
//关闭所有子节点
oTree.prototype.closeTo=function(id)
{
    var node=this.aNodes[id];
    this.close(id);
    if(node._hc) 
    {
        for(i in node._c)
        this.closeTo(node._c[i].id);
    }
}
//打开任意节点，如果不在dom中但在aNodes中，则创建之，如果不在dom中且不在aNodes中，则ajax载入后尝试创建之。
oTree.prototype.openTo=function(id,did)
{
    var pids=this.getPids(id);
    if(pids.length==0 && did) 
    {
        this.loadUrl(did);
        pids=this.getPids(id);
    }
    for(i in pids) this.open(pids[i]);
    if(this.aNodes[id]) this.open(id);
}
// [Cookie] Sets value in a cookie
oTree.prototype.setCookie = function(cookieName, cookieValue, expires, path, domain, secure) {
	document.cookie =
		escape(cookieName) + '=' + escape(cookieValue)
		+ (expires ? '; expires=' + expires.toGMTString() : '')
		+ (path ? '; path=' + path : '')
		+ (domain ? '; domain=' + domain : '')
		+ (secure ? '; secure' : '');
};

// [Cookie] Gets a value from a cookie
oTree.prototype.getCookie = function(cookieName) {
	var cookieValue = '';
	var posName = document.cookie.indexOf(escape(cookieName) + '=');
	if (posName != -1) {
		var posValue = posName + (escape(cookieName) + '=').length;
		var endPos = document.cookie.indexOf(';', posValue);
		if (endPos != -1) cookieValue = unescape(document.cookie.substring(posValue, endPos));
		else cookieValue = unescape(document.cookie.substring(posValue));
	}
	return (cookieValue);
};

// [Cookie] Checks if a node id is in a cookie
oTree.prototype.isOpen = function(id) {
	var aOpen = this.getCookie('co' + this.obj).split(',');
	for (var n=0; n<aOpen.length; n++)
		if (aOpen[n] == id) return true;
	return false;
};
// [Cookie] open or close
oTree.prototype.setStatusCookie = function(status,id) {
	var arr=this.getCookie('co' + this.obj).split(',');
	var str='';
    for (var n=0; n<arr.length; n++)
    {
        if (arr[n] != id && arr[n])  str+=arr[n]+',';
    }
    if(status) str+=id+',';
	this.setCookie('co' + this.obj, str);
};