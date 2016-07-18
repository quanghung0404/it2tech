/**
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
*/

var JSNISInstallImageSources = 
{	
	options: {commercial: false},
	installStatus: false,
	element: null,
	install: function (el, options)
	{
		if (JSNISInstallImageSources.installStatus) {return;}; // one install request at the time
		
		JSNISInstallImageSources.installStatus = true;
		JSNISInstallImageSources.element = $(el);
		JSNISInstallImageSources.options = Object.merge(JSNISInstallImageSources.options,options);
		JSNISInstallImageSources.download();
	},
	
	setOptions: function(el, options)
	{
		JSNISInstallImageSources.element = $(el);
		JSNISInstallImageSources.options = options;
	},
	
	installCommercial: function (formName, showID, buttonID)
	{
		var topObject = window.top.JSNISInstallImageSources;
		topObject.options.username		= document.forms[formName].username.value;
		topObject.options.password		= document.forms[formName].password.value;
		topObject.options.showID		= $(showID);
		topObject.options.buttonID		= $(buttonID);
		topObject.options.commercial	= true;
		topObject.download();
	},
	
	download: function()
	{
		var elementParent = JSNISInstallImageSources.element.getParent();

		if (!JSNISInstallImageSources.options.commercial)
		{
			JSNISInstallImageSources.element.addClass('jsn-imagesource-downloading');
			JSNISInstallImageSources.element.removeClass('jsn-showlist-imagesource-update');
			JSNISInstallImageSources.element.removeClass('jsn-showlist-imagesource-install');
			elementParent.removeClass('jsn-item-container');			
		}
		if (JSNISInstallImageSources.options.commercial)
		{
			JSNISInstallImageSources.options.buttonID.disabled = true;
			JSNISInstallImageSources.options.buttonID.addClass('jsn-button-disabled');
		}
		var tmpOptions = {};
		tmpOptions.processText 	= JSNISInstallImageSources.options.process_text;
		tmpOptions.waitText 	= JSNISInstallImageSources.options.wait_text;
		tmpOptions.parentID 	= JSNISInstallImageSources.options.download_element_id;	
		tmpOptions.textTag 	= "span";	

		var changetext		= new JSNISInstallChangeText(tmpOptions);
		var parent			= $(tmpOptions.parentID);
		var span			= parent.getElement("span");
		var error			= '';

		var tmpCoreOptions	= new JSNISInstallImageSources.cloneObject(this.options);
		delete tmpCoreOptions.process_text;
		delete tmpCoreOptions.wait_text;
		delete tmpCoreOptions.process_text;
		delete tmpCoreOptions.error_code;
		delete tmpCoreOptions.showID;
		delete tmpCoreOptions.commercial;	
		delete tmpCoreOptions.download_element_id;	
		delete tmpCoreOptions.buttonID;
		setTimeout(
			function(){
				span.setStyle('display', 'block');
			}, 3000);
		var jsonRequest = new Request.JSON({
			url: 'index.php?option=com_imageshow&controller=installer&task=downloadImageSource&rand='+ Math.random(),
			onSuccess: function(jsonObj)
			{
				if (jsonObj.success) 
				{
					if (JSNISInstallImageSources.options.commercial)
					{
						JSNISInstallImageSources.element.addClass('jsn-imagesource-downloading');
						JSNISInstallImageSources.element.removeClass('jsn-showlist-imagesource-update');
						JSNISInstallImageSources.element.removeClass('jsn-showlist-imagesource-install');
						elementParent.removeClass('jsn-item-container');
						window.top.SqueezeBox.close();
					}
					JSNISInstallImageSources.installPackage(jsonObj.package_path);	
				}
				else 
				{
					if (JSNISInstallImageSources.options.commercial)
					{
						if (JSNISInstallImageSources.options.error_code != undefined)
						{
							if (JSNISInstallImageSources.options.error_code[jsonObj.message] != undefined)
							{
								error = JSNISInstallImageSources.options.error_code[jsonObj.message];
							}
						}
						
						JSNISInstallImageSources.options.showID.set('html', error);
						JSNISInstallImageSources.options.buttonID.disabled = false;
						JSNISInstallImageSources.options.buttonID.removeClass('jsn-button-disabled');
					}
					else
					{
						if (JSNISInstallImageSources.options.error_code != undefined)
						{
							if (JSNISInstallImageSources.options.error_code[jsonObj.message] != undefined)
							{
								error = JSNISInstallImageSources.options.error_code[jsonObj.message];
							}
						}						
						alert(error.replace(/(<([^>]+)>)/ig,""));
						if (JSNISInstallImageSources.options.update)
						{
							JSNISInstallImageSources.element.addClass('jsn-showlist-imagesource-update');
						}
						if (JSNISInstallImageSources.options.install)
						{
							JSNISInstallImageSources.element.addClass('jsn-showlist-imagesource-install');
						}						
						elementParent.addClass('jsn-item-container');
					}
				}
				span.setStyle('display', 'none');
				changetext.destroy();
				JSNISInstallImageSources.element.removeClass('jsn-imagesource-downloading');
				JSNISInstallImageSources.installStatus = false;
			},
			onFailure: function()
			{
				span.setStyle('display', 'none');
				changetext.destroy();
				JSNISInstallImageSources.element.removeClass('jsn-imagesource-downloading');
				JSNISInstallImageSources.installStatus = false;
				elementParent.addClass('jsn-item-container');
			}
		}).post(tmpCoreOptions);
	},
	
	installPackage: function(packagePath) 
	{
		JSNISInstallImageSources.element.addClass('jsn-imagesource-installing');
		var elementParent = JSNISInstallImageSources.element.getParent();	
		var jsonRequest = new Request.JSON({
				url: 'index.php', 
				onSuccess: function(jsonObj)
				{
					if (jsonObj.success == false) 
					{
						alert(jsonObj.message);
					} else {
						if ($('redirectLink') != undefined && $('redirectLink').value != '')
						{	
							window.location=$('redirectLink').value;
						}
						else
						{
							window.location.reload();
						}							
					}
					JSNISInstallImageSources.element.removeClass('jsn-imagesource-installing');
					elementParent.addClass('jsn-item-container');
				}, 
				onFailure: function()
				{
					JSNISInstallImageSources.element.removeClass('jsn-imagesource-installing');
					elementParent.addClass('jsn-item-container');
				}
			}).get({'option': 'com_imageshow',
						 'controller': 'installer',
						 'task': 'installImageSource',
						 'package_path': packagePath,
						 'rand': Math.random()});
	},

	cloneObject: function(source) 
	{
	    for (i in source) 
	    {
	    	if (typeof source[i] == 'source') 
	        {		
	    		this[i] = new cloneObject(source[i]);
	        }
	        else
	        {
	            this[i] = source[i];
	        }
	    }
	}	
};