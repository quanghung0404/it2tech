<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: default_manual_restore.php 14287 2012-07-23 11:14:16Z haonv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
$session 		= JFactory::getSession();
$restoreResult 	= $session->get('JSNISRestore');
?>
<form action="index.php?option=com_imageshow&controller=maintenance"
	method="POST" name="adminFormRestore" enctype="multipart/form-data"
	id="frm_restore">
	<fieldset>
		<legend>
		<?php echo JText::_('MAINTENANCE_DATA_RESTORE')?>
		</legend>
		<table border="0" width="100%" align="center" cellpadding="2"
			cellspacing="0">
			<?php if (!is_array($restoreResult)) { ?>
			<tr>
				<td>
					<p class="item-title">
					<?php echo JText::_('MAINTENANCE_BACKUP_BACKUP_FILE'); ?>
						:
					</p>
					<p>
						<input type="file" id="file-upload" name="filedata" size="70"
							onchange="return setRestoreButtonState(this.form);" /> <input
							type="hidden" name="option" value="com_imageshow" /> <input
							type="hidden" name="controller" value="maintenance" /> <input
							type="hidden" name="task" value="restore" />
							<?php echo JHTML::_( 'form.token' ); ?>
					</p>
					<div class="form-actions">
						<button class="btn btn-primary disabled" type="button"
							value="<?php echo JText::_('MAINTENANCE_BACKUP_RESTORE'); ?>"
							onclick="return restore();" disabled="disabled"
							name="button_backup_restore">
							<?php echo JText::_('MAINTENANCE_BACKUP_RESTORE'); ?>
						</button>
					</div>
				</td>
			</tr>
			<?php } else if (is_array($restoreResult)) { ?>
			<tr>
				<td>
					<p id="jsn-restore-result">
					<?php echo JText::_('MAINTENANCE_RESTORE_RESULT_HEADER');?>
					</p>
					<ul>
					<?php if (is_array($restoreResult) && $restoreResult['extractFile'] == false){ ?>
						<li><?php echo JText::_('MAINTENANCE_RESTORE_EXTRACT_BACKUP_FILE')?>
							<span class="jsn-icon jsn-icon-failed jsn-restore-icon-failure">&nbsp;</span>
							<p id="jsn-restore-extract-failure">
							<?php echo $restoreResult['message']; ?>
							</p>
						</li>
						<input type="hidden" name="option" value="com_imageshow" />
						<input type="hidden" name="controller" value="maintenance" />
						<input type="hidden" name="task" value="restore" />
						<?php echo JHTML::_( 'form.token' ); ?>
						<?php } else {?>
						<li><?php echo JText::_('MAINTENANCE_RESTORE_EXTRACT_BACKUP_FILE')?>
							<span class="jsn-icon jsn-icon-check jsn-restore-icon-success">&nbsp;</span>
						</li>
						<li id="jsn-restore-data-wrap"><?php echo JText::_('MAINTENANCE_RESTORE_RESTORE_DATA')?>
						<?php if ($restoreResult['requiredSourcesNeedInstall'] || $restoreResult['requiredThemesNeedInstall']) { ?>
							<span class="jsn-icon jsn-icon-failed jsn-restore-icon-failure">&nbsp;</span>
							<div id="jsn-restore-data" class="jsn-restore-icon-process">
								<img
									src="components/com_imageshow/assets/images/icons-16/icon-16-loading-circle.gif" />
							</div>
							<div class="jsn-restore-warning">
								<div id="jsn-restore-install-required-warning">
								<?php
								$elementType = 'theme';
								$pluginInstall = '';

								if (count($restoreResult['requiredSourcesNeedInstall'])){
									$elementType = 'imagesource';
									$pluginInstall = $restoreResult['requiredSourcesNeedInstall'][0]->name;
								} else {
									$pluginInstall = $restoreResult['requiredThemesNeedInstall'][0]->name;
								}
								echo '<p>'.JText::sprintf('MAINTENANCE_RESTORE_INSTALL_PLUGIN_FROM_CUSTOMER_AREA', $pluginInstall).'</p>';
								?>

									<input id="pluign_file" size="55" type="file"
										name="pluign_file" /> <input type="hidden" name="task"
										value="installJSNPluginForRestore" /> <input type="hidden"
										name="option" value="com_imageshow" /> <input type="hidden"
										name="controller" value="maintenance" /> <input type="hidden"
										name="element_type" id="element_type"
										value="<?php echo $elementType; ?>" />
										<?php echo JHTML::_( 'form.token' ); ?>

								</div>
								<ul class="jsn-restore-install-required">
								<?php if (count($restoreResult['requiredSourcesNeedInstall'])) {?>
									<li id="jsn-restore-required-sources"><?php echo JText::_('MAINTENANCE_RESTORE_INSTALL_REQUIRED_IMAGE_SOURCES')?>
										<div class="jsn-restore-icon-process">
											<img
												src="components/com_imageshow/assets/images/icons-16/icon-16-loading-circle.gif" />
										</div> <span class="jsn-restore-change-text"
										id="jsn-restore-source-change-text"></span> <span
										class="jsn-icon jsn-icon-check jsn-restore-icon-success">&nbsp;</span>
									</li>
									<?php } ?>
									<?php if (count($restoreResult['requiredThemesNeedInstall'])) {?>
									<li id="jsn-restore-required-themes"><?php echo JText::_('MAINTENANCE_RESTORE_INSTALL_REQUIRED_IMAGE_THEMES')?>
										<div class="jsn-restore-icon-process">
											<img
												src="components/com_imageshow/assets/images/icons-16/icon-16-loading-circle.gif" />
										</div> <span class="jsn-restore-change-text"
										id="jsn-restore-theme-change-text"></span> <span
										class="jsn-icon jsn-icon-check jsn-restore-icon-success">&nbsp;</span>
									</li>
									<?php }?>
								</ul>
							</div> <?php } else { ?> <span
							class="jsn-icon jsn-icon-check jsn-restore-icon-success">&nbsp;</span>
							<input type="hidden" name="option" value="com_imageshow" /> <input
							type="hidden" name="controller" value="maintenance" /> <input
							type="hidden" name="task" value="restore" /> <?php echo JHTML::_( 'form.token' ); ?>
							<?php } ?>
						</li>
						<?php }?>
					</ul>
					<div id="jsn-restore-database-success"
					<?php echo ($restoreResult['error'] == false)? ' style="display:block;" ' : ''?>>
						<hr />
						<?php echo JText::_('MAINTENANCE_RESTORE_RESTORE_DATABASE_SUCCESS')?>
					</div>
					<div id="jsn-restore-buttons"
						class="form-actions <?php echo ($restoreResult['error'] == false)? ' jsn-restore-installing-success ' : ''?>">
						<?php if ($restoreResult['requiredSourcesNeedInstall'] || $restoreResult['requiredThemesNeedInstall']) { ?>
						<button class="btn agree_install_sample_local" type="submit"
							name="button_installation_data">
							<?php echo JText::_('MAINTENANCE_SAMPLE_DATA_INSTALL_REQUIRED_PLUGIN');?>
						</button>
						<?php } ?>
						<button class="btn jsn-restore-button-cancel" type="button"
							value="<?php echo JText::_('MAINTENANCE_RESTORE_CANCEL'); ?>"
							onclick="return clearSessionRestoreResult();"
							name="button_backup_restore">
							<?php echo JText::_('MAINTENANCE_RESTORE_CANCEL'); ?>
						</button>
						<button class="btn jsn-restore-button-finish" type="button"
							value="<?php echo JText::_('MAINTENANCE_RESTORE_FINISH'); ?>"
							onclick="return clearSessionRestoreResult();"
							name="button_backup_restore">
							<?php echo JText::_('MAINTENANCE_RESTORE_FINISH'); ?>
						</button>
					</div>
				</td>
			</tr>
			<?php } ?>
		</table>
	</fieldset>
</form>
