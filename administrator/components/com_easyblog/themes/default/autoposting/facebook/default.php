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
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_SETTINGS'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_INFO'); ?></div>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_ENABLE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_ENABLE'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_facebook', $this->config->get('integrations_facebook')); ?>
                        </div>
                    </div>

                    <div class="form-group" data-facebook-api>
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="input-group input-group-link">
                                <input type="text" name="integrations_facebook_api_key" class="form-control" value="<?php echo $this->config->get('integrations_facebook_api_key');?>" size="60" />
                                <span class="input-group-btn">
                                    <a href="http://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-facebook-app" target="_blank" class="btn btn-default">
                                        <i class="fa fa-life-ring"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" data-facebook-secret>
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_SECRET_KEY'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_SECRET_KEY'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_SECRET_KEY_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="form-inline">
                                <div class="input-group input-group-link">
                                    <input type="text" name="integrations_facebook_secret_key" class="form-control" value="<?php echo $this->config->get('integrations_facebook_secret_key');?>" size="60" />
                                    <span class="input-group-btn">
                                        <a href="http://stackideas.com/docs/easyblog/how-tos/setting-up-facebook-integration.html" target="_blank" class="btn btn-default">
                                            <i class="fa fa-life-ring"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_ACCESS'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_ACCESS'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_OAUTH_ALLOW_ACCESS_DESC', 'Facebook'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php if ($associated) { ?>
                                <div>
                                    <div style="margin-top:5px;">
                                        <?php echo $client->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=facebook', true);?>
                                    </div>

                                    <div style="margin:15px 0 8px 0;border: 1px dashed #d7d7d7;padding: 20px;">
                                        <p>
                                            <?php echo JText::_('COM_EASYBLOG_FACEBOOK_EXPIRE_TOKEN');?> <b><?php echo $expire; ?></b>.
                                        </p>

                                        <a href="javascript:void(0);" class="btn btn-default btn-sm" id="facebook-login">
                                            <i class="fa fa-refresh"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_FACEBOOK_RENEW_TOKEN');?>
                                        </a>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <?php echo $client->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=facebook', true);?>

                                <div class="mt-10">
                                    <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_ACCESS_DESC');?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_PAGES'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_PAGES_INFO'); ?></div>
                </div>

                <div class="panel-body">
                    <?php if ($associated) { ?>
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_PAGE_IMPERSONATION'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_PAGE_IMPERSONATION'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_PAGE_IMPERSONATION_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_facebook_impersonate_page', $this->config->get('integrations_facebook_impersonate_page')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_PAGE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_PAGE'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_PAGE_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php if ($pages) { ?>
                            <select name="integrations_facebook_page_id[]" class="form-control" multiple="multiple">
                                <?php foreach ($pages as $page) { ?>
                                <option value="<?php echo $page->id;?>" <?php echo ($storedPages && in_array($page->id, $storedPages)) ? ' selected="selected"' : '';?>>
                                    <?php echo $page->name;?>
                                </option>
                                <?php } ?>
                            </select>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="form-group">
                        <div>
                            <b><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_PAGES_UNAVAILABLE');?></b>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_GROUPS'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_GROUPS_INFO'); ?></div>
                </div>

                <div class="panel-body">
                    <?php if ($associated) { ?>
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_GROUP'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_GROUP'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_FACEBOOK_GROUP_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_facebook_impersonate_group', $this->config->get('integrations_facebook_impersonate_group')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_GROUPS'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_GROUPS'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_SELECT_GROUPS_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">

                            <?php if ($groups) { ?>
                            <select name="integrations_facebook_group_id[]" class="form-control" multiple="multiple" size="10">
                                <?php foreach ($groups as $group) { ?>
                                <option value="<?php echo $group->id;?>" <?php echo in_array($group->id, $storedGroups) ? ' selected="selected"' : '';?>>
                                    <?php echo $group->name;?>
                                </option>
                                <?php } ?>
                            </select>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="form-group">
                        <b><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_FACEBOOK_GROUPS_UNAVAILABLE');?></b>
                    </div>
                    <?php } ?>
                </div>
            </div>

        </div>

        <div class="col-lg-6">


            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_GENERAL'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_GENERAL_INFO'); ?></div>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_DESC', 'Facebook'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_facebook_centralized', $this->config->get('integrations_facebook_centralized')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_facebook_centralized_auto_post', $this->config->get('integrations_facebook_centralized_auto_post')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASUBLOG_AUTOPOST_ON_UPDATES'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASUBLOG_AUTOPOST_ON_UPDATES'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_UPDATES_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_facebook_centralized_send_updates', $this->config->get('integrations_facebook_centralized_send_updates')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_CONTENT_FROM'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_CONTENT_FROM'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_CONTENT_FROM_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <select name="integrations_facebook_source" class="form-control">
                                <option value="intro"<?php echo $this->config->get( 'integrations_facebook_source' ) == 'intro' ?  ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_INTROTEXT' ); ?></option>
                                <option value="content"<?php echo $this->config->get( 'integrations_facebook_source' ) == 'content' ?  ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_CONTENT' ); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_CONTENT_LENGTH'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_CONTENT_LENGTH'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_CONTENT_LENGTH_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="form-inline">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="integrations_facebook_blogs_length" class="form-control text-center" value="<?php echo $this->config->get('integrations_facebook_blogs_length');?>" size="5" />
                                        <span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_FACEBOOK_CHARACTERS');?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_FACEBOOK_ALLOW_AUTHOR_USE_OWN_FACEBOOK_ACCOUNT'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FACEBOOK_ALLOW_AUTHOR_USE_OWN_FACEBOOK_ACCOUNT'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT_DESC', 'Facebook'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_facebook_centralized_and_own', $this->config->get('integrations_facebook_centralized_and_own')); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php echo $this->html('form.action'); ?>
</form>
