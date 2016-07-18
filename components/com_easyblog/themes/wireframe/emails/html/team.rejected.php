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
        <?php echo JText::_('COM_EASYBLOG_NOTIFICATION_TEAMBLOG_REJECTED'); ?>
    </td>
</tr>

<tr>
    <td style="background: #fff; padding: 40px 100px; text-align: center">
        <?php echo JText::sprintf('COM_EASYBLOG_NOTIFICATION_TEAMBLOG_REQUEST_REJECTED', '<a href="' . $teamLink . '" style="font-weight:bold;color:#477fda;text-decoration:none">' . $teamName . '</a>');?>

        <div style="padding-top: 30px;">
            <a href="<?php echo $teamLink;?>" style="text-decoration: none; font-weight: bold; color: #247acf; font-size: 24px; letter-spacing: -.5px;"><?php echo $teamName;?></a>
        </div>
    </td>
</tr>

<tr>
    <td style="background: #fff; padding: 0 40px 40px; text-align: center">
        <div style="padding: 50px; background: #fafafa;">
            <img src="<?php echo $teamAvatar;?>" width="60" height="60" style="border-radius: 100%; display: inline-block;" />
            <div style="padding-top: 20px 0 40px;">
                <a href="<?php echo $teamLink;?>" style="text-decoration: none; font-weight: bold; color: #247acf;"><?php echo $teamName;?></a>
            </div>
            <div style="padding: 40px 30px">
                <?php echo $teamDescription; ?>
            </div>
            <div>
                <a href="<?php echo $teamLink;?>" style="
                    background: #3ab54a;
                    display: inline-block;
                    line-height: 50px;
                    text-decoration: none;
                    font-weight: bold;
                    color: #fff;
                    width: 160px;
                    border-radius: 3px;
                    margin: 0 5px;
                "><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_VIEW_TEAM');?></a>
            </div>
        </div>
    </td>
</tr>