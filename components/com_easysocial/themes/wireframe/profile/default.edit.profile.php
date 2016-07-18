<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-container" data-profile-edit>
    <a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
        <i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_SIDEBAR_TOGGLE');?>
    </a>
    <div class="es-sidebar" data-sidebar>

        <?php echo $this->render('module' , 'es-profile-edit-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

        <div class="es-widget es-widget-borderless">
            <div class="es-widget-head">
                <div class="widget-title pull-left">
                    <?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_ABOUT');?>
                </div>
            </div>

            <div class="es-widget-body">
                <ul class="fd-nav fd-nav-stacked feed-items">
                    <?php $i = 0; ?>
                    <?php foreach ($steps as $step){ ?>
                        <li data-for="<?php echo $step->id;?>" class="step-item<?php echo $i == 0 ? ' active' :'';?>" data-profile-edit-fields-step>
                            <a href="javascript:void(0);"><?php echo $step->get('title'); ?></a>
                        </li>
                        <?php $i++; ?>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <?php if ($this->config->get('users.display.profiletype', true) && $this->my->hasCommunityAccess()) { ?>
        <div class="es-widget es-widget-borderless">
            <div class="es-widget-head">
                <div class="widget-title pull-left">
                    <?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_YOUR_PROFILE');?>
                </div>
            </div>

            <div class="es-widget-body">
                <?php echo JText::sprintf('COM_EASYSOCIAL_PROFILE_SIDEBAR_YOUR_PROFILE_INFO', '<a href="' . $profile->getPermalink() . '">' . $profile->getTitle() . '</a>');?>
            </div>
        </div>
        <?php } ?>

        <?php if ($showSocialTabs){ ?>
        <div class="es-widget es-widget-borderless">
            <div class="es-widget-head"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_SOCIALIZE');?></div>

            <div class="es-widget-body">
                <ul class="fd-nav fd-nav-stacked feed-items">
                    <?php if ($associatedFacebook){ ?>
                    <li data-for="facebook" data-profile-edit-fields-step data-profile-edit-facebook>
                        <a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_SOCIALIZE_FACEBOOK');?></a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <?php } ?>

        <?php if ($this->my->deleteable()){ ?>
        <div class="es-widget es-widget-borderless">
            <div class="es-widget-head"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_DELETE');?></div>

            <div class="es-widget-body">
                <a href="javascript:void(0);" class="fd-small" data-profile-edit-delete><?php echo JText::_('COM_EASYSOCIAL_DELETE_YOUR_PROFILE_BUTTON');?></a>
            </div>
        </div>
        <?php } ?>

        <?php echo $this->render('module' , 'es-profile-edit-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
    </div>

    <div class="es-content">

        <?php echo $this->render('module' , 'es-profile-edit-before-contents'); ?>

        <div class="profile-wrapper" data-profile-edit-fields>
            <form method="post" action="<?php echo JRoute::_('index.php'); ?>" class="form-horizontal" data-profile-fields-form autocomplete="off">
                <div class="edit-form">
                    <div class="tab-content profile-content">
                        <?php $i = 0; ?>
                        <?php foreach ($steps as $step) { ?>
                        <div class="step-content step-<?php echo $step->id;?> <?php if ($i == 0) { ?>active<?php } ?>"
                            data-profile-edit-fields-content data-id="<?php echo $step->id; ?>"
                        >
                            <?php if ($step->fields){ ?>
                                <?php foreach ($step->fields as $field){ ?>
                                    <?php if (!empty($field->output)) { ?>
                                    <div data-profile-edit-fields-item data-element="<?php echo $field->element; ?>" data-id="<?php echo $field->id; ?>" data-required="<?php echo $field->required; ?>" data-fieldname="<?php echo SOCIAL_FIELDS_PREFIX . $field->id; ?>">
                                        <?php echo $field->output; ?>
                                    </div>
                                    <?php } ?>

                                    <?php if (!$field->getApp()->id) { ?>
                                    <div class="alert alert-danger"><?php echo JText::_('COM_EASYSOCIAL_FIELDS_INVALID_APP'); ?></div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <?php $i++; ?>
                        <?php } ?>

                        <?php if ($associatedFacebook) { ?>
                        <div class="step-content step-facebook" data-profile-edit-fields-content data-id="facebook">
                            <div class="edit-form social-integrations">
                                <legend class="es-legend"><?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_INTEGRATIONS');?></legend>
                                <div class="es-desp">
                                    <?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_INTEGRATIONS_ASSOCIATED');?>
                                </div>

                                <?php if (isset($fbUserMeta[ 'avatar' ]) && isset($fbUserMeta[ 'link' ]) && isset($fbUserMeta[ 'username' ])){ ?>
                                <div class="es-avatar-wrapper">
                                    <div class="es-avatar pull-left">
                                        <img src="<?php echo $fbUserMeta['avatar'];?>" width="16" />
                                    </div>
                                    <div class="es-username">
                                        <a href="<?php echo $fbUserMeta['link'];?>" target="_blank" class="label label-info"><?php echo $fbUserMeta['username']; ?></a>
                                    </div>
                                </div>
                                <?php } ?>

                                <ul class="yesno-list mb-20">
                                    <?php if ($this->config->get('oauth.facebook.push')){ ?>
                                    <li>
                                        <div class="yesno-item pull-left fd-small">
                                            <?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_INTEGRATIONS_PUSH_STREAM_ITEMS');?>
                                        </div>
                                        <div class="pull-right">
                                            <?php echo $this->html('grid.boolean' , 'oauth.facebook.push' , $fbOAuth->push , 'push' , array('data-oauth-facebook-push=""')); ?>
                                        </div>
                                    </li>
                                    <?php } ?>
                                </ul>

                                <legend class="es-legend"><?php echo JText::_('COM_EASYSOCIAL_OAUTH_FACEBOOK_REVOKE_ACCESS');?></legend>
                                <?php echo $facebookClient->getRevokeButton(FRoute::profile(array('layout' => 'edit' , 'external' => true)));?>
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
                <div class="form-actions">
                    
                    <?php if ($this->my->hasCommunityAccess()) { ?>
                    <div class="pull-left">
                        <a href="<?php echo $this->my->getPermalink();?>" class="btn btn-sm btn-es-danger"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></a>
                    </div>
                    <?php } ?>

                    <div class="pull-right">
                        <button type="button" class="btn btn-sm btn-es-primary" data-profile-fields-save><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON');?></button>

                        <?php if ($this->my->hasCommunityAccess()) { ?>
                        <button type="button" class="btn btn-sm btn-es-primary" data-profile-fields-save-close><?php echo JText::_('COM_EASYSOCIAL_SAVE_AND_CLOSE_BUTTON');?></button>
                        <?php } ?>
                    </div>
                </div>

                <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid');?>" />
                <input type="hidden" name="option" value="com_easysocial" />
                <input type="hidden" name="controller" value="profile" />
                <input type="hidden" name="task" value="save" />
                <input type="hidden" name="<?php echo FD::token();?>" value="1" />

                <input type="hidden" name="associatedFacebook" value="<?php echo $associatedFacebook ? 1 : ''; ?>" />
            </form>
        </div>

        <?php echo $this->render('module' , 'es-profile-edit-after-contents'); ?>
    </div>
</div>
