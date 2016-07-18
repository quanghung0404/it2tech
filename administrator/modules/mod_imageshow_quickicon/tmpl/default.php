<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: default.php 6505 2011-05-31 11:01:31Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
$lang 			= JFactory::getLanguage();
$document 		= JFactory::getDocument();
$lang->load('mod_imageshow_quickicon');
$document->addStyleSheet(JURI::root(true).'/administrator/modules/mod_imageshow_quickicon/assets/css/mod_imageshow_quickicon.css');
$buttons 		= modImageShowQuickIconHelper::getButtons();
?>
<div class="row-striped">
	<div class="row-fluid">
		<div class="span12">
			<?php
			foreach ($buttons as $button)
			{
				echo modImageShowQuickIconHelper::button($button);
			}
			?>
		</div>
	</div>
</div>