<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die;

?>
<div id="sigPro" class="sigProAddGallery J<?php echo $this->version; ?> sigModalAddGallery">
    <form action="index.php" method="post" name="adminForm" id="adminForm" target="_parent">
        <label for="folder" class="sigFloatLeft"><?php echo JText::_('COM_SIGPRO_FOLDER_NAME'); ?></label>
        <div class="sigProAddWrapper">
        	<input autocomplete="off" type="text" name="folder" id="folder" value="" size="50" maxlength="250" />
        	<a class="sigProButton sigProProceedButton" style="display: none;" href="#"><?php echo JText::_('COM_SIGPRO_PROCEED'); ?></a>
       		<span class="sigProValidation"></span> <input type="hidden" id="sigProValidationStatus" name="sigProValidationStatus" value="" />
		</div>
		<div class="clr"></div>
        <input type="hidden" name="option" value="com_sigpro" />
        <input type="hidden" name="view" value="gallery" />
        <input type="hidden" name="task" value="add" />
        <input type="hidden" name="tmpl" value="<?php echo JRequest::getCmd('parentTmpl'); ?>" />
        <input type="hidden" name="type" value="<?php echo $this->type; ?>" />
        <input type="hidden" name="editorName" value="<?php echo $this->editorName; ?>" />
    	<?php if($this->template): ?>
    	<input type="hidden" name="template" value="<?php echo $this->template; ?>" />
    	<?php endif; ?>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>