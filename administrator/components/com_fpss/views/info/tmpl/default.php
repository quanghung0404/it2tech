<?php
/**
 * @version		$Id: default.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<form action="index.php" method="post" name="adminForm">
	<table class="table" cellpadding="0" cellspacing="0" border="0" id="fpssInfoPage">
		<tr>
			<td>
			  <fieldset class="adminform">
			    <legend><?php echo JText::_('FPSS_SYSTEM_INFORMATION'); ?></legend>
			    <table class="adminlist table table-striped">
			      <thead>
			        <tr>
			          <th><?php echo JText::_('FPSS_CHECK'); ?></th>
			          <th><?php echo JText::_('FPSS_RESULT'); ?></th>
			        </tr>
			      </thead>
			      <tbody>
			        <tr>
			          <td><strong><?php echo JText::_('FPSS_WEB_SERVER'); ?></strong></td>
			          <td><?php echo $this->server; ?></td>
			        </tr>
			        <tr>
			          <td><strong><?php echo JText::_('FPSS_PHP_VERSION'); ?></strong></td>
			          <td><?php echo $this->php_version; ?></td>
			        </tr>
			        <tr>
			          <td><strong><?php echo JText::_('FPSS_MYSQL_VERSION'); ?></strong></td>
			          <td><?php echo $this->db_version; ?></td>
			        </tr>
			        <tr>
			          <td><strong><?php echo JText::_('FPSS_GD_IMAGE_LIBRARY'); ?></strong></td>
			          <td><?php if ($this->gd_check) {$gdinfo=gd_info(); echo $gdinfo["GD Version"];} else echo JText::_('FPSS_DISABLED'); ?></td>
			        </tr>
			        <tr>
			          <td><strong><?php echo JText::_('FPSS_UPLOAD_LIMIT'); ?></strong></td>
			          <td><?php echo ini_get('upload_max_filesize'); ?></td>
			        </tr>
			        <tr>
			          <td><strong><?php echo JText::_('FPSS_MEMORY_LIMIT'); ?></strong></td>
			          <td><?php echo ini_get('memory_limit'); ?></td>
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
			<td>
			  <fieldset class="adminform">
			    <legend><?php echo JText::_('FPSS_DIRECTORY_PERMISSIONS'); ?></legend>
			    <table class="adminlist table table-striped">
			      <thead>
			        <tr>
			          <th><?php echo JText::_('FPSS_CHECK'); ?></th>
			          <th><?php echo JText::_('FPSS_RESULT'); ?></th>
			        </tr>
			      </thead>
			      <tfoot>
			        <tr>
			          <th colspan="2">&nbsp;</th>
			        </tr>
			      </tfoot>
			      <tbody>
			        <tr>
			          <td><strong>media/com_fpss</strong></td>
			          <td><?php if ($this->media_folder_check) echo JText::_('FPSS_WRITABLE'); else echo JText::_('FPSS_NOT_WRITABLE'); ?></td>
			        </tr>
			        <tr>
			          <td><strong>cache</strong></td>
			          <td><?php if ($this->cache_folder_check) echo JText::_('FPSS_WRITABLE'); else echo JText::_('FPSS_NOT_WRITABLE'); ?></td>
			        </tr>
			      </tbody>
			    </table>
			  </fieldset>	
			  <fieldset class="adminform">
			    <legend><?php echo JText::_('FPSS_MODULES'); ?></legend>
			    <table class="adminlist table table-striped">
			      <thead>
			        <tr>
			          <th><?php echo JText::_('FPSS_CHECK'); ?></th>
			          <th><?php echo JText::_('FPSS_RESULT'); ?></th>
			        </tr>
			      </thead>
			      <tfoot>
			        <tr>
			          <th colspan="2">&nbsp;</th>
			        </tr>
			      </tfoot>
			      <tbody>
			        <tr>
			          <td><strong>mod_fpss</strong></td>
			          <td><?php echo (is_null(JModuleHelper::getModule('mod_fpss')))?JText::_('FPSS_NOT_INSTALLED'):JText::_('FPSS_INSTALLED'); ?></td>
			        </tr>
			        <tr>
			          <td><strong>mod_fpss_stats</strong> (<?php echo JText::_('FPSS_ADMINISTRATOR'); ?>)</td>
			          <td><?php echo (is_null(JModuleHelper::getModule('mod_fpss'))) ? JText::_('FPSS_NOT_INSTALLE') : JText::_('FPSS_INSTALLED'); ?></td>
			        </tr>
			      </tbody>
			    </table>
			  </fieldset>	
			</td>
		</tr>
	</table>
</form>
