<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: mod_imageshow_quickicon.php 6505 2011-05-31 11:01:31Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
require_once dirname(__FILE__).'/helper.php';
$fileFactory = JPATH_ROOT . DS. 'administrator'.DS.'components'.DS.'com_imageshow'.DS.'classes'.DS.'jsn_is_factory.php';
if (JFile::exists($fileFactory))
{
	require_once $fileFactory;

	require JModuleHelper::getLayoutPath('mod_imageshow_quickicon', $params->get('layout', 'default'));
}
else
{
	modImageShowQuickIconHelper::approveModule('mod_imageshow_quickicon', 0);
}
?>