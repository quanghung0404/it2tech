<?php

/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Upgrade
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Display messagess
if (JFactory::getApplication()->input->getInt('ajax') != 1)
{
	echo $this->msgs;
}
$upgradeBenefitsProUnlimitedStandard = '<ul>';
$upgradeBenefitsProUnlimitedStandard .= '<li>' . JText::_('JSN_MOBILIZE_UPGRADE_BENEFITS_PRO_STANDARD_LINE_1') . '</li>';
$upgradeBenefitsProUnlimitedStandard .= '<li>' . JText::_('JSN_MOBILIZE_UPGRADE_BENEFITS_PRO_STANDARD_LINE_2') . '</li>';
$upgradeBenefitsProUnlimitedStandard .= '<li>' . JText::_('JSN_MOBILIZE_UPGRADE_BENEFITS_PRO_STANDARD_LINE_3') . '</li>';
$upgradeBenefitsProUnlimitedStandard .= '<li>' . JText::_('JSN_MOBILIZE_UPGRADE_BENEFITS_PRO_STANDARD_LINE_4') . '</li>';
$upgradeBenefitsProUnlimitedStandard .= '</ul>';
$upgradeBenefitsProUnlimited = '<ul>';
$upgradeBenefitsProUnlimited .= '<li>' . JText::_('JSN_MOBILIZE_UPGRADE_BENEFITS_PRO_UNLIMITED_LINE_1') . '</li>';
$upgradeBenefitsProUnlimited .= '<li>' . JText::_('JSN_MOBILIZE_UPGRADE_BENEFITS_PRO_UNLIMITED_LINE_2') . '</li>';
$upgradeBenefitsProUnlimited .= '</ul>';
// Display config form
JSNUpgradeHelper::render($this->product, $upgradeBenefitsProUnlimitedStandard, $upgradeBenefitsProUnlimited);
