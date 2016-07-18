<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<?php if( !$user->isViewer() ){ ?>
<div class="es-header-mini es-frosty">

	<div class="es-header-mini-cover" style="background-image: url('<?php echo $user->getCover();?>');background-position: <?php echo $user->getCoverPosition();?>;">
		<b></b>
		<b></b>
	</div>

	<div class="es-header-mini-avatar">
		<a class="es-avatar es-avatar-md" href="<?php echo $user->getPermalink();?>">
			<img alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" src="<?php echo $user->getAvatar( SOCIAL_AVATAR_SQUARE );?>" />
		</a>

        <?php if ($user->hasCommunityAccess()) { ?>
		<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() , 'size' => 'small' ) ); ?>
        <?php } ?>
	</div>

	<div class="es-header-mini-body" data-appscroll>

		<div class="es-header-mini-meta">
			<ul class="fd-reset-list">
				<li>
					<h2 class="h4 es-cover-title">
						<a href="<?php echo $user->getPermalink();?>" title="<?php echo $this->html( 'string.escape' , $user->getName() );?>"><?php echo $user->getName();?></a>
					</h2>
				</li>
			</ul>

            <?php if ($user->hasCommunityAccess()) { ?>
			<div class="fd-small">
				<a href="<?php echo FRoute::profile( array( 'id' => $user->getAlias() , 'layout' => 'about' ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_MORE_INFO' ); ?></a>

				<?php if( $this->access->allowed( 'reports.submit' ) ){ ?>
				&bull;
				<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_USER , $user->id , $user->getName() , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER' ) , '' , JText::_( 'COM_EASYSOCIAL_PROFILE_REPORT_USER_DESC' ) , $user->getPermalink( true , true )); ?>
				<?php } ?>
			</div>
            <?php } ?>

		</div>

		<?php if($user->hasCommunityAccess() && $user->getApps( 'profile' ) ){ ?>
		<div class="btn- btn-scroll" data-appscroll-buttons>
			<a href="javascript:void(0);" class="btn btn-left" data-appscroll-prev-button>
				<i class="fa fa-caret-left"></i>
			</a>
			<a href="javascript:void(0);" class="btn btn-right" data-appscroll-next-button>
				<i class="fa fa-caret-right"></i>
			</a>
		</div>

		<div class="es-header-mini-apps-action" data-appscroll-viewport>
			<ul class="fd-nav fd-nav- es-nav-apps" data-appscroll-content>
				<?php foreach( $user->getApps( 'profile' ) as $app ){ ?>
				<li>
					<a class="btn btn-clean" href="<?php echo $app->getUserPermalink( $user->getAlias() );?>">
						<span><?php echo $app->get( 'title' ); ?></span>
						<img src="<?php echo $app->getIcon();?>" class="es-nav-apps-icons" />
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>

	</div>

    <?php if ($user->hasCommunityAccess()) { ?>
    <div class="es-header-mini-footer">
        <div class="">
            <ul class="es-header-mini-action-nav">
                <?php if( $user->id != $this->my->id ){ ?>
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
                        <a href="<?php echo FRoute::profile( array( 'layout' => 'edit' ));?>" class="btn btn-clean">
                            <!-- <i class="icon-es-aircon-document"></i> -->
                            <?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_UPDATE_PROFILE' );?>
                        </a>
                    </li>
                <?php } ?>


            </ul>
        </div>
    </div>
    <?php } ?>

</div>
<?php } ?>
