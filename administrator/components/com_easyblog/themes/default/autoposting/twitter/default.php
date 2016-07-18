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
<form name="adminForm" action="index.php" method="post" class="adminForm" id="adminForm">
    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_APP_SETTINGS'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_APP_SETTINGS_INFO');?></div>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_ENABLE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_ENABLE'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_twitter', $this->config->get('integrations_twitter')); ?>
                        </div>
                    </div>

                    <div class="form-group" data-twitter-api>
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_API_KEY'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_API_KEY'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_API_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="input-group input-group-link">
                                <input type="text" name="integrations_twitter_api_key" class="form-control" value="<?php echo $this->config->get('integrations_twitter_api_key');?>" />
                                <span class="input-group-btn">
                                    <a href="http://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-twitter-autoposting" target="_blank" class="btn btn-default">
                                        <i class="fa fa-life-ring"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" data-twitter-secret>
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_SECRET_KEY'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_SECRET_KEY'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_SECRET_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="input-group input-group-link">
                                <input type="text" name="integrations_twitter_secret_key" class="form-control" value="<?php echo $this->config->get('integrations_twitter_secret_key');?>" size="60" />
                                <span class="input-group-btn">
                                    <a href="http://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-twitter-autoposting" target="_blank" class="btn btn-default">
                                        <i class="fa fa-life-ring"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_TWITTER_ACCESS'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_TWITTER_ACCESS'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ALLOW_ACCESS_DESC', 'Twitter');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php if ($associated) { ?>
                                <?php echo $client->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=twitter', true);?>
                            <?php } else { ?>
                                <?php echo $client->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=twitter', true);?>
                                <div class="mt-10">
                                    <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_ACCESS_DESC');?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_INFO');?></div>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_ENABLE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_ENABLE'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_twitter_shorten_url', $this->config->get('integrations_twitter_shorten_url')); ?>
                        </div>
                    </div>

                    <div class="form-group" data-twitter-secret>
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_APIKEY'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_APIKEY'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_APIKEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="input-group input-group-link">
                                <input type="text" name="integrations_twitter_urlshortener_apikey" class="form-control" value="<?php echo $this->config->get('integrations_twitter_urlshortener_apikey');?>" size="60" />
                                <span class="input-group-btn">
                                    <a href="http://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-twitter-autoposting" target="_blank" class="btn btn-default">
                                        <i class="fa fa-life-ring"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_TWITTER_INFO');?></div>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_DESC', 'Twitter');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_twitter_centralized', $this->config->get('integrations_twitter_centralized')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_twitter_centralized_auto_post', $this->config->get('integrations_twitter_centralized_auto_post')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASUBLOG_AUTOPOST_ON_UPDATES'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASUBLOG_AUTOPOST_ON_UPDATES'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_UPDATES_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_twitter_centralized_send_updates', $this->config->get('integrations_twitter_centralized_send_updates')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_DESC', 'Twitter'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <textarea name="main_twitter_message" class="form-control"><?php echo $this->config->get('main_twitter_message', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_STRING'));?></textarea>
                            <br />
                            <div><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_MESSAGE_DESC');?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT', 'Twitter'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT', 'Twitter'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT_DESC', 'Twitter'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_twitter_centralized_and_own', $this->config->get('integrations_twitter_centralized_and_own')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $this->html('form.action'); ?>
</form>
