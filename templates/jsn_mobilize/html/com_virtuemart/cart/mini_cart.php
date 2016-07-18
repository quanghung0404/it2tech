<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
	<a href="<?php echo $this->continue_link; ?>"><?php echo JText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a>
	<a style ="float:right;" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart'); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_SHOW') ?></a>
	<div class="clearbreak"></div>
