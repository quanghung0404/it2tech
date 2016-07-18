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
<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<b><?php echo JText::_('COM_EASYBLOG_COMPOSER_PANEL_AUTHOR');?></b>
	</div>
	<div class="eb-composer-fieldset-content">
		<div class="eb-composer-field style-bordered">

            <div class="eb-sub-author current-user hide" data-eb-composer-current-author>
                

                <div class="row-table">
                    <div class="col-cell cell-avatar">
                        <img src="<?php echo $user->getAvatar();?>" class="avatar" width="50" height="50" />
                    </div>

                    <div class="col-cell">
                        <div class="eb-sub-author-details">
                            <div class="authorship-title text-muted text-uppercase text-small">
                                <?php echo JText::_('COM_EASYBLOG_COMPOSER_PANEL_AUTHOR_YOU_ARE');?>
                            </div>
                            <div><?php echo $user->getName();?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eb-sub-author current-author" data-eb-composer-author>
                

                <div class="row-table">
                    <div class="col-cell cell-avatar">
                        <img src="<?php echo $post->getAuthor()->getAvatar();?>" class="avatar" width="50" height="50" data-eb-composer-authoravatar />

                        <?php if ($post->isStandardSource() && !$contribution) { ?>
                        <img width="30" height="30" class="avatar hide" data-eb-composer-associateavatar />
                        <?php } ?>

                        <?php if ($post->isStandardSource() && $contribution) { ?>
                        <img width="30" height="30" class="avatar" data-eb-composer-associateavatar 
                            src="<?php echo $contribution->getAvatar();?>" />
                        <?php } ?>

                        <?php if (!$post->isStandardSource() && $post->getBlogContribution()) { ?>
                        <img width="30" height="30" class="avatar" data-eb-composer-associateavatar 
                            src="<?php echo $post->getBlogContribution()->getAvatar();?>" />
                        <?php } ?>

                    </div>
                    <div class="col-cell">
                        <div class="eb-sub-author-details"> 
                            <div class="authorship-title text-muted text-uppercase text-small">
                                <?php echo JText::_('COM_EASYBLOG_COMPOSER_PANEL_AUTHOR_POSTING_UNDER');?>
                            </div>
                            <div data-eb-composer-authorname><?php echo $post->getAuthor()->getName();?></div>
                            
                            <?php if ($post->isStandardSource() && !$contribution) { ?>
                            <div class="text-muted" data-eb-composer-source-name data-eb-composer-associatename></div>
                            <?php } ?>

                            <?php if ($post->isStandardSource() && $contribution) { ?>
                            <div class="text-muted" data-eb-composer-source-name data-eb-composer-associatename>
                                <?php echo $contribution->getTitle();?>
                            </div>
                            <?php } ?>

                            <?php if (!$post->isStandardSource() && $post->getBlogContribution()) { ?>
                            <div class="text-muted" data-eb-composer-source-name data-eb-composer-associatename>
                                <?php echo $post->getBlogContribution()->getTitle();?>
                            </div>
                            <?php } ?>

                            <form class="eb-composer-authorship" data-eb-composer-form="authorship">
                                <input type="hidden" name="created_by" id="created_by" value="<?php echo empty($author) ? $user->id : $author->id;?>" data-eb-composer-authorid />
                                <input type="hidden" name="source_id" id="source_id" value="<?php echo $post->source_id;?>" data-eb-composer-associateid />
                                <input type="hidden" name="source_type" id="source_type" value="<?php echo $post->source_type;?>" data-eb-composer-associatetype />
                            </form>
                        </div>
                    </div>
                    <?php if (EB::isSiteAdmin() || $this->acl->get('moderate_entry') || $author->hasTeams()) { ?>
                    <div class="col-cell cell-tight eb-sub-author-switch">
                        <a href="javascript:void(0);" data-eb-composer-switch-author><i class="fa fa-pencil"></i></a>
                    </div>
                    <?php } ?>
                </div>
            </div>                        

            <div class="eb-action-pick eb-composer-pick-author" data-eb-composer-author-picker>
                <div class="eb-action-pick-finder">

                    <ul class="eb-action-pick-tabs reset-list">
                        <?php if (EB::isSiteAdmin() || $this->acl->get('moderate_entry')) { ?>
                        <li class="active" data-author-type data-type="authors">
                            <a href="javascript:void(0);">
                                <?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTHORS');?>
                            </a>
                        </li>
                        <?php } ?>

                        <li class="<?php echo !EB::isSiteAdmin() && !$this->acl->get('moderate_entry') ? 'active' : '';?>" data-author-type data-type="associates">
                            <a href="javascript:void(0);">
                                <?php echo JText::_('COM_EASYBLOG_COMPOSER_ASSOCIATES');?>
                            </a>
                        </li>
                    </ul>

                    <div class="eb-action-pick-content tab-content">

                        <?php if (EB::isSiteAdmin() || $this->acl->get('moderate_entry')) { ?>
                        <div class="tab-pane in active" data-tab-content data-type="authors">
                            <div class="eb-composer-pick-form">
                                <div>
                                    <input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_PLACEHOLDER_FIND_A_PERSON', true);?>" data-author-search />
                                </div>
                                <div>
                                    <a class="btn btn-default" href="javascript:void(0);" data-author-search-cancel><?php echo JText::_('COM_EASYBLOG_COMPOSER_CANCEL');?></a>
                                </div>
                            </div>

                            <div class="eb-composer-pick-list" data-eb-composer-author-list>
                                <div class="loading-authors">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="tab-pane<?php echo !EB::isSiteAdmin() && !$this->acl->get('moderate_entry') ? ' active' : '';?>" data-tab-content data-type="associates">
                            <div class="eb-composer-pick-form">
                                <div>
                                    <input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_FIND_ASSOCIATES');?>" data-associates-search />
                                </div>
                                <div>
                                    <a class="btn btn-default" href="javascript:void(0);" data-associates-search-cancel><?php echo JText::_('COM_EASYBLOG_COMPOSER_CANCEL');?></a>
                                </div>
                            </div>
                            <div class="eb-composer-pick-list" data-eb-composer-associates-list>
                                <div class="loading-authors">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>