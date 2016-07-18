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

<div class="es-profile-header" data-profile-header data-id="<?php echo $user->id;?>" data-name="<?php echo $this->html( 'string.escape' , $user->getName() );?>" data-avatar="<?php echo $user->getAvatar();?>">
    <div class="es-profile-header-heading <?php echo $this->template->get('profile_cover') ? ' with-cover' : ' without-cover';?>">
        <?php if ($this->template->get('profile_cover') && (!isset($showCover) || $showCover)) { ?>
            <?php echo $this->includeTemplate("site/profile/cover"); ?>
        <?php } ?>

        <?php echo $this->includeTemplate("site/profile/avatar"); ?>

        <?php echo $this->render('widgets', 'user', 'profile', 'afterAvatar', array($user)); ?>
    </div>

    <div class="es-profile-header-body fd-cf">
        <div class="es-profile-header-action pull-right">

            <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'beforeActions' , array( $user ) ); ?>

            <?php echo $this->render( 'module' , 'es-profile-before-actions' ); ?>
                <?php if ($user->id != $this->my->id) { ?>

                    <?php if ($this->my->isSiteAdmin()) { ?>
                    <div class="followAction"
                        data-id="<?php echo $user->id; ?>"
                        style="position:relative;"
                    >
                        <?php echo $this->loadTemplate( 'site/profile/default.header.admin', array( 'user' => $user ) ); ?>
                    </div>
                    <?php } ?>

                    <?php if (!$user->isBlockedBy($this->my->id)) { ?>
                    <?php $privacy = $this->my->getPrivacy(); ?>
                        <?php if ($privacy->validate('friends.request' , $user->id)) { ?>
                        <div class="friendsAction"
                            data-id="<?php echo $user->id; ?>"
                            data-callback="<?php echo base64_encode( JRequest::getURI() ); ?>"
                            data-profile-friends
                            data-friend="<?php echo $user->getFriend( $this->my->id )->id;?>"
                        >
                            <?php echo $this->loadTemplate( 'site/profile/default.header.friends' , array( 'user' => $user ) ); ?>
                        </div>
                        <?php } ?>

                        <?php if ($this->config->get( 'followers.enabled')) { ?>
                        <div class="followAction"
                            data-id="<?php echo $user->id; ?>"
                            data-profile-followers
                            style="position:relative;"
                        >
                            <?php if (FD::get('Subscriptions')->isFollowing($user->id, SOCIAL_TYPE_USER)) { ?>
                                <?php echo $this->loadTemplate('site/profile/button.followers.unfollow'); ?>
                            <?php } else { ?>
                                <?php echo $this->loadTemplate('site/profile/button.followers.follow'); ?>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <?php if( $privacy->validate( 'profiles.post.message' , $user->id ) && $this->config->get( 'conversations.enabled' ) && $this->access->allowed( 'conversations.create' ) ){ ?>
                        <div>
                            <?php echo $this->loadTemplate( 'site/profile/button.conversations.new' ); ?>
                        </div>
                        <?php } ?>
                    <?php } ?>

                <?php } else { ?>
                    <div>
                        <a href="<?php echo FRoute::profile( array( 'layout' => 'edit' ));?>" class="btn btn-clean btn-block btn-sm">
                            <i class="fa fa-cog mr-5"></i>
                            <?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_UPDATE_PROFILE' );?>
                        </a>
                    </div>
                <?php } ?>

                <?php if( $this->template->get( 'profile_points' , true ) && $this->config->get( 'points.enabled' ) ){ ?>
                    <div>
                        <a href="<?php echo FRoute::points( array( 'userid' => $user->getAlias() , 'layout' => 'history' ) );?>" class="btn btn-clean btn-block">

                            <div class="text-center fd-small">
                                <strong><?php echo JText::_('COM_EASYSOCIAL_PROFILE_POINTS');?></strong>
                            </div>

                            <div class="text-center">
                                <span style="font-size: 26px;font-weight:700;line-height:21px"><?php echo $user->getPoints();?></span>
                            </div>
                        </a>
                    </div>
                <?php } ?>

            <?php echo $this->render( 'module' , 'es-profile-after-actions' ); ?>

            <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'afterActions' , array( $user ) ); ?>
        </div>

        <div>
            <?php echo $this->render( 'module' , 'es-profile-before-name' ); ?>
            <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'beforeName' , array( $user ) ); ?>

            <h2 class="es-profile-header-title">
                <a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
            </h2>

            <?php echo $this->render( 'fields' , 'user' , 'profile' , 'afterName' , array( 'HEADLINE' , $user ) ); ?>

            <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'afterName' , array( $user ) ); ?>

            <?php echo $this->render( 'module' , 'es-profile-after-name' ); ?>

            <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'beforeBadges' , array( $user ) ); ?>

            <?php if( $this->config->get( 'badges.enabled' ) && $user->badgesViewable( FD::user()->id ) && $user->getBadges() && $this->template->get( 'profile_badges' ) ){ ?>
            <div class="mt-5 es-teaser-about">
                <ul class="fd-reset-list es-badge-list">
                    <?php foreach( $user->getBadges() as $badge ){ ?>
                    <li class="es-badge-item">
                        <a href="<?php echo $badge->getPermalink();?>" class="badge-link" data-es-provide="tooltip" data-placement="top" data-original-title="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>">
                        <img class="es-badge-icon" alt="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>" src="<?php echo $badge->getAvatar();?>"></a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>

            <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'afterBadges' , array( $user ) ); ?>

            <?php if( $this->template->get( 'profile_age' , true ) ){ ?>
            <div class="mt-5 es-teaser-about">
                <div class="fd-small">
                    <?php echo $this->render( 'fields' , 'user' , 'profile' , 'profileHeaderA' , array( 'BIRTHDAY' , $user ) ); ?>
                </div>
            </div>
            <?php } ?>

            <?php if( $this->template->get( 'profile_gender' , true ) ){ ?>
            <div class="mt-5 es-teaser-about">
                <div class="fd-small">
                    <?php echo $this->render( 'fields' , 'user' , 'profile' , 'profileHeaderA' , array( 'GENDER' , $user ) ); ?>
                </div>
            </div>
            <?php } ?>

            <?php if( $this->template->get( 'profile_lastlogin' , true ) ){ ?>
            <div class="mt-5 es-teaser-about">
                <div class="fd-small"><?php echo $this->render( 'fields' , 'user' , 'profile' , 'profileHeaderA' , array( 'JOOMLA_LASTLOGIN' , $user ) ); ?></div>
            </div>
            <?php } ?>

            <?php if( $this->template->get( 'profile_joindate' , true ) ){ ?>
            <div class="mt-5 es-teaser-about">
                <div class="fd-small"><?php echo $this->render( 'fields' , 'user' , 'profile' , 'profileHeaderA' , array( 'JOOMLA_JOINDATE' , $user ) ); ?></div>
            </div>
            <?php } ?>

            <?php if( $this->template->get( 'profile_address' , true ) ){ ?>
            <div class="mt-5 es-teaser-about">
                <div class="fd-small"><?php echo $this->render( 'fields' , 'user' , 'profile' , 'profileHeaderB' , array( 'ADDRESS' , $user ) ); ?></div>
            </div>
            <?php } ?>

            <?php if( $this->template->get( 'profile_website' , true ) ){ ?>
            <div class="mt-5 es-teaser-about">
                <div class="fd-small">
                    <?php echo $this->render( 'fields' , 'user' , 'profile' , 'profileHeaderD' , array( 'URL' , $user ) ); ?>
                </div>
            </div>
            <?php } ?>

            <?php echo $this->render( 'module' , 'es-profile-before-info' ); ?>


            <?php if ((!$this->my->guest && $this->my->id != $user->id && $this->config->get('users.blocking.enabled')) || ($this->my->id != $user->id && $this->template->get('profile_report', true) && $this->access->allowed('reports.submit') && $this->config->get('reports.enabled'))) { ?>
            <div class="mv-10 fd-small">
                <?php if (!$this->my->guest && $this->my->id != $user->id && $this->config->get('users.blocking.enabled') ){ ?>
                    <?php echo FD::blocks()->getForm($user->id); ?> &middot;
                <?php } ?>
                
                <?php if ($this->my->id != $user->id && $this->template->get('profile_report', true) && $this->access->allowed('reports.submit') && $this->config->get('reports.enabled')){ ?>
                    <?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_USER , $user->id , $user->getName() , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER' ) , '' , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER_DESC' ) , $user->getPermalink( true , true ) ); ?>
                <?php } ?>
            </div>
            <?php } ?>

            <?php echo $this->render( 'module' , 'es-profile-after-info' ); ?>

            <?php echo $this->render( 'widgets' , 'user' , 'profile' , 'afterInfo' , array( $user ) ); ?>
        </div>
    </div>

    <div class="es-profile-header-footer">
        <nav class="es-list-vertical-divider pull-left">

            <?php if ($this->config->get('photos.enabled')) { ?>
            <span>
                <a href="<?php echo FRoute::albums(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>">
                    <i class="fa fa-picture-o"></i>
                    <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_USER_ALBUMS' , $user->getTotalAlbums()), $user->getTotalAlbums()); ?>
                </a>
            </span>
            <?php } ?>

            <?php if ($this->config->get('video.enabled', true)) { ?>
            <span>
                <a href="<?php echo FRoute::videos(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>">
                    <i class="fa fa-film"></i>
                    <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_VIDEOS' , $user->getTotalVideos()), $user->getTotalVideos()); ?>
                </a>
            </span>
            <?php } ?>

            <span>
                <a href="<?php echo FRoute::friends( array( 'userid' => $user->getAlias() ) );?>">
                    <i class="fa fa-users"></i>
                    <?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GENERIC_FRIENDS' , $user->getTotalFriends() ) , $user->getTotalFriends() ); ?>
                </a>
            </span>
            <?php if( $this->config->get( 'followers.enabled' ) ){ ?>
            <span>
                <a href="<?php echo FRoute::followers( array( 'userid' => $user->getAlias() ) );?>">
                    <i class="fa fa-share-alt"></i>
                    <?php echo $user->getTotalFollowers();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_FOLLOWERS' , $user->getTotalFollowers() ) ); ?>
                </a>
            </span>
            <?php } ?>

            <?php if( $this->config->get('badges.enabled' ) && $user->badgesViewable( FD::user()->id ) ){ ?>
            <span>
                <a href="<?php echo FRoute::badges( array( 'layout' => 'achievements' , 'userid' => $user->getAlias() ) );?>">
                    <i class="fa fa-trophy"></i>
                    <?php echo $user->getTotalBadges();?> <?php echo JText::_( FD::string()->computeNoun( 'COM_EASYSOCIAL_ACHIEVEMENTS' , $user->getTotalBadges() ) ); ?>
                </a>
            </span>
            <?php } ?>
        </nav>

        <nav class="pull-right">
            <?php if( $this->template->get( 'profile_type' ) ){ ?>
            <span>
                <a href="<?php echo $user->getProfile()->getPermalink();?>" class="profile-type">
                    <i class="fa fa-list-alt"></i> <?php echo $user->getProfile()->get('title');?>
                </a>
            </span>
            <?php } ?>
        </nav>
    </div>
    
</div>
