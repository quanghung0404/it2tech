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
<div class="es-header-mini" data-id="<?php echo $group->id;?>" data-name="<?php echo $this->html( 'string.escape' , $group->getName() );?>" data-avatar="<?php echo $group->getAvatar();?>" data-es-group-item>

	<div class="es-header-mini-cover" style="background-image: url('<?php echo $group->getCover();?>');background-position: <?php echo $group->getCoverPosition();?>;">
		<b></b>
		<b></b>
	</div>

	<div class="es-header-mini-avatar">
		<a class="es-avatar es-avatar-md" href="<?php echo $group->getPermalink();?>">
			<img alt="<?php echo $this->html( 'string.escape' , $group->getName() );?>" src="<?php echo $group->getAvatar( SOCIAL_AVATAR_SQUARE );?>" />
		</a>
	</div>

	<div class="es-header-mini-body" data-appscroll>
		<div class="es-header-mini-meta">
			<ul class="fd-reset-list">
				<li>
					<h2 class="h4 es-cover-title">
						<a href="<?php echo $group->getPermalink();?>" title="<?php echo $this->html( 'string.escape' , $group->getName() );?>"><?php echo $group->getName();?></a>
					</h2>
					<?php if( $group->isOpen() ){ ?>
					<span class="label label-success" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="top">
						<i class="fa fa-globe"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?>
					</span>
					<?php } ?>

					<?php if( $group->isClosed() ){ ?>
					<span class="label label-danger" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="top">
						<i class="fa fa-lock"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?>
					</span>
					<?php } ?>
				</li>
			</ul>

			<div class="fd-small info-actions">
				<a href="<?php echo FRoute::groups( array( 'layout' => 'item', 'type' => 'info', 'id' => $group->getAlias() ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_MORE_ABOUT_THIS_GROUP' ); ?></a>

				<?php if( $this->access->allowed( 'reports.submit' ) ){ ?>
				&middot; <?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_GROUPS , $group->id , $group->getName() , JText::_( 'COM_EASYSOCIAL_GROUPS_REPORT_THIS_GROUP' ) ); ?>
				<?php } ?>
			</div>

		</div>

		<?php if ( ( !isset($showApps) || (isset($showApps) && $showApps)) && $group->getApps() && ( $group->isMember() || $group->isOpen() ) ){ ?>
		<div class="btn- btn-scroll" data-appscroll-buttons>
			<a href="javascript:void(0);" class="btn btn-left" data-appscroll-prev-button>
				<i class="fa fa-caret-left"></i>
			</a>
			<a href="javascript:void(0);" class="btn btn-right" data-appscroll-next-button>
				<i class="fa fa-caret-right"></i>
			</a>
		</div>

		<div class="es-header-mini-apps-action" data-appscroll-viewport>
			<ul class="fd-nav es-nav-apps" data-appscroll-content>
				<?php foreach( $group->getApps() as $app ){ ?>
				<li>
					<a class="btn btn-clean" href="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() , 'appId' => $app->getAlias() ) );?>">
						<span><?php echo $app->getAppTitle(); ?></span>
						<img src="<?php echo $app->getIcon();?>" class="es-nav-apps-icons" />
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>

	</div>

	<div class="es-header-mini-footer">
		<div class="pull-left">
			<div class="es-list-vertical-divider mb-0 ml-0">
				<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupStatsStart' , array( $group ) ); ?>
				<span>
					<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $group->getCategory()->getAlias() ) );?>">
						<i class="fa fa-database"></i> <?php echo $group->getCategory()->get( 'title' ); ?>
					</a>
				</span>

	            <?php if ($this->config->get('video.enabled', true) && $group->getParams()->get('videos', true)) { ?>
	            <span>
	                <a href="<?php echo FRoute::videos(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>">

	                    <i class="fa fa-film"></i>
	                    &#8207;
	                    <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_EVENTS_VIDEOS', $group->getTotalVideos()), $group->getTotalVideos()); ?>
	                </a>
	            </span>
	            <?php } ?>

	            <?php if ($this->config->get('photos.enabled', true) && $group->getCategory()->getAcl()->get('photos.enabled', true) && $group->getParams()->get('photo.albums', true)) { ?>
				<span>
					<a href="<?php echo FRoute::albums( array( 'uid' => $group->getAlias() , 'type' => SOCIAL_TYPE_GROUP ) );?>">
						<i class="fa fa-photo"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_ALBUMS' , $group->getTotalAlbums() ) , $group->getTotalAlbums() ); ?>
					</a>
				</span>
				<?php } ?>
				
				<span>
					<i class="fa fa-users"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_MEMBERS' , $group->getTotalMembers() ) , $group->getTotalMembers() ); ?>
				</span>

				<?php if ($this->config->get('groups.hits.display')) { ?>
				<span>
					<i class="fa fa-eye"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_VIEWS' , $group->hits ) , $group->hits ); ?></a>
				</span>
				<?php } ?>
				
				<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupStatsEnd' , array( $group ) ); ?>
				<span>
					<?php echo FD::sharing( array('url' => $group->getPermalink(false, true), 'display' => 'dialog', 'text' => JText::_( 'COM_EASYSOCIAL_STREAM_SOCIAL' ) , 'css' => 'fd-small' ) )->getHTML( true ); ?>
				</span>
			</div>
		</div>

        <?php if( !$group->isMember() && !$group->isPendingMember() ){ ?>
		<div class="pull-right">
			<span class="action">

				<a class="btn btn-es-primary" href="javascript:void(0);" data-es-group-join><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_JOIN_THIS_GROUP' );?> &rarr;</a>

			</span>
		</div>
        <?php } ?>

	</div>
</div>
