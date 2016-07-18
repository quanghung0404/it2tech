<?php
/**
 * @version    $Id: default_folder.php 16394 2012-09-25 08:31:07Z giangnd $
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
$act = JRequest::getCmd('act','custom');
?>
<div class="item">
	<a
		href="index.php?option=com_imageshow&amp;controller=media&amp;view=imageslist&amp;act=<?php echo $act;?>&amp;tmpl=component&amp;event=loadMediaImagesList&amp;theme=<?php echo $this->_showcaseThemeName; ?>&amp;folder=<?php echo $this->_tmp_folder->path_relative; ?>">
		<img
		src="<?php echo dirname(JURI::base()); ?>/media/media/images/folder.gif"
		width="80" height="80" alt="<?php echo $this->_tmp_folder->name; ?>" />
		<span><?php echo $this->_tmp_folder->name; ?> </span> </a>
</div>
