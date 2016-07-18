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
<div class="eb-composer-panel active" data-eb-composer-panel data-id="post-options">

    <div class="eb-composer-panel-content" data-scrolly="y">
        <div data-scrolly-viewport>
            <?php echo $this->output('site/composer/form/author'); ?>
            <?php echo $this->output('site/composer/form/category'); ?>

            <?php if ($this->config->get('layout_composer_tags')) { ?>
            <?php echo $this->output('site/composer/form/tags'); ?>
            <?php } ?>

            <form name="publishing" class="eb-composer-publish" data-eb-composer-form autocomplete="off">

                <?php if (($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) ||
                          ($this->config->get('layout_composer_creationdate')) ||
                          ($this->config->get('layout_composer_publishingdate')) ||
                          ($this->config->get('layout_composer_unpublishdate'))
                        ) { ?>
                <div class="eb-composer-fieldset" data-name="post_properties">
                    <div class="eb-composer-fieldset-header">
                        <strong><?php echo JText::_('COM_EASYSOCIAL_COMPOSER_POST_PROPERTIES');?></strong>
                    </div>
                    <div class="eb-composer-fieldset-content">
                        <?php if ($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) { ?>
                            <?php echo $this->output('site/composer/form/language'); ?>
                        <?php } ?>

                        <?php echo $this->output('site/composer/form/creation_date'); ?>

                        <?php echo $this->output('site/composer/form/publish_date'); ?>
                        <?php echo $this->output('site/composer/form/unpublish_date'); ?>
                    </div>
                </div>
                <?php } ?>

                <?php if (($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) &&
                         EB::isAssociationEnabled()
                        ) { ?>
                    <?php echo $this->output('site/composer/form/association'); ?>
                <?php } ?>

                <?php
                    if (($this->config->get('integrations_facebook_centralized_and_own') && $author->hasOauth('facebook')) ||
                        ($this->config->get('integrations_twitter_centralized_and_own') && $author->hasOauth('twitter')) ||
                        ($this->config->get('integrations_linkedin_centralized_and_own') && $author->hasOauth('linkedin'))
                        ) {
                ?>
                <div class="eb-composer-fieldset" data-name="social_publishing">
                    <div class="eb-composer-fieldset-header">
                        <strong><?php echo JText::_('COM_EASYSOCIAL_COMPOSER_SOCIAL_PUBLISHING');?></strong>
                    </div>
                    <div class="eb-composer-fieldset-content">
                        <?php echo $this->output('site/composer/form/autopost'); ?>
                    </div>
                </div>
                <?php } ?>

                <div class="eb-composer-fieldset" data-name="general">
                    <div class="eb-composer-fieldset-header">
                        <strong><?php echo JText::_('COM_EASYSOCIAL_COMPOSER_GENERAL');?></strong>
                    </div>
                    <div class="eb-composer-fieldset-content">
                        <?php if ($this->acl->get('contribute_frontpage') ) { ?>
                        <?php echo $this->output('site/composer/form/frontpage'); ?>
                        <?php } ?>

                        <?php if ($this->config->get('layout_composer_comment')) { ?>
                            <?php if ($this->config->get('main_comment') && $this->acl->get('change_setting_comment')) { ?>
                            <?php echo $this->output('site/composer/form/comment'); ?>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($this->acl->get('change_setting_subscription') && $this->config->get('main_subscription')) { ?>
                        <?php echo $this->output('site/composer/form/subscription'); ?>
                        <?php echo $this->output('site/composer/form/email'); ?>
                        <?php } ?> 
                    </div>
                </div>

                <?php if ($this->config->get('layout_composer_privacy')) { ?>
                    <?php if (($this->config->get('main_blogprivacy_override') && $this->acl->get('enable_privacy')) || ($this->config->get('main_password_protect') && !$post->isFeatured())) { ?>
                    <div class="eb-composer-fieldset" data-name="privacy">
                        <div class="eb-composer-fieldset-header">
                            <strong><?php echo JText::_('COM_EASYSOCIAL_COMPOSER_PRIVACY');?></strong>
                        </div>

                        <div class="eb-composer-fieldset-content">
                            <?php if ($this->config->get('main_blogprivacy_override') && $this->acl->get('enable_privacy')) { ?>
                                <?php echo $this->output('site/composer/form/privacy'); ?>
                            <?php } ?>

                            <?php if ($this->config->get('main_password_protect') && !$post->isFeatured()) { ?>
                                <?php echo $this->output('site/composer/form/password'); ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                <?php } ?>

                <?php if ($this->config->get('main_copyrights')) { ?>
                <div class="eb-composer-fieldset" data-name="copyright">
                    <div class="eb-composer-fieldset-header">
                        <strong><?php echo JText::_('COM_EASYSOCIAL_COMPOSER_COPYRIGHTS');?></strong>
                    </div>
                    <div class="eb-composer-fieldset-content">
                        <?php echo $this->output('site/composer/form/copyright'); ?>
                    </div>
                </div>
                <?php } ?>
            </form>

        </div>
    </div>

</div>
