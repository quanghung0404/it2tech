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
<script type="text/javascript">
jQuery(document).ready( function(){

	<?php if( $reinstall ){ ?>
		es.ajaxUrl	= "<?php echo JURI::root();?>administrator/index.php?option=com_easysocial&ajax=1&reinstall=1";
	<?php } ?>

	<?php if( $update ){ ?>
		es.ajaxUrl	= "<?php echo JURI::root();?>administrator/index.php?option=com_easysocial&ajax=1&update=1";
	<?php } ?>
	// Immediately proceed with synchronization
	es.maintenance.init();

});
</script>
<form name="installation" data-installation-form>

	<p><?php echo JText::_('COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_DESC'); ?></p>

	<div data-sync-progress>
		<ol class="install-logs list-reset" data-progress-logs="">
			<li class="pending" data-progress-syncuser>
				<b class="split__title"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_SYNC_USERS' );?></b>
				<span class="progress-state text-info"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_EXECUTING' );?></span>
				<div class="notes">
					<ul style="list-unstyled" data-progress-syncuser-items>
					</ul>
				</div>
			</li>
			<li class="pending" data-progress-syncprofiles>
				<b class="split__title"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_SYNC_PROFILES' );?></b>
				<span class="progress-state text-info"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_EXECUTING' );?></span>
				<div class="notes">
					<ul style="list-unstyled" data-progress-syncprofile-items>
					</ul>
				</div>
			</li>
			<li class="pending" data-progress-execscript>
				<b class="split__title"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_EXEC_SCRIPTS' );?></b>
				<span class="progress-state text-info"><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_MAINTENANCE_EXECUTING' );?></span>
				<div class="notes">
					<ul style="list-unstyled" data-progress-execscript-items>
					</ul>
				</div>
			</li>
		</ol>
	</div>

	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />

	<?php if( $reinstall ){ ?>
	<input type="hidden" name="reinstall" value="1" />
	<?php } ?>

	<?php if( $update ){ ?>
	<input type="hidden" name="update" value="1" />
	<?php } ?>
</form>
