<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<select name="<?php echo $formElement;?>[<?php echo $field->id;?>]" class="form-control"<?php echo $params->get('multiple') ? ' multiple="multiple"' : '';?>>
    <?php foreach ($options as $option) { ?>
    <option value="<?php echo $option->value;?>"<?php echo in_array($option->value, $selected) ? ' selected="selected"' : '';?>><?php echo $option->title;?></option>
    <?php } ?>
</select>