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
<?php if ($this->config->get('layout_composer_publishingdate')) { ?>
<div class="eb-composer-field row">
    <label
        for="publish_up"
        class="eb-composer-field-label col-sm-5"
        data-eb-provide="popover"
        data-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_PUBLISH_DATE'); ?>"
        data-content="<?php echo JText::_('COM_EASYBLOG_COMPOSER_PUBLISH_DATE_HELP'); ?>"
        data-placement="left"
        data-html="true"
    >
        <?php echo JText::_('COM_EASYBLOG_COMPOSER_PUBLISH_DATE'); ?>
    </label>

    <div class="eb-composer-field-content col-sm-7" data-publish>
        <span data-preview>
            <?php if ($post->publish_up && $post->publish_up != '0000-00-00 00:00:00') { ?>
                <?php echo $this->html('string.date', $post->getFormDateValue('publish_up'), JText::_('COM_EASYBLOG_DATE_DMY24H')); ?>
            <?php } else { ?>
                <?php echo JText::_('COM_EASYBLOG_COMPOSER_IMMEDIATELY');?>
            <?php } ?>
        </span>

        <a href="javascript:void(0);" class="btn btn-xs btn-default" 
            data-calendar 
            data-eb-provide="popover" 
            data-placement="left" 
            data-content="<?php echo JText::_('COM_EASYBLOG_COMPOSER_PUBLISH_DATE_SELECT_DATE'); ?>"
        >
            <i class="fa fa-calendar"></i>
        </a>

        <a href="javascript:void(0);" class="btn btn-xs btn-default"  style="display: none;" 
            data-cancel
            data-eb-provide="popover" 
            data-placement="left" 
            data-content="<?php echo JText::_('COM_EASYBLOG_COMPOSER_PUBLISH_DATE_RESET_DATE'); ?>"
        >
            <i class="fa fa-close"></i>
        </a>

        <input type="hidden" name="publish_up" data-datetime value="<?php echo $post->getFormDateValue('publish_up'); ?>" />
    </div>
</div>
<?php } else { ?>
<input type="hidden" name="publish_up" data-datetime value="<?php echo $post->getFormDateValue('publish_up'); ?>" />
<?php } ?>