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
<div class="stream-apps-content mt-10 mb-10">
	<div class="media">
		<div class="media-object pull-left">
			<img class="es-avatar es-avatar-md" src="<?php echo $group->getAvatar();?>" />
		</div>

		<div class="media-body">
			<h4 class="es-stream-content-title">
				<a href="<?php echo $group->getPermalink();?>"><?php echo $group->getName(); ?></a>

				<?php if( $group->isOpen() ){ ?>
				<span class="label label-success" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_GROUPS_OPEN_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
					<i class="fa fa-globe"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?>
				</span>
				<?php } ?>

				<?php if( $group->isClosed() ){ ?>
				<span class="label label-danger" data-original-title="<?php echo FD::_('COM_EASYSOCIAL_GROUPS_CLOSED_GROUP_TOOLTIP' , true );?>" data-es-provide="tooltip" data-placement="bottom">
					<i class="fa fa-lock"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?>
				</span>
				<?php } ?>
			</h4>

			<ul class="stream-apps-meta ml-0 pl-0">
				<li>
					<span>
						<a href="<?php echo FRoute::groups( array( 'layout' => 'category' , 'id' => $group->getCategory()->getAlias() ) );?>">
							<i class="fa fa-database"></i> <?php echo $group->getCategory()->get( 'title' ); ?>
						</a>
					</span>
				</li>
				<li>
					<span>
						<a href="<?php echo FRoute::albums( array( 'uid' => $group->id , 'type' => SOCIAL_TYPE_GROUP ) );?>">
							<i class="fa fa-photo"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_ALBUMS' , $group->getTotalAlbums() ) , $group->getTotalAlbums() ); ?>
						</a>
					</span>
				</li>
				<li>
					<span>
						<i class="fa fa-users"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_MEMBERS' , $group->getTotalMembers() ) , $group->getTotalMembers() ); ?>
					</span>
				</li>
				<li>
					<span>
						<i class="fa fa-eye"></i> <?php echo JText::sprintf( FD::string()->computeNoun( 'COM_EASYSOCIAL_GROUPS_VIEWS' , $group->hits ) , $group->hits ); ?>
					</span>
				</li>
			</ul>

			<p class="mb-10 mt-10 blog-description">
				<?php echo $this->html('string.truncater', $group->getDescription(), 350);?>
			</p>

			<a href="<?php echo $group->getPermalink();?>"><?php echo JText::_( 'APP_USER_GROUPS_VIEW_GROUP' ); ?> &rarr;</a>
		</div>
	</div>
</div>
