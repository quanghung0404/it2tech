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
<?php if( $pendingUsers ){ ?>
<!-- <h4><?php echo JText::_( 'COM_EASYSOCIAL_WIDGET_TITLE_PENDING_USERS' );?></h4> -->
<!-- <hr /> -->

<ul class="list-unstyled es-items-list" data-widget-pending-users>
	<?php if( $pendingUsers ){ ?>
		<?php foreach( $pendingUsers as $user ){ ?>
			<li data-pending-item data-id="<?php echo $user->id;?>">
				<div class="es-media">
					<div class="es-media-object">
						<a href="<?php echo $user->getPermalink();?>" class="es-avatar ml-0 mr-0" target="_blank">
							<img src="<?php echo $user->getAvatar( SOCIAL_AVATAR_LARGE );?>" class="es-user-avatar"/>
						</a>
					</div>
					<div class="es-media-body">
						<div class="row">
							<div class="col-md-8">
								<div class="mb-5"><b><?php echo $user->getName();?> (<?php echo $user->username; ?>)</b></div>
								<i class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_REGISTERED' );?> <?php echo $user->getRegistrationDate()->toLapsed();?></i>
							</div>
							<div class="col-md-4 es-form-control">
								<a href="javascript:void(0);" class="btn btn-sm btn-es-success" data-pending-approve>
									<?php echo JText::_( 'COM_EASYSOCIAL_APPROVE_BUTTON' ); ?>
								</a>
								<a href="javascript:void(0);" class="btn btn-sm btn-es-danger" data-pending-reject>
									<?php echo JText::_( 'COM_EASYSOCIAL_DECLINE_BUTTON' ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</li>

		<?php } ?>
	<?php } ?>
</ul>
<?php } ?>
