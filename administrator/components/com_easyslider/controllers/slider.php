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
 * Slider controller.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
//Import filesystem libraries. Perhaps not necessary, but does not hurt
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JSNEasySliderControllerSlider extends JControllerForm
{
    protected $option = 'com_easyslider';

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_app = JFactory::getApplication();
        $this->_input = $this->_app->input;
        $this->_config = JSNConfigHelper::get();
    }


    /**
     * update an existed slider
     * @return JSON string
     */
    public function updateSliderData()
    {
        JSession::checkToken('get') or jexit('Invalid Token');

        $post = $this->_input->getArray($_POST);

        $post['slider_data'] = $this->_input->get('slider_data', '{}', 'RAW');

        $model = $this->getModel("slider");
        echo $model->updateSliderData($post);
        if (isset($post['deleteImages']) && !empty($post['deleteImages']))
        {
            echo $this->delete($post['deleteImages']);
        }
        exit();
    }


    /**
     * create a new slider
     * @return JSON string
     */
    public function createNewSlider()
    {
        JSession::checkToken('get') or jexit('Invalid Token');

        $post = $this->_input->getArray($_POST);
        $post['slider_data'] = $this->_input->get('slider_data', '{}', 'RAW');
        $post['access'] = 1;
        $post['published'] = 1;

        $models = $this->getModel("sliders");
        $post['first'] = $models->getCountSliders() == 0 ? true : false;

        $model = $this->getModel("slider");
        echo $model->createNewSlider($post);

        if (isset($post['deleteImages']) && !empty($post['deleteImages']))
        {
            echo $this->delete($post['deleteImages']);
        }
        exit();
    }


    /**
     * @param video url (youtube, vimeo)
     * @return ratio video width / height
     */
    public function getVideoRatio()
    {
        JSession::checkToken('get') or jexit('Invalid Token');
        $post = $this->_input->getArray($_POST);

        $model = $this->getModel("slider");

        if ( isset( $post['video_url'] ) ) {
            if ( preg_match('/youtube/is', $post['video_url']) ) {
                echo $model->getYoutubeRatio($post['video_url']);
            }
            if ( preg_match('/vimeo/is', $post['video_url']) ) {
                echo $model->getVimeoRatio($post['video_url']);
            }

        }

        exit();
    }


}
