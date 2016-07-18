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
<div class="es-app-item" data-apps-item data-id="<?php echo $app->id; ?>">
	<div class="es-app row-table">
		<div class="col-cell es-app-thumbnail">
			<img class="es-app-image" alt="" src="<?php echo $app->getIcon( SOCIAL_APPS_ICON_LARGE );?>">
		</div>

		<div class="col-cell es-app-details">
			<div class="es-app-header row-table">
				<div class="col-cell">
					<b class="es-app-name">
						<?php echo $app->getAppTitle();?>
					</b>

					<div class="es-app-version">
						v<?php echo $app->getMeta()->version; ?>
					</div>
				</div>

				<?php if( !$app->default ){ ?>
				<div class="col-cell cell-tight es-app-actions">
					<a class="btn btn-medium btn-es btn-sm" <?php if( !$app->hasUserSettings() || !$app->isInstalled() ) { ?>style="display: none;"<?php } ?> data-apps-item-settings>
						<i class="fa fa-cog"></i>
					</a>

					<a class="btn btn-medium btn-es-danger btn-sm" <?php if( !$app->isInstalled() ) { ?>style="display: none;"<?php } ?> href="javascript:void(0);" data-apps-item-installed>
						<?php echo JText::_( 'COM_EASYSOCIAL_UNINSTALL_BUTTON' ); ?>
					</a>

					<a class="btn btn-medium btn-es-primary btn-sm" <?php if( $app->isInstalled() ) { ?>style="display: none;"<?php } ?> href="javascript:void(0);" data-apps-item-install>
						<?php echo JText::_( 'COM_EASYSOCIAL_INSTALL_BUTTON' ); ?>
					</a>
				</div>
				<?php } ?>
			</div>

			<div class="es-app-content mt-10">
				<?php echo JText::_( $app->getUserDesc() ); ?>
			</div>
		</div>
	</div>
</div>
