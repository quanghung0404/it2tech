<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-discussions<?php echo $discussion->lock ? ' is-locked' : ''; ?>">
    <div class="es-content group-discussion-item<?php echo $answer ? ' is-resolved' : ''; ?><?php echo !$replies ? ' is-unanswered' : ''; ?><?php echo $discussion->lock ? ' is-locked' : ''; ?>"
        data-event-discussion-item
        data-eventid="<?php echo $event->id; ?>"
        data-id="<?php echo $discussion->id; ?>">
        <div class="es-content-wrap pl-10 pt-10">
            <div class="row">
                <div class="col-md-9">
                    <div class="mb-15">
                        <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'appId' => $app->getAlias())); ?>">&larr; <?php echo JText::_('COM_EASYSOCIAL_BACK_TO_DISCUSSIONS'); ?></a>
                    </div>

                    <div class="discussion-header">
                        <div class="discussion-avatar pull-left">
                            <img src="<?php echo $author->getAvatar(); ?>" title="<?php echo $this->html('string.escape', $author->getName()); ?>" class="es-avatar" data-popbox="module://easysocial/profile/popbox" data-user-id="<?php echo $author->id; ?>"/>
                        </div>

                        <h3 class="discussion-title pull-left">
                            <a href="<?php echo FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'discussionId' => $discussion->id), false); ?>">
                                <?php echo $discussion->get('title'); ?>
                            </a>
                            <div class="discussion-status">
                                <span class="label label-success label-resolved"><?php echo JText::_('APP_EVENT_DISCUSSIONS_RESOLVED'); ?></span>
                                <span class="label label-warning label-locked"><i class="fa fa-lock locked-icon"></i> <?php echo JText::_('APP_EVENT_DISCUSSIONS_LOCKED'); ?></span>
                                <span class="label label-danger label-unanswered"><?php echo JText::_('APP_EVENT_DISCUSSIONS_UNANSWERED'); ?></span>
                            </div>
                        </h3>

                        <?php if ($event->isAdmin() || $this->my->isSiteAdmin() || $discussion->created_by == $this->my->id) { ?>
                        <div class="pull-right btn-group">
                            <a class="dropdown-toggle_ btn btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
                                <i class="icon-es-dropdown"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-user messageDropDown">
                                <?php if ($event->isAdmin() || $this->my->isSiteAdmin()) { ?>
                                <li class="discussion-unlock-action">
                                    <a href="javascript:void(0);" data-discussion-unlock><?php echo JText::_('APP_EVENT_DISCUSSIONS_UNLOCK'); ?></a>
                                </li>
                                <li class="discussion-lock-action">
                                    <a href="javascript:void(0);" data-discussion-lock><?php echo JText::_('APP_EVENT_DISCUSSIONS_LOCK'); ?></a>
                                </li>
                                <?php } ?>
                                <li>
                                    <a href="<?php echo FRoute::apps(array('layout' => 'canvas', 'customView' => 'edit', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'discussionId' => $discussion->id), false); ?>">
                                        <?php echo JText::_('APP_EVENT_DISCUSSIONS_EDIT'); ?>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="javascript:void(0);" data-discussion-delete><?php echo JText::_('APP_EVENT_DISCUSSIONS_DELETE'); ?></a>
                                </li>
                            </ul>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="discussion-content mb-20">
                        <?php echo $discussion->getContent(); ?>
                    </div>

                    <div class="discussion-replies<?php echo !$replies ? ' is-empty' : ''; ?>" data-replies-wrapper>
                        <div class="clearfix">
                            <h4 class="pull-left"><?php echo JText::_('APP_EVENT_DISCUSSIONS_REPLIES'); ?> (<span data-reply-count><?php echo $discussion->total_replies; ?></span>)</h4>
                            <?php if ($event->getGuest()->isGuest()) { ?>
                            <a href="<?php echo FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'discussionId' => $discussion->id), false); ?>#reply" class="pull-right btn btn-es-success btn-small mt-5 btn-post-reply"><?php echo JText::_('APP_EVENT_DISCUSSIONS_POST_REPLY'); ?> <i class="fa fa-share-2 "></i></a>
                            <?php } ?>
                        </div>
                        <hr />

                        <ul class="fd-reset-list replies" data-reply-list>
                            <?php foreach ($replies as $reply) { ?>
                                <?php echo $this->loadTemplate('apps/event/discussions/canvas/item.reply', array('reply' => $reply, 'answer' => $answer, 'event' => $event, 'question' => $discussion, 'files' => $files)); ?>
                            <?php } ?>
                        </ul>

                        <div class="empty">
                            <?php echo JText::_('APP_EVENT_DISCUSSIONS_REPLIES_EMPTY'); ?>
                        </div>

                        <?php if ($event->getGuest()->isGuest()) { ?>
                        <div class="discussion-response-form">
                            <h4><?php echo JText::_('APP_EVENT_DISCUSSIONS_YOUR_RESPONSE'); ?></h4>
                            <hr />
                            <a id="reply"></a>

                            <form data-reply-form class="reply-form">
                                <div class="alert alert-dismissable alert-error alert-empty">
                                    <button type="button" class="close" data-bs-dismiss="alert">×</button>
                                    <?php echo JText::_('APP_EVENT_DISCUSSIONS_EMPTY_REPLY_ERROR'); ?>
                                </div>

                                <?php echo FD::bbcode()->editor('reply_content', '', array('files' => $files, 'uid' => $event->id, 'type' => SOCIAL_TYPE_EVENT, 'controllerName' => 'events'), array('data-reply-content' => '')); ?>

                                <div class="form-actions">
                                    <button type="button" class="pull-right btn btn-es-primary btn-large" data-reply-submit><?php echo JText::_('APP_EVENT_DISCUSSIONS_SUBMIT_REPLY'); ?> &rarr;</a>
                                </div>
                            </form>

                            <div class="locked-form">
                                <i class="fa fa-lock"></i>
                                <?php echo JText::_('APP_EVENT_DISCUSSIONS_IS_LOCKED'); ?>
                            </div>
                        </div>
                        <?php } ?>

                    </div>

                </div>


                <div class="col-md-3 discussion-meta">

                    <?php if ($event->getGuest()->isGuest()) { ?>
                    <div class="mt-5">
                        <a href="<?php echo FRoute::apps(array('layout' => 'canvas', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'customView' => 'create')); ?>"
                            class="btn btn-es-primary btn-create"><i class="fa fa-pencil"></i>&nbsp; <?php echo JText::_('APP_EVENT_DISCUSSIONS_CREATE_DISCUSSION'); ?> &rarr;</a>
                    </div>
                    <?php } ?>

                    <?php if ($params->get('stats_sidebar', true)) { ?>
                    <div class="stats">
                        <h4><?php echo JText::_('APP_EVENT_DISCUSSIONS_STATISTICS'); ?></h4>
                        <hr />
                        <ul class="list-unstyled">
                            <li>
                                <i class="fa fa-user"></i> <?php echo JText::sprintf('APP_EVENT_DISCUSSIONS_STARTED_BY', $this->html('html.user', $author->id)); ?>
                            </li>
                            <li>
                                <i class="fa fa-calendar"></i> <?php echo FD::date($discussion->created)->format(JText::_('DATE_FORMAT_LC1')); ?>
                            </li>
                            <li>
                                <i class="fa fa-eye"></i> <?php echo JText::sprintf(FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_HITS', $discussion->hits), $discussion->hits); ?>
                            </li>
                            <li>
                                <i class="fa fa-users"></i> <?php echo JText::sprintf(FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_PARTICIPANTS', count($participants)), count($participants)); ?>
                            </li>
                            <li>
                                <i class="fa fa-comments"></i> <?php echo JText::sprintf(FD::string()->computeNoun('APP_EVENT_DISCUSSIONS_TOTAL_REPLIES', $discussion->total_replies), '<span data-reply-count>' . $discussion->total_replies . '</span>'); ?>
                            </li>

                            <?php if ($answer) { ?>
                            <li>
                                <i class="fa fa-fire"></i> <?php echo JText::sprintf('APP_EVENT_DISCUSSIONS_ANSWERED_BY', '<a href="#reply-' . $answer->id . '">' . $answer->author->getName() . '</a>'); ?>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>

                    <?php if ($params->get('participants_sidebar', true)) { ?>
                    <div class="participants">
                        <h4><?php echo JText::_('APP_EVENT_DISCUSSIONS_PARTICIPANTS'); ?></h4>
                        <hr />

                        <div class="es-widget">
                            <div class="es-widget-body">

                                <ul class="list-unstyled widget-list-grid">
                                    <?php foreach ($participants as $participant) { ?>
                                    <li>
                                        <div class="es-avatar-wrap">
                                            <a href="<?php echo $participant->getPermalink(); ?>"
                                                class="es-avatar es-avatar-sm"
                                                data-popbox="module://easysocial/profile/popbox"
                                                data-user-id="<?php echo $participant->id; ?>"
                                            >
                                                <img src="<?php echo $participant->getAvatar(); ?>" title="<?php echo $this->html('string.escape', $participant->getName()); ?>" />
                                            </a>
                                        </div>
                                    </li>
                                    <?php } ?>
                                </ul>

                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>

            </div>
        </div>
    </div>
</div>
