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
defined('_JEXEC') or die('Restricted access');
?>

<div id="fd" class="eb eb-mod mod-easyblogquickpost mod-items-compact<?php echo $params->get( 'moduleclass_sfx' ) ?>">
    <div class="mod-item">
       <a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=standard');?>">
            <i class="fa fa-pencil text-muted"></i>
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_STANDARD');?></span>
        </a>
    </div>

    <div class="mod-item">
        <a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=photo');?>">
            <i class="fa fa-camera text-muted"></i>
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_PHOTO');?></span>
        </a>
    </div>
    <div class="mod-item">
        <a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=video');?>">
            <i class="fa fa-video-camera text-muted"></i>
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_VIDEO');?></span>
        </a>
    </div>
    <div class="mod-item">
        <a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=quote');?>">
            <i class="fa fa-quote-left text-muted"></i>
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_QUOTE');?></span>
        </a>
    </div>
    <div class="mod-item">
        <a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=quickpost&type=link');?>">
            <i class="fa fa-link text-muted"></i>
            <span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TOOLBAR_QUICK_POST_LINK');?></span>
        </a>
    </div>
</div>

