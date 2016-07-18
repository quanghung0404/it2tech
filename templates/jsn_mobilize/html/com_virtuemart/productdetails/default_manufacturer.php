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
<div class="manufacturer">
    <?php
    $link = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $this->product->virtuemart_manufacturer_id . '&tmpl=component');
    $text = $this->product->mf_name;

    /* Avoid JavaScript on PDF Output */
    if (strtolower(JRequest::getWord('output')) == "pdf") {
	echo JHTML::_('link', $link, $text);
    } else {
	?>
        <span class="bold"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') ?></span><a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $link ?>"><?php echo $text ?></a>
    <?PHP } ?>
</div>