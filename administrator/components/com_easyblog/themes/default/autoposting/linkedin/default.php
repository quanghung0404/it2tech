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
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN_APP_SETTINGS'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN_APP_SETTINGS_INFO'); ?></div>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN_ENABLE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN_ENABLE'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_LINKEDIN_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_linkedin', $this->config->get('integrations_linkedin')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_API_KEY'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_API_KEY'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_API_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="input-group input-group-link">
                                <input type="text" name="integrations_linkedin_api_key" class="form-control" value="<?php echo $this->config->get('integrations_linkedin_api_key');?>" />
                                <span class="input-group-btn">
                                    <a href="http://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-linkedin-autoposting" target="_blank" class="btn btn-default">
                                        <i class="fa fa-life-ring"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_SECRET_KEY'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_SECRET_KEY'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_SECRET_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="input-group input-group-link">
                                <input type="text" name="integrations_linkedin_secret_key" class="form-control" value="<?php echo $this->config->get('integrations_linkedin_secret_key');?>" size="60" />
                                <span class="input-group-btn">
                                    <a href="http://stackideas.com/docs/easyblog/administrators/autoposting/setting-up-linkedin-autoposting" target="_blank" class="btn btn-default">
                                        <i class="fa fa-life-ring"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_ACCESS'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_ACCESS'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php if ($associated) { ?>
                                <div>
                                    <div style="margin-top:5px;">
                                        <?php echo $client->getRevokeButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=linkedin', true);?>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <?php echo $client->getLoginButton(rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=autoposting&layout=linkedin', true);?>

                                <div class="mt-10">
                                    <?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_ACCESS_DESC');?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_INFO'); ?></div>
                </div>

                <div class="panel-body">
                    <?php if ($companies && !empty($companies)){ ?>
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_SELECT_COMPANY'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_SELECT_COMPANY'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_SELECT_COMPANY');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <select name="integrations_linkedin_company[]" multiple style="height: 150px;">
                                <?php foreach ($companies as $company) { ?>
                                <option value="<?php echo $company->id;?>"<?php echo in_array($company->id , $storedCompanies ) ? ' selected="selected"' : '';?>><?php echo $company->title;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php } else { ?>
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_LINKEDIN_COMPANIES_UNAVAILABLE');?></b>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_GENERAL_TITLE'); ?></b>
                    <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_GENERAL_DESC');?></div>
                </div>

                <div class="panel-body">
                    <div class="form-group<?php echo $this->config->get('integrations_twitter_default') ? ' hide' : '';?>" data-twitter-secret>
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOSTING_CENTRALIZED'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_DESC', 'LinkedIn');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_linkedin_centralized', $this->config->get('integrations_linkedin_centralized')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_AUTOPOST_ON_NEW_POST_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_linkedin_centralized_auto_post', $this->config->get('integrations_linkedin_centralized_auto_post')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASUBLOG_AUTOPOST_ON_UPDATES'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASUBLOG_AUTOPOST_ON_UPDATES'); ?>"
                                data-content="<?php echo JText::_('COM_EASUBLOG_AUTOPOST_ON_UPDATES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_linkedin_centralized_send_updates', $this->config->get('integrations_linkedin_centralized_send_updates')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_DEFAULT_MESSAGE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_DEFAULT_MESSAGE'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SOCIALSHARE_LINKEDIN_DEFAULT_MESSAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <textarea name="main_linkedin_message" class="form-control" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('main_linkedin_message', JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_DEFAULT_MESSAGE_STRING'));?></textarea>
                            <div class="mt-10"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_MESSAGE_DESC');?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT', 'LinkedIn'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT', 'LinkedIn'); ?>"
                                data-content="<?php echo JText::sprintf('COM_EASYBLOG_INTEGRATIONS_CENTRALIZED_ALLOW_USER_OWN_ACCOUNT_DESC', 'LinkedIn');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'integrations_linkedin_centralized_and_own', $this->config->get('integrations_linkedin_centralized_and_own')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $this->html('form.action'); ?>
</form>
