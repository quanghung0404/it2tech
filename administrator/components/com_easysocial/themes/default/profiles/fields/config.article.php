<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$title = '';

if ($value) {
    $db = ES::db();
    $query = 'SELECT `title` FROM ' . $db->qn('#__content') . ' WHERE `id`=' . $db->Quote($value);

    $db->setQuery($query);
    $title = $db->loadResult();
}
?>
<span class="input-group">
    <input type="text" class="form-control" disabled="disabled" size="35" value="<?php echo $title;?>" placeholder="<?php echo JText::_('COM_EASYSOCIAL_SELECT_ARTICLE');?>" data-article-title />
    <div class="input-group-btn">
        <a href="javascript:void(0);" class="btn btn-es-primary" data-article-browser>
            <?php echo JText::_('COM_EASYSOCIAL_SELECT_ARTICLE'); ?>
        </a>
        <a href="javascript:void(0);" class="btn btn-es" data-article-remove>
            ×
        </a>
    </div>
</span>
<input type="hidden" 
    data-name="<?php echo $name;?>" 
    name="config_<?php echo $name;?>"
    id="config_<?php echo $name;?>" 
    value="<?php echo $value;?>" 

    data-fields-config-param 
    data-fields-config-param-field-<?php echo $name;?>
/>

