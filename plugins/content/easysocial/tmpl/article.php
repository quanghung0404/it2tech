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
<div id="fd" class="es">

	<?php if ($displayInfo) { ?>
	<div class="es-article-author">
		<div class="row">
			<div class="col-md-12">
				<div class="pull-left">
					<img src="<?php echo $author->getAvatar( SOCIAL_AVATAR_LARGE );?>" class="es-avatar es-avatar-md es-avatar-rounded" />
				</div>

				<div class="pull-left ml-15">
					<h5>
						<a href="<?php echo $author->getPermalink();?>"><?php echo $author->getName();?></a>
					</h5>

					<div class="es-article-author-badges">
						<ul>
							<?php if( $badges ){ ?>
								<?php foreach( $badges as $badge ){ ?>
									<li>
										<a href="<?php echo $badge->getPermalink();?>">
											<img src="<?php echo $badge->getAvatar();?>" data-es-provide="tooltip" data-original-title="<?php echo FD::string()->escape( $badge->get( 'title' ) );?>" />
										</a>
									</li>
								<?php } ?>
							<?php } ?>
						</ul>
					</div>

					<?php if( $author->id != $my->id ){ ?>
					<div class="es-article-author-actions">
						<ul>
							<li>
								<a href="javascript:void(0);"
									data-es-conversations-compose
									data-es-conversations-id="<?php echo $author->id;?>"
								><span><?php echo JText::_( 'PLG_CONTENT_EASYSOCIAL_MESSAGE_AUTHOR' ); ?></span></a>
							</li>
							<?php if( !$author->isFollowed( $my->id ) ){ ?>
							<li>
								<a href="javascript:void(0);"
								data-es-followers-follow
								data-es-followers-id="<?php echo $author->id;?>"><span><?php echo JText::_( 'PLG_CONTENT_EASYSOCIAL_FOLLOW_AUTHOR' ); ?></span></a>
							</li>
							<?php } ?>

							<?php if( !$author->isFriends( $my->id ) ){ ?>
								<?php if( $author->getFriend( $my->id )->state == SOCIAL_FRIENDS_STATE_PENDING ){ ?>
									<li>
										<a href="<?php echo FRoute::friends( array( 'filter' => 'request' ) ); ?>"><span><?php echo JText::_( 'PLG_CONTENT_EASYSOCIAL_PENDING_APPROVAL' ); ?></span></a>
									</li>
								<?php } else { ?>
									<li>
										<a href="javascript:void(0);"
										 data-es-friends-add data-es-friends-id="<?php echo $author->id;?>"
										 ><span><?php echo JText::_( 'PLG_CONTENT_EASYSOCIAL_ADD_AS_FRIEND' ); ?></span></a>
									</li>
								<?php } ?>
							<?php } ?>
						</ul>
					</div>
					<?php } ?>
				</div>

				<div class="pull-right es-article-author-info">
					<a class="btn btn-clean" href="<?php echo FRoute::points( array( 'id' => $author->getAlias() , 'layout' => 'history' ) );?>">
						<div class="center">
							<strong><?php echo JText::_( 'PLG_CONTENT_EASYSOCIAL_POINTS' );?></strong>
						</div>

						<div class="mt-10 center">
							<span class="points-counter"><?php echo $author->getPoints();?></span>
						</div>
					</a>
					<a class="btn btn-clean" href="<?php echo FRoute::friends( array( 'userid' => $author->getAlias() ) );?>">
						<div class="center">
							<strong><?php echo JText::_( 'PLG_CONTENT_EASYSOCIAL_FRIENDS' );?></strong>
						</div>

						<div class="mt-10 center">
							<span class="points-counter"><?php echo $author->getTotalFriends();?></span>
						</div>
					</a>
					<a class="btn btn-clean" href="<?php echo FRoute::followers( array( 'userid' => $author->getAlias() ) );?>">
						<div class="center">
							<strong><?php echo JText::_( 'PLG_CONTENT_EASYSOCIAL_FOLLOWERS' );?></strong>
						</div>

						<div class="mt-10 center">
							<span class="points-counter"><?php echo $author->getTotalFollowers();?></span>
						</div>
					</a>
				</div>
			</div>

		</div>

	</div>
	<?php } ?>

	<?php echo $comments; ?>
</div>
