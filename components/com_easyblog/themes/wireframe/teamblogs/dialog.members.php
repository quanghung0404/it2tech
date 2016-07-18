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
<dialog>
    <width>400</width>
    <height>400</height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function()
        {
            this.parent.close();
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_MEMBERS'); ?></title>
    <content>
        <div>
        <?php if ($members) { ?>
            <?php foreach ($members as $member) { ?>
                <div class="eb-stats-author row-table">
                    <a class="col-cell" href="<?php echo $member->getPermalink();?>" class="eb-avatar">
                        <img src="<?php echo $member->getAvatar(); ?>" width="50" height="50" alt="<?php echo $member->getName();?>" />
                    </a>
                    <div class="col-cell">
                        <b>
                            <a href="<?php echo $member->getPermalink();?>"><?php echo $member->getName();?></a>
                        </b>
                        <div>
                            <?php echo $this->getNouns('COM_EASYBLOG_AUTHOR_POST_COUNT', $member->getTotalPosts(), true); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        </div>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CLOSE_BUTTON'); ?></button>
    </buttons>
</dialog>
