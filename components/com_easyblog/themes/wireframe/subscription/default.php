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
defined('_JEXEC') or die('Unauthorized Access');
?>

<div class="eb-subscription">

    <?php if (in_array('site', $groups)) { ?>
    <div class="eb-subscribe row-table cell-top">
        <div class="col-cell eb-subscribe-thumb cell-tight">
            <i class="fa fa-globe"></i>
        </div>
        <div class="col-cell cell-ellipse eb-subscribe-details">
            <h3 class="reset-heading text-ellipsis"><?php echo JText::_('Site Subscription');?></h3>
            <p class="text-small text-muted">
                <?php echo JText::_('COM_EASYBLOG_SUBSCRIBED_ON');?> <?php echo $subscriptions['site'][0]->getSubscriptionDate()->format(JText::_('DATE_FORMAT_LC1'));?>
            </p>
            <a href="javascript:void(0);" data-blog-unsubscribe data-subscription-id="<?php echo $subscriptions['site'][0]->id;?>" data-return="<?php echo base64_encode(JRequest::getUri());?>">
                <?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE');?>
            </a>
        </div>
    </div>
    <?php } ?>

    <?php foreach ($groups as $group) { ?>

        <?php if ($group == 'site') { continue; } ?>
        
        <p class="eb-subscribe-header text-uppercase text-bold"><?php echo $group;?></p>

        <?php foreach ($subscriptions[$group] as $subscription) { ?>
        <div class="eb-subscribe row-table cell-top">
            <?php if ($subscription->object->objAvatar) { ?>
            <div class="col-cell eb-subscribe-thumb cell-tight">
                <a href="<?php echo $subscription->object->objPermalink;?>" class="eb-avatar" class="eb-avatar">
                    <img src="<?php echo $subscription->object->objAvatar;?>" />
                </a>
            </div>
            <?php } ?>

            <div class="col-cell cell-ellipse eb-subscribe-details">
                <h3 class="reset-heading text-ellipsis"><?php echo $subscription->object->title;?></h3>
                <p class="text-small text-muted">
                    <?php echo JText::_('COM_EASYBLOG_SUBSCRIBED_ON');?> <?php echo $subscription->getSubscriptionDate()->format(JText::_('DATE_FORMAT_LC1'));?>
                </p>
                <a href="javascript:void(0);" class="btn btn-sm btn-danger" data-blog-unsubscribe data-subscription-id="<?php echo $subscription->id;?>" data-return="<?php echo base64_encode(JRequest::getUri());?>">
                    <?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE');?>
                </a>
            </div>
        </div>
        <?php } ?>
        
    <?php } ?>

    <?php if (!$subscriptions) { ?>
    <div class="eb-empty">
        <?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_NO_SUBSCRIPTIONS_YET_CURRENTLY'); ?>
    </div>
    <?php } ?>
</div>
