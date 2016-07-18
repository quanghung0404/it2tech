<?php

/**
 * @version     $Id: forms.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Controller
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

/**
 * Forms controllers of JControllerForm
 *
 * @package     Controllers
 * @subpackage  Forms
 * @since       1.6
 */
class JSNMobilizeControllerProfiles extends JControllerAdmin
{
	protected $option = JSN_MOBILIZE;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		// Get input object
		$app = JFactory::getApplication();
		$this->input = $app->input;
		//$this->input = JFactory::getApplication()->input;

		parent::__construct($config);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'profile', $prefix = 'JSNMobilizeModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = $this->input->getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural("JSN_MOBILIZE_PROFILE_DELETED", count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	/**
	 * Method to clone an existing module.
	 *
	 * @since    1.6
	 *
	 * @return void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$pks = $this->input->getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($pks);
		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('JSN_MOBILIZE_ERROR_NO_PROFILE_SELECTED'));
			}
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(JText::plural('JSN_MOBILIZE_N_PROFILES_DUPLICATED', count($pks)));
		} catch (Exception $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		$this->setRedirect('index.php?option=com_mobilize&view=profiles');
	}
	/**
	 * Upload background template
	 *
	 * @return path file;
	 */
	public function uploadImage(){
		
		JSession::checkToken('get') or jexit('Invalid Token');
		$app = JFactory::getApplication();
		$input = $app->input;
		$file	= $input->files->get('fileupload', null, 'raw');
		
		if($file){
			$output_dir = $_SERVER['DOCUMENT_ROOT'] . JURI::root(true) ."/uploads/";
			if(!file_exists($output_dir)){
				mkdir($output_dir, 0777,true);
			}
			
			if(isset($file))
			{
				//Filter the file types , if you want.
				if ($file["error"] > 0)
				{
					echo "Error: " . $file["error"] . "<br>";
				}
				else
				{
					//move the uploaded file to uploads folder;
					if(move_uploaded_file($file["tmp_name"],$output_dir. $file["name"])){
						echo "uploads/" . $file["name"];die;
                        /* $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
						if($_SERVER['SERVER_PORT'] !== ''){
							echo $protocol . $_SERVER['SERVER_NAME'] .':'.$_SERVER['SERVER_PORT']. JURI::root(true) ."/uploads/" . $_FILES["fileupload"]["name"];die;
						}else{
							echo $protocol . $_SERVER['SERVER_NAME'] . JURI::root(true) ."/uploads/" . $_FILES["fileupload"]["name"];die;
						} */
					}
				}
			}
		}
	}
	/**
	 * Save Session Style
	 *
	 * @since    1.6
	 *
	 * @return void
	 */
	public function saveSessionStyle()
	{
		JSession::checkToken('get') or jexit('Invalid Token');
		
		$app = JFactory::getApplication();
		$post = $app->input->getArray($_POST);
		
		if (!empty($post['mobilize']))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "REPLACE INTO `#__jsn_mobilize_config` (name, value) VALUES ('tmp_config'," . $db->quote($post['mobilize']) . ")";
			$db->setQuery($query);
			if (!$db->execute())
			{
				JError::raiseWarning(500, $db->getErrorMsg());
			}
		}
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();
		if (empty($pks) && empty($order))
		{
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), jText::_('COM_MOBILIZE_ERROR_NO_ITEMS_SELECTED'), 'error');
			return false;
		}
		else
		{
			// Save the ordering
			$return = $model->saveorder($pks, $order);

			if ($return === false)
			{
				// Reorder failed
				$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
				return false;
			}
			else
			{
				// Reorder succeeded.
				$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
				return true;
			}
		}

	}
}
