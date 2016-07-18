<?php
/**
 * @version    $Id: data.php 16204 2012-09-20 04:31:14Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * Data controller of JSN Framework Sample component
 */
class ImageShowControllerData extends JSNDataController
{
	/**
	 * Contructor
	 *
	 * @return void
	 *
	 */

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Restore data from uploaded backup file.
	 *
	 * @return void
	 */

	public function restore()
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		// Validate request
		$this->initializeRequest($input);

		// Initialize variables
		$this->model = $this->getModel($input->getCmd('controller') ? $input->getCmd('controller') : $input->getCmd('view'));
		$backup      = $_FILES['datarestore'];
		$file_ext 	 = JFile::getExt($backup['name']);
		$msg		 = '';

		// Attempt to restore data
		if ($file_ext == 'zip')
		{
			$this->model->restore($backup);
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=' . $input->getCmd('option') . '&view=maintenance&s=maintenance&g=data#data-back-restore', false));
		}
		else
		{
			$msg = JText::_('MAINTENANCE_BACKUP_FORMAT_FILE_RESTORE_INCORRECT');
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=' . $input->getCmd('option') . '&view=maintenance&s=maintenance&g=data#data-back-restore', false), $msg, 'error');
		}

	}

	/**
	 * Clear restore session.
	 *
	 * @return void
	 */

	public function clearRestoreSession()
	{
		// Get input object
		$input = JFactory::getApplication()->input;

		$session = JFactory::getSession();
		$session->set('JSNISRestore', null);
		JFactory::getApplication()->redirect(JRoute::_('index.php?option=' . $input->getCmd('option') . '&view=maintenance&s=maintenance&g=data#data-back-restore', false));
		exit();
	}
}