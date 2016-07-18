<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-widget">
    <div class="es-widget-head">
        <div class="pull-left widget-title">
            <?php echo JText::_('APP_GROUP_MEMBERS_WIDGET_MEMBERS_TITLE'); ?>
            
        </div>
        <span class="widget-label">(<?php echo $group->getTotalMembers();?>)</span>

        <?php if ($members) { ?>
            <a class="fd-small pull-right" href="<?php echo $link;?>"><?php echo JText::_('APP_GROUP_MEMBERS_WIDGET_VIEW_ALL');?></a>
        <?php } ?>
    </div>
    <div class="es-widget-body">
        <ul class="widget-list-grid">
            <?php foreach ($members as $member) { ?>
                <li><?php echo $this->html('html.user', $member, true, 'top-left', true); ?></li>
            <?php } ?>
        </ul>
    </div>
</div>
