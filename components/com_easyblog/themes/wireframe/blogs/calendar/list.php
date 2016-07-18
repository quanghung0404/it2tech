<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-calendar-topbar">
    <div class="eb-calendar-topbar__date">
        <?php echo $date->format('F');?>, <?php echo $date->format('Y');?>
    </div>
    <a href="<?php echo EBR::_('index.php?option=com_easyblog&view=calendar&calendar=1');?>" class="eb-calendar-topbar__toggle"><?php echo JText::_('COM_EASYBLOG_SWITCH_TO_CALENDAR_VIEW');?></a>
</div>

<div class="eb-calendar eb-calendar-list eb-responsive">
    <?php foreach ($posts as $post) { ?>
    <div class="eb-calendar__item">
        <a href="<?php echo $post->getPermalink(); ?>" class="eb-calendar__link">
            <i class="eb-calendar__item-icon fa fa-file-text text-muted"></i>
            <span class="eb-calendar__item-title"><?php echo $post->title;?></span>
            <span class="eb-calendar__item-date">
                <?php echo EB::date($post->created)->toFormat(JText::_('DATE_FORMAT_LC1')); ?>
            </span>
        </a>
    </div>
    <?php } ?>
</div>