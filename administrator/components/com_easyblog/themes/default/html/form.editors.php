<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<select name="<?php echo $element;?>" class="form-control">
    <?php foreach ($editors as $editor) { ?>
        <option value="<?php echo $editor->value;?>"<?php echo $selected == $editor->value ? ' selected="selected"' : '';?>><?php echo $editor->text;?></option>
    <?php } ?>

    <?php if ($composer) { ?>
        <option value="composer"<?php echo $selected == 'composer' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_COMPOSER_EDITOR');?></option>
    <?php } ?>
</select>