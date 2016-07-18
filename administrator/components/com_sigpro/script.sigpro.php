<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

class Com_SigProInstallerScript
{

	public function postflight($type, $parent)
	{
		$db = JFactory::getDBO();
 		$status = new stdClass;
        $status->modules = array();
        $status->plugins = array();
		$manifest = $parent->getParent()->manifest;
		$plugins = $manifest->xpath('plugins/plugin');
		$modules = $manifest->xpath('modules/module');
		$src = $parent->getParent()->getPath('source');
		
		// Migrate plugin settings to component. Only for first time installs
		$db->setQuery("SELECT params FROM #__extensions WHERE type='plugin' AND folder = 'content' AND element = 'jw_sigpro'");
		$params = $db->loadResult();
		if($params && $params !='{}')
		{
			$db->setQuery("UPDATE #__extensions SET params = ".$db->quote($params)." WHERE type='component' AND element = 'com_sigpro'");
			$db->query();
			
			$db->setQuery("UPDATE #__extensions SET params = '{}' WHERE type='plugin' AND folder = 'content' AND element = 'jw_sigpro'");
			$db->query();
		}
		
		foreach ($plugins as $plugin)
		{
			$name = (string)$plugin->attributes()->plugin;
			$group = (string)$plugin->attributes()->group;
			$path = $src.'/plugins/'.$group.'/'.$name;
			$installer = new JInstaller;
			$result = $installer->install($path);
			if ($result)
			{
				if (JFile::exists(JPATH_SITE.'/plugins/'.$group.'/'.$name.'/'.$name.'.xml'))
				{
					JFile::delete(JPATH_SITE.'/plugins/'.$group.'/'.$name.'/'.$name.'.xml');
				}
				JFile::move(JPATH_SITE.'/plugins/'.$group.'/'.$name.'/'.$name.'.j25.xml', JPATH_SITE.'/plugins/'.$group.'/'.$name.'/'.$name.'.xml');

				$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote($name)." AND folder=".$db->Quote($group);
				$db->setQuery($query);
				$db->query();
			}
			$status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
		}

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
            $status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
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
		$plugins = $manifest->xpath('plugins/plugin');
		$modules = $manifest->xpath('modules/module');
		$src = $parent->getParent()->getPath('source');
		foreach ($plugins as $plugin)
		{
			$name = (string)$plugin->attributes()->plugin;
			$group = (string)$plugin->attributes()->group;
			$query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND element = ".$db->Quote($name)." AND folder = ".$db->Quote($group);
			$db->setQuery($query);
			$IDs = $db->loadColumn();
			if (count($IDs))
			{
				foreach ($IDs as $id)
				{
					$installer = new JInstaller;
					$result = $installer->uninstall('plugin', $id);
					$status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
				}
			}
		}
        foreach ($modules as $module)
        {
            $name = (string)$module->attributes()->module;
            $client = (string)$module->attributes()->client;
			$client_id = $client == 'administrator' ? 1 : 0;
            $db = JFactory::getDBO();
            $query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='module' AND element = ".$db->Quote($name)." AND client_id = ".$client_id;
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

    private function installationResults($status)
    {
        $language = JFactory::getLanguage();
        $language->load('com_sigpro');
        $rows = 0; ?>
        <img src="components/com_sigpro/images/SimpleImageGalleryPro_300x88_24.png" alt="Simple Image Gallery Pro" align="right" />
        <h2>Simple Image Gallery Pro <?php echo JText::_('COM_SIGPRO_INSTALLATION_STATUS'); ?></h2>
        <table class="adminlist table table-striped">
            <thead>
                <tr>
                    <th class="title" colspan="2"><?php echo JText::_('COM_SIGPRO_EXTENSION'); ?></th>
                    <th width="30%"><?php echo JText::_('COM_SIGPRO_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="row0">
                    <td class="key" colspan="2"><?php echo 'Simple Image Gallery Pro '.JText::_('COM_SIGPRO_COMPONENT'); ?></td>
                    <td><strong><?php echo JText::_('COM_SIGPRO_INSTALLED'); ?></strong></td>
                </tr>
                <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_SIGPRO_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_SIGPRO_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                <tr class="row<?php echo(++$rows % 2); ?>">
                    <td class="key"><?php echo $module['name']; ?></td>
                    <td class="key"><?php echo ucfirst($module['client']); ?></td>
                    <td><strong><?php echo ($module['result'])?JText::_('COM_SIGPRO_INSTALLED'):JText::_('COM_SIGPRO_NOT_INSTALLED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if (count($status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_SIGPRO_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_SIGPRO_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                <tr class="row<?php echo(++$rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                    <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                    <td><strong><?php echo ($plugin['result'])?JText::_('COM_SIGPRO_INSTALLED'):JText::_('COM_SIGPRO_NOT_INSTALLED'); ?></strong></td>
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
	$language->load('com_sigpro');
	$rows = 0; ?>
        <h2><?php echo JText::_('COM_SIGPRO_REMOVAL_STATUS'); ?></h2>
        <table class="adminlist table table-striped">
            <thead>
                <tr>
                    <th class="title" colspan="2"><?php echo JText::_('COM_SIGPRO_EXTENSION'); ?></th>
                    <th width="30%"><?php echo JText::_('COM_SIGPRO_STATUS'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            <tbody>
                <tr class="row0">
                    <td class="key" colspan="2"><?php echo 'Simple Image Gallery Pro '.JText::_('COM_SIGPRO_COMPONENT'); ?></td>
                    <td><strong><?php echo JText::_('COM_SIGPRO_REMOVED'); ?></strong></td>
                </tr>
                <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_SIGPRO_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_SIGPRO_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                <tr class="row<?php echo(++$rows % 2); ?>">
                    <td class="key"><?php echo $module['name']; ?></td>
                    <td class="key"><?php echo ucfirst($module['client']); ?></td>
                    <td><strong><?php echo ($module['result'])?JText::_('COM_SIGPRO_REMOVED'):JText::_('COM_SIGPRO_NOT_REMOVED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (count($status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_SIGPRO_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_SIGPRO_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                <tr class="row<?php echo(++$rows % 2); ?>">
                    <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
                    <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                    <td><strong><?php echo ($plugin['result'])?JText::_('COM_SIGPRO_REMOVED'):JText::_('COM_SIGPRO_NOT_REMOVED'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php
	}

}
