<?php
/**
 * @version    $Id: default_image.php 16394 2012-09-25 08:31:07Z giangnd $
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
$objPlgMediaHelper = new MediaHelper();
?>
<div class="item jsn-graphic">
	<a
		href="javascript:JSNISImageManager.populateFields('<?php echo $this->_tmp_img->path_relative; ?>')">
		<img
		src="<?php echo $this->baseURL.'/'.$this->_tmp_img->path_relative; ?>"
		class="jsn-graphic-showcase"
		width="<?php echo $this->_tmp_img->width_60; ?>"
		height="<?php echo $this->_tmp_img->height_60; ?>"
		alt="<?php echo $this->_tmp_img->name; ?> - <?php echo $objPlgMediaHelper->parseSize($this->_tmp_img->size); ?>" />
		<span> <?php echo $this->_tmp_img->name; ?> <br /> <?php echo $this->_tmp_img->width .'x'.$this->_tmp_img->height;?>
	</span> </a>
</div>
