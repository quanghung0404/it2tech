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
        <?php echo JText::_('COM_EASYBLOG_NOTIFICATION_REJECTED_BLOG_POST'); ?>
    </td>
</tr>

<tr>
    <td style="background: #fff; padding: 40px 100px; text-align: center">
        <?php echo JText::sprintf('COM_EASYBLOG_NOTIFICATION_NEW_BLOG_REJECTED', $blogTitle);?>

        <div style="padding-top: 30px;">
            <a href="<?php echo $blogLink;?>" style="text-decoration: none; font-weight: bold; color: #247acf; font-size: 24px; letter-spacing: -.5px;"><?php echo $blogTitle;?></a>
        </div>

    </td>
</tr>

<tr>
    <td style="background: #fff; padding: 0 40px 40px; text-align: center">
        <div style="padding: 50px; background: #fafafa;">
        	<?php if ($rejectMessage) { ?>
        	<div>
        		<?php echo JText::_('COM_EASYBLOG_NOTIFICATION_NEW_BLOG_REJECTED_REASON'); ?>
        	</div>
            <div style="padding: 40px 30px">
                <?php echo $rejectMessage; ?>
            </div>
            <?php } ?>

            <div>
                <a href="<?php echo $blogEditLink;?>" style="
                    background: #3ab54a;
                    display: inline-block;
                    line-height: 50px;
                    text-decoration: none;
                    font-weight: bold;
                    color: #fff;
                    width: 160px;
                    border-radius: 3px;
                    margin: 0 5px;
                "><?php echo JText::_('COM_EASYBLOG_NOTIFICATION_REVIEW_YOUR_POST');?></a>
            </div>
        </div>
    </td>
</tr>