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
<form name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" data-cb-migrator-form>
<div class="migrators">
	<div class="row">
		<div class="col-md-6">
			<div class="panel" data-start-widget>
				<div class="panel-head">
					<b><?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_JS_READ_FIRST_TITLE' );?></b>
					<p><?php echo JText::_( 'COM_EASYSOCIAL_MIGRATORS_CB_DESC' ); ?></p>
				</div>

				<div class="panel-body">
					<p class="mt-20"><?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_JS_ENSURE' ); ?></p>

					<ol class="instructions mb-20">
						<li>
							<?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_BACKUP_EXISTING_DB' );?>
						</li>
						<li>
							<?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_SET_OFFLINE' );?>
						</li>
					</ol>

					<hr />
					<?php if( $installed ){ ?>
						<a href="javascript:void(0);" class="btn btn-large btn-es-primary" data-initiate-migration><?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_RUN_NOW' );?></a>
					<?php } else { ?>
						<div class="text-error">
							<strong><i class="icon-es-delete mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_CB_NOT_FOUND' ); ?></strong>
						</div>
					<?php } ?>
					<hr>

					<div class="mt-20 small">
						<p><span class="label label-danger small"><?php echo JText::_( 'COM_EASYSOCIAL_FOOTPRINT_NOTE' );?>:</span></p>

						<ol>
							<li>
								<?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_CB_WARNING_TEXT' );?>
							</li>
							<li>
								<?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_JOMSOCIAL_FOOTNOTE' );?>
							</li>
						</ol>
					</div>
				</div>
			</div>

			<?php if( $installed ){ ?>
				<?php echo $this->includeTemplate( 'admin/migrators/cb.bind' ); ?>
			<?php } ?>

		</div>
	</div>

	<div class="row">
		<div class="col-md-6">

			<div class="panel" data-migration-result style="display: none;">
				<div class="panel-head">
					<b><?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_RESULT' );?></b>
				</div>

				<div class="panel-body">
					<div class="es-progress-wrap">
						<div class="discoverProgress" stlye="display: none;">
							<div style="width: 0%;text-align:left;padding-left: 5px;" class="bar"></div>
							<div class="progress-result"></div>
						</div>
					</div>

					<a href="javascript:void(0);" class="viewLog btn btn-es-inverse btn-medium" style="display: none;">
						<?php echo JText::_( 'COM_EASYSOCIAL_VIEW_LOGS_BUTTON' );?>
					</a>

					<ul class="scannedResult es-scanned-result list-unstyled">
						<li class="empty">
							<?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_JOMSOCIAL_NO_ITEM' ); ?>
						</li>
					</ul>
				</div>
			</div>

			<a href="<?php echo JRoute::_( 'index.php?option=com_easysocial&view=migrators&layout=cb' ); ?>" style="display: none;" data-jomsocial-back-button >
				<?php echo JText::_( 'COM_EASYSOCIAL_MIGRATOR_BACK_TO_CB_PAGE' );?>
			</a>
		</div>
	</div>
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="controller" value="migrators" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' );?>
</div>

</form>
