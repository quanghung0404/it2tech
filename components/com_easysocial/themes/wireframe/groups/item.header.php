<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-profile-header"
	data-id="<?php echo $group->id;?>"
	data-name="<?php echo $this->html( 'string.escape' , $group->getName() );?>"
	data-avatar="<?php echo $group->getAvatar();?>">

	<div class="es-profile-header-heading with-cover">
		<?php echo $this->includeTemplate( 'site/groups/cover', array('cover' => $group->getCoverData()) ); ?>
		<?php echo $this->includeTemplate( 'site/groups/avatar' ); ?>
		<?php echo $this->render( 'widgets' , 'group' , 'item' , 'afterAvatar' , array( $group ) ); ?>
	</div>

	<div class="es-profile-header-body fd-cf">
		<div class="es-profile-header-action pull-right">
			<?php echo $this->render('module', 'es-groups-before-actions'); ?>
			<?php echo $this->render('widgets', 'group', 'item', 'beforeActions', array($group)); ?>

			<?php if ($group->isPendingMember()) { ?>
			<div>
				<div class="btn-group">
					<a class="btn btn-block btn-es dropdown-toggle btn-sm" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="fa fa-eye"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_PENDING_APPROVAL' );?> <i class="fa fa-caret-down"></i></a>
					<ul class="dropdown-menu dropdown-menu-user messageDropDown">
						<li>
							<a href="javascript:void(0);" data-es-group-withdraw><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_WITHDRAW_REQUEST' );?></a>
						</li>
					</ul>
				</div>
			</div>
			<?php } ?>

			<?php if ($group->isInvited() && !$group->isMember()) { ?>
			<div>
				<a class="btn btn-block btn-es-success btn-sm" href="javascript:void(0);" data-es-group-respond>
					<i class="fa fa-flash mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_RESPOND_TO_INVITATION' );?>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->my->getAccess()->get('groups.allow.join') && !$group->isInviteOnly() && !$group->isMember() && !$group->isPendingMember() && !$group->isInvited()) { ?>
			<div>
				<a class="btn btn-block btn-es-success btn-sm" href="javascript:void(0);" data-es-group-join>
					<i class="fa fa-flash mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_JOIN_THIS_GROUP');?>
				</a>
			</div>
			<?php } ?>

			<?php if ($group->isMember()) { ?>
			<div>
				<a class="btn btn-block btn-es btn-sm" href="javascript:void(0);" data-es-group-invite>
					<i class="fa fa-paper-plane-o mr-5"></i>
					<?php echo JText::_('COM_EASYSOCIAL_GROUPS_INVITE_FRIENDS');?>
				</a>
			</div>
			<?php } ?>

			<?php if ($group->isMember() && !$group->isOwner()) { ?>
			<div>
				<a class="btn btn-block btn-sm btn-es-danger" href="javascript:void(0);" data-es-group-leave>
					<i class="fa fa-sign-out mr-5"></i>
					<?php echo JText::_('COM_EASYSOCIAL_GROUPS_LEAVE_GROUP');?>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->my->isSiteAdmin() || $group->isOwner() || $group->isAdmin()) { ?>
			<div class="dropdown_">
				<a class="btn btn-block btn-es-primary btn-sm" href="javascript:void(0);" data-bs-toggle="dropdown">
					<i class="fa fa-cog mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_GROUPS_MANAGE_GROUP');?> <i class="fa fa-caret-down"></i>
				</a>

				<ul class="dropdown-menu dropdown-menu-user messageDropDown">
					<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupAdminStart' , array( $group ) ); ?>

					<li>
						<a href="<?php echo FRoute::groups( array( 'layout' => 'edit' , 'id' => $group->getAlias() ) );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_EDIT_GROUP' );?></a>
					</li>

					<?php if($this->my->isSiteAdmin() || $group->isOwner()){ ?>
					<li class="divider"></li>
					<li>
						<a href="javascript:void(0);" data-es-group-unpublish><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_UNPUBLISH_GROUP' );?></a>
					</li>
					<li>
						<a href="javascript:void(0);" data-es-group-delete><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_DELETE_GROUP' );?></a>
					</li>
					<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupAdminEnd' , array( $group ) ); ?>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>

			<?php echo $this->render( 'module' , 'es-groups-after-actions' ); ?>
			<?php echo $this->render( 'widgets' , 'group' , 'item' , 'afterActions' , array( $group ) ); ?>
		</div>

		<div>
			<?php echo $this->render( 'module' , 'es-groups-before-name' ); ?>

			<h2 class="es-profile-header-title">
				<a href="<?php echo $group->getPermalink();?>"><?php echo $group->getName();?></a>
			</h2>

			<?php echo $this->render( 'module' , 'es-groups-after-name' ); ?>

			<nav class="es-list-vertical-divider mt-10">
				<?php if( $group->isOpen() ){ ?>
				<span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
					<i class="fa fa-globe muted"></i>
					<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?>
				</span>
				<?php } ?>

				<?php if( $group->isClosed() ){ ?>
				<span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP_TOOLTIP');?>" data-es-provide="tooltip" data-placement="bottom">
					<i class="fa fa-lock muted"></i>
					<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?>
				</span>
				<?php } ?>


				<?php if( $group->isInviteOnly() ){ ?>
				<span data-original-title="<?php echo FD::_('COM_EASYSOCIAL_GROUPS_INVITE_GROUP_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="bottom">
					<i class="fa fa-lock muted"></i>
					<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITE_GROUP' ); ?>
				</span>
				<?php } ?>

				<span>
					<i class="fa fa-folder muted"></i>
					<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $group->getCategory()->getAlias() ) );?>">
						<?php echo $group->getCategory()->get( 'title' ); ?>
					</a>
				</span>

				<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'afterCategory' , array( $group ) ); ?>
			</nav>

			<?php if( !$group->isOwner() && $this->access->allowed( 'reports.submit' ) && $this->config->get( 'reports.enabled' ) ){ ?>
			<div class="page-more">
				<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_GROUPS , $group->id , $group->getName() , JText::_( 'COM_EASYSOCIAL_GROUPS_REPORT_GROUP' ) ); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<div class="es-profile-header-footer">
		<nav class="es-list-vertical-divider pull-left">
			<?php echo $this->render('widgets', 'group', 'groups', 'groupStatsStart', array($group)); ?>

            <?php if ($this->config->get('video.enabled', true) && $group->getParams()->get('videos', true) && $group->getCategory()->getAcl()->get('videos.create', true)) { ?>
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

					<i class="fa fa-photo"></i>
                    &#8207;
					<?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_ALBUMS' , $group->getTotalAlbums() ) , $group->getTotalAlbums() ); ?>
				</a>
			</span>
			<?php } ?>

			<?php if ($this->config->get('groups.hits.display')) { ?>
			<span>
				<i class="fa fa-eye"></i>
                &#8207;
				<?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_VIEWS' , $group->hits ) , $group->hits ); ?>
			</span>
			<?php } ?>

			<span>
                &#8207;
				<?php echo FD::sharing( array( 'url' => $group->getPermalink(false, true), 'display' => 'dialog', 'text' => JText::_( 'COM_EASYSOCIAL_STREAM_SOCIAL' ) , 'css' => 'fd-small' ) )->getHTML( true ); ?>
			</span>
			<?php echo $this->render( 'widgets' , 'group' , 'groups' , 'groupStatsEnd' , array( $group ) ); ?>
		</nav>
	</div>
</div>
