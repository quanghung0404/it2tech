/**
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
*/
var JSNISSampleDataRequiredSourceElements 	= null;
var JSNISSampleDataRequiredThemeElements 	= null;
var JSNISSampleDataRequiredElements 		= null;
var JSNISSampleDataTotalRequiredElements 	= null;
var JSNISSampleDataInstallSampleDataName 	= null;
var JSNISSampleDataCommercial			 	= null;
var JSNISSampleData = {
	installSampleData: function()
	{		
		$('jsn-start-installing-sampledata').setStyle('display', 'none');	
		$('jsn-installing-sampledata').setStyle('display', 'block');
		JSNISSampleData.downloadPackage();
	},

	downloadPackage: function()
	{
		$('jsn-downloading-sampledata').setStyle('display', 'inline-block');
		$('jsn-span-unsuccessful-downloading-sampledata-message').set('html', '');
		$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'none');
		$('jsn-download-sampledata-success').setStyle('display', 'none');
		$('jsn-span-unsuccessful-downloading-sampledata').setStyle('display', 'none');
		$('jsn-span-unsuccessful-downloading-sampledata-message').setStyle('display', 'none');
		$('jsn-install-sample-data-package-title').setStyle('display', 'none');
		$('jsn-sampledata-warnings').setStyle('display', 'none');
		$('jsn-sampledata-ul-warnings').set('html', '');
		$('jsn-sample-data-text-alert').setStyle('display', 'none');
		
		var url = 'index.php';
		var jsonRequest = new Request.JSON({url: url, onSuccess: function(jsonObj){
		if(jsonObj.download)
		{
			$('jsn-downloading-sampledata').setStyle('display', 'none');
			$('jsn-download-sampledata-success').setStyle('display', 'inline-block');
			$('jsn-install-sample-data-package-title').setStyle('display', 'list-item');
			JSNISSampleDataInstallSampleDataName = jsonObj.file_name;
			JSNISSampleData.installPackage(jsonObj.file_name);
		}
		else
		{
			$('jsn-downloading-sampledata').setStyle('display', 'none');
			$('jsn-span-unsuccessful-downloading-sampledata').setStyle('display', 'inline-block');
			$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
			$('jsn-span-unsuccessful-downloading-sampledata-message').set('html', jsonObj.message);
			$('jsn-span-unsuccessful-downloading-sampledata-message').setStyle('display', 'block');
		}
		}}).get({'option': 'com_imageshow', 'controller': 'maintenance', 'task': 'downloadSampleDataPackage', 'sample_download_url': $('sample_download_url').value, 'rand': Math.random()});
	},
	
	installPackage: function(fileName) 
	{
		$('jsn-span-installing-sampledata-state').setStyle('display', 'inline-block');
		$('jsn-span-unsuccessful-installing-sampledata-message').set('html', '');
		$('jsn-span-unsuccessful-installing-sampledata-message').setStyle('display', 'none');
		$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'none');
		$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');
		$('jsn-install-sampledata-unsuccessful').setStyle('display', 'none');
		var url = 'index.php';
		var jsonRequest = new Request.JSON({url: url, onSuccess: function(jsonObj){
		if(jsonObj.install)
		{
			$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
			$('jsn-span-successful-installing-sampledata').setStyle('display', 'inline-block');		
			$('jsn-installing-sampledata-successfully').setStyle('display', 'block');
		}
		else
		{
			$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
			$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
			$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
			$('jsn-span-unsuccessful-installing-sampledata-message').set('html', jsonObj.message);
			$('jsn-span-unsuccessful-installing-sampledata-message').setStyle('display', 'block');
			if (jsonObj.warnings != undefined)
			{
				var count = jsonObj.warnings.length;
				if (count)
				{
					$('jsn-sampledata-warnings').setStyle('display', 'block');
					JSNISSampleDataRequiredThemeElements 	= jsonObj.themes;
					JSNISSampleDataRequiredSourceElements 	= jsonObj.sources;
					JSNISSampleDataRequiredElements			= jsonObj.elements;
					JSNISSampleDataTotalRequiredElements	= jsonObj.total_elements;
					JSNISSampleDataCommercial				= jsonObj.commercial;
					JSNISSampleDataLightCartErrorCode 		= jsonObj.light_cart_error_code;
					JSNISSampleData.renderWarning(jsonObj.warnings);
				}
			}
		}
		}}).get({'option': 'com_imageshow', 'controller': 'maintenance', 'task': 'installSampledata', 'file_name': fileName, 'rand': Math.random()});
	},

	renderWarning: function(data)
	{
		var warnings = data;
		var count	 = warnings.length;
		var ul 		 = $('jsn-sampledata-ul-warnings');
		if (count)
		{
			for(var i=0; i < count; i++)
			{
				var li = new Element('li', {html: warnings[i]});
				li.inject(ul);
			}
		}
	},
	installCommercial: function (login)
	{
		JSNISSampleData.installAllRequiredPlugins(login);
	},	
	installAllRequiredPlugins: function(login)
	{	
		if (JSNISSampleDataCommercial)
		{
			if(login != true)
			{
				url='index.php?option=com_imageshow&controller=maintenance&task=login&layout=form_login&js_class=JSNISSampleData&tmpl=component';
				SqueezeBox.fromElement($('jsn-sampledata-a-link-install-all-requried-plugins'), {parse: 'rel', url: url});
				return;
			}
		}
		var countSources = JSNISSampleDataRequiredSourceElements.length;
		$('jsn-span-installing-sampledata-state').setStyle('display', 'inline-block');
		$('jsn-install-sampledata-unsuccessful').setStyle('display', 'none');	
		$$('#jsn-sampledata-ul-warnings span').setStyle('color', '#000');	
		$('jsn-span-unsuccessful-installing-sampledata-message').setStyle('display', 'none');
		$('jsn-sampledata-link-install-all-requried-plugins').setStyle('display', 'none');
		$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'none');
		if(countSources)
		{	
			JSNISSampleData.overwriteInstallSource();
			for (var i = 0; i < countSources; i++)
			{
				var sources = JSON.decode(JSNISSampleDataRequiredSourceElements[i]);
				JSNISInstallImageSources.install(sources.download_element_id, sources);
			}
		}
		
		var countThemes = JSNISSampleDataRequiredThemeElements.length;
		if(countThemes)
		{	
			JSNISSampleData.overwriteInstallThemes();
			for (var i = 0; i < countThemes; i++)
			{
				var themes = JSON.decode(JSNISSampleDataRequiredThemeElements[i]);
				JSNISInstallShowcaseThemes.install(themes.download_element_id, themes);
			}		
		}		
	},
	
	overwriteInstallSource: function()
	{
		JSNISInstallImageSources.install=function (el, options)
		{
			JSNISInstallImageSources.options = Object.merge(JSNISInstallImageSources.options,options);
			JSNISInstallImageSources.download(el);
		};
		JSNISInstallImageSources.download=function(el){
			$(el).removeClass('jsn-sampledata-installation-wait');
			var tmpOptions = {};
			tmpOptions.processText 	= JSNISInstallImageSources.options.process_text;
			tmpOptions.waitText 	= JSNISInstallImageSources.options.wait_text;
			tmpOptions.parentID 	= JSNISInstallImageSources.options.download_element_id;	
			tmpOptions.textTag 		= "p";	
			var changetext 			= new JSNISInstallChangeText(tmpOptions);			
			var jsonRequest = new Request.JSON({
				url: 'index.php?option=com_imageshow&controller=installer&task=downloadImageSource&rand='+ Math.random(), 
				onSuccess: function(jsonObj)
				{
					if (jsonObj.success) 
					{
						//changetext.destroy();
						JSNISInstallImageSources.installPackage(jsonObj.package_path, el);	
					}
					else 
					{
						//changetext.destroy();
						$(el).addClass('jsn-sampledata-installation-failure');
						$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
						$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');	
						$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');	
						$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');

						if (JSNISSampleDataLightCartErrorCode[jsonObj.message]) {
							alert(JSNISSampleDataLightCartErrorCode[jsonObj.message]);
						} else {
							alert(jsonObj.message);
						}						
					}
				},
				onFailure: function()
				{
					$(el).addClass('jsn-sampledata-installation-failure');
					$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
					$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');	
					$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
					$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
				}
			}).post(JSNISInstallImageSources.options);
		};
		
		JSNISInstallImageSources.installPackage=function(packagePath, el){	
			var jsonRequest = new Request.JSON({
					url: 'index.php', 
					onSuccess: function(jsonObj)
					{
						if (jsonObj.success == true)
						{
							JSNISSampleData.checkRequiredElementsIsInstalled();
							$(el).addClass('jsn-sampledata-installation-success');
						}
						else
						{
							$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
							$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');
							$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
							$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
							alert(jsonObj.message);
						}
					}, 
					onFailure: function()
					{
						$(el).addClass('jsn-sampledata-installation-failure');
						$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
						$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');	
						$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
						$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
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
		
		JSNISInstallShowcaseThemes.download=function(){
			JSNISInstallShowcaseThemes.element.removeClass('jsn-sampledata-installation-wait');
			var tmpOptions = {};
			tmpOptions.processText 	= JSNISInstallShowcaseThemes.options.process_text;
			tmpOptions.waitText 	= JSNISInstallShowcaseThemes.options.wait_text;
			tmpOptions.parentID 	= JSNISInstallShowcaseThemes.options.download_element_id;	
			tmpOptions.textTag 		= "p";	
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
						JSNISInstallShowcaseThemes.element.addClass('jsn-sampledata-installation-failure');
						$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
						$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');	
						$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
						$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
						if (JSNISSampleDataLightCartErrorCode[jsonObj.message]) {
							alert(JSNISSampleDataLightCartErrorCode[jsonObj.message]);
						} else {
							alert(jsonObj.message);
						}
					}
				},
				onFailure: function()
				{
					JSNISInstallShowcaseThemes.element.addClass('jsn-sampledata-installation-failure');
					$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
					$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');	
					$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
					$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
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
							JSNISSampleData.checkRequiredElementsIsInstalled();
							JSNISInstallShowcaseThemes.element.addClass('jsn-sampledata-installation-success');
						}
						else
						{
							$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
							$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');	
							$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
							$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
							alert(jsonObj.message);
						}
					},
					onFailure: function()
					{
						JSNISInstallShowcaseThemes.element.addClass('jsn-sampledata-installation-failure');
						$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
						$('jsn-span-successful-installing-sampledata').setStyle('display', 'none');	
						$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
						$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
					}
				}).get({'option': 'com_imageshow',
					 'controller': 'installer',
					 'task': 'installShowcaseTheme',
					 'package_path': packagePath,
					 'rand': Math.random()});
		};		
	},
	reInstallPackage: function(fileName) 
	{
		var jsonRequest = new Request.JSON({
			url: 'index.php',
			onSuccess: function(jsonObj)
			{
				if(jsonObj.install == true)
				{
					$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
					$('jsn-span-successful-installing-sampledata').setStyle('display', 'inline-block');	
					$('jsn-installing-sampledata-successfully').setStyle('display', 'block');
					$('jsn-sampledata-ul-warnings').setStyle('display', 'none');
				}
			}	
		}).get({'option': 'com_imageshow', 'controller': 'maintenance', 'task': 'installSampledata', 'file_name': fileName, 'rand': Math.random()});			
	},	
	checkRequiredElementsIsInstalled: function()
	{
		var jsonRequest = new Request.JSON({
			url: 'index.php',
			onSuccess: function(jsonObj)
			{
				if (jsonObj.check == true) 
				{			
					JSNISSampleData.reInstallPackage(JSNISSampleDataInstallSampleDataName);
				}								
			}, 
			onFailure: function()
			{
				$('jsn-install-sampledata-unsuccessful').setStyle('display', 'inline-block');
				$('jsn-span-installing-sampledata-state').setStyle('display', 'none');
			}
		}).get({'option': 'com_imageshow',
			 'controller': 'installer', 
			 'task': 'checkRequiredElementsIsInstalled',
			 'elements': JSNISSampleDataRequiredElements,
			 'total_elements': JSNISSampleDataTotalRequiredElements,
			 'rand': Math.random()});
	}
};