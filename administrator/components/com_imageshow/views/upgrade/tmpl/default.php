<?php
/**
 * @author    JoomlaShine.com
 * @copyright JoomlaShine.com
 * @link      http://joomlashine.com/
 * @package   JSN Framework Sample
 * @version   $Id: default.php 17267 2012-10-19 09:17:11Z haonv $
 * @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Display messages
if (JRequest::getInt('ajax') != 1)
{
	echo $this->msgs;
}
// Display config form
JSNUpgradeHelper::render($this->product, JText::_('UPGRADER_STANDARD_BENEFITS'), JText::_('UPGRADER_UNLIMITED_BENEFITS'));