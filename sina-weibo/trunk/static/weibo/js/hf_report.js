
/**!
 * Hapyfish Client Page Loading Time Report
 * Version: 1.0.0
 *
 * Copyright (c) 2011 hapyfish.com
 * Author: zx
 */

var HFcLoadTm = {
		img: null,
		enterTm: 0,
		flashStartLoadTm: 0,
		flashFinishLoadTm: 0,
		leaveTm: 0,
		isNewUser: 0,
		setNewUser: function() {
			HFcLoadTm.isNewUser = 1;
		},
		enter: function() {
			var dt = new Date();
			HFcLoadTm.enterTm = dt.getTime();
			//$('#tm1').html(HFcLoadTm.enterTm);
		},
		flashStartLoad: function() {
			var dt = new Date();
			HFcLoadTm.flashStartLoadTm = dt.getTime();
		},
		flashFinishLoad: function() {
			var dt = new Date();
			HFcLoadTm.flashFinishLoadTm = dt.getTime();
		},
		leave: function() {
			var dt = new Date();
			HFcLoadTm.leaveTm = dt.getTime();
			var url = 'log/report';
			var param = {'type':'cLoadTm', 'tm1':HFcLoadTm.enterTm, 'tm2':HFcLoadTm.flashStartLoadTm, 'tm3':HFcLoadTm.flashFinishLoadTm, 'tm4':HFcLoadTm.leaveTm, 'isNew':HFcLoadTm.isNewUser};
			HFcLoadTm.sendReq(url, param);
		},

		noflash: function() {
			var url = 'log/report';
			var param = {'type':'noflash', 'isNew':HFcLoadTm.isNewUser};
			HFcLoadTm.sendReq(url, param);
		},
		nocookie: function() {
			var url = 'log/report';
			var param = {'type':'nocookie', 'isNew':HFcLoadTm.isNewUser};
			HFcLoadTm.sendReq(url, param);
		},

		sendReq: function(url, objParam) {
			var strParam = '';
			var reqUrl = url;
			if (objParam) {
				for (skey in objParam) {
					if (strParam == '') {
						strParam += skey + '=' + objParam[skey];
					}
					else {
						strParam += '&' + skey + '=' + objParam[skey];
					}
				}
				reqUrl += '?' + strParam;
			}
			//var img = new Image();
			this.img.src = reqUrl;
		},

		init: function() {
			this.img = new Image();
			this.enter();
			//window.onbeforeunload = this.leave;
			window.onunload = this.leave;
		}
}

HFcLoadTm.init();