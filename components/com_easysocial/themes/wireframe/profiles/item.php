<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-category-item" data-es-profile-item>
    <div class="es-category-header">
        <div class="row">
            <div class="col-md-8">
                <div class="media">
                    <div class="media-object pull-left">
                        <img src="<?php echo $profile->getAvatar();?>" class="es-avatar" />
                    </div>

                    <div class="media-body">
                        <h2 class="profile-title"><?php echo $profile->get('title'); ?></h2>
                    </div>
                </div>

                <?php if ($profile->get('description')) { ?>
                    <p class="category-desc"><?php echo $profile->get('description'); ?></p>
                <?php } else { ?>
                    <p class="category-desc"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_NO_DESCRIPTION_SET_YET');?>
                <?php } ?>

            </div>
            <div class="col-md-4">
                <div class="category-graph">
                    <div class="h5"><?php echo JText::_('COM_EASYSOCIAL_USERS_REGISTERED_IN_PROFILE'); ?></div>

                    <span data-profile-gravity-chart>
                        <?php echo implode(',' , $stats); ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="es-category-meta">
            <ul class="fd-reset-list category-stats pull-left">
                <li>
                    <i class="fa fa-users"></i> <?php echo JText::sprintf(FD::string()->computeNoun('COM_EASYSOCIAL_USERS_COUNT', $totalUsers), $totalUsers); ?>
                </li>
            </ul>

            <?php if ($this->my->guest) { ?>
            <a href="<?php echo FRoute::registration();?>" class="btn btn-es-primary btn-sm pull-right">
                <?php echo JText::_('COM_EASYSOCIAL_REGISTER_BUTTON'); ?> &rarr;
            </a>
            <?php } ?>
        </div>
    </div>




    <div class="es-container">
        <div class="es-sidebar" data-sidebar>
            <!-- do not remove this element. This element is needed for the stream loodmore to work properly -->
            <div data-dashboardSidebar-menu data-type="profile" data-id="<?php echo $profile->id;?>" class="active"></div>

            <?php echo $this->render('module', 'es-profiles-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

            <?php if ($profile->get('community_access')) { ?>
            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_RANDOM_MEMBERS');?></div>
                </div>

                <div class="es-widget-body">
                    <?php if ($randomMembers) { ?>
                    <ul class="widget-list-grid">
                        <?php foreach( $randomMembers as $user ){ ?>
                        <li>
                            <div class="es-avatar-wrap">
                                <a href="<?php echo $user->getPermalink();?>"
                                    class="es-avatar es-avatar-sm"
                                    data-popbox="module://easysocial/profile/popbox"
                                    data-user-id="<?php echo $user->id;?>"
                                >
                                    <img alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" src="<?php echo $user->getAvatar();?>" />
                                </a>
                            </div>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php } else { ?>
                    <div class="fd-small">
                        <?php echo JText::_('COM_EASYSOCIAL_GROUPS_NO_MEMBERS_HERE'); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <div class="es-widget">
                <div class="es-widget-head">
                    <div class="pull-left widget-title"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_RANDOM_ALBUMS');?></div>
                </div>

                <div class="es-widget-body">
                    <?php if ($albums) { ?>
                        <ul class="widget-list-grid">
                        <?php foreach ($albums as $album) { ?>
                            <li>
                                <div class="es-avatar-wrap">
                                    <a href="<?php echo $album->getPermalink();?>" class="es-avatar es-avatar-sm"
                                        data-original-title="<?php echo $this->html('string.escape', $album->get('title'));?>"
                                        data-es-provide="tooltip"
                                        data-placement="bottom"
                                    >
                                        <img alt="<?php echo $this->html('string.escape', $album->get('title'));?>" src="<?php echo $album->getCover('square');?>" />
                                    </a>
                                </div>
                            </li>
                        <?php } ?>
                        </ul>
                    <?php } else { ?>
                    <div class="fd-small">
                        <?php echo JText::_('COM_EASYSOCIAL_PROFILES_NO_ALBUMS_CREATED'); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>


            <?php echo $this->render('module', 'es-profiles-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
        </div>

        <div class="es-content">
            <div class="es-filterbar">
                <div class="filterbar-title h5 pull-left"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_RECENT_UPDATES' ); ?></div>
            </div>

            <div class="es-content-wrap" data-es-group-item-content>
                <?php echo $stream->html();?>
            </div>
        </div>
    </div>

</div>
