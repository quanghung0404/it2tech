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
<div class="eb-composer-field row">
    <label class="eb-composer-field-label col-sm-5"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PUBLISH_TO'); ?></label>

    <div class="eb-composer-field-content col-sm-7">
        <div class="eb-composer-list-checkbox composer-autopost" style="margin-top: 5px;">
            <?php if ($this->config->get('integrations_facebook_centralized_and_own') && $author->hasOauth('facebook')) { ?>
            <div class="eb-checkbox checkbox-inline">
                <input id="autopost-facebook" name="autoposting[]" value="facebook" type="checkbox"
                    data-autopost-facebook
                    <?php if ($author->getOauth('facebook')->isShared($post->id)) { ?>
                    disabled="disabled"
                    <?php } else { ?>
                    <?php echo $author->getOauth('facebook')->auto ? ' checked' : '';?>
                    <?php } ?>
                />
                <label for="autopost-facebook" class="<?php echo $this->config->get('integrations_facebook_centralized_auto_post') ? 'checked' : '';?>"
                    data-eb-provide="tooltip"
                    data-placement="bottom"
                    <?php if ($author->getOauth('facebook')->isShared($post->id)) { ?>
                    data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_FACEBOOK_INFO_SHARED');?>"
                    <?php } else { ?>
                    data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_FACEBOOK_INFO');?>"
                    <?php } ?>
                >
                    <i class="fa fa-facebook-square" style="font-size: 15px;"></i>
                </label>
            </div>
            <?php } ?>

            <?php if ($this->config->get('integrations_twitter_centralized_and_own') && $author->hasOauth('twitter')) { ?>
            <div class="eb-checkbox checkbox-inline">
                <input id="autopost-twitter" name="autoposting[]" value="twitter" type="checkbox"
                    data-autopost-twitter
                    <?php if ($author->getOauth('twitter')->isShared($post->id)) { ?>
                    disabled="disabled"
                    <?php } else { ?>
                    <?php echo $author->getOauth('twitter')->auto ? ' checked' : '';?>
                    <?php } ?>
                />
                <label for="autopost-twitter"
                    data-eb-provide="tooltip"
                    data-placement="bottom"
                    <?php if ($author->getOauth('twitter')->isShared($post->id)) { ?>
                    data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_TWITTER_INFO_SHARED');?>"
                    <?php } else { ?>
                    data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_TWITTER_INFO');?>"
                    <?php } ?>
                >
                    <i class="fa fa-twitter-square" style="font-size: 15px;"></i>
                </label>
            </div>
            <?php } ?>

            <?php if ($this->config->get('integrations_linkedin_centralized_and_own') && $author->hasOauth('linkedin')) { ?>
            <div class="eb-checkbox checkbox-inline">
                <input id="autopost-linkedin" name="autoposting[]" value="linkedin" type="checkbox"
                    data-autopost-linkedin
                    <?php if ($author->getOauth('linkedin')->isShared($post->id)) { ?>
                    disabled="disabled"
                    <?php } else { ?>
                    <?php echo $author->getOauth('linkedin')->auto ? ' checked' : '';?>
                    <?php } ?>
                />
                <label for="autopost-linkedin" class="<?php echo $this->config->get('integrations_linkedin_centralized_auto_post') ? 'checked' : '';?>"
                    data-eb-provide="tooltip" data-placement="bottom"
                    <?php if ($author->getOauth('linkedin')->isShared($post->id)) { ?>
                    data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_LINKEDIN_INFO_SHARED');?>"
                    <?php } else { ?>
                    data-original-title="<?php echo JText::_('COM_EASYBLOG_COMPOSER_AUTOPOST_LINKEDIN_INFO');?>"
                    <?php } ?>

                >
                    <i class="fa fa-linkedin-square" style="font-size: 15px;"></i>
                </label>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
