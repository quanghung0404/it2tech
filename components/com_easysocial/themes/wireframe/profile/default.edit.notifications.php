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
<form method="post" action="<?php echo JRoute::_( 'index.php' );?>" data-profile-notifications-form class="form-horizontal">
<div class="es-container" data-edit-notification>
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>
	<div class="es-sidebar" data-sidebar>

		<?php echo $this->render( 'module' , 'es-profile-editnotifications-sidebar-top' ); ?>

		<?php $i = 0; ?>
		<?php foreach( array( 'system', 'others' ) as $group ) {
			if( isset( $alerts[ $group ] ) ) {
		?>
		<div class="es-widget es-widget-borderless">
			<div class="es-widget-head">
                <div class="widget-title pull-left">
				    <?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_SIDEBAR_NOTIFICATIONS_GROUP_' . strtoupper( $group ) );?>
                </div>
			</div>

			<div class="es-widget-body">
				<ul class="fd-nav fd-nav-stacked">
				<?php

					foreach( $alerts[$group] as $element => $alert ) { ?>
					<?php $active = $i == 0 ? 'class="active"' : ''; ?>

					<li data-notification-item data-alert-element="<?php echo $element; ?>" <?php echo $active; ?>>
						<a href="javascript:void(0);"><?php echo $alert['title']; ?></a>
					</li>

					<?php $i++; ?>
				<?php
					}
				?>
				</ul>
			</div>
		</div>
		<?php
		 		}
			}
		?>

		<?php echo $this->render( 'module' , 'es-profile-editnotifications-sidebar-bottom' ); ?>
	</div>

	<div class="es-content">

		<?php echo $this->render( 'module' , 'es-profile-editnotifications-before-contents' ); ?>

		<div class="tab-content notification-content form-notifications">
		<?php $i = 0; ?>
		<?php foreach( array( 'system', 'others' ) as $group ) {
				if( isset( $alerts[$group] ) ) {
		?>
			<?php foreach( $alerts[$group] as $element => $alert ) { ?>

				<?php $display = $i > 0 ? 'style="display: none;"' : ''; ?>
				<div class="mt-15 mb-15 mr-15 ml-15 notification-content-<?php echo $element; ?>" data-notification-content data-alert-element="<?php echo $element; ?>" <?php echo $display; ?>>
					<div class="h5 es-title-font"><?php echo $alert[ 'title' ];?></div>
					<hr />
					<table width="100%">
						<tr>
							<td width="55%">&nbsp;</td>
							<td width="5%">&nbsp;</td>
							<td width="20%" style="text-align:center;"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_SYSTEM'); ?></td>
							<td width="20%" style="text-align:center;"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_NOTIFICATION_EMAIL'); ?></td>
						</tr>
						<?php foreach( $alert[ 'data' ] as $rule ) { ?>
						<tr>
							<td><span class="fd-small"><?php echo $rule->getTitle(); ?></span></td>
							<td>
								<i class="fa fa-question-circle" <?php echo $this->html( 'bootstrap.popover' , $rule->getTitle() , $rule->getDescription()  , 'bottom' ); ?>></i>
							</td>
							<td class="pa-5 text-center"><?php echo $rule->system >= 0 ? $this->html( 'grid.boolean', 'system[' . $rule->id . ']', $rule->system ) : JText::_( 'COM_EASYSOCIAL_PROFILE_NOTIFICATION_NOT_APPLICABLE' ); ?></td>
							<td class="pa-5 text-center"><?php echo $rule->email >= 0 ? $this->html( 'grid.boolean', 'email[' . $rule->id .']', $rule->email ) : JText::_( 'COM_EASYSOCIAL_PROFILE_NOTIFICATION_NOT_APPLICABLE' ); ?></td>
						</tr>
						<?php } ?>
					</table>
				</div>

				<?php $i++; ?>
			<?php } ?>
		<?php
			}
			}
		?>

		</div>

		<div class="form-actions">
			<div class="pull-right">
				<button class="btn btn-sm btn-es-primary" data-profile-notifications-save><?php echo JText::_( 'COM_EASYSOCIAL_SAVE_BUTTON' );?></button>
			</div>
		</div>

		<?php echo $this->render( 'module' , 'es-profile-editnotifications-after-contents' ); ?>
	</div>
</div>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="profile" />
<input type="hidden" name="task" value="saveNotification" />
<input type="hidden" name="<?php echo FD::token();?>" value="1" />
</form>
