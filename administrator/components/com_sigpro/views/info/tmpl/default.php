<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die;

?>
<div class="sigTopWhiteFix"></div>
<div class="sigGrayFix"></div>
<div id="sigPro" class="J<?php echo $this->version; ?>">
	<div class="sigProHeader sigLight">
		<div class="sigProTopRow"></div>
		<div class="sigProLogo sigFloatLeft"> <i class="hidden"><?php echo JText::_('COM_SIGPRO'); ?></i> </div>
		<span class="sigMenuVersion"><?php echo JText::_('COM_SIGPRO_VERSION'); ?></span>
		<div class="sigProMainMenu sigFloatRight">
			<ul class="sigThin sigLightMenu">
				<li>
					<a href="index.php?option=com_sigpro&amp;view=galleries&amp;type=site" class="sigProMenuItems"><i class="sig-icon sig-icon-picture"></i>Site Galleries</a>
				</li>
				<li>
					<a href="index.php?option=com_sigpro&amp;view=galleries&amp;type=k2" class="sigProMenuItems"><i class="sig-icon sig-icon-picture"></i>K2 Galleries</a>
				</li>
				<li>
					<a href="index.php?option=com_sigpro&amp;view=media" class="sigProMenuItems"><i class="sig-icon sig-icon-archive"></i>Media Manager</a>
				</li>
			</ul>
		</div>
	</div>
	<div id="adminFormContainer" class="sigInformationForm">
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="sigInformationForm">
			<div class="sigProToolbar sigTransition sigBoxSizing sigSlidingItem">
				<div class="sigProUpperToolbar sigInformationToolbar">
					<h3 class="sigProPageTitle sigPurple"><?php echo strip_tags($this->title); ?></h3>
				</div>
			</div>
			<div class="sigInformationNav sigSideNavBar sigTransition sigSlidingItem">
				<ul class="sigSideNav" id="sigInfoNav">
					<li>
						<a class="sigAnchor" href="#sigAbout"><?php echo JText::_('COM_SIGPRO_ABOUT'); ?></a>
					</li>
					<li>
						<a class="sigAnchor" href="#sigCredits"><?php echo JText::_('COM_SIGPRO_CREDITS'); ?></a>
					</li>
					<li>
						<a class="sigAnchor" href="#sigSysInfo"><?php echo JText::_('COM_SIGPRO_SYSTEM_INFORMATION'); ?></a>
					</li>
					<li>
						<a class="sigAnchor" href="#sigPermissions"><?php echo JText::_('COM_SIGPRO_DIRECTORY_PERMISSIONS'); ?></a>
					</li>
					<li>
						<a class="sigAnchor" href="#sigPlugins"><?php echo JText::_('COM_SIGPRO_PLUGINS'); ?></a>
					</li>
				</ul>
			</div>
			<?php echo $this->sidebar; ?>
			<div class="sigProGrid sigInformationWrap sigTransition sigSlidingItem">
				<table cellpadding="0" cellspacing="0" border="0" id="sigProInfoTable" class="table">
					<tr>
						<td id="sigAbout">
							<fieldset class="adminform">
								<legend> <?php echo JText::_('COM_SIGPRO_ABOUT'); ?> </legend>
								<div> <?php echo JText::_('COM_SIGPRO_ABOUT_TEXT'); ?> </div>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td id="sigCredits">
							<fieldset class="adminform">
								<legend> <?php echo JText::_('COM_SIGPRO_CREDITS'); ?> </legend>
								<table class="adminlist table table-striped">
									<thead>
										<tr>
											<th><?php echo JText::_('COM_SIGPRO_PROVIDER'); ?></th>
											<th><?php echo JText::_('COM_SIGPRO_VERSION'); ?></th>
											<th><?php echo JText::_('COM_SIGPRO_TYPE'); ?></th>
											<th><?php echo JText::_('COM_SIGPRO_LICENSE'); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><a target="_blank" href="http://jquery.com">jQuery</a></td>
											<td>1.x</td>
											<td><?php echo JText::_('COM_SIGPRO_JS_LIB'); ?></td>
											<td><?php echo JText::_('COM_SIGPRO_MIT'); ?></td>
										</tr>
										<tr>
											<td><a target="_blank" href="http://jqueryui.com/">jQuery UI</a></td>
											<td>1.8.x</td>
											<td><?php echo JText::_('COM_SIGPRO_JS_LIB'); ?></td>
											<td><?php echo JText::_('COM_SIGPRO_MIT'); ?></td>
										</tr>
										<tr>
											<td><a target="_blank" href="http://www.plupload.com/">Plupload</a></td>
											<td>1.5.6</td>
											<td><?php echo JText::_('COM_SIGPRO_UPLOAD_HANDLER'); ?></td>
											<td><?php echo JText::_('COM_SIGPRO_GPL'); ?></td>
										</tr>
										<tr>
											<td><a target="_blank" href="http://elfinder.org/">elFinder</a></td>
											<td>2.0 (rc1)</td>
											<td><?php echo JText::_('COM_SIGPRO_FILE_MANAGER'); ?></td>
											<td><?php echo JText::_('COM_SIGPRO_BSD'); ?></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="4">&nbsp;</th>
										</tr>
									</tfoot>
								</table>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td id="sigSysInfo">
							<fieldset class="adminform">
								<legend> <?php echo JText::_('COM_SIGPRO_SYSTEM_INFORMATION'); ?> </legend>
								<table class="adminlist table table-striped">
									<thead>
										<tr>
											<th><?php echo JText::_('COM_SIGPRO_CHECK'); ?></th>
											<th><?php echo JText::_('COM_SIGPRO_RESULT'); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><strong><?php echo JText::_('COM_SIGPRO_PHP_VERSION'); ?></strong></td>
											<td><?php echo phpversion(); ?></td>
										</tr>
										<tr>
											<td><strong><?php echo JText::_('COM_SIGPRO_GD_IMAGE_LIBRARY'); ?></strong></td>
											<td><?php echo ($this->info['gd'])? $this->info['gd'] : JText::_('COM_SIGPRO_DISABLED'); ?></td>
										</tr>
										<tr>
											<td><strong><?php echo JText::_('COM_SIGPRO_UPLOAD_LIMIT'); ?></strong></td>
											<td><?php echo $this->info['upload']; ?></td>
										</tr>
										<tr>
											<td><strong><?php echo JText::_('COM_SIGPRO_MEMORY_LIMIT'); ?></strong></td>
											<td><?php echo $this->info['memory']; ?></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="2">&nbsp;</th>
										</tr>
									</tfoot>
								</table>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td id="sigPermissions">
							<fieldset class="adminform">
								<legend> <?php echo JText::_('COM_SIGPRO_DIRECTORY_PERMISSIONS'); ?> </legend>
								<table class="adminlist table table-striped">
									<thead>
										<tr>
											<th><?php echo JText::_('COM_SIGPRO_CHECK'); ?></th>
											<th><?php echo JText::_('COM_SIGPRO_RESULT'); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($this->info['permissions'] as $folder => $isWritable): ?>
										<tr>
											<td><strong><?php echo $folder; ?></strong></td>
											<td><?php echo ($isWritable)?JText::_('COM_SIGPRO_WRITABLE'):JText::_('COM_SIGPRO_NOT_WRITABLE'); ?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="2">&nbsp;</th>
										</tr>
									</tfoot>
								</table>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td id="sigPlugins">
							<fieldset class="adminform">
								<legend> <?php echo JText::_('COM_SIGPRO_PLUGINS'); ?> </legend>
								<table class="adminlist table table-striped">
									<thead>
										<tr>
											<th><?php echo JText::_('COM_SIGPRO_CHECK'); ?></th>
											<th><?php echo JText::_('COM_SIGPRO_RESULT'); ?></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><strong>Content - Simple Image Gallery PRO</strong></td>
											<td><?php echo ($this->info['plg_content_sigpro'])?JText::_('COM_SIGPRO_INSTALLED'):JText::_('COM_SIGPRO_NOT_INSTALLED'); ?>- <?php echo ($this->info['plg_content_sigpro_enabled'])?JText::_('COM_SIGPRO_ENABLED'):JText::_('COM_SIGPRO_DISABLED'); ?></td>
										</tr>
										<tr>
											<td><strong>K2 - Simple Image Gallery PRO</strong></td>
											<td><?php echo ($this->info['plg_k2_sigpro'])?JText::_('COM_SIGPRO_INSTALLED'):JText::_('COM_SIGPRO_NOT_INSTALLED'); ?>- <?php echo ($this->info['plg_k2_sigpro_enabled'])?JText::_('COM_SIGPRO_ENABLED'):JText::_('COM_SIGPRO_DISABLED'); ?></td>
										</tr>
										<tr>
											<td><strong>Editors XTD - Simple Image Gallery PRO</strong></td>
											<td><?php echo ($this->info['plg_editors-xtd_sigpro'])?JText::_('COM_SIGPRO_INSTALLED'):JText::_('COM_SIGPRO_NOT_INSTALLED'); ?>- <?php echo ($this->info['plg_editors-xtd_sigpro_enabled'])?JText::_('COM_SIGPRO_ENABLED'):JText::_('COM_SIGPRO_DISABLED'); ?></td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="2">&nbsp;</th>
										</tr>
									</tfoot>
								</table>
							</fieldset>
						</td>
					</tr>
				</table>
				<div class="sigInformationDummyHeight"></div>
			</div>
		</form>
	</div>
</div>
<div class="sigBtmWhiteFix"></div>
