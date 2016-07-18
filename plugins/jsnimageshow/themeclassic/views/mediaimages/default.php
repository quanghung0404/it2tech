<?php
/**
 * @version    $Id: default.php 16394 2012-09-25 08:31:07Z giangnd $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
if (count($this->images) > 0 || count($this->folders) > 0) { ?>
<script type='text/javascript'>
		window.addEvent('domready', function()
		{
			var div_graphic = $$('div.jsn-graphic');
			div_graphic.addEvent('click', function()
			{
				div_graphic.removeClass('jsn-graphic-selected');
				this.addClass('jsn-graphic-selected');
			});
		});

		function setBGImageSelected()
		{
			var div = $$('div.jsn-graphic');
			var str = escape(window.parent.$('f_url').value);

			if (str != '')
			{
				div.each(function(element, i)
				{
					var src = element.getElementsByTagName('IMG')[0].src;

					if(src.indexOf(str) != -1 && str != '')
					{
						element.addClass('jsn-graphic-selected');
					}
				});
			}
		}

		setTimeout("setBGImageSelected()", 200);
	</script>
<div class="manager">
<?php for ($i=0,$n=count($this->folders); $i<$n; $i++) :
$objThemeMedia->setFolder($i, $this);
include(dirname(__FILE__).DS.'default_folder.php');
endfor; ?>

<?php for ($i=0,$n=count($this->images); $i<$n; $i++) :
$objThemeMedia->setImage($i, $this);
include(dirname(__FILE__).DS.'default_image.php');
endfor; ?>
</div>
<?php } else { ?>
<table width="100%" height="100%" border="0" cellpadding="0"
	cellspacing="0">
	<tr>
		<td>
			<div align="center"
				style="font-size: large; font-weight: bold; color: #CCCCCC; font-family: Helvetica, sans-serif;">
				<?php echo JText::_( 'No Images Found' ); ?>
			</div>
		</td>
	</tr>
</table>
				<?php } ?>
