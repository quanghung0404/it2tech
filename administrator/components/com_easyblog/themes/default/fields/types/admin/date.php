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
<div class="form-group">
    <label for="page_title" class="col-md-5">
        <?php echo JText::_('COM_EASYBLOG_FIELDS_DEFAULT_VALUE'); ?>

        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_DEFAULT_VALUE'); ?>"
            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_DEFAULT_VALUE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
    </label>

    <div class="col-md-7">
        <div class="input-group date" data-date-picker>
            <input type="text" class="form-control" name="params[default]" value="<?php echo $params->get('default');?>" />
            <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </span>
        </div>
    </div>
</div>
