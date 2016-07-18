<?php
/**
 * @package         Advanced Template Manager
 * @version         1.6.4PRO
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

jimport('joomla.filesystem.file');

$this->config->show_assignto_groupusers = (int) (
	$this->config->show_assignto_usergrouplevels
	|| $this->config->show_assignto_users
);

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
$this->config->show_assignto_easyblog      = (int) ($this->config->show_assignto_easyblog && NNFrameworkFunctions::extensionInstalled('easyblog'));
$this->config->show_assignto_flexicontent  = (int) ($this->config->show_assignto_flexicontent && NNFrameworkFunctions::extensionInstalled('flexicontent'));
$this->config->show_assignto_form2content  = (int) ($this->config->show_assignto_form2content && NNFrameworkFunctions::extensionInstalled('form2content'));
$this->config->show_assignto_k2            = (int) ($this->config->show_assignto_k2 && NNFrameworkFunctions::extensionInstalled('k2'));
$this->config->show_assignto_zoo           = (int) ($this->config->show_assignto_zoo && NNFrameworkFunctions::extensionInstalled('zoo'));
$this->config->show_assignto_akeebasubs    = (int) ($this->config->show_assignto_akeebasubs && NNFrameworkFunctions::extensionInstalled('akeebasubs'));
$this->config->show_assignto_hikashop      = (int) ($this->config->show_assignto_hikashop && NNFrameworkFunctions::extensionInstalled('hikashop'));
$this->config->show_assignto_mijoshop      = (int) ($this->config->show_assignto_mijoshop && NNFrameworkFunctions::extensionInstalled('mijoshop'));
$this->config->show_assignto_redshop       = (int) ($this->config->show_assignto_redshop && NNFrameworkFunctions::extensionInstalled('redshop'));
$this->config->show_assignto_virtuemart    = (int) ($this->config->show_assignto_virtuemart && NNFrameworkFunctions::extensionInstalled('virtuemart'));
$this->config->show_assignto_cookieconfirm = (int) ($this->config->show_assignto_cookieconfirm && NNFrameworkFunctions::extensionInstalled('cookieconfirm'));

$assignments = array(
	'menuitems',
	'homepage',
	'date',
	'groupusers',
	'languages',
	'ips',
	'geo',
	'templates',
	'urls',
	'os',
	'browsers',
	'components',
	'tags',
	'content',
	'easyblog',
	'flexicontent',
	'form2content',
	'k2',
	'zoo',
	'akeebasubs',
	'hikashop',
	'mijoshop',
	'redshop',
	'virtuemart',
	'cookieconfirm',
	'php',
);
foreach ($assignments as $i => $ass)
{
	if ($ass != 'menuitems' && (!isset($this->config->{'show_assignto_' . $ass}) || !$this->config->{'show_assignto_' . $ass}))
	{
		unset($assignments[$i]);
	}
}

$html = array();

$html[] = $this->render($this->assignments, 'assignments');

$html[] = '<div id="' . rand(1000000, 9999999) . '___home.0" class="nntoggler">';

if (count($assignments) > 1)
{
	$html[] = $this->render($this->assignments, 'match_method');
	$html[] = $this->render($this->assignments, 'show_assignments');
}
else
{
	$html[] = '<input type="hidden" name="show_assignments" value="1" />';
}

foreach ($assignments as $ass)
{
	$html[] = $this->render($this->assignments, 'assignto_' . $ass);
}

$show_assignto_users = (int) $this->config->show_assignto_users;
$html[] = '<input type="hidden" name="show_users" value="' . $show_assignto_users . '" />';
$html[] = '<input type="hidden" name="show_usergrouplevels" value="' . (int) $this->config->show_assignto_usergrouplevels . '" />';

$html[] = '</div>';

echo implode("\n\n", $html);
