/**
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
*/

var JSNISInstallChangeText = new Class(
{	
	options : { processText: '', waitText: ''},
	
	initialize: function(options)
	{
		this.options = Object.merge(this.options, options);
		this.parent = $(this.options.parentID);
		this.text = this.parent.getElement(this.options.textTag);
		this.startChangeText();
		return this;
	},
	
	startChangeText: function()
	{
		this.changeText();
		this.loopChangeText.push(this.changeText.bind(this).periodical(10000));
	},
	
	changeText: function()
	{
		this.text.innerHTML = this.options.processText;
		this.loopChangeText.push(setTimeout(
				function(){
					this.text.innerHTML = this.options.waitText;
				}.bind(this), 
				5000
		));
	},
	
	loopChangeText : [],
	
	destroy: function()
	{
		this.loopChangeText.each(function(el){
			clearInterval(el);
		});
		
		this.text.innerHTML = '';
	}
});

var JSNISInstallDefault = {
	error: false,
	options: {},
		
	getListInstall: function(options)
	{
		JSNISInstallDefault.options = options;
		
		var jsonRequest = new Request.JSON({
			url: 'index.php?option=com_imageshow&controller=installer&task=getListPluginInstall&rand='+ Math.random(), 
			onSuccess: function(jsonObj)
			{
				var imageSources	= jsonObj.imageSources;
				var themes			= jsonObj.themes;
				imageSources.each(function(el)
				{
					el.type = 'imagesource';
					if (el.task == 'new' && el.commercial == false) {
						el.task = 'downloadImageSource';
						el.installTask = 'installImageSource';
						if (JSNISInstallDefault.options.ftp != null)
						{
							el.ftp = JSNISInstallDefault.options.ftp;
						}						
						JSNISInstallDefault.createDownloadRequest(el);
					} else if (el.task == ''){
						$('jsn-install-process-source').style.display = 'inline';
						$('jsn-install-process-source').addClass('jsn-install-success');
					}
				});

				themes.each(function(el)
				{
					el.type = 'theme';
					if (el.task == 'new' && el.commercial == false) {
						el.task = 'downloadShowcaseTheme';
						el.installTask = 'installShowcaseTheme';
						if (JSNISInstallDefault.options.ftp != null)
						{
							el.ftp = JSNISInstallDefault.options.ftp;
						}						
						JSNISInstallDefault.createDownloadRequest(el);
					} else if (el.task == '') {
						$('jsn-install-themes').style.display = 'list-item';
						$('jsn-install-process-theme').style.display = 'inline';
						$('jsn-install-process-theme').addClass('jsn-install-success');
					}
				});

				if (imageSources.length == 0) {
					$('jsn-install-process-source').style.display = 'inline';
					$('jsn-install-process-source').addClass('jsn-install-success');
				}

				if (themes.length == 0) {
					$('jsn-install-process-theme').style.display = 'inline';
					$('jsn-install-process-theme').addClass('jsn-install-success');
				}
				
				if (JSNISInstallDefault.options.restoreDatabase){
					var restoreData = {};
					restoreData.action = 'restoreDatabase';
					restoreData.argument = {'backup_file' : JSNISInstallDefault.options.backupFile};
					JSNISInstallDefault.afterInstallDefault.push(restoreData);
				}

				JSNISInstallDefault.totalDownloadRequest = JSNISInstallDefault.downloadRequests.length;
				if (JSNISInstallDefault.totalDownloadRequest > 0) {
					JSNISInstallDefault.downloadRequests[JSNISInstallDefault.countDownloadRequest].post();
				} 
				else 
				{
					if (JSNISInstallDefault.afterInstallDefault.length) {
						JSNISInstallDefault.callAfterInstallDefault();
					} else {
						JSNISInstallDefault.showButton();
						JSNISInstallDefault.showSuccessMessage();
					}
				}
			}
		}).get();
	},
	
	countDownloadRequest: 0,
	
	totalDownloadRequest: 0,

	downloadRequests: [],
	
	currentEl : null,
	
	createDownloadRequest: function(el)
	{
		var jsonRequest = new Request.JSON({
			url: 'index.php?option=com_imageshow&controller=installer&rand='+ Math.random(),
			data: el,
			onRequest: function()
			{
				if (el.type == "imagesource")
				{
					JSNISInstallDefault.options.parentID = JSNISInstallDefault.options.parentSourceID;
					setTimeout(
							function(){
								var changetext1 = new JSNISInstallChangeText(JSNISInstallDefault.options);	
							}, 3000);	
								
				}
				if (el.type == "theme")
				{
					$('jsn-install-themes').style.display = 'list-item';
					JSNISInstallDefault.options.parentID = JSNISInstallDefault.options.parentThemeID;
					setTimeout(
							function(){
								var changetext2 = new JSNISInstallChangeText(JSNISInstallDefault.options);	
							}, 3000);			
				}
				JSNISInstallDefault.currentEl = el;
				JSNISInstallDefault.toggleProcess('inline');
			},
			onSuccess: function(jsonObj)
			{
				if(jsonObj.success) {
					JSNISInstallDefault.installPackage(jsonObj.package_path, el);	
				} else {
					JSNISInstallDefault.logError(el);
					JSNISInstallDefault.toggleProcess('failure');
					JSNISInstallDefault.installManual();
				}
			},
			onCancel: function(){
				JSNISInstallDefault.logError(el);
				JSNISInstallDefault.toggleProcess('failure');
				JSNISInstallDefault.installManual();
			},
			onFailure: function(){
				JSNISInstallDefault.logError(el);
				JSNISInstallDefault.toggleProcess('failure');
				JSNISInstallDefault.installManual();
			}
		});
		
		JSNISInstallDefault.downloadRequests.push(jsonRequest);
	},
	
	installManual: function() 
	{
		if (this.options.restoreDatabase) {
			$('jsn-install-download-backup-file').style.display = 'block';
		}

		if (JSNISInstallDefault.currentEl.type == 'imagesource') 
		{
			this.currentEl.parentId = 'jsn-install-imagesources';
			this.currentEl.downloadPluginText = this.options.manualDownloadText + ' ' 
												+ this.currentEl.full_name + ' ' 
												+ this.options.manualImageSourceText + ' ' + this.options.manualPackageText;
			this.currentEl.actionForm = 'index.php?option=com_imageshow&controller=installer&task=installImagesourceManual';
			this.currentEl.requireInstallText = this.options.manualRequiredSourcesInstallText;
			this.currentEl.installPluginText = this.options.manualInstallText + ' ' + this.currentEl.full_name + ' ' + this.options.manualImageSourceText + ' ';
		} 
		else 
		{
			this.currentEl.parentId = 'jsn-install-themes';
			this.currentEl.downloadPluginText = this.options.manualDownloadText + ' ' 
												+ this.currentEl.full_name + ' ' 
												+ this.options.manualThemeText + ' ' + this.options.manualPackageText;
			this.currentEl.actionForm = 'index.php?option=com_imageshow&controller=installer&task=installThemeManual';
			this.currentEl.requireInstallText = this.options.manualRequiredThemesInstallText;
			this.currentEl.installPluginText = this.options.manualInstallText + ' ' + this.currentEl.full_name + ' ' + this.options.manualThemeText + ' ';
		}
		this.currentEl.requireDefaultInstallText = this.options.manualRequiredDefaultInstallText;

		this.currentEl.downloadLink = this.options.downloadLink + 
				'&identified_name=' + encodeURI(this.currentEl.identify_name) + 
				'&edition=' + encodeURI(this.currentEl.edition) + 
				'&joomla_version=' + encodeURI(this.currentEl.joomla_version) +
				'&language=' + encodeURI(this.options.language) +
				'&based_identified_name=imageshow&upgrade=yes';
		
		this.currentEl.redirectLink = this.options.redirectLink;
		this.currentEl.manualInstallButton 	= this.options.manualInstallButton;
		this.currentEl.manualThenSelectItText = this.options.manualThenSelectItText;
		this.currentEl.dowloadInstallationPackageText = this.options.dowloadInstallationPackageText;
		this.currentEl.selectDownloadPackageText = this.options.selectDownloadPackageText;
		this.currentEl.manualDownloadText = this.options.manualDownloadText;
		this.currentEl.formPluginInstall = true;
		var manualInstall = new JSNInstallManual(this.currentEl); 
		manualInstall.startManualInstall();
	},
	
	logError: function(el)
	{
		JSNISInstallDefault.error = true;
		JSNISInstallDefault.errorElements.push(el);
	},
	
	errorElements: [],
	
	toggleProcess: function(status)
	{
		if (this.currentEl) 
		{
			if (!this.currentEl.default_install) status = 'none';

			if (this.currentEl.task == 'downloadShowcaseTheme') 
			{
				if (status == 'none') {
					$('jsn-install-process-theme').style.display = 'inline';
					$('jsn-install-process-theme').addClass('jsn-install-success');
				}
				
				if (status == 'inline') {
					$('jsn-install-process-theme').style.display = 'inline';
					$('jsn-install-process-theme').removeClass('jsn-install-success');
				}
				
				if (status == 'failure') {
					$('jsn-install-process-theme').style.display = 'inline';
					$('jsn-install-process-theme').addClass('jsn-install-failure');
					$('jsn-install-process-theme').getElements('.jsn-icon16').removeClass('jsn-icon-ok');
					$('jsn-install-process-theme').getElements('.jsn-icon16').addClass('jsn-icon-remove');
				}
			}
			
			if (this.currentEl.task == 'downloadImageSource') 
			{
				if (status == 'none') {
					$('jsn-install-process-source').addClass('jsn-install-success');
				}
				
				if (status == 'inline') {
					$('jsn-install-process-source').removeClass('jsn-install-success');
					
				}
				
				if (status == 'failure') {
					$('jsn-install-process-source').addClass('jsn-install-failure');
					$('jsn-install-process-source').getElements('.jsn-icon16').removeClass('jsn-icon-ok');
					$('jsn-install-process-source').getElements('.jsn-icon16').addClass('jsn-icon-remove');
				}
			}
		}
	},
	
	callNextDownloadRequest: function()
	{
		JSNISInstallDefault.countDownloadRequest = JSNISInstallDefault.countDownloadRequest + 1;
		if (JSNISInstallDefault.countDownloadRequest < JSNISInstallDefault.totalDownloadRequest) 
		{
			JSNISInstallDefault.downloadRequests[JSNISInstallDefault.countDownloadRequest].post();
		} 
		else 
		{
			if(JSNISInstallDefault.error == true) {
				JSNISInstallDefault.showError();
			} 
			else 
			{
				if (JSNISInstallDefault.afterInstallDefault.length == 0) {
					JSNISInstallDefault.installSuccess();
				}
			}

			JSNISInstallDefault.callAfterInstallDefault();
		}
	},
	
	installSuccess: function()
	{
		JSNISInstallDefault.showButton();
		JSNISInstallDefault.showSuccessMessage();
	},
	
	showButton: function()
	{
		$('jsn-installation-buttons').style.display = 'block';
		
		if (JSNISInstallDefault.error == true) {
			$('jsn-installation-button-close').style.display = 'inline-block';
		} else {
			$('jsn-installation-button-finish').style.display = 'inline-block';
		}
	},
	
	showSuccessMessage: function()
	{
		$('jsn-installation-successfull-message').style.display = 'block';
	},
	
	showError: function()
	{
		JSNISInstallDefault.showButton();
		if (JSNISInstallDefault.error == true) {
			JSNISInstallDefault.showDownloadLink();
		}
		if (JSNISInstallDefault.errorElements.length > 0)
		{
			var flagTheme = false;
			var flagSource = false;
			var errorTheme = new Element('div', {'class': 'jsn-install-process-missing-wapper', 'styles': {'display' : 'none'}})
				, errorSource = new Element('div', {'class': 'jsn-install-process-missing-wapper', 'styles': {'display' : 'none'}});
			errorTheme.style.display = 'block';
			errorSource.style.display = 'block';
			errorTheme.innerHTML = errorTheme.innerHTML + '<span class="jsn-install-process-missing-element">' + JSNISInstallDefault.options.messageTheme + '</span>'; 
			errorSource.innerHTML = errorSource.innerHTML + '<span class="jsn-install-process-missing-element">' + JSNISInstallDefault.options.messageSource + '</span>';
			var themeContent	= '';
			var sourceContent	= '';

			JSNISInstallDefault.errorElements.each(function(el)
			{
				if (el.type == 'theme') 
				{
					themeContent = themeContent + '<li>'+el.full_name+'</li>';
					flagTheme = true;
				}
				
				if (el.type == 'imagesource') 
				{
					sourceContent = sourceContent + '<li>'+el.full_name+'</li>';
					flagSource = true;
				}
			});

			errorSource.innerHTML = errorSource.innerHTML + '<ul>' + sourceContent + '</ul>';
			errorTheme.innerHTML = errorTheme.innerHTML + '<ul>' + themeContent + '</ul>';

			if (flagTheme) {
				$('jsn-install-themes').appendChild(errorTheme);
			} if (flagSource) {
				$('jsn-install-imagesources').appendChild(errorSource);
			}
		}
	},
	
	installPackage: function(packagePath, el)
	{
		var jsonRequest = new Request.JSON({
			url: 'index.php', 
			onSuccess: function(jsonObj)
			{
				if(jsonObj.success == false) {
					JSNISInstallDefault.logError(el);
					JSNISInstallDefault.toggleProcess('failure');
					JSNISInstallDefault.installManual();
				} else {
					JSNISInstallDefault.toggleProcess('none');
					JSNISInstallDefault.callNextDownloadRequest();
				}
			},
			onFailure: function()
			{
				JSNISInstallDefault.logError(el);
				JSNISInstallDefault.toggleProcess('failure');
				JSNISInstallDefault.installManual();
			}
			}).get({'option': 'com_imageshow',
					 'controller': 'installer',
					 'task': el.installTask,
					 'package_path': packagePath,
					 'rand': Math.random()});
	},
	
	afterInstallDefault: [],
	
	callAfterInstallDefault: function()
	{
		if (JSNISInstallDefault.countDownloadRequest >= JSNISInstallDefault.totalDownloadRequest - 1) 
		{
			if (JSNISInstallDefault.afterInstallDefault.length > 0 && JSNISInstallDefault.error == false)
			{
				JSNISInstallDefault.afterInstallDefault.each(function(el) {
					JSNISInstallDefault[el.action](el.argument);
				});
				JSNISInstallDefault.afterInstallDefault = [];
			}
			else if (JSNISInstallDefault.afterInstallDefault.length > 0 && JSNISInstallDefault.error == true)
			{
				JSNISInstallDefault.restoreDataFailure();
			}
		}
	},
	
	restoreDatabase: function(data)
	{
		$('jsn-installation-successfull-message').style.display = 'none';
		$('jsn-installation-buttons').style.display = 'none';
		
		var jsonRequest = new Request.JSON({
				url : 'index.php?option=com_imageshow&controller=installer&task=restoreDatabase&rand=' + Math.random(),
				data: data,
				onRequest: function()
				{
					$('jsn-install-migrate-data').style.display = 'list-item';
					JSNISInstallDefault.options.parentID = 'jsn-install-migrate-data';
					setTimeout(
							function(){
								var changetext3 = new JSNISInstallChangeText(JSNISInstallDefault.options);	
							}, 3000);		
				},
				onSuccess: function(jsonObj)
				{
					if(!jsonObj.success) {
						JSNISInstallDefault.showDownloadLink();
						JSNISInstallDefault.restoreDataFailure();
					} 
					JSNISInstallDefault.toggleProcess('none');
					$('jsn-install-process-migrate').addClass('jsn-install-success');
					JSNISInstallDefault.afterRestoreData();
				},
				onFailure: function()
				{
					JSNISInstallDefault.toggleProcess('none');
					JSNISInstallDefault.showDownloadLink();
					JSNISInstallDefault.restoreDataFailure();
					JSNISInstallDefault.afterRestoreData();
				}
			}).get(data);
	},
	
	afterRestoreData: function(){
		JSNISInstallDefault.installSuccess();
	},
	
	restoreDataFailure: function()
	{
		var dataFailure = new Element('p');
		dataFailure.innerHTML =  JSNISInstallDefault.options.restoreDataFailureText;
		dataFailure.injectTop('jsn-install-download-backup-file');
		$('jsn-install-process-migrate').addClass('jsn-install-failure');
		$('jsn-install-process-migrate').getElements('.jsn-icon16').removeClass('jsn-icon-ok');
		$('jsn-install-process-migrate').getElements('.jsn-icon16').addClass('jsn-icon-remove');
	},
	
	beforeDownload: function(){},
	
	showDownloadLink: function()
	{
		if (!JSNISInstallDefault.downloadLink){
			JSNISInstallDefault.downloadLink = $('jsn-install-download-backup-file');
		}
		if (JSNISInstallDefault.downloadLink){
			JSNISInstallDefault.downloadLink.style.display = 'block';
		}
	},
	
	downloadLink: null,
	
	restoreInstallRequiredPlugins: function()
	{
		$('jsn-restore-data-wrap').addClass('jsn-restore-installing');
		$('jsn-restore-button-cancel').style.display = 'none';
		JSNISInstallDefault.toggleProcess = function(){};
		JSNISInstallDefault.showError = function(){};
		
		JSNISInstallDefault.options.imagesources.each(function(el)
		{
			JSNISInstallDefault.restoreRequiredPlugins.push('source'+el.identify_name);
			JSNISInstallDefault.restoreTotalRequiredPlugins++;
		});
		
		JSNISInstallDefault.options.themes.each(function(el)
		{
			JSNISInstallDefault.restoreRequiredPlugins.push(el.identify_name);
			JSNISInstallDefault.restoreTotalRequiredPlugins++;
		});
		
		JSNISInstallDefault.overwriteInstallSource();
		
		JSNISInstallDefault.options.imagesources.each(function(el)
		{
			el.download_element_id = 'jsn-restore-required-sources';
			el.process_text = JSNISInstallDefault.options.processText;
			el.wait_text = JSNISInstallDefault.options.waitText;
			JSNISInstallImageSources.install(el.download_element_id, el);
		});

		JSNISInstallDefault.overwriteInstallThemes();
		
		JSNISInstallDefault.options.themes.each(function(el)
		{
			el.download_element_id = 'jsn-restore-required-themes';
			el.lightCartErrorCode = JSNISInstallDefault.options.lightCartErrorCode;
			el.process_text = JSNISInstallDefault.options.processText;
			el.wait_text = JSNISInstallDefault.options.waitText;
			JSNISInstallShowcaseThemes.install(el.download_element_id, el);
		});
	},
	
	installCommercial: function ()
	{
		JSNISInstallDefault.restoreInstallRequiredPlugins();
	},	
	
	restoreInstall: function(options) {
		JSNISInstallDefault.options = Object.merge(JSNISInstallDefault.options, options);
		JSNISInstallDefault.restoreInstallRequiredPlugins();
	},
	
	setOption: function(options) {
		JSNISInstallDefault.options = Object.merge(JSNISInstallDefault.options, options);
	},
	
	countRequriedSource: 0,
	countRequriedTheme: 0,
	
	overwriteInstallSource: function()
	{
		JSNISInstallImageSources.install=function (el, options)
		{
			JSNISInstallImageSources.element = $(el);
			JSNISInstallImageSources.options = Object.merge(JSNISInstallImageSources.options, options);
			JSNISInstallImageSources.download();
		};
		
		JSNISInstallImageSources.download = function()
		{
			$('jsn-restore-data-wrap').addClass('jsn-restore-installing-source');
			var tmpOptions = {};
			tmpOptions.processText 	= JSNISInstallImageSources.options.process_text;
			tmpOptions.waitText 	= JSNISInstallImageSources.options.wait_text;
			tmpOptions.parentID 	= JSNISInstallImageSources.options.download_element_id;
			tmpOptions.textTag 		= "span";
			var changetext			= new JSNISInstallChangeText(tmpOptions);			
			var jsonRequest = new Request.JSON({
				url: 'index.php?option=com_imageshow&controller=installer&task=downloadImageSource&rand='+ Math.random(), 
				onSuccess: function(jsonObj)
				{
					if (jsonObj.success) 
					{
						JSNISInstallImageSources.installPackage(jsonObj.package_path);	
					}
					else 
					{
						JSNISInstallDefault.error = true;
						$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');
						
						if (JSNISInstallImageSources.options.lightCartErrorCode[jsonObj.message]) {
							alert(JSNISInstallImageSources.options.lightCartErrorCode[jsonObj.message]);
						} else {
							alert(jsonObj.message);
						}
					}
				},
				onFailure: function()
				{
					JSNISInstallDefault.error = true;	$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');
				}
			}).post(JSNISInstallImageSources.options);
		};
		
		JSNISInstallImageSources.installPackage = function(packagePath){	
			var jsonRequest = new Request.JSON({
					url: 'index.php',
					onSuccess: function(jsonObj)
					{
						if (jsonObj.success == true) 
						{
							JSNISInstallDefault.countRequriedSource++;
							JSNISInstallDefault.restoreCheckRequiredPlugin();
							
							if (JSNISInstallDefault.countRequriedSource  == JSNISInstallDefault.options.imagesources.length){
								$('jsn-restore-data-wrap').addClass('jsn-restore-installing-source-success');
							}
						}
						else
						{
							JSNISInstallDefault.error = true;
							$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');
						}	
					}, 
					onFailure: function()
					{
						JSNISInstallDefault.error = true;
						$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');
					}
				}).get({'option': 'com_imageshow', 
							 'controller': 'installer',
							 'task': 'installImageSource',
							 'package_path': packagePath,
							 'rand': Math.random()});
		};		
	},
	
	overwriteInstallThemes: function()
	{
		JSNISInstallShowcaseThemes.install=function (el, options)
		{
			JSNISInstallShowcaseThemes.element = $(el);
			JSNISInstallShowcaseThemes.options = Object.merge(JSNISInstallShowcaseThemes.options, options);
			JSNISInstallShowcaseThemes.download();
		};
		
		JSNISInstallShowcaseThemes.download=function()
		{
			$('jsn-restore-data-wrap').addClass('jsn-restore-installing-theme');
			var tmpOptions = {};
			tmpOptions.processText 	= JSNISInstallShowcaseThemes.options.process_text;
			tmpOptions.waitText 	= JSNISInstallShowcaseThemes.options.wait_text;
			tmpOptions.parentID 	= JSNISInstallShowcaseThemes.options.download_element_id;	
			tmpOptions.textTag 		= "span";	
			var changetext 			= new JSNISInstallChangeText(tmpOptions);			
			var jsonRequest = new Request.JSON({
				url: 'index.php?option=com_imageshow&controller=installer&task=downloadShowcaseTheme&rand='+ Math.random(), 
				onSuccess: function(jsonObj)
				{
					if (jsonObj.success)
					{
						JSNISInstallShowcaseThemes.installPackage(jsonObj.package_path);
					}
					else
					{
						JSNISInstallDefault.error = true;
						$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');

						if (JSNISInstallShowcaseThemes.options.lightCartErrorCode[jsonObj.message]) {
							alert(JSNISInstallShowcaseThemes.options.lightCartErrorCode[jsonObj.message]);
						} else {
							alert(jsonObj.message);
						}
					}
				},
				onFailure: function()
				{
					JSNISInstallDefault.error = true;
					$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');
				}
			}).post(JSNISInstallShowcaseThemes.options);
		};
		
		JSNISInstallShowcaseThemes.installPackage=function(packagePath){	
			var jsonRequest = new Request.JSON({
					url: 'index.php', 
					onSuccess: function(jsonObj)
					{
						if(jsonObj.success == true)
						{	
							JSNISInstallDefault.countRequriedTheme++;
							JSNISInstallDefault.restoreCheckRequiredPlugin();
							if  (JSNISInstallDefault.countRequriedTheme  == JSNISInstallDefault.options.themes.length){
								$('jsn-restore-data-wrap').addClass('jsn-restore-installing-theme-success');
							}
						}
						else
						{
							JSNISInstallDefault.error = true;
							$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');
						}	
					}, 
					onFailure: function()
					{
						JSNISInstallDefault.error = true;
						$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');
					}
				}).get({'option': 'com_imageshow',
					 'controller': 'installer',
					 'task': 'installShowcaseTheme',
					 'package_path': packagePath,
					 'rand': Math.random()});
		};		
	},
	
	restoreCheckRequiredPlugin: function()
	{
		var jsonRequest = new Request.JSON({
			url: 'index.php',
			onSuccess: function(jsonObj)
			{
				if (jsonObj.check == true && JSNISInstallDefault.error == false) 
				{			
					JSNISInstallDefault.maintenanceRestoreDatabase(JSNISInstallDefault.options.backup_file.name);
				}								
			}
		}).get({'option': 'com_imageshow',
			 'controller': 'installer',
			 'task': 'checkRequiredElementsIsInstalled',
			 'elements': JSNISInstallDefault.restoreRequiredPlugins.toString(),
			 'total_elements': JSNISInstallDefault.restoreTotalRequiredPlugins,
			 'rand': Math.random()});
	},
	
	restoreRequiredPlugins: [],
	restoreTotalRequiredPlugins: 0,
	
	maintenanceRestoreDatabase: function(fileName)
	{
		var jsonRequest =  new Request.JSON({
			url: 'index.php',
			onSuccess: function(jsonObj)
			{
				if(jsonObj.success == true)
				{	
					//$('jsn-accordion-restore-pane').getParent().style.height = 'auto';
					$$('.jsn-restore-icon-failure').removeClass("jsn-icon-remove");
					$$('.jsn-restore-icon-failure').addClass("jsn-icon-ok");
					$('jsn-restore-database-success').style.display = 'block';
					$('jsn-restore-data-wrap').className = 'jsn-restore-installing-success';
					$('jsn-restore-buttons').addClass('jsn-restore-installing-success');
				} else {
					$$('.jsn-restore-icon-failure').removeClass("jsn-icon-ok");
					$$('.jsn-restore-icon-failure').addClass("jsn-icon-remove");
					$('jsn-restore-data-wrap').addClass('jsn-restore-installing-failure');
					$('jsn-restore-buttons').removeClass('jsn-restore-installing-success');
				}
			}	
		}).get({'option': 'com_imageshow', 'controller': 'maintenance', 'task': 'reRestoreDatabase', 'backup_file': fileName, 'rand': Math.random()});
	}
};