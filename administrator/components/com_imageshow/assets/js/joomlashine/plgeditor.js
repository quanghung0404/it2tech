/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: plgeditor.js 13761 2012-07-04 04:35:59Z giangnd $
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
					$('showlist-icon-warning').setStyle('display', '');	
					$('showlist-icon-edit').setStyle('display', 'none');
					$('jsn-link-edit-showlist').href= "javascript: void(0);";
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
					$('showlist-icon-warning').setStyle('display', 'none');
					$('showlist-icon-edit').setStyle('display', '');
					$('jsn-link-edit-showlist').href ="index.php?option=com_imageshow&controller=showlist&task=edit&cid[]="+showlist.value;
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
						$('jsn-showlist-icon-warning').addClass('show-icon-warning');
						$('showlist-icon-warning').setStyle('display', '');	
						$('showlist-icon-edit').setStyle('display', 'none');
						$('jsn-link-edit-showlist').href= "javascript: void(0);";
						JSNISPlgEditor.disableButton(true);					
					}else{
						if (showlist.value != 0 && showcase.value != 0)
						{
							JSNISPlgEditor.disableButton(false);
						}
						showlist.style.background = 'none';
						showlist.style.color = '#000';
						$('showlist-icon-warning').setStyle('display', 'none');	
						$('showlist-icon-edit').setStyle('display', '');
						$('jsn-link-edit-showlist').href= "index.php?option=com_imageshow&controller=showlist&task=edit&cid[]="+showlist.value;
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
					$('showcase-icon-warning').setStyle('display', '');
					$('showcase-icon-edit').setStyle('display', 'none');
					$('jsn-link-edit-showcase').href= "javascript: void(0);";
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
					$('showcase-icon-warning').setStyle('display', 'none');
					$('showcase-icon-edit').setStyle('display', '');
					$('jsn-link-edit-showcase').href= "index.php?option=com_imageshow&controller=showcase&task=edit&cid[]="+showcase.value;			
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
						$('showcase-icon-warning').setStyle('display', '');
						$('showcase-icon-edit').setStyle('display', 'none');
						$('jsn-link-edit-showcase').href= "javascript: void(0);";
						JSNISPlgEditor.disableButton(true);					
					}else{
						if (showlist.value != 0 && showcase.value != 0)
						{
							JSNISPlgEditor.disableButton(false);
						}
						showcase.style.background = 'none';
						showcase.style.color = '#000';
						$('showcase-icon-warning').setStyle('display', 'none');
						$('showcase-icon-edit').setStyle('display', '');
						$('jsn-link-edit-showcase').href= "index.php?option=com_imageshow&controller=showcase&task=edit&cid[]="+showcase.value;					
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