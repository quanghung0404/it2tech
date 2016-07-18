<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<tr>
    <td style="background: #55585d; color: #fff; line-height: 1; font-size: 18px; font-weight: bold; text-align: center; padding: 30px;">
        <?php echo JText::_('COM_EASYBLOG_NOTIFICATION_NEW_COMMENT_REQUIRES_MODERATION'); ?>
    </td>
</tr>

<tr>
    <td style="background: #fff; padding: 40px 100px; text-align: center">
        <?php echo JText::_('COM_EASYBLOG_NOTIFICATION_NEW_COMMENT_POSTED_REQUIRES_MODERATION');?>

        <div style="padding-top: 30px;">
            <a href="<?php echo $blogLink;?>" style="text-decoration: none; font-weight: bold; color: #247acf; font-size: 24px; letter-spacing: -.5px;"><?php echo $blogTitle;?></a>
        </div>
    </td>
</tr>

<tr>
    <td style="background: #fff; padding: 0 40px 40px; text-align: center">
        <div style="padding: 50px; background: #fafafa;">
            <img src="<?php echo $commentAuthorAvatar;?>" width="60" height="60" style="border-radius: 100%; display: inline-block;" />
            <div style="padding-top: 20px 0 40px;">
                <span style="text-decoration: none; font-weight: bold; color: #247acf;"><?php echo $commentAuthor;?></span>
                <div style="font-size: 14px; font-weight: bold; color: #888; letter-spacing: -.5px;">
                    <?php echo JText::_('COM_EASYBLOG_NOTIFICATION_ON');?> <?php echo $commentDate;?>
                </div>
            </div>
            <div style="padding: 40px 30px">
                <?php echo $commentContent; ?>
            </div>
            <div>
	            <a href="<?php echo $approveLink;?>" style="
	                background: #3ab54a;
	                display: inline-block;
	                line-height: 50px;
	                text-decoration: none;
	                font-weight: bold;
	                color: #fff;
	                width: 160px;
	                border-radius: 3px;
	                margin: 0 5px;
	            "><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_APPROVE_COMMENT');?></a>
	            <a href="<?php echo $rejectLink;?>" style="
	                background: #c3c1b5;
	                display: inline-block;
	                line-height: 50px;
	                text-decoration: none;
	                font-weight: bold;
	                color: #fff;
	                width: 160px;
	                border-radius: 3px;
	                margin: 0 5px;
	            "><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_REJECT_COMMENT');?></a>
            </div>
        </div>
    </td>
</tr>