<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

$category = JRequest::getInt('category_id');
?>

<div class="discuss-mod discuss-ask<?php echo $params->get('moduleclass_sfx') ?>">
    <?php if ($params->get('onlinestate', 1) && $work->enabled()) { ?>
        <div class="discuss-ask__header clearfix">
            <div class="pull-right">
                <?php echo JText::_('COM_EASYDISCUSS_SUPPORT_IS_CURRENTLY'); ?>
                <a class="discuss-ask-status <?php echo strtolower($status); ?>"><?php echo $status; ?></a>
            </div>
        </div>
    <?php } ?>

    <div class="discuss-ask__content">
        <a href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=ask&category=' . $category);?>">
        	<span><?php echo JText::_('MOD_ASK_POST_QUESTION');?></span>
        </a>
    </div>
    <?php if ($params->get('workschedule', 1) && $work->enabled()) { ?>
        <div class="discuss-ask__footer">
            <div class="discuss-ask--support" style="text-align:center;">
                <div class=""><?php echo JText::_('COM_EASYDISCUSS_WORK_OFFICIAL_WORKING_HOURS'); ?></div>
                <div class="">
                    <?php echo $options['workDayLabel']; ?> <?php echo ($options['workExceptionLabel']) ? $options['workExceptionLabel'] : ''; ?><br />
                    <?php echo $options['workTimeLabel']; ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
