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
<div class="es-content" data-group-news-item data-id="<?php echo $news->id; ?>" data-group-id="<?php echo $event->id; ?>">
    <div class="es-content-wrap">
        <div class="app-news app-group">

            <div class="mb-5">
                <a href="<?php echo FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'appId' => $app->getAlias())); ?>">&larr; <?php echo JText::_('COM_EASYSOCIAL_BACK_TO_NEWS'); ?></a>
            </div>

            <div class="fd-cf group-news-item">

                    <div class="fd-cf ">
                        <h3 class="pull-left">
                            <a href="<?php echo FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'newsId' => $news->id), false); ?>"><?php echo $news->get('title'); ?></a>
                        </h3>
                        <div class="pull-right btn-group mt-20">
                            <a class="dropdown-toggle_ btn btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
                                <i class="icon-es-dropdown"></i>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo FRoute::apps(array('layout' => 'canvas', 'customView' => 'form', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'newsId' => $news->id)); ?>">
                                        <?php echo JText::_('APP_EVENT_NEWS_EDIT_ITEM'); ?>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="javascript:void(0);" data-news-delete><?php echo JText::_('APP_EVENT_NEWS_DELETE_ITEM'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <?php if ($params->get('display_author', true) || $params->get('display_hits', true) || $params->get('display_date', true)) { ?>
                    <div class="group-news-meta">
                        <ul class="fd-reset-list">
                            <?php if ($params->get('display_author', true)) { ?>
                            <li>
                                <i class="fa fa-user"></i> <a href="<?php echo $author->getPermalink(); ?>"><?php echo $author->getName(); ?></a>
                            </li>
                            <?php } ?>

                            <?php if ($params->get('display_date', true)) { ?>
                            <li>
                                <i class="fa fa-calendar"></i> <?php echo FD::date($news->created)->format(JText::_('DATE_FORMAT_LC')); ?>
                            </li>
                            <?php } ?>

                            <?php if ($params->get('display_hits', true)) { ?>
                            <li>
                                <i class="fa fa-eye"></i> <?php echo JText::sprintf(FD::string()->computeNoun('APP_EVENT_NEWS_HITS', $news->hits), $news->hits); ?>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>

                    <div class="news-snippet">
                        <?php echo $news->content; ?>
                    </div>

                    <div class="group-news-actions">
                        <div class="es-action-wrap pl-0">
                            <ul class="fd-reset-list es-action-feedback">
                                <li>
                                    <a href="javascript:void(0);" class="fd-small"><?php echo $likes->button(); ?></a>
                                </li>
                                <li>
                                    <?php echo FD::sharing(array('url' => FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT, 'id' => $app->getAlias(), 'articleId' => $news->id), false), 'display' => 'dialog', 'text' => JText::_('COM_EASYSOCIAL_STREAM_SOCIAL'), 'css' => 'fd-small'))->getHTML(false); ?>
                                </li>
                            </ul>
                        </div>

                        <div data-news-counter class="es-stream-counter<?php echo empty($likes->data) ? ' hide' : ''; ?>">
                            <div class="es-stream-actions"><?php echo $likes->toHTML(); ?></div>
                        </div>

                        <div class="es-stream-actions">
                            <?php if ($params->get('allow_comments', true) && $news->comments) { ?>
                                <?php echo $comments->getHTML(array('hideEmpty' => false)); ?>
                            <?php } ?>
                        </div>

                    </div>


            </div>
        </div>
    </div>
</div>
