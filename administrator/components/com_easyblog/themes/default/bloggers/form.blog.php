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
<div class="row">
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGER_BLOG_SETTINGS');?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGER_BLOG_SETTINGS_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_AVATAR'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_AVATAR'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_AVATAR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-9">
                        <img id="user-avatar" src="<?php echo $author->getAvatar();?>" style="border: 1px solid #eee;" />
                        <?php if ($this->config->get('layout_avatar') && $this->config->get('layout_avatarIntegration') == 'default') { ?>
                            <input type="file" name="avatar" id="avatar" style="display: block;" size="65" />
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PERMALINK'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PERMALINK'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PERMALINK_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-9">
                        <input class="form-control" type="text" name="user_permalink" id="user_permalink" value="<?php echo $this->html('string.escape', $author->permalink);?>" />
                        <div class="small"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_EDIT_PERMALINK_USAGE' ); ?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PAGE_TITLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PAGE_TITLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PAGE_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-9">
                        <input class="form-control" id="title" name="title" value="<?php echo $this->html('string.escape', $author->title);?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PAGE_DESCRIPTION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PAGE_DESCRIPTION'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PAGE_DESCRIPTION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-9">
                        <textarea name="description" class="form-control"><?php echo $author->getDescription(true);?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_WEBSITE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_WEBSITE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_WEBSITE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-9">
                        <input class="form-control" type="text" name="url" id="url" value="<?php echo $this->html('string.escape', $author->url);?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BLOG_THEMES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BLOG_THEMES'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BLOG_THEMES');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>
                    
                    <div class="col-md-8">
                        <select name="theme" class="form-control">
                            <option value="global"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BLOG_THEMES_THEME_GLOBAL');?></option>
                            <?php foreach ($multithemes->availableThemes as $theme) { ?>
                                <option value="<?php echo $theme;?>"<?php echo $multithemes->selectedTheme == $theme ? ' selected="selected"' : '';?>><?php echo ucfirst($theme);?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NICKNAME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NICKNAME'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NICKNAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-9">
                        <input class="form-control" type="text" id="nickname" name="nickname" value="<?php echo $this->html('string.escape', $author->nickname);?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BIOGRAPHY_INFO'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BIOGRAPHY_INFO'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BIOGRAPHY_INFO_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-9">
                        <?php echo $editor->display('biography', $author->getBiography(true) , '100%', '200', '10', '10' , array('pagebreak','ninjazemanta','image','readmore' , 'article') ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_FACEBOOK');?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_FACEBOOK_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?>"
                            data-content="<?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ALLOW_ACCESS_DESC', 'Facebook');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php if ($facebook->id) { ?>
                            <div>
                                <?php echo $facebookClient->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
                            </div>
                            <div><?php echo JText::_( 'COM_EASYBLOG_INTEGRATIONS_FACEBOOK_REVOKE_ACCESS_DESC' );?></div>
                        <?php } else { ?>
                            <div><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_ACCESS_DESC');?></div>

                            <?php echo $facebookClient->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?>"
                            data-content="<?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES_DESC', 'Facebook');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'integrations_facebook_auto', $facebook->auto); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_TWITTER');?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_TWITTER_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?>"
                            data-content="<?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ALLOW_ACCESS_DESC', 'Twitter');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php if ($twitter->id) { ?>
                            <div>
                                <?php echo $twitterClient->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
                            </div>
                        <?php } else { ?>
                            <div><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_ACCESS_DESC');?></div>

                            <?php echo $twitterClient->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_TWITTER_MESSAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_TWITTER_MESSAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_TWITTER_MESSAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <textarea id="integrations_twitter_message" name="integrations_twitter_message" class="form-control"><?php echo (empty($twitter->message)) ? $this->config->get('main_twitter_message', 'Published a new blog entry title:{title} under category:{category}. {link}') : $twitter->message; ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?>"
                            data-content="<?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES_DESC', 'Facebook');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'integrations_twitter_auto', $twitter->auto); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_LINKEDIN');?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_SOCIAL_LINKEDIN_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_OAUTH_ALLOW_ACCESS'); ?>"
                            data-content="<?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ALLOW_ACCESS_DESC', 'Facebook');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php if ($linkedin->id) { ?>
                            <?php echo $linkedinClient->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
                        <?php } else { ?>
                            <div><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?></div>

                            <?php echo $linkedinClient->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=bloggers&layout=form&id=' . $user->id, false, $user->id);?>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES'); ?>"
                            data-content="<?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ENABLE_AUTO_UPDATES_DESC', 'LinkedIn');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'integrations_linkedin_auto', $linkedin->auto); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_PROTECTED_MODE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_PROTECTED_MODE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_PROTECTED_MODE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'integrations_linkedin_private', $linkedin->private); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
