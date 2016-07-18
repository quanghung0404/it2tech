<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="fd" class="es mod-es-recent-polls<?php echo $suffix;?> es-responsive">
    <div class="es-poll-results">
    <?php if ($polls) { ?>
        <?php foreach($polls as $poll) { ?>
        <?php
            $pollAuthor = ES::user($poll->created_by);
            $timeLapsed = ES::date($poll->created)->toLapsed();
        ?>
        <div class="es-poll-result">
            <a href="<?php echo FRoute::stream(array('layout' => 'item', 'id' => $poll->uid)); ?>" class="es-poll-result__title"><?php echo $poll->title; ?></a>

            <?php if ($params->get('display_pollitems', true)) { ?>
                <?php foreach ($poll->items as $item) { ?>
                <div class="es-poll-result__item">
                    <div class="es-poll-result__item__title">
                        <?php echo $item->value; ?> <span>(<?php echo $item->percentage; ?>%)</span>
                    </div>
                    <?php if ($params->get('display_pollitems_scorebar', true)) { ?>
                        <div class="es-polls-progress progress">
                            <div style="width: <?php echo $item->percentage; ?>%" class="progress-bar progress-bar-success"></div>
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
            <?php } ?>
            <div class="media">
                <?php if ($params->get('display_author', true)) { ?>
                    <div class="media-object pull-left">
                        <div class="es-avatar es-avatar-xs">
                            <a href="<?php echo $pollAuthor->getPermalink(); ?>"><img alt="<?php echo $pollAuthor->getName(); ?>" src="<?php echo $pollAuthor->getAvatar(SOCIAL_AVATAR_SMALL);?>"></a>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($params->get('display_author', true) || $params->get('display_createdate', true)) { ?>
                    <?php
                        $metaContent = '';
                        if ($params->get('display_author', true) && $params->get('display_createdate', true)) {
                            $metaContent = JText::sprintf('MOD_EASYSOCIAL_RECENTPOLLS_CREATED_BY_TIME_LAPSED', $pollAuthor->getPermalink(), $pollAuthor->getName(), $pollAuthor->getName(), $timeLapsed);
                        } else if ($params->get('display_author', true) && !$params->get('display_createdate', true)) {
                            $metaContent = JText::sprintf('MOD_EASYSOCIAL_RECENTPOLLS_CREATED_BY', $pollAuthor->getPermalink(), $pollAuthor->getName(), $pollAuthor->getName());
                        } else if (!$params->get('display_author', true) && $params->get('display_createdate', true)) {
                            $metaContent = JText::sprintf('MOD_EASYSOCIAL_RECENTPOLLS_CREATED_TIME_LAPSED', $timeLapsed);
                        }
                    ?>
                    <div class="media-body">
                        <?php echo $metaContent; ?>
                    </div>
                <?php } ?>
            </div>

        </div>

        <?php } ?>
    <?php } ?>
    </div>
</div>
