<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Items controller.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
include_once JPATH_COMPONENT_ADMINISTRATOR . '/classes/jsn.easyslider.sliders.php';

class JSNEasySliderControllerSliders extends JControllerAdmin
{
    protected $option = "com_easyslider";

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string $name The model name. Optional.
     * @param   string $prefix The class prefix. Optional.
     * @param   array $config Configuration array for model. Optional.
     *
     * @return  object  The model.
     */
    public function getModel($name = 'slider', $prefix = 'JSNEasySliderModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Get List Item
     *
     * @return  void
     */
    public function getListSlider()
    {
    	$objJSNEasySliderSliders 	= new JSNEasySliderSliders();
    	$sliders 					= $objJSNEasySliderSliders->getSlidersWithoutState();
    	
        jexit(json_encode($sliders));
    }

	/**
	 * Method to clone an existing slider
	 */
	public function duplicate()
	{
		$pks = $this->input->getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($pks);

		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('JSN_EASYSLIDER_ERROR_NO_SLIDER_SELECTED'));
			}
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(JText::plural('JSN_EASYSLIDER_N_SLIDER_DUPLICATED', count($pks)));
		}
		catch (Exception $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$this->setRedirect('index.php?option=com_easyslider&view=sliders');
	}
	
	public function convertSliderData()
	{
		JSession::checkToken('get') or jexit('Invalid Token');
		$app = JFactory::getApplication();
		$input = $app->input;
		$convertedData = $input->get('converted_data', '{}', 'RAW');
		$model = $this->getModel('sliders');
		$result = $model->convertSliderData($convertedData);
		$msg	= array();
		if ($result)
		{
			$msg['message'] = JText::_('JSN_EASYSLIDER_UPDATE_SUCCESSFULLY', true); //Update data successful
			$msg['error'] = false;
		}
		else
		{
			$msg['message'] = JText::_('JSN_EASYSLIDER_UPDATE_UNSUCCESSFULLY', true);
			$msg['error'] = true;
		}	
		
		echo json_encode($result);
		exit();
	}

	public function importData(){
		JSession::checkToken('get') or jexit('Invalid Token');
		$app 		= JFactory::getApplication();
		$input 		= $app->input;
		$data 		= $input->get('import_data', '{}', 'RAW');
		$data 		= json_decode($data);
		$model 		= $this->getModel('sliders');
		$result = $model->importData($data);
		echo json_encode($result);
		exit();
	}
 }
