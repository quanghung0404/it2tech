<?php
/**
 * @version    $Id: default.php 16077 2012-09-17 02:30:25Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// Display messages
if (JRequest::getInt('ajax') != 1)
{
	echo $this->msgs;
}
?>
<div id="jsn-imageshow-help" class="jsn-page-help">
	<div class="jsn-bootstrap">
		<div class="row-fluid">
			<div class="span4">
				<h2 class="jsn-section-header">
				<?php echo JText::_('HELP_DOCUMENTATION'); ?>
				</h2>
				<div class="jsn-section-content">
				<?php echo JText::_('HELP_DES_DOCUMENTATION'); ?>
					<ul>
						<li><a class="jsn-link-action"
							href="http://www.joomlashine.com/joomla-extensions/jsn-imageshow-docs.zip"
							target="_blank"><?php echo JText::_('HELP_DOWNLOAD_PDF_DOCUMENTATION'); ?>
						</a></li>
						<li><a class="jsn-link-action"
							href="http://www.joomlashine.com/joomla-extensions/jsn-imageshow-videos.html"
							target="_blank"><?php echo JText::_('HELP_WATCH_QUICK_START_VIDEO'); ?>
						</a></li>
					</ul>
				</div>
			</div>
			<div class="span4">
				<h2 class="jsn-section-header">
				<?php echo JText::_('HELP_SUPPORT_FORUM'); ?>
				</h2>
				<div class="jsn-section-content">
				<?php echo JText::_('HELP_DES_CHECK_SUPPORT_FORUM'); ?>
					<ul>
					<?php
					if (strtolower($this->xml['edition']) == 'pro standard' || strtolower($this->xml['edition']) == 'pro unlimited')
					{
						?>
						<li><a class="jsn-link-action"
							href="http://www.joomlashine.com/forum/" target="_blank"><?php echo JText::_('HELP_CHECK_SUPPORT_FORUM'); ?>
						</a></li>
						<?php
					}
					?>
					<?php
					if ($this->shortEdition != 'pro')
					{
						?>
						<li><a class="jsn-link-action"
							href="http://www.joomlashine.com/joomla-extensions/buy-jsn-imageshow.html"
							target="_blank"><?php echo JText::_('HELP_BUY_PRO_STANDARD_EDITION'); ?>
						</a></li>
						<?php
					}
					?>
					</ul>
				</div>
			</div>
			<div class="span4">
				<h2 class="jsn-section-header">
				<?php echo JText::_('HELP_HELPDESK_SYSTEM'); ?>
				</h2>
				<div class="jsn-section-content">
				<?php echo JText::_('HELP_DES_HELPDESK_SYSTEM'); ?>
					<ul>
					<?php
					if (strtolower($this->xml['edition']) == 'pro unlimited')
					{
						?>
						<li><a class="jsn-link-action"
							href="http://www.joomlashine.com/dedicated-support.html"
							target="_blank"><?php echo JText::_('HELP_SUBMIT_TICKET_IN_HELPDESK_SYSTEM'); ?>
						</a></li>
						<?php
					}
					?>
					<?php
					if ($this->shortEdition != 'pro')
					{
						?>
						<li><a class="jsn-link-action"
							href="http://www.joomlashine.com/joomla-extensions/buy-jsn-imageshow.html"
							target="_blank"><?php echo JText::_('HELP_BUY_PRO_UNLIMITED_EDITION'); ?>
						</a></li>
						<?php
					}
					?>
					<?php
					if (strtolower($this->xml['edition']) == 'pro standard')
					{
						?>
						<li><a class="jsn-link-action"
							href="http://www.joomlashine.com/docs/general/how-to-upgrade-to-pro-unlimited-edition.html"
							target="_blank"><?php echo JText::_('HELP_UPGRADE_TO_PRO_UNLIMITED_EDITION'); ?>
						</a></li>
						<?php
							}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
