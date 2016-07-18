<?php
/**
 * @package         Advanced Module Manager
 * @version         5.3.6PRO-revPRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLog::add('ModulesHelperXML is deprecated. Do not use.', JLog::WARNING, 'deprecated');

/**
 * Helper for parse XML module files
 * @deprecated  3.2  Do not use.
 */
class ModulesHelperXML
{
	/**
	 * Parse the module XML file
	 *
	 * @param   array &$rows XML rows
	 *
	 * @return  void
	 *
	 * @deprecated  3.2  Do not use.
	 */
	public function parseXMLModuleFile(&$rows)
	{
		foreach ($rows as $i => $row)
		{
			if ($row->module == '')
			{
				$rows[$i]->name    = 'custom';
				$rows[$i]->module  = 'custom';
				$rows[$i]->descrip = 'Custom created module, using Module Manager New function';
			}
			else
			{
				$data = JInstaller::parseXMLInstallFile($row->path . '/' . $row->file);

				if ($data['type'] == 'module')
				{
					$rows[$i]->name    = $data['name'];
					$rows[$i]->descrip = $data['description'];
				}
			}
		}
	}
}
