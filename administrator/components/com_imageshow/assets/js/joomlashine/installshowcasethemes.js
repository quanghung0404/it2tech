/**
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
*/

var JSNISInstallShowcaseThemes = 
{	
	options: {commercial: false},
	installStatus: false,
	element: null,
	install: function (el, options)
	{
		if (JSNISInstallShowcaseThemes.installStatus) 
		{
			return;
		}
		JSNISInstallShowcaseThemes.installStatus = true;
		JSNISInstallShowcaseThemes.element = $(el);
		JSNISInstallShowcaseThemes.options = Object.merge(JSNISInstallShowcaseThemes.options, options);
		JSNISInstallShowcaseThemes.download();
	},

	setOptions: function(el, options)
	{
		JSNISInstallShowcaseThemes.element = $(el);
		JSNISInstallShowcaseThemes.options = options;
	},
	
	installCommercial: function (formName, showID, buttonID)
	{
		var topObject = window.top.JSNISInstallShowcaseThemes;
		topObject.options.username = document.forms[formName].username.value;
		topObject.options.password = document.forms[formName].password.value;
		topObject.options.showID     = $(showID);
		topObject.options.buttonID   = $(buttonID);
		topObject.options.commercial = true;		
		topObject.download();
	},

	download: function()
	{
		var elementParent = JSNISInstallShowcaseThemes.element.getParent();
		if (!JSNISInstallShowcaseThemes.options.commercial)
		{
			JSNISInstallShowcaseThemes.element.addClass('jsn-showcasetheme-downloading');
			elementParent.removeClass('jsn-item-container');	
			if (JSNISInstallShowcaseThemes.options.update)
			{
				JSNISInstallShowcaseThemes.element.removeClass('jsn-showcase-theme-update');
			}
			if (JSNISInstallShowcaseThemes.options.install)
			{
				JSNISInstallShowcaseThemes.element.removeClass('jsn-showcase-theme-install');
			}	
		}
		if (JSNISInstallShowcaseThemes.options.commercial)
		{
			JSNISInstallShowcaseThemes.options.buttonID.disabled = true;
			JSNISInstallShowcaseThemes.options.buttonID.addClass('jsn-button-disabled');
		}		
		var tmpOptions = {};
		tmpOptions.processText	= JSNISInstallShowcaseThemes.options.process_text;
		tmpOptions.waitText 	= JSNISInstallShowcaseThemes.options.wait_text;
		tmpOptions.parentID 	= JSNISInstallShowcaseThemes.options.download_element_id;	
		tmpOptions.textTag 	= "span";	
		var changetext 		= new JSNISInstallChangeText(tmpOptions);
		var parent			= $(tmpOptions.parentID);
		var span			= parent.getElement("span");	
		var error			= '';
		var tmpCoreOptions	= new JSNISInstallShowcaseThemes.cloneObject(this.options);
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
			url: 'index.php?option=com_imageshow&controller=installer&task=downloadShowcaseTheme&rand='+ Math.random(), 
			onSuccess: function(jsonObj)
			{
				if (jsonObj.success) 
				{
					if (JSNISInstallShowcaseThemes.options.commercial)
					{
						JSNISInstallShowcaseThemes.element.addClass('jsn-showcasetheme-downloading');
						JSNISInstallShowcaseThemes.element.removeClass('jsn-showcase-theme-update');
						JSNISInstallShowcaseThemes.element.removeClass('jsn-showcase-theme-install');
						elementParent.removeClass('jsn-item-container');
						window.top.SqueezeBox.close();
					}					
					changetext.destroy();
					JSNISInstallShowcaseThemes.installPackage(jsonObj.package_path, tmpCoreOptions);	
				}
				else 
				{
					changetext.destroy();
					if (JSNISInstallShowcaseThemes.options.commercial)
					{
						if (JSNISInstallShowcaseThemes.options.error_code != undefined)
						{
							if (JSNISInstallShowcaseThemes.options.error_code[jsonObj.message] != undefined)
							{
								error = JSNISInstallShowcaseThemes.options.error_code[jsonObj.message];
							}
						}
						JSNISInstallShowcaseThemes.options.showID.set('html', error);
						JSNISInstallShowcaseThemes.options.buttonID.disabled = false;
						JSNISInstallShowcaseThemes.options.buttonID.removeClass('jsn-button-disabled');
					}
					else
					{
						if (JSNISInstallShowcaseThemes.options.error_code != undefined)
						{
							if (JSNISInstallShowcaseThemes.options.error_code[jsonObj.message] != undefined)
							{
								error = JSNISInstallShowcaseThemes.options.error_code[jsonObj.message];
							}
						}
						alert(error);
						if (JSNISInstallShowcaseThemes.options.update)
						{
							JSNISInstallShowcaseThemes.element.addClass('jsn-showcase-theme-update');
						}
						if (JSNISInstallShowcaseThemes.options.install)
						{
							JSNISInstallShowcaseThemes.element.addClass('jsn-showcase-theme-install');
						}						
						elementParent.addClass('jsn-item-container');
					}
				}
				span.setStyle('display', 'none');
				JSNISInstallShowcaseThemes.installStatus = false;
				JSNISInstallShowcaseThemes.element.removeClass('jsn-showcasetheme-downloading');
			},
			onFailure: function()
			{
				span.setStyle('display', 'none');
				changetext.destroy();
				JSNISInstallShowcaseThemes.element.removeClass('jsn-showcasetheme-downloading');
				JSNISInstallShowcaseThemes.installStatus = false;
				elementParent.addClass('jsn-item-container');
			}
		}).post(tmpCoreOptions);
	},
	
	installPackage: function(packagePath, options) 
	{
		JSNISInstallShowcaseThemes.element.addClass('jsn-showcasetheme-installing');
		var elementParent = JSNISInstallShowcaseThemes.element.getParent();	
		var jsonRequest = new Request.JSON({
				url: 'index.php',
				onSuccess: function(jsonObj)
				{
					JSNISInstallShowcaseThemes.element.removeClass('jsn-showcasetheme-installing');
					if (jsonObj.success == false)
					{
						alert(jsonObj.message);
					} else {
						if ($('redirectLink').value != '')
						{
							window.location=$('redirectLink').value+options.identify_name;
						}
						else
						{
							window.location.reload(true);
						}
					}
					elementParent.addClass('jsn-item-container');
				}}).get({'option': 'com_imageshow',
						 'controller': 'installer',
						 'task': 'installShowcaseTheme',
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