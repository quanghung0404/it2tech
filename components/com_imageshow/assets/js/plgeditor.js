/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
var JSNISPlgEditor = 
{
		init: function()
		{
			JSNISPlgEditor.paramSelect();
		},
		
		paramSelect: function()
		{			
			var showcase 	  =  $('showcase_id');
			var showlist 	  = $('showlist_id');
			if (showlist != undefined)
			{
				var countShowlist = showlist.options.length;
				
				if(showlist.value == 0)
				{
					showlist.style.background = '#CC0000';
					showlist.style.color = '#fff';
					JSNISPlgEditor.disableButton(true);				
				}
				else
				{
					if (showcase != undefined && showlist.value && showcase.value)
					{
						JSNISPlgEditor.disableButton(false);
					}	
					else
					{
						JSNISPlgEditor.disableButton(true);
					}				
					showlist.style.background = '#FFFFDD';
					showlist.style.color = '#000';
				}			
				for(var i = 0; i < countShowlist; i++) 
				{				
					showlist.options[i].style.background = '#FFFFDD';
					showlist.options[i].style.color = '#000';
				}	
				showlist.addEvent('change',function()
				{
					if(showlist.value == 0)
					{		
						showlist.style.background = '#CC0000';
						showlist.style.color = '#fff';
						JSNISPlgEditor.disableButton(true);					
					}else{
						if (showlist.value != 0 && showcase.value != 0)
						{
							JSNISPlgEditor.disableButton(false);
						}
						showlist.style.background = 'none';
						showlist.style.color = '#000';
					}
				});	
			}
			
			if (showcase != undefined)
			{
				var countShowcase = showcase.options.length;
				if(showcase.value == 0)
				{
					showcase.style.background = '#CC0000';
					showcase.style.color = '#fff';
					JSNISPlgEditor.disableButton(true);		
				}
				else
				{
					if (showlist != undefined && showlist.value && showcase.value)
					{
						JSNISPlgEditor.disableButton(false);
					}
					else
					{
						JSNISPlgEditor.disableButton(true);
					}
					showcase.style.background = '#FFFFDD';
					showcase.style.color = '#000';		
				}
				for(var i = 0; i < countShowcase; i++) 
				{				
					showcase.options[i].style.background = '#FFFFDD';
					showcase.options[i].style.color = '#000';
					
				}
				
				showcase.addEvent('change',function()
				{
					if(showcase.value == 0){
						showcase.style.background = '#CC0000';
						showcase.style.color = '#fff';
						JSNISPlgEditor.disableButton(true);					
					}else{
						if (showlist.value != 0 && showcase.value != 0)
						{
							JSNISPlgEditor.disableButton(false);
						}
						showcase.style.background = 'none';
						showcase.style.color = '#000';			
					}
				});	
			}
		},
		
		disableButton: function(status)
		{
			if (!status)
			{
				$('btn_insert_button').removeClass('disabled');
				$('btn_insert_button').disabled = false;
			}
			else
			{
				$('btn_insert_button').addClass('disabled');
				$('btn_insert_button').disabled = true;	
			}	
			
		}	
};

window.addEvent('domready', function(){
	JSNISPlgEditor.init();
});