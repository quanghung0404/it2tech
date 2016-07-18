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
<?php if ($this->my->isSiteAdmin() && $user->isBlock()) { ?>
<div class="es-user-banned alert alert-info">
    <?php echo JText::_('COM_EASYSOCIAL_PROFILE_USER_IS_BANNED');?>
</div>
<?php } ?>

<div class="es-profile-header es-frosty" data-profile-header data-id="<?php echo $user->id;?>" data-name="<?php echo $this->html( 'string.escape' , $user->getName() );?>" data-avatar="<?php echo $user->getAvatar();?>">

    <div class="es-profile-header-heading <?php echo $this->template->get( 'profile_cover' ) ? ' with-cover' : ' without-cover';?>">

        <?php if( $this->template->get( 'profile_cover' ) ){ ?>
        <?php if( !isset( $showCover ) || $showCover ){ ?>
        <?php echo $this->includeTemplate("site/profile/cover"); ?>
        <?php } ?>
        <?php } ?>

        <?php echo $this->includeTemplate("site/profile/avatar"); ?>

        <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'afterAvatar' , array( $user ) ); ?>
    </div>

    <div class="es-profile-header-body">

        <div class="es-profile-header-info">
            <!-- <ul class="fd-reset-list"> -->

                <h2 class="es-profile-header-title">
                    <a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
                </h2>

                <!-- Shown in mobile view -->
                <div class="es-profile-header-meta">
                    <a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>">
                        <?php echo $user->getTotalFriends();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FRIENDS' , $user->getTotalFriends() ) ); ?>
                    </a>

                    <?php if( $this->config->get( 'followers.enabled' ) ){ ?>
                    &middot;
                    <a href="<?php echo FRoute::followers( array( 'userid' => $user->getAlias() ) );?>">
                        <?php echo $user->getTotalFollowers();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FOLLOWERS' , $user->getTotalFollowers() ) ); ?>
                    </a>
                    <?php } ?>

                    <?php if( $this->config->get('badges.enabled' ) ){ ?>
                    &middot;
                    <a href="<?php echo FRoute::badges( array( 'layout' => 'achievements' , 'userid' => $user->getAlias() ) );?>">
                        <?php echo $user->getTotalBadges();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_ACHIEVEMENTS' , $user->getTotalBadges() ) ); ?>
                    </a>
                    <?php } ?>
                </div>

                <?php if( $this->template->get( 'profile_gender' , true ) ){ ?>
                <div class="mt-5 es-teaser-about">
                    <?php echo $this->render( 'fields' , 'user' , 'profile' , 'profileHeaderA' , array( 'GENDER' , $user ) ); ?>
                </div>
                <?php } ?>

                <?php if( $this->template->get( 'profile_type' ) ){ ?>
                <div>
                    <a href="<?php echo $user->getProfile()->getPermalink();?>" class="profile-type">
                        <i class="fa fa-list-alt"></i> <?php echo $user->getProfile()->get('title');?>
                    </a>
                </div>
                <?php } ?>

            <!-- </ul> -->

            <?php echo $this->render( 'module' , 'es-profile-before-info' ); ?>

            <div class="es-profile-header-view-full-text mt-5">
                <?php if ($this->my->id != $user->id && $this->config->get('users.blocking.enabled') ){ ?>
                <?php echo FD::blocks()->getForm($user->id); ?> &middot;
                <?php } ?>

                <a href="<?php echo FRoute::profile( array( 'id' => $user->getAlias() , 'layout' => 'about' ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_MORE_INFO' ); ?></a>

                <?php if( $this->my->id != $user->id && $this->template->get( 'profile_report' , true ) && $this->access->allowed( 'reports.submit' ) && $this->config->get( 'reports.enabled' ) ){ ?>
                &middot;
                <?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_USER , $user->id , $user->getName() , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER' ) , '' , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER_DESC' ) , $user->getPermalink( true , true ) ); ?>
                <?php } ?>
            </div>

            <?php echo $this->render( 'module' , 'es-profile-after-info' ); ?>

            <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'afterInfo' , array( $user ) ); ?>

        </div>

        <!-- es-header-stat showing in desktop view only -->
        <div class="es-profile-header-stat">
            <ul class="list-inline">

                <?php if ($this->config->get('video.enabled')) { ?>
                <li>
                    <a href="<?php echo FRoute::videos(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>" class="btn btn-clean">
                        <div class="center">
                            <span><?php echo $user->getTotalVideos();?></span>
                        </div>

                        <div class="center stat-desc">
                            <span><?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_VIDEOS_STRING', $user->getTotalVideos()), $user->getTotalVideos()); ?></span>
                        </div>
                    </a>
                </li>
                <?php } ?>

                <li>
                    <a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>" class="btn btn-clean">
                        <div class="center">
                            <span><?php echo $user->getTotalFriends();?></span>
                        </div>

                        <div class="center stat-desc">
                            <span><?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FRIENDS' , $user->getTotalFriends() ) ); ?></span>
                        </div>
                    </a>
                </li>

                <?php if( $this->config->get( 'followers.enabled' ) ){ ?>
                <li>
                    <a href="<?php echo FRoute::followers( array( 'userid' => $user->getAlias() ) );?>" class="btn btn-clean">
                        <div class="center">
                            <span><?php echo $user->getTotalFollowers();?></span>
                        </div>

                        <div class="center stat-desc">
                            <span><?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FOLLOWERS' , $user->getTotalFollowers() ) ); ?></span>
                        </div>
                    </a>
                </li>
                <?php } ?>

                <?php if( $this->config->get('badges.enabled' ) ){ ?>
                <li>
                    <a href="<?php echo FRoute::badges( array( 'layout' => 'achievements' , 'userid' => $user->getAlias() ) );?>" class="btn btn-clean">
                        <div class="center">
                            <span><?php echo $user->getTotalBadges();?></span>
                        </div>

                        <div class="center stat-desc">
                            <span><?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_ACHIEVEMENTS' , $user->getTotalBadges() ) ); ?></span>
                        </div>
                    </a>
                </li>
                <?php } ?>

                <?php if( $this->template->get( 'profile_points' , true ) && $this->config->get( 'points.enabled' ) ){ ?>
                    <li>
                        <a href="<?php echo FRoute::points( array( 'userid' => $user->getAlias() , 'layout' => 'history' ) );?>" class="btn btn-clean">

                            <div class="center">
                                <span><?php echo $user->getPoints();?></span>
                            </div>

                            <div class="center stat-desc">
                                <span><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_POINTS' );?></span>
                            </div>
                        </a>
                    </li>
                <?php } ?>


            </ul>
        </div>
        <div class="es-profile-header-footer">
            <div class="es-profile-header-action">

                <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'beforeActions' , array( $user ) ); ?>

                <?php echo $this->render( 'module' , 'es-profile-before-actions' ); ?>
                <ul class="fd-reset-list es-profile-header-action-nav">
                    <?php if( $user->id != $this->my->id ){ ?>

                        <?php if ($this->my->isSiteAdmin()) { ?>
                        <li class="followAction"
                            data-id="<?php echo $user->id; ?>"
                            style="position:relative;"
                        >
                            <?php echo $this->loadTemplate( 'site/profile/default.header.admin', array( 'user' => $user ) ); ?>
                        </li>
                        <?php } ?>

                        <?php
                            $privacy = $this->my->getPrivacy();

                            if( $privacy->validate( 'friends.request' , $user->id ) )
                            {
                        ?>
                        <li class="friendsAction"
                            data-id="<?php echo $user->id; ?>"
                            data-callback="<?php echo base64_encode( JRequest::getURI() ); ?>"
                            data-profile-friends
                            data-friend="<?php echo $user->getFriend( $this->my->id )->id;?>"
                        >
                            <?php echo $this->loadTemplate( 'site/profile/default.header.friends' , array( 'user' => $user ) ); ?>
                        </li>

                        <?php } ?>

                        <?php if( $this->config->get( 'followers.enabled' ) ){ ?>
                        <li class="followAction"
                            data-id="<?php echo $user->id; ?>"
                            data-profile-followers
                            style="position:relative;"
                        >
                            <?php if( FD::get( 'Subscriptions' )->isFollowing( $user->id , SOCIAL_TYPE_USER ) ){ ?>
                                <?php echo $this->loadTemplate( 'site/profile/button.followers.unfollow' ); ?>
                            <?php } else { ?>
                                <?php echo $this->loadTemplate( 'site/profile/button.followers.follow' ); ?>
                            <?php } ?>
                        </li>
                        <?php } ?>

                        <?php if( $privacy->validate( 'profiles.post.message' , $user->id ) && $this->config->get( 'conversations.enabled' ) && $this->access->allowed( 'conversations.create' ) ){ ?>
                        <li>
                            <?php echo $this->loadTemplate( 'site/profile/button.conversations.new' ); ?>
                        </li>
                        <?php } ?>

                    <?php } else { ?>
                        <li>
                            <a href="<?php echo FRoute::profile( array( 'layout' => 'edit' ));?>" class="btn">
                                <?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_UPDATE_PROFILE' );?>
                            </a>
                        </li>
                    <?php } ?>


                </ul>
                <?php echo $this->render( 'module' , 'es-profile-after-actions' ); ?>

                <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'afterActions' , array( $user ) ); ?>
            </div>

        </div>


    </div>

</div>
