<?php
/**
 * @version		$Id: script.fpss.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class Com_fpssInstallerScript
{
	function postflight($type, $parent)
	{
		require_once (JPATH_ADMINISTRATOR.'/components/com_fpss/helpers/legacy.php');
		FPSSHelperLegacy::setup();
		$db = JFactory::getDBO();
		$status = new stdClass;
		$status->modules = array();
		$src = $parent->getParent()->getPath('source');
		$manifest = $parent->getParent()->manifest;
		$modules = $manifest->xpath('modules/module');
		foreach ($modules as $module)
		{
			$name = (string)$module->attributes()->module;
			$client = (string)$module->attributes()->client;
			if (is_null($client))
			{
				$client = 'site';
			}
			($client == 'administrator') ? $path = $src.'/administrator/modules/'.$name : $path = $src.'/modules/'.$name;
			$installer = new JInstaller;
			$result = $installer->install($path);
			if ($result)
			{
				$root = $client == 'administrator' ? JPATH_ADMINISTRATOR : JPATH_SITE;
				if (JFile::exists($root.'/modules/'.$name.'/'.$name.'.xml'))
				{
					JFile::delete($root.'/modules/'.$name.'/'.$name.'.xml');
				}
				JFile::move($root.'/modules/'.$name.'/'.$name.'.j25.xml', $root.'/modules/'.$name.'/'.$name.'.xml');
			}
			$status->modules[] = array(
				'name' => $name,
				'client' => $client,
				'result' => $result
			);

		}

		// Publish the statistics module
		$position = version_compare(JVERSION, '3.0', 'ge') ? 'cpanel' : 'icon';
		$query = "UPDATE #__modules SET position=".$db->quote($position).", ordering='100', published=1 WHERE module='mod_fpss_stats'";
		$db->setQuery($query);
		$db->query();
		$query = "SELECT id FROM #__modules WHERE module = 'mod_fpss_stats'";
		$db->setQuery($query);
		$id = (int)$db->loadResult();
		$query = "INSERT IGNORE INTO #__modules_menu (moduleid, menuid) VALUES({$id}, 0)";
		$db->setQuery($query);
		$db->query();

		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_fpss/admin.fpss.php'))
		{
			JFile::delete(JPATH_ADMINISTRATOR.'/components/com_fpss/admin.fpss.php');
		}

		// Install sample data
		$query = "SELECT COUNT(*) FROM #__fpss_slides";
		$db->setQuery($query);
		$numOfSlides = $db->loadResult();

		$query = "SELECT COUNT(*) FROM #__fpss_categories";
		$db->setQuery($query);
		$numOfCategories = $db->loadResult();

		if ($numOfSlides == 0 && $numOfCategories == 0)
		{
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_fpss/tables');
			FPSSModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_fpss/models');
			$model = FPSSModel::getInstance('slides', 'FPSSModel');
			$model->import();
		}

		$this->installationResults($status);
	}

    public function uninstall($parent)
    {
        $db = JFactory::getDBO();
        $status = new stdClass;
        $status->modules = array();
        $status->plugins = array();
        $manifest = $parent->getParent()->manifest;
        $modules = $manifest->xpath('modules/module');
        foreach ($modules as $module)
        {
            $name = (string)$module->attributes()->module;
            $client = (string)$module->attributes()->client;
            $db = JFactory::getDBO();
            $query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='module' AND element = ".$db->Quote($name)."";
            $db->setQuery($query);
            $extensions = $db->loadColumn();
            if (count($extensions))
            {
                foreach ($extensions as $id)
                {
                    $installer = new JInstaller;
                    $result = $installer->uninstall('module', $id);
                }
                $status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
            }
            
        }
        $this->uninstallationResults($status);
    }


	function update($type)
	{
		$db = JFactory::getDBO();
		$fields = $db->getTableColumns('#__fpss_categories');
		if (!array_key_exists('asset_id', $fields))
		{
			$query = "ALTER TABLE #__fpss_categories ADD `asset_id` int(10) unsigned NOT NULL DEFAULT '0'";
			$db->setQuery($query);
			$db->query();
		}
		$fields = $db->getTableColumns('#__fpss_slides');
		if (!array_key_exists('asset_id', $fields))
		{
			$query = "ALTER TABLE #__fpss_slides ADD `asset_id` int(10) unsigned NOT NULL DEFAULT '0'";
			$db->setQuery($query);
			$db->query();
		}
	}

    private function installationResults($status)
    {
        $language = JFactory::getLanguage();
        $language->load('com_fpss');
        $rows = 0; ?>
		<h2><?php echo JText::_('FPSS_INSTALLATION_STATUS'); ?></h2>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th class="title" colspan="2"><?php echo JText::_('FPSS_EXTENSION'); ?></th>
					<th width="30%"><?php echo JText::_('FPSS_STATUS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2"><?php echo 'FPSS '.JText::_('FPSS_COMPONENT'); ?></td>
					<td><strong><?php echo JText::_('FPSS_INSTALLED'); ?></strong></td>
				</tr>
				<?php if (count($status->modules)) : ?>
				<tr>
					<th><?php echo JText::_('FPSS_MODULE'); ?></th>
					<th><?php echo JText::_('FPSS_CLIENT'); ?></th>
					<th></th>
				</tr>
				<?php foreach ($status->modules as $module) : ?>
				<tr class="row<?php echo(++$rows % 2); ?>">
					<td class="key"><?php echo $module['name']; ?></td>
					<td class="key"><?php echo ucfirst($module['client']); ?></td>
					<td><strong><?php echo ($module['result'])?JText::_('FPSS_INSTALLED'):JText::_('FPSS_NOT_INSTALLED'); ?></strong></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
		</table>
    <?php
	}
	private function uninstallationResults($status)
	{
	$language = JFactory::getLanguage();
	$language->load('com_fpss');
	$rows = 0;
 	?>
	<h2><?php echo JText::_('FPSS_REMOVAL_STATUS'); ?></h2>
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th class="title" colspan="2"><?php echo JText::_('FPSS_EXTENSION'); ?></th>
				<th width="30%"><?php echo JText::_('FPSS_STATUS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="row0">
				<td class="key" colspan="2"><?php echo 'FPSS '.JText::_('FPSS_COMPONENT'); ?></td>
				<td><strong><?php echo JText::_('FPSS_REMOVED'); ?></strong></td>
			</tr>
			<?php if (count($status->modules)) : ?>
			<tr>
				<th><?php echo JText::_('FPSS_MODULE'); ?></th>
				<th><?php echo JText::_('FPSS_CLIENT'); ?></th>
				<th></th>
			</tr>
			<?php foreach ($status->modules as $module) : ?>
			<tr class="row<?php echo(++$rows % 2); ?>">
				<td class="key"><?php echo $module['name']; ?></td>
				<td class="key"><?php echo ucfirst($module['client']); ?></td>
				<td><strong><?php echo ($module['result'])?JText::_('FPSS_REMOVED'):JText::_('FPSS_NOT_REMOVED'); ?></strong></td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3"></td>
			</tr>
		</tfoot>
	</table>
    <?php
	}
}
