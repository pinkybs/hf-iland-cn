/**
 * 浏览器对象，包括类别属性
 */
var Browser = {};

/**
 * 判断是否为IE旧版浏览器
 * @type boolean
 */
Browser.isIE = window.ActiveXObject ? true : false;

/**
 * 判断是否为IE7浏览器
 * @type boolean
 */
Browser.isIE7 = Browser.isIE && window.XMLHttpRequest;

/**
 * 判断是否为Mozilla浏览器
 * @type boolean
 */
Browser.isMozilla = Browser.isIE ? false : (typeof document.implementation != 'undefined') && (typeof document.implementation.createDocument != 'undefined') && (typeof HTMLDocument != 'undefined');

/**
 * 判断是否为Firefox浏览器
 * @type boolean
 */
Browser.isFirefox = Browser.isIE ? false : (navigator.userAgent.toLowerCase().indexOf("firefox") != -1);

/**
 * 判断是否为Safari浏览器
 * @type boolean
 */
Browser.isSafari = Browser.isIE ? false : (navigator.userAgent.toLowerCase().indexOf("safari") != -1);

/**
 * 判断是否为Opera浏览器
 * @type boolean
 */
Browser.isOpera = Browser.isIE ? false : (navigator.userAgent.toLowerCase().indexOf("opera") != -1);

/**
 * $ 取代 document.getElementById
 * @param {String} id 元素在document中的id或者uniqueID
 * @return 指定的id的元素。如不存在则返回nfull。
 * @type DocumentElement
 * @version 1.0
 * @author zishunchen
 */
/*
var $ = function(id) {
    return document.getElementById(id);
}
*/
/**
 * $n 取代 document.getElementsByName
 * @param {String} name 元素在document中的name或者id（IE only）
 * @return 指定的name的元素集合。如不存在则返回[]。
 * @type set of DocumentElement
 * @version 1.0
 * @author stonehuang
 */
var $n = function(name) {
    return document.getElementsByName(name);
}

/**
 * 获取对象坐标
 * @param {Object} obj
 * @return 坐标数组，["top","left","width","height"]
 * @type Array
 */
var getPosition = function(obj) {
    if (!obj) {
        return {};
    }
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

/**
 * 设置文件Cookie
 * @param {String} name 字段名称
 * @param {Object} value 值
 * @param {Int} timeout 过期时间
 * @param {String} 域名, 默认域名为"qq.com"
 */
var setFileCookie = function(name, value, timeout, domain) {
    var expires = new Date();
    if (!timeout) {
        timeout = 311040000000;
    }
    if (!domain) {
        domain = "itil.isd.com";
    }
    expires.setTime(expires.getTime() + timeout);
    document.cookie = name + "=" + value + ";expires=" + expires.toGMTString() + "; path=/; domain=" + domain;
}

/**
 * 设置Cookie
 * @param {String} name 字段名称
 * @param {Object} value 值
 * @param {String} path 路径
 * @param {String} 域名,默认域名为"qq.com"
 */
var setCookie = function(name, value, path, domain) {
    if (!path) {
        path = "/";
    }
    if (!domain) {
        domain = "itil.isd.com";
    }
    document.cookie = name + "=" + value + "; path=" + path + "; domain=" + domain;
}

/**
 * 获取Cookie
 * @param {String} name 字段名称
 */
var getCookie = function(name){
    var r = new RegExp("(\\b)" + name + "=([^;]*)(;|$)");
    var m = document.cookie.match(r);
    return (!m ? '' : m[2]);
}

/**
 * 清除Cookie
 * @param {String} name 字段名称
 * @param {String} path 路径
 * @param {String} 域名
 */
var deleteCookie = function(name, path, domain) {
    if (!path) {
        path = "/";
    }
    if (!domain) {
        domain = "itil.isd.com";
    }
    document.cookie = name + "=" + "; path=" + path + "; domain=" + domain + "; expires=Thu, 1 Jan 1970 00:00:01 UTC";
}

/**
 * 获取web参数
 * @param {String} name
 * @param {Boolean} cancelBubble
 */
var getParameter = function(name, cancelBubble) {
    var r = new RegExp("(\\?|#|&)" + name + "=([^&#]*)(&|#|$)");
    var m = location.href.match(r);
    if ((!m || m == "") && !cancelBubble) {
        m = top.location.href.match(r);
    }
    return (!m ? "" : m[2]);
}

/**
 * 引用新的JS脚本
 *
 * @param {Object} src
 * @param {Object} option 可以为回调函数或者延迟回收时间
 * @param {Object} _doc 指定创建script的document
 */
var includeJS = function(src, option, _doc) {
    if(!_doc) {
        _doc = document;
    }
    var callback;

    var s = _doc.createElement("script");
    s.id = "includeScript" + Math.round(Math.random() * 10000);

    if (typeof option == "function") {
        callback = option;
    } else if (typeof option == "number") {
        callback = new Function("setTimeout(\"try{removeElement(_doc.getElementById('" + s.id + "'))}catch(ex){}\"," + option + ")");
    }
    if (callback) {
        if (Browser.isIE) {
            s.onreadystatechange = function() {
                if (s.readyState != "loaded" && s.readyState != "complete") {
                    return;
                }
                s.onreadystatechange = null;
                //setTimeout(callback,61);
                callback();
            };
        } else {
            s.onload = callback;
        }
    }
    if(!!src) {
        s.src = src;
    }
    _doc.getElementsByTagName("head")[0].appendChild(s);
    return s;
}

if (Browser.isMozilla) {
    //includeJS("http://" + dataCenterUrl + "/mozilla.js");
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
            removeElement(i);
            i = null;
        };
        i.errcallback = errcallback;
        i.src = "javascript:\"<script>function " + cFN + "(data){frameElement.callback(data)};<\/script><script src='" + url + "' charset='" + charset + "'><\/script><script>setTimeout('frameElement.errcallback({error:{msg:\\\"服务器繁忙.\\\",type:900}})',0)<\/script>\"";
        document.body.appendChild(i);
    }
}

/**
 * 解析XML，返回XML对象
 * @param {String} xml字符串
 * @return (Object) xml对象
 */
var parseXml = function(xml) {
    if (Browser.isIE) {
        var result = getXmlDom();
        result.loadXML(xml);
    } else {
        var parser = new DOMParser();
        var result = parser.parseFromString(xml, "text/xml");
    }
    return result;
}

/**
 * 获取XmlDom解释器
 */
var getXmlDom = function() {
    if (!Browser.isIE) {
        return null;
    }
    var xmldomversions = ['MSXML2.DOMDocument.5.0', 'MSXML2.DOMDocument.4.0', 'MSXML2.DOMDocument.3.0', 'MSXML2.DOMDocument', 'Microsoft.XMLDOM'];
    for (var i = 0; i < xmldomversions.length; i++) {
        try {
            return new ActiveXObject(xmldomversions[i]);
        } catch (e) {

        }
    }
    return null;
}

/**
 * 将对象转为字符串
 * @param {Object} o
 * @return (String)
 */
var objToStr = function(o) {
    var r = [];
    if (typeof o == 'string') {
        return "\"" + o.replace(/([\'\"\\])/g, "\\$1").replace(/(\n)/g, "\\n").replace(/(\r)/g, "\\r").replace(/(\t)/g, "\\t") + "\"";
    }
    if (typeof o == "undefined") {
        return "undefined";
    }
    if (typeof o == "object") {
        if (o === null) {
            return "null";
        } else if (!o.sort) {
            for (var i in o) {
                r.push('"' + i + '"' + ":" + objToStr(o[i]));
            }
            r = "{" + r.join() + "}";
        } else {
            for (var i = 0; i < o.length; i++) {
                r.push(objToStr(o[i]));
            }
            r = "[" + r.join() + "]";
        }
        return r;
    }
    return o.toString();
}

/**
 * 克隆一个对象
 * @param {Object} o
 * @return {Object} 返回克隆的对象
 */
var cloneObj = function(o) {
    if (typeof o == 'object') {
        var r = (o.sort) ? [] : {};
        for (var i in o) {
            r[i] = cloneObj(o[i]);
        }
        return r;
    }
    return o;
}

/**
 * 比较两个变量是否相同
 * @param {Object} fobj
 * @param {Object} sobj
 * @return (Boolean)
 */
var compare = function(fobj, sobj) {
    var ftype = typeof(fobj);
    var stype = typeof(sobj);
    if (ftype == stype) {
        if (ftype == "object") {
            if (fobj.constructor == Array && sobj.constructor == Array) {
                return compareArray(fobj, sobj);
            } else if (fobj.constructor != Array && sobj.constructor != Array) {
                return compareObject(fobj, sobj);
            }
            return false;
        }
        return fobj == sobj;
    }
    return false;
}

/**
 * 比较两个对象是否相同
 * @param {Object} fobj
 * @param {Object} sobj
 * @return (Boolean)
 */
var compareObject = function(fobj, sobj) {
    for (var ele in fobj) {
        if (sobj[ele] == undefined) {
            return false;
        }
        if (!compare(fobj[ele], sobj[ele])) {
            return false;
        }
    }
    for (var ele in sobj) {
        if (fobj[ele] == undefined) {
            return false;
        }
        if (!compare(fobj[ele], sobj[ele])) {
            return false;
        }
    }
    return true;
}

/**
 * 对比两个数组是否相同
 * @param {Object} farr
 * @param {Object} sarr
 * @return (Boolean)
 */
var compareArray = function(farr, sarr) {
    if (farr.length != sarr.length) {
        return false;
    }
    for (var i = 0; i < farr.length; i++) {
        if (!compare(farr[i], sarr[i])) {
            return false;
        }
    }
    return true;
}

/**
 * 计算对象大小
 * @param {Object} obj
 * @author shenkong(shenkong@php.net)
 */
var count = function(obj) {
    var length = 0;
    if (typeof obj == 'object') {
        for (k in obj) {
            length++;
        }
    }
    return length;
}

/**
 * 检查数组中是否存在某个值
 * 在 haystack 中搜索 needle ，如果找到则返回 TRUE，否则返回 FALSE。
 * @param {Object} needle
 * @param {Object} haystack
 * @author shenkong(shenkong@php.net)
 */
var inObj = function(needle, haystack) {
    for (key in haystack) {
        if (compare(needle, haystack[key])) {
            return true;
        }
    }
    return false;
}

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
 * 返回 input 中所有的值并给其建立数字索引
 * @param {Object} input
 * @author shenkong(shenkong@php.net)
 */
var objValues = function(input) {
    var o = [];
    for (key in input) {
        o.push(input[key]);
    }
    return o;
}

/**
 * 返回数组中所有的键名
 * 返回 input 数组中的数字或者字符串的键名。
 * 如果指定了可选参数 search_value ，则只返回该值的键名。否则 input 数组中的所有键名都会被返回。
 * @param {Object} input
 * @param {Object} search_value
 * @author shenkong(shenkong@php.net)
 */
var objKeys = function(input, search) {
    if (typeof input != 'object') {
        return false;
    }
    var o = [];
    if (search == undefined) {
        for (key in input) {
            o.push(key);
        }
    } else {
        for (key in input) {
            if (compare(input[key], search)) {
                o.push(key);
            }
        }
    }
    return o;
}

/**
 * 创建一个数组，用一个数组的值作为其键名，另一个数组的值作为其值。
 * 如果两个数组的单元数不同或者数组为空时返回 FALSE。
 * 如果keys数组值有非字符串或数值的返回 FALSE。
 * @param {Object} keys
 * @param {Object} values
 * @author shenkong(shenkong@php.net)
 */
var objCombine = function(keys, values) {
    if (keys.length != values.length) {
        return false;
    }
    if (keys.length == 0) {
        return false;
    }
    if (typeof keys != 'object' || typeof values != 'object') {
        return false;
    }
    var o = {};
    for (var i = 0; i < keys.length; i++) {
        if (typeof keys[i] != 'number' && typeof keys[i] != 'string') {
            return false;
        }
        o[keys[i]] = values[i];
    }
    return o;
}

/**
 * 在数组中搜索给定的值，如果成功则返回相应的键名
 * 在 haystack 中搜索 needle 参数并在找到的情况下返回键名，否则返回 FALSE。
 * 如果 needle 在 haystack 中出现不止一次，则返回第一个匹配的键。
 * 要返回所有匹配值的键，应该用 objKeys() 加上可选参数 search 来代替。
 * @param {Object} needle
 * @param {Object} haystack
 * @author shenkong(shenkong@php.net)
 */
var objSearch = function(needle, haystack) {
    if (typeof haystack != 'object') {
        return false;
    }
    for (key in haystack) {
        if (compare(haystack[key], needle)) {
            return key;
        }
    }
    return false;
}

/**
 * 统计数组中所有的值出现的次数
 * 返回一个对象，该对象用 input 数组中的值作为键名，该值在 input 数组中出现的次数作为值。
 * @param {Object} input
 * @author shenkong(shenkong@php.net)
 */
var objCountValues = function(input) {
    var o = {};
    for (key in input) {
        if (o[input[key]] == undefined) {
            o[input[key]] = 1;
        } else {
            o[input[key]]++;
        }
    }
    return o;
}

/**
 * 交换对象中的键和值
 * 返回一个反转后的 object，例如 trans 中的键名变成了值，而 trans 中的值成了键名。
 * 如果同一个值出现了多次，则最后一个键名将作为它的值，所有其它的都丢失了。
 * @param {Object} trans
 * @author shenkong(shenkong@php.net)
 */
var objFlip = function(trans) {
    var o = {};
    for (key in trans) {
        o[trans[key]] = key;
    }
    return o;
}

/**
 * 返回对象的最后一个单元
 * @param {Object} obj
 * @author shenkong(shenkong@php.net)
 */
var end = function(obj) {
    for (key in obj) {
    }
    return obj[key];
}

/**
 * 返回对象的第一个单元
 * @param {Object} obj
 * @author shenkong(shenkong@php.net)
 */
var first = function(obj) {
    for (key in obj) {
        return obj[key];
    }
}

/**
 * 将回调函数作用到给定数组的单元上
 * 返回一个对象，该对象包含了 arr1 中的所有单元经过 callback 作用过之后的单元。
 * callback 接受的参数数目应该和传递给 objMap() 函数的数组数目一致。
 * @param {Object} callback
 * @author shenkong(shenkong@php.net)
 */
var objMap = function(callback, arr1) {
    var o = [];

    if (callback == null) {
        callback = function() {
            var a = [];
            for (var i = 0; i < arguments.length; i++) {
                a.push(arguments[i]);
            }
            return a;
        }
    }

    for (var i = 1; i < arguments.length; i++) {
        arguments[i] = objValues(arguments[i]);
    }
    var length = 0;
    for (var i = 1; i < arguments.length; i++) {
        if (arguments[i].length > length) {
            length = arguments[i].length;
        }
    }
    for (var i = 0; i < length; i++) {
        var cmd = [];

        for (var j = 1; j < arguments.length; j++) {
            cmd.push('arguments[' + j + '][' + i + ']');
        }
        cmd = 'o[' + i + '] = callback(' + cmd.join(',') + ')';
        eval(cmd);
    }
    return o;
}

var setLocation = function(data) {
    if (!parent.frames['locationFrame']) {
        return;
    }
    var obj =  parent.frames['locationFrame'].document;
    if (obj.readyState != "complete") {
        obj.onreadystatechange = function () {
            if (obj.readyState == "complete") {
                var str = '<a target="contentFrame" href="' + data[0]['url'] + '" id="home" onfocus="this.blur();">' + data[0]['title'] + '</a>';
                for (var i = 1; i < data.length; i++) {
                    str += ' <img src="./image/right4.gif" style="vertical-align:middle;" />';
                    str += ' <a target="contentFrame" href="' + data[i]['url'] + '" class="nav" onfocus="this.blur();">' + data[i]['title'] + '</a>';
                }
                obj.getElementById('location').innerHTML = str;
            }
        }
    } else {
        var str = '<a target="contentFrame" href="' + data[0]['url'] + '" id="home" onfocus="this.blur();">' + data[0]['title'] + '</a>';
        for (var i = 1; i < data.length; i++) {
            str += ' <img src="./image/right4.gif" style="vertical-align:middle;" />';
            str += ' <a target="contentFrame" href="' + data[i]['url'] + '" class="nav" onfocus="this.blur();">' + data[i]['title'] + '</a>';
        }
        obj.getElementById('location').innerHTML = str;
    }
}

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

var createNotice = function(type, notice) {
    var str = '<div class="notice">'
    str += '<span class="notice_image' + type +'">&nbsp;</span>' + notice;
    str += '</div>';
    return str;
}
var createWarning = function(warning) {
    var str = '<div class="warning">'
    str += '<span class="warning_image">&nbsp;</span>' + warning;
    str += '</div>';
    return str;
}
var createError = function(error) {
    var str = '<div class="error">'
    str += '<span class="error_image' + type +'">&nbsp;</span>' + error;
    str += '</div>';
    return str;
}

var loading = function() {
    $('loading').className = 'loading';
    $('loading_iframe').className = 'loading_iframe';
    var top = document.documentElement.scrollTop + document.documentElement.clientHeight / 2 - $('loading').offsetHeight / 2;
    var left =document.documentElement.scrollLeft + document.documentElement.clientWidth / 2 - $('loading').offsetWidth / 2;
    //alert(document.documentElement.scrollWidth)
    //alert(document.documentElement.scrollHeight)
    $('loading_iframe').style.height = document.body.scrollHeight;
    $('loading').style.top = top;
    $('loading').style.left = left;
    window.onscroll = window.onresize = function() {
        var top = document.documentElement.scrollTop + document.documentElement.clientHeight / 2 - $('loading').offsetHeight / 2;
        var left = document.documentElement.scrollLeft + document.documentElement.clientWidth / 2 - $('loading').offsetWidth / 2;
        $('loading').style.top = top;
        $('loading').style.left = left;
    }
}
var unloading = function() {
    $('loading').className = 'unloading';
    $('loading_iframe').className = 'unloading_iframe';
    window.onscroll = window.onresize = null;
}

var uniqId = function() {
    var str = '', d;
    d = new Date();
    str += d.getYear();
    str += d.getMonth();
    str += d.getDate();
    str += d.getHours();
    str += d.getMinutes();
    str += d.getSeconds();
    str += d.getMilliseconds();
    str += Math.floor(Math.random()*999+1);
    return str;
}

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
            _rows[i].className = i % 2 ? 'tr' : 'tr_spec';
            _rows[i].cells[0].className =  i % 2 ? 'td_spec2' : 'td_spec1';
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
            oTable[tableId] = $(tableId);
            sortCells[tableId] = cells;
            cellStatus[tableId] = {};
            limit = rows;
            for (var i = 0; i < cells.length; i++) {
                _addEvent(tableId, cells[i]);
                _addStyle(tableId, cells[i]);
                _addTitle(tableId, cells[i]);
            }
            if (rows > 0) {
                for (var i = 1; i < $(tableId).rows.length; i++) {
                    if (i > rows) {
                        $(tableId).rows[i].style.display = 'none';
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

var ajaxHistory = {};
(function() {
    var historyList = [];
    var historyOffset = 0;
    var historyFrame = {};
    var oldStatus = {};
    var lastKey = 0;
    if (top.ajaxHistory == ajaxHistory) {
        ajaxHistory = {
            init : function(frameName) {
                historyFrame = frames[frameName];
            },
            get : function(key) {
                return historyList[key];
            },
            set : function(frameName, funcName, args) {
                lastKey = historyOffset + 1;
                if (oldStatus.funcName != undefined) {
                    if (compare(oldStatus, {'frameName':frameName,'funcName':funcName,'args':args,'href':frames[frameName].document.location.href})) {
                        return;
                    }
                    historyList[historyOffset] = {};
                    historyList[historyOffset] = oldStatus;
                    historyOffset++;
                    historyList.length = historyOffset;
                    var url = historyFrame.location.protocol + "//" + historyFrame.location.host + historyFrame.location.pathname + "?";
                    historyFrame.location = url + (historyOffset % 2) + "#" + historyOffset;
                    oldStatus = {'frameName':frameName,'funcName':funcName,'args':args,'href':frames[frameName].document.location.href};
                } else {
                    oldStatus = {'frameName':frameName,'funcName':funcName,'args':args,'href':frames[frameName].document.location.href};
                }
            },
            setFirst : function(frameName, funcName, args) {
                if (historyOffset == 0 && historyList[historyOffset] == undefined) {
                    ajaxHistory.set(frameName, funcName, args);
                } else {
                    lastKey = historyFrame.location.hash.substr(1);
                    if (historyList[historyOffset - 1] != undefined && frames[frameName].document.location.href != historyList[historyOffset - 1].href && historyList[historyFrame.location.hash.substr(1) - (-1)] == undefined) {
                        ajaxHistory.set(frameName, funcName, args);
                    } else if (historyList[historyFrame.location.hash.substr(1) - (-1)] != undefined && historyList[historyFrame.location.hash.substr(1) - 0] != undefined && frames[frameName].document.location.href != historyList[historyFrame.location.hash.substr(1) - 0].href && frames[frameName].document.location.href == historyList[historyFrame.location.hash.substr(1) - (-1)].href) {
                        oldStatus = {'frameName':frameName,'funcName':funcName,'args':args,'href':frames[frameName].document.location.href};
                        window.history.go(1);
                    } else {
                        if (historyFrame.location.hash.substr(1) == historyOffset) {
                            var tmpFunc = frames[oldStatus.frameName][oldStatus.funcName];
                            if(typeof(tmpFunc) == 'function'){
                                tmpFunc.apply(null, [oldStatus.args]);
                            }
                        } else {
                            oldStatus = {'frameName':frameName,'funcName':funcName,'args':args,'href':frames[frameName].document.location.href};
                        }
                    }
                }
            },
            go : function(key) {
                key = key - 0;
                if (key < historyOffset) {
                    if (historyList[historyOffset] == undefined) {
                        historyList[historyOffset] = oldStatus;
                    }
                } else if (key > historyOffset) {
                } else {
                    lastKey = key;
                    return;
                }
                historyOffset = key;
                if (objKeyExists(historyOffset, historyList)) {
                    try {
                        if (historyList[historyOffset].href != frames[historyList[historyOffset].frameName].document.location.href) {
                            if (lastKey > key) {
                                window.history.go(-1);
                            }
                            if ($(historyList[historyOffset].frameName).readyState != "complete") {
                                $(historyList[historyOffset].frameName).onreadystatechange = function () {
                                    if ($(historyList[historyOffset].frameName).readyState == "complete") {
                                        var tmpFunc = frames[historyList[historyOffset].frameName][historyList[historyOffset].funcName];
                                        if(typeof(tmpFunc) == 'function'){
                                            tmpFunc.apply(null, [historyList[historyOffset].args]);
                                        }
                                        $(historyList[historyOffset].frameName).onreadystatechange = null;
                                    } else {
                                    }
                                }
                            } else {
                                var tmpFunc = frames[historyList[historyOffset].frameName][historyList[historyOffset].funcName];
                                if(typeof(tmpFunc) == 'function'){
                                    tmpFunc.apply(null, [historyList[historyOffset].args]);
                                }
                            }
                        } else {
                            var tmpFunc = frames[historyList[historyOffset].frameName][historyList[historyOffset].funcName];
                            if(typeof(tmpFunc) == 'function'){
                                tmpFunc.apply(null, [historyList[historyOffset].args]);
                            } else {
                            }
                        }
                    } catch (e) {
                        alert(e.message)
                    }
                }
                lastKey = key;
            },
            debug : function(key) {
                alert(historyFrame.location);
                alert(historyOffset);
                alert(objToStr(historyList[key]))
                alert(lastKey);
            }
        }
    } else {
        ajaxHistory = top.ajaxHistory;
    }
})();