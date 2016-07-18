<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="milestone-stream">
    <h4>
        <a href="<?php echo $permalink; ?>"><?php echo $milestone->title; ?></a>
    </h4>
    <div class="meta">
        <?php if ($milestone->user_id) { ?>
        <span class="mr-5"><i class="fa fa-user"></i> <?php echo JText::sprintf('APP_EVENT_TASKS_STREAM_RESPONSIBILITY_OF', $this->html('html.user', $milestone->user_id)); ?></span>
        <?php } ?>
        <span>
            <i class="fa fa-calendar"></i> <?php echo JText::sprintf('APP_EVENT_TASKS_DUE_ON', FD::date(strtotime($milestone->due))->format(JText::_('DATE_FORMAT_LC1'))); ?>
        </span>
    </div>
    <?php if ($milestone->description) { ?>
    <hr />
    <p><?php echo $milestone->getContent(); ?></p>
    <?php } ?>
</div>
