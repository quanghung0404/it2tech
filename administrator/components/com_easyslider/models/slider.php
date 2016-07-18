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
 * slider model.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
include_once JPATH_COMPONENT_ADMINISTRATOR . '/classes/jsn.easyslider.sliders.php';

class JSNEasySliderModelSlider extends JModelAdmin
{

    protected $option = "com_easyslider";

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string $type The table name. Optional.
     * @param   string $prefix The class prefix. Optional.
     * @param   array $config Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     */
    public function getTable($type = 'Slider', $prefix = 'JSNEasySliderTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array $data Data for the form.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    mixed    A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_easyslider.slider', 'slider', array('control' => 'jform', 'load_data' => $loadData));

        return $form;
    }

    /**
     * Load pre-defined data to fill into form.
     *
     * @return  object
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_easyslider.edit.form.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }
        return $data;
    }


    public function updateSliderData($data = array())
    {
        $result = array();
        try
        {
            if (!empty($data))
            {
                $query = $this->_db->getQuery(true);

                $fields = array(
                    $this->_db->quoteName('slider_title') . ' = ' . $this->_db->quote($data['slider_title']),
                    $this->_db->quoteName('slider_data') . ' = ' . $this->_db->quote($data['slider_data']),
                );

                // Conditions for which records should be updated.
                $conditions = array(
                    $this->_db->quoteName('slider_id') . ' = ' . $data['slider_id'],
                );

                $query->update($this->_db->quoteName('#__jsn_easyslider_sliders'))->set($fields)->where($conditions);

                $this->_db->setQuery($query);
                $this->_db->execute();
                $result['message'] = JText::_('JSN_EASYSLIDER_UPDATE_SUCCESSFULLY', true); //Update data successful
                $result['error'] = false;
            }
            else
            {
                $result['message'] = JText::_('JSN_EASYSLIDER_UPDATE_UNSUCCESSFULLY', true);
                $result['error'] = true;
            }

            return json_encode($result);
        } catch (Exception $e)
        {
            // catch any database errors.
            $result['message'] = JText::_('JSN_EASYSLIDER_UPDATE_UNSUCCESSFULLY', true);
            $result['error'] = true;
            return json_encode($result);
        }
    }

    public function createNewSlider($data = array())
    {
        $result = array();

        $objJSNEasySliderSliders = new JSNEasySliderSliders();
        $totalSliders = $objJSNEasySliderSliders->countSilderItems();

        /*Check if it is FREE edition then show warning message to alert that FREE edition only allows create maximum of 3 sliders*/
        $edition = defined('JSN_EASYSLIDER_EDITION') ? JSN_EASYSLIDER_EDITION : "free";
        if (strtolower($edition) == 'free')
        {
            if ($totalSliders !== false && $totalSliders >= 3)
            {
                $result['message'] = JText::_('JSN_EASYSLIDER_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SLIDERS_IN_FREE_EDITION', true);
                $result['error'] = true;
                return json_encode($result);
            }
        }

        try
        {
            $query = $this->_db->getQuery(true);

            // Insert columns.
            $columns = array('slider_title', 'published', 'access', 'slider_data');

            // Insert values.
            $values = array($this->_db->quote($data['slider_title']), $data['published'], $data['access'], $this->_db->quote($data['slider_data']));

            // Prepare the insert query.
            $query
                ->insert($this->_db->quoteName('#__jsn_easyslider_sliders'))
                ->columns($this->_db->quoteName($columns))
                ->values(implode(',', $values));

            $this->_db->setQuery($query);
            $this->_db->execute();
            $sliderID = $this->_db->insertid();

            $result['message'] = JText::_('JSN_EASYSLIDER_INSERT_SUCCESSFULLY', true); //Update data successful
            $result['error'] = false;
            $result['slider_id'] = $sliderID;
            $result['first'] = $data['first'];
            return json_encode($result);
        } 
        catch (Exception $e)
        {
            // catch any database errors.
            $result['message'] = JText::_('JSN_EASYSLIDER_INSERT_UNSUCCESSFULLY', true);
            $result['error'] = true;
            return json_encode($result);
        }
    }

	/**
	 * Method to duplicate slider.
	 *
	 * @param   array   &$pks   An array of primary key IDs.
	 *
	 * @return  boolean True if successful.
	 */
	public function duplicate(&$pks)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$db = $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.create', 'com_easyslider'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$table = $this->getTable();
		$checkEditionLimit = true;
		foreach ($pks as $pk)
		{
			$edition = defined('JSN_EASYSLIDER_EDITION') ? JSN_EASYSLIDER_EDITION : "free";
			if (strtolower($edition) == 'free')
			{
				$dataListSlider = JSNEasySliderHelper::getSliders();
				if (count($dataListSlider) >= 3)
				{
					$checkEditionLimit = false;
				}
			}
			if ($checkEditionLimit)
			{
				if ($table->load($pk, true))
				{
					// Reset the id to create a new record
					$table->slider_id = 0;

					$m = null;
					if (preg_match('#\((\d+)\)$#', $table->slider_title, $m))
					{
						$table->slider_title = preg_replace('#\(\d+\)$#', '(' . ($m[1] + 1) . ')', $table->slider_title);
					}
					else
					{
						$table->slider_title .= ' (2)';
					}

					$table->published = 0;
					$table->access    = 1;

					if (!$table->check() || !$table->store())
					{
						throw new Exception($table->getError());
					}
				}
				else
				{
					throw new Exception($table->getError());
				}
			}
		}

		if (!$checkEditionLimit)
		{
			$msg = JText::sprintf('JSN_EASYSLIDER_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SLIDER_IN_FREE_EDITION', 0) . ' <a class="jsn-link-action" href="index.php?option=com_easyslider&view=upgrade">' . JText::_("JSN_EASYSLIDER_UPGRADE_EDITION") . '</a>';
			throw new Exception($msg);
		}

		return true;
	}

    /**
     * @param $url
     * @param null $refer
     * @param bool|false $ispost
     * @param null $data
     * @param null $cookies
     * @param int $timeout
     * @return curl object
     */
    private function setOption($url, $refer = null, $ispost = false, $data = null, $cookies = null, $timeout = 10)
    {
        $curl  =  curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.52 Safari/536.5');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, $refer == null ? $url : $refer);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');

        if ($ispost && $data != null){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if (null !== $cookies){
            curl_setopt($curl, CURLOPT_COOKIE, $cookies);
        }
        return $curl;
    }

    /**
     * @param $url
     * @param null $refer
     * @param bool|false $ispost
     * @param null $data
     * @param null $cookies
     * @param int $timeout
     * @return html string
     */

    private function getContent($url, $refer = null, $ispost = false, $data = null, $cookies = null, $timeout = 10)
    {
        $curl  		=  self::setOption($url, $refer, $ispost, $data, $cookies, $timeout);
        $response  	=  curl_exec($curl);
        curl_close($curl);
        return $response;
    }

	public function getYoutubeRatio( $youtube )
	{
        $url = 'https://www.youtube.com/oembed?url=';
        $html = self::getContent( $url.  urlencode($youtube) . '&format=json' );
        $result = array();
        $result['status'] = false;
        if ( $data = json_decode($html) ) {
            if ( isset($data->width) && isset($data->height)) {
                $result['status'] = true;
                $result['width']  = $data->width;
                $result['height'] = $data->height;
            }
        }

        return json_encode($result);
	}
	/**
	 * @param string video url
	 *
	 * @return  float video ratio
	 */
	public function getVimeoRatio( $vimeo )
	{
        $html = self::getContent( $vimeo );
        $result = array();
        $result['status'] = false;

        if ( preg_match('#(<head[^>]*>[\s\S]*<\/head>)#is', $html, $matches) ) {

            if ( preg_match('#<meta[^>]*og:video:width[^\'|"]*"\s+content=[\'|"](.?[^\'|"]+)#is', $matches[1], $w) ) {
                $width = $w[1];
            }
            if ( preg_match('#<meta[^>]*og:video:height[^\'|"]*"\s+content=[\'|"](.?[^\'|"]+)#is', $matches[1], $h) ) {
                $height = $h[1];
            }
            if ( isset( $width ) && is_numeric($width) && isset( $height ) && is_numeric($height) ) {
                $result['status'] = true;
                $result['width']  = $width;
                $result['height'] = $height;
                return json_encode($result);
            }
        }
        return json_encode($result);
	}

}
