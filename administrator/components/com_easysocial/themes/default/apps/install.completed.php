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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="row">

	<div class="col-md-4">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_APPS_APPLICATION_INFO' );?></b>
				<p><?php echo $desc; ?></p>
			</div>

			<div class="panel-body">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td>
								<?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_AUTHOR' );?>
							</td>
							<td>
								<?php echo $meta->author; ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_VERSION' );?>
							</td>
							<td>
								<?php echo $meta->version; ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALLER_WEBSITE' );?>
							</td>
							<td>
								<a href="<?php echo $meta->url; ?>" target="_blank"><?php echo $meta->url; ?></a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALL_COMPLETED_WIDGET_TITLE' );?></b>
				<?php if( !empty( $output ) ){ ?>
				<p><?php echo $output; ?></p>
				<?php } else { ?>
				<p><?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALL_INSTALLATION_COMPLETED_MESSAGE' ); ?></p>
				<?php } ?>
			</div>

			<div class="panel-body">
				<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=apps' );?>" class="btn btn-es-primary btn-medium">
					<?php echo JText::_( 'COM_EASYSOCIAL_BACK_TO_APPLICATION_LISTINGS' ); ?>
				</a>
				<?php echo JText::_( 'COM_EASYSOCIAL_OR' ); ?> <a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=apps&layout=install' );?>"><?php echo JText::_( 'COM_EASYSOCIAL_APPS_INSTALL_OTHER_APPS' );?></a>
			</div>
		</div>
	</div>

</div>
