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
<?php if (EB::socialbuttons()->enabled()) { ?>
<div class="eb-share">
    <div class="eb-share-buttons<?php echo ' is-' . $this->config->get('social_button_size'); ?> <?php echo EB::getLanguageTag() == 'en-GB' ? 'is-english': ''; ?>">

        <?php if (EB::socialbuttons()->get('facebook', $post)->isEnabled()) { ?>
        <div class="eb-share-facebook <?php echo $this->config->get('main_facebook_like_send') == '1' ? 'has-sendbtn' : '' ?>">
            <?php echo EB::socialbuttons()->get('facebook', $post)->html(); ?>
        </div>
        <?php } ?>

        <?php if (EB::socialbuttons()->get('twitter', $post)->isEnabled()) { ?>
        <div class="eb-share-twitter">
            <?php echo EB::socialbuttons()->get('twitter', $post)->html(); ?>
        </div>
        <?php } ?>

        <?php if (EB::socialbuttons()->get('google', $post)->isEnabled()) { ?>
        <div class="eb-share-google-plus">
            <?php echo EB::socialbuttons()->get('google', $post)->html(); ?>
        </div>
        <?php } ?>

        <?php if (EB::socialbuttons()->get('linkedin', $post)->isEnabled()) { ?>
        <div class="eb-share-linkedin">
            <?php echo EB::socialbuttons()->get('linkedin', $post)->html(); ?>
        </div>
        <?php } ?>

        <?php if (EB::socialbuttons()->get('xing', $post)->isEnabled()) { ?>
        <div class="eb-share-xing">
            <?php echo EB::socialbuttons()->get('xing', $post)->html(); ?>
        </div>
        <?php } ?>

        <?php if (EB::socialbuttons()->get('vk', $post)->isEnabled()) { ?>
        <div class="eb-share-vk">
            <?php echo EB::socialbuttons()->get('vk', $post)->html(); ?>
        </div>
        <?php } ?>

        <?php if (EB::socialbuttons()->get('stumbleupon', $post)->isEnabled()) { ?>
        <div class="eb-share-stumbleupon">
            <?php echo EB::socialbuttons()->get('stumbleupon', $post)->html(); ?>
        </div>
        <?php } ?>
        <?php if (EB::socialbuttons()->get('pinterest', $post)->isEnabled()) { ?>
        <div class="eb-share-pinterest">
            <?php echo EB::socialbuttons()->get('pinterest', $post)->html(); ?>
        </div>
        <?php } ?>
        <?php if (EB::socialbuttons()->get('reddit', $post)->isEnabled()) { ?>
        <div class="eb-share-reddit">
            <?php echo EB::socialbuttons()->get('reddit', $post)->html(); ?>
        </div>
        <?php } ?>
        <?php if (EB::socialbuttons()->get('pocket', $post)->isEnabled()) { ?>
        <div class="eb-share-pocket">
            <?php echo EB::socialbuttons()->get('pocket', $post)->html(); ?>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
