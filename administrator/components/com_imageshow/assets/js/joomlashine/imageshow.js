/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: imageshow.js 17065 2012-10-16 04:06:37Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
var JSNISImageShow = {
	ChooseProfileFolder:function(){
		if($('add_image_manual_auto').checked == true){			
			$('user_select_folder').disabled = false;
		}
		if($('add_image_manual').checked == true){
			$('user_select_folder').disabled = true;
		}
	},
	ShowListCheckAlternativeContent:function(){
			var value = $('alternative_status').options[$('alternative_status').selectedIndex].value;
			if(value == 2){
				$('wrap-btt-article').setStyle('display', '');	
			}else{
				$('wrap-btt-article').setStyle('display', 'none');	
			}
			if(value == 1){
				$('wrap-btt-module').setStyle('display', '');	
			}else{
				$('wrap-btt-module').setStyle('display', 'none');	
			}
			
			if(value == 3){
				$('wrap-btt-image').setStyle('display', '');	
			}else{
				$('wrap-btt-image').setStyle('display', 'none');	
			}
	},
	ShowListCheckSeoContent:function(){
		var value = $('seo_status').options[$('seo_status').selectedIndex].value;
		if(value == 1){
			$('wrap-seo-article').setStyle('display', '');	
		}else{
			$('wrap-seo-article').setStyle('display', 'none');	
		}
		if(value == 2){
			$('wrap-seo-module').setStyle('display', '');	
		}else{
			$('wrap-seo-module').setStyle('display', 'none');	
		}
	},
	ShowListCheckAuthorizationContent:function(){
			var value = $('authorization_status').options[$('authorization_status').selectedIndex].value;
			if(value == 1){
				$('wrap-aut-article').setStyle('display', '');	
			}else{
				$('wrap-aut-article').setStyle('display', 'none');	
			}
	},

	SetStatusMessage:function(token, msg_id){
		var url  = 'index.php?option=com_imageshow&controller=maintenance&task=setstatusmsg&msg_id='+msg_id+'&'+token+'=1';	
		var ajax = new Request({
			url: url,
			method: 'get',
			onComplete: function(response) {
			}
		});
		ajax.send();
	},
	
	ReplaceVals: function (n) {
		if (n == "a") { n = 10; }
		if (n == "b") { n = 11; }
		if (n == "c") { n = 12; }
		if (n == "d") { n = 13; }
		if (n == "e") { n = 14; }
		if (n == "f") { n = 15; }
		
		return n;
	},
	
	hextorgb: function (strPara) {
		var casechanged=strPara.toLowerCase(); 
		var stringArray=casechanged.split("");
		if(stringArray[0] == '#'){
			for(var i = 1; i < stringArray.length; i++){			
				if(i == 1 ){
					var n1 = JSNISImageShow.ReplaceVals(stringArray[i]);				
				}else if(i == 2){
					var n2 = JSNISImageShow.ReplaceVals(stringArray[i]);
				}else if(i == 3){
					var n3 = JSNISImageShow.ReplaceVals(stringArray[i]);
				}else if(i == 4){
					var n4 = JSNISImageShow.ReplaceVals(stringArray[i]);
				}else if(i == 5){
					var n5 = JSNISImageShow.ReplaceVals(stringArray[i]);
				}else if(i == 6){
					var n6 = JSNISImageShow.ReplaceVals(stringArray[i]);
				}			
			}
			
			var returnval = ((16 * n1) + (1 * n2));
			var returnval1 = 16 * n3 + n4;
			var returnval2 = 16 * n5 + n6;
			return new Array(((16 * n1) + (1 * n2)), ((16 * n3) + (1 * n4)), ((16 * n5) + (1 * n6)));
		}
		return new Array(255, 0, 0);
	},
	
	switchShowcaseTheme: function(me)
	{
		$('adminForm').redirectLinkTheme.value = me.href;
		$('adminForm').task.value = 'switchTheme';
		$('adminForm').submit();
	},
	
	showlistSaveButtonsStatus: function(status)
	{
		$('jsn-showlist-toolbar-css').innerHTML = ''; // remove style css
	},
	
	deleteObsoleteThumbnails:function(token){
		var url  = 'index.php?option=com_imageshow&controller=maintenance&task=deleteobsoletethumbnails&'+token+'=1';	
		var smallLoader 		= $('jsn-creating-thumbnail');
		var smallSuccessful 	= $('jsn-creat-thumbnail-successful');
		var smallUnsuccessful 	= $('jsn-creat-thumbnail-unsuccessful');
		smallSuccessful.removeClass ('jsn-fade-out');
		var button				= $('jsn-button-delete-obsolete-thumnail');
		smallLoader.setStyle('display', 'inline-block');	
		smallSuccessful.setStyle('display', 'none');	
		smallUnsuccessful.setStyle('display', 'none');	

		button.disabled = true;
		button.addClass('button-disabled');
		var ajax = new Request({
			url: url,
			method: 'get',
			onComplete: function(response) {			
				var data = JSON.decode(response);
				if(data.existed_folder)
				{
					smallSuccessful.setStyle('display', 'inline-block');
					setTimeout("$('jsn-creat-thumbnail-successful').addClass('jsn-fade-out')", 3000);
				}
				else
				{
					alert(data.message);
					smallUnsuccessful.setStyle('display', 'inline-block');
					setTimeout("$('jsn-creat-thumbnail-unsuccessful').setStyle('display', 'none')", 3000);
				}
				button.removeClass('button-disabled');
				button.disabled = false;
				smallLoader.setStyle('display', 'none');				
			}
		});
		ajax.send();
	},
	
	initShowlist: function(){ 
		// will be define in view 
	},
	
	getScriptCheckThumb: function(showlistID)
	{
		var ajax = new Request({
			url: 'index.php?option=com_imageshow&controller=images&task=getScriptCheckThumb&showlist_id='+showlistID +'&rand=' + Math.random(),
			method: 'get',
			noCache: true,
			onComplete: function(response)
			{
			var script   = document.createElement('script');
				script.type  = 'text/javascript';
				script.text  = response;			
				document.body.appendChild(script);
			}
		});
		ajax.send();
	},
	
	checkThumbCallBack: function()
	{
		// will be defined base on view layout
	},
	
	confirmChangeSource: function($msg, showlistID, countImage)
	{
		var confirmBox = false;
		
		if (countImage > 0) 
		{
			var confirmBox = confirm($msg);
		}
		
		if (confirmBox == true || countImage == 0)
		{
			var ajax = new Request({
				url: 'index.php?option=com_imageshow&controller=showlist&task=changeSource&showlist_id='+showlistID+'&rand='+ Math.random(),
				method: 'get',
				onComplete: function(response)
				{
					window.location.reload(true);
				}
			});
			ajax.send();
		}
	},
	
	getFormInput: function(formID)
	{
		var options = {};
		
		$(formID).getElements('input, select, textarea', true).each(function(el)
		{
			if (el.disabled == false)
			{
				var name = el.name;
				
				if (el.type == 'radio')
				{
					if (el.checked == true) {
						var value = el.getProperty('value');
					}
				}
				else
				{
					var value = el.getProperty('value');
				}
				if(value!=undefined)
					options[name] = value;
			}
		});	
		
		return options;
	},
	
	submitProfile: function(formID)
	{
		var values = JSNISImageShow.getFormInput(formID);
		var link = '';
			
		try {
			var link = 'index.php?option=' + values['option'] + '&controller=' + values['controller'] + '&task=' + values['task'];
		}catch(err){}
		var ajax = new Request({
			url: link,
			method: 'post',
			data: values,
			onComplete: function(response)
			{
				window.parent.location.reload(true);
			}
		});
		ajax.send();
	},
	
	parseVersionString: function (str)
	{
		if (typeof(str) != 'string') {return false;}
		var x = str.split('.');		 
		return x;		 
	},
	
	checkVersion: function (runningVersionParam, latestVersionParam)
	{
		var check				= false;
		var self				= this;
		var runningVersion		= JSNISImageShow.parseVersionString(runningVersionParam);
		var countRunningVersion	= runningVersion.length;
		var latestVersion 		= JSNISImageShow.parseVersionString(latestVersionParam);
		var countLatestVersion 	= latestVersion.length;
		var count = 0;

		if	(countRunningVersion > countLatestVersion) {
			count = countLatestVersion;
		} else {
			count = countRunningVersion;
		}
		
		var minIndex = count - 1;
		
		for (var i = 0; i < count; i++)
		{					
			if (runningVersion[i] < latestVersion[i])
			{
				check = true;
				break;
			}
			else if(runningVersion[i] == latestVersion[i] && i == minIndex && countRunningVersion < countLatestVersion)
			{
				check = true;
				break;
			}			
			else if(runningVersion[i] == latestVersion[i])
			{
				continue;
			}
			else
			{
				break;
			}
		}
		
		return check;
	},
	
	confirmChangeTheme: function($msg, showcaseID)
	{
		var r = confirm($msg);
		if (r == true)
		{
			var url	= window.location.href;
			var tmpl = (url.test('tmpl=component'))?'&tmpl=component':'';
			var ajax = new Request({
				url: 'index.php?option=com_imageshow&controller=showcase&task=changeTheme&showcase_id='+showcaseID+'&rand='+ Math.random(),
				method: 'get',
				onComplete: function(response)
				{
					window.location='index.php?option=com_imageshow&controller=showcase&task=edit&cid[]='+showcaseID+tmpl;
				}
			});
			ajax.send();
		}
		else
		{
		  return;
		}
	},
	
	_openModal: function()
	{
		var sizes		= window.getSize();
		$('jsn-is-tmp-sbox-window').setStyles({
			display: 'block',
			width: window.getCoordinates().width,
			height: window.getScrollSize().y
		});
		
		$('jsn-is-img-box-loading').setStyles({
			'display'	: 'block',
			'left'		: (window.getCoordinates().width - $('jsn-is-img-box-loading').getStyle('width').toInt()) / 2,
			'top'		: (window.getCoordinates().height - $('jsn-is-img-box-loading').getStyle('height').toInt()) / 2
		});		
	}
};