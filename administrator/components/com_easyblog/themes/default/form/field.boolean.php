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

$booleanKey = ($prefix) ? $prefix . $field->attributes->name : 'params[' . $field->attributes->name . ']';
$checked = $params->get($prefix . $field->attributes->name, $default);
?>
<div class="btn-group-yesno"
    data-bp-toggle="radio-buttons"
    data-eb-provide="popover"
    data-content=""
    data-original-title=""
    data-placement="">

<?php foreach ($field->options as $option) {
    if ($skipEmpty && ($option->value < 0)) { continue; }

    $className = "btn-yes";
    $toggleValue = $option->value;
    $label = JText::_('COM_EASYBLOG_GRID_YES');

    if ($option->value == -1) {
        $className = 'btn-inherit';
        $label = JText::_('COM_EASYBLOG_GRID_INHERIT');
    } else if ($option->value == 0) {
        $className = 'btn-no';
        $label = JText::_('COM_EASYBLOG_GRID_NO');
    }
?>
    <button type="button" class="btn <?php echo $className; echo ($checked == $option->value) ? ' active' : '';?>" data-bp-toggle-value="<?php echo $option->value; ?>"><?php echo $label; ?></button>
<?php } ?>
    <input type="hidden" id="<?php echo $booleanKey; ?>" name="<?php echo $booleanKey ;?>" value="<?php echo $checked; ?>" />
</div>

