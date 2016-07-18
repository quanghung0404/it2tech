/**
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
*/

var JSNISSampleDataManual = {
	downloadPackage: function()
	{
		$('jsn-downloading-sampledata').setStyle('display', 'inline-block');
		$('jsn-span-unsuccessful-downloading-sampledata-message').set('html', '');
		$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'none');
		$('jsn-installing-sampledata_install_requried_plugin').setStyle('display', 'none');
		$('jsn-download-sampledata-success').setStyle('display', 'none');
		$('jsn-span-unsuccessful-downloading-sampledata').setStyle('display', 'none');
		$('jsn-span-unsuccessful-downloading-sampledata-message').setStyle('display', 'none');
		$('jsn-install-sample-data-package-title').setStyle('display', 'none');
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
			$('jsn-installing-sampledata_install_requried_plugin').setStyle('display', 'none');
			$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
			$('jsn-span-unsuccessful-downloading-sampledata-message').set('html', jsonObj.message);
			$('jsn-span-unsuccessful-downloading-sampledata-message').setStyle('display', 'block');
		}
		}}).get({'option': 'com_imageshow', 'controller': 'maintenance', 'task': 'downloadSampleDataPackage', 'rand': Math.random()});
	},
	
	installPackage: function(fileName) 
	{
		$('jsn-span-installing-sampledata-state').setStyle('display', 'inline-block');
		$('jsn-span-unsuccessful-installing-sampledata-message').set('html', '');
		$('jsn-span-unsuccessful-installing-sampledata-message').setStyle('display', 'none');
		$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'none');
		$('jsn-installing-sampledata_install_requried_plugin').setStyle('display', 'none');
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
							
			if (jsonObj.required_elements != undefined)
			{	
				var count = jsonObj.required_elements.length;
				if (count)
				{
					for(var i=0; i<count; i++)
					{
						var element = jsonObj.required_elements[i];
						$('jsn-installing-sampledata_install_requried_plugin').setStyle('display', 'block');	
						$('jsn-span-unsuccessful-installing-sampledata-message').setStyle('display', 'block');
						$('jsn-span-unsuccessful-installing-sampledata-message').set('html', element.text);
						$('element_type').value=element.type;
						break;
					}
				}	
			}
			else
			{
				$('jsn-span-unsuccessful-installing-sampledata-message').setStyle('display', 'block');
				$('jsn-span-unsuccessful-installing-sampledata-message').set('html', jsonObj.message);
				$('jsn-installing-sampledata-unsuccessfully').setStyle('display', 'block');
			}	
		}
		}}).get({'option': 'com_imageshow', 'controller': 'maintenance', 'task': 'executeInstallSampledataManually', 'file_name': fileName, 'rand': Math.random()});
	}
};