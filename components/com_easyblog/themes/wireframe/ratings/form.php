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
<form id="<?php echo $elementId; ?>-form"<?php echo $entry ? ' itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"' : '';?>
    class="eb-rating-form<?php echo $voted ? ' voted' : '';?>"
    data-rating-form
    data-locked="<?php echo $locked ? 1 : 0;?>"
    data-id="<?php echo $uid;?>"
    data-type="<?php echo $type;?>"
>
    <?php if ($entry) { ?>
    <meta itemprop="ratingValue" content="<?php echo round($rating / 2, 2);?>" />
    <meta itemprop="worstRating" content="1">
    <meta itemprop="bestRating" content="5">
    <?php } ?>

    <?php if ($voted) { ?>
    <div class="col-cell eb-rating-text voted"><?php echo JText::_('COM_EASYBLOG_RATINGS_ALREADY_RATED');?>:</div>
    <?php } ?>

    <?php if (!$locked) { ?>
    <div id="<?php echo $elementId; ?>-command" class="col-cell eb-rating-text" data-rating-text><?php echo $text;?>:</div>
    <?php } ?>

    <div id="<?php echo $elementId; ?>" class="col-cell eb-rating-stars star-location<?php echo $voted ? ' voted' : '';?>" data-type="<?php echo $type;?>" data-id="<?php echo $uid;?>">
        <input type="radio" name="newrate" value="1" title="Very poor"<?php echo ($rating == 1 ) ? ' checked="checked"' : '';?> class="odd" />
        <input type="radio" name="newrate" value="2" title="Poor"<?php echo ($rating == 2 ) ? ' checked="checked"' : '';?> />
        <input type="radio" name="newrate" value="3" title="Not that bad"<?php echo ($rating == 3 ) ? ' checked="checked"' : '';?> />
        <input type="radio" name="newrate" value="4" title="Fair"<?php echo ($rating == 4 ) ? ' checked="checked"' : '';?> />
        <input type="radio" name="newrate" value="5" title="Average"<?php echo ($rating == 5 ) ? ' checked="checked"' : '';?> />
        <input type="radio" name="newrate" value="6" title="Almost good"<?php echo ($rating == 6 ) ? ' checked="checked"' : '';?> />
        <input type="radio" name="newrate" value="7" title="Good"<?php echo ($rating == 7 ) ? ' checked="checked"' : '';?> />
        <input type="radio" name="newrate" value="8" title="Very good"<?php echo ($rating == 8 ) ? ' checked="checked"' : '';?> />
        <input type="radio" name="newrate" value="9" title="Excellent"<?php echo ($rating == 9 ) ? ' checked="checked"' : '';?> />
        <input type="radio" name="newrate" value="10" title="Perfect"<?php echo ($rating == 10 ) ? ' checked="checked"' : '';?> />
        <input type="hidden" id="<?php echo $elementId; ?>-uid" value="<?php echo $uid;?>" />
        <input type="hidden" id="<?php echo $elementId; ?>-rating" value="<?php echo $rating;?>"/>
    </div>

    <div class="col-cell eb-rating-voters">
        <?php if ($this->config->get('main_ratings_display_raters')) { ?>
        <a class="eb-rating-link" href="javascript:void(0);" data-rating-voters>
        <?php } ?>

            <b class="eb-ratings-value" title="<?php echo JText::sprintf('COM_EASYBLOG_RATINGS_TOTAL_VOTES', $total, $this->getNouns('COM_EASYBLOG_RATINGS_VOTES_COUNT', $total));?>" data-rating-value>
                <span data-rating-total <?php echo $entry ? ' itemprop="ratingCount"' : '';?>><?php echo $total;?></span>
                <b title="<?php echo JText::_( 'COM_EASYBLOG_RATINGS_ALREADY_RATED' );?>">
                    <i class="fa fa-check"></i>
                </b>
            </b>

        <?php if ($this->config->get('main_ratings_display_raters')) { ?>
        </a>
        <?php } ?>
    </div>
</form>
