<?php

/**
 * @version     $Id: form.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Models
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');

/**
 * JSNUniform model Form
 *
 * @package     Modales
 * @subpackage  Form
 * @since       1.6
 */
class JSNMobilizeModelProfile extends JModelAdmin {

    protected $option = JSN_MOBILIZE;

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $type    The table name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable  A JTable object
     *
     * @since   11.1
     */
    public function getTable($type = 'Profile', $prefix = 'JSNMobilizeTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return        mixed        A JForm object on success, false on failure
     *
     * @since        1.6
     */
    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_mobilize.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));
        return $form;
    }

    /**
     * (non-PHPdoc)
     *
     * @see JModelForm::loadFormData()
     *
     * @return object
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_mobilize.edit.profile.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since   11.1
     */
    public function getItem($pk = null) {
        $item = parent::getItem($pk);
        return $item;
    }

    /**
     * Override save method to save form fields to database
     *
     * @param   array  $data  Data form
     *
     * @return boolean
     */
    public function save($data) {
    	$app = JFactory::getApplication();
    	$post = $app->input->getArray($_POST);
        
        $checkCreate = true;
        if (empty($data['profile_id']) || $data['profile_id'] == 0) {

            $edition = defined('JSN_MOBILIZE_EDITION') ? JSN_MOBILIZE_EDITION : "free";
            if (strtolower($edition) == "free") {
                $dataListForm = JSNMobilizeHelper::getProfiles();

                if (count($dataListForm) >= 1) {
                    $checkCreate = false;
                }
            }
        }
        if ($checkCreate) {
            if (empty($data['profile_id'])) {
                $db = $this->getDbo();
                $db->setQuery("UPDATE #__jsn_mobilize_profiles SET `ordering` = ordering+1");
                $db->execute();
            }
            if (($result = parent::save($data))) {
                $this->saveDataDesign($post);
                $this->saveOSSupport($post);
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query = "REPLACE INTO `#__jsn_mobilize_config` (name, value) VALUES ('tmp_config','')";
            $db->setQuery($query);
            if (!$db->execute()) {
                JError::raiseWarning(500, $db->getErrorMsg());
            }
            return $result;
        } else {
            $msg = JText::sprintf('JSN_MOBILIZE_YOU_HAVE_REACHED_THE_LIMITATION_OF_1_PROFILE_IN_FREE_EDITION', 0) . ' <a class="jsn-link-action" href="index.php?option=com_mobilize&view=upgrade">' . JText::_("JSN_MOBILIZE_UPGRADE_EDITION") . '</a>';
            $this->setError($msg);
            return false;
        }
    }

    /**
     * Save form Design
     *
     * @param   array  $post  Data form
     *
     * @return boolean
     */
    public function saveDataDesign($post) {
        $profileId = $this->getState($this->getName() . '.id');

        if (isset($post['jsnmobilize']) && count($post['jsnmobilize']) && $profileId) {
            $getDataDesign = $this->getDataDesign();

            $listData = array();
            if (!empty($getDataDesign)) {
                foreach ($getDataDesign as $item) {
                    $listData[$item->name] = $item->design_id;
                }
            }
            if (isset($post['style'])) {
                $table = JTable::getInstance('Design', 'JSNMobilizeTable');
                $table->bind(array('design_id' => isset($listData['mobilize-style']) ? intval($listData['mobilize-style']) : 0, 'profile_id' => $profileId, 'name' => 'mobilize-style', 'value' => json_encode($post['style'])));
                if (!$table->store()) {
                    $this->setError($table->getError());
                    return false;
                }

                $getStyle = JSNMobilizeHelper::generateStyle($post['style']);
                $urlFolder = JPATH_ROOT . "/templates/jsn_mobilize/css/profiles";
                $check = true;
                if (!JFolder::exists(JPath::clean($urlFolder))) {
                    if (!JFolder::create(JPath::clean($urlFolder), 0777)) {
                        $check = false;
                    }
                } elseif (!is_writable(JPath::clean($urlFolder))) {
                    $check = false;
                }
                if ($check) {
                    $fileName = "profile_" . (int) $profileId . ".css";
                    $file = JPath::clean($urlFolder . "/" . $fileName);
                    if (!JFile::write($file, $getStyle, true)) {
                        $check = false;
                    }
                }
                if ($check) {
                    $table = JTable::getInstance('Design', 'JSNMobilizeTable');
                    $table->bind(array('design_id' => isset($listData['mobilize-css-file']) ? intval($listData['mobilize-css-file']) : 0, 'profile_id' => $profileId, 'name' => 'mobilize-css-file', 'value' => $fileName));
                    if (!$table->store()) {
                        $this->setError($table->getError());
                        return false;
                    }
                } else {
                    $table = JTable::getInstance('Design', 'JSNMobilizeTable');
                    $table->bind(array('design_id' => isset($listData['mobilize-css-file']) ? intval($listData['mobilize-css-file']) : 0, 'profile_id' => $profileId, 'name' => 'mobilize-css-file', 'value' => ""));
                    if (!$table->store()) {
                        $this->setError($table->getError());
                        return false;
                    }
                }
                if (!empty($post['mobilize_custom_css_files'])) {
                    $table = JTable::getInstance('Design', 'JSNMobilizeTable');
                    $table->bind(array('design_id' => isset($listData['mobilize-custom-css-files']) ? intval($listData['mobilize-custom-css-files']) : 0, 'profile_id' => $profileId, 'name' => 'mobilize-custom-css-files', 'value' => json_encode($post['mobilize_custom_css_files'])));
                    if (!$table->store()) {
                        $this->setError($table->getError());
                        return false;
                    }
				}else{
					$table = JTable::getInstance('Design', 'JSNMobilizeTable');
                    $table->bind(array('design_id' => isset($listData['mobilize-custom-css-files']) ? intval($listData['mobilize-custom-css-files']) : 0, 'profile_id' => $profileId, 'name' => 'mobilize-custom-css-files', 'value' => ''));
                    if (!$table->store()) {
                        $this->setError($table->getError());
                        return false;
                    }
				}

                if (!empty($post['mobilize_custom_css_code'])) {
                    $table = JTable::getInstance('Design', 'JSNMobilizeTable');
                    $table->bind(array('design_id' => isset($listData['mobilize-custom-css-code']) ? intval($listData['mobilize-custom-css-code']) : 0, 'profile_id' => $profileId, 'name' => 'mobilize-custom-css-code', 'value' => $post['mobilize_custom_css_code']));
                    if (!$table->store()) {
                        $this->setError($table->getError());
                        return false;
                    }
                    if ($check) {
                        $fileName = "custom_css_profile_" . (int) $profileId . ".css";
                        $file = JPath::clean($urlFolder . "/" . $fileName);
                        JFile::write($file, $post['mobilize_custom_css_code'], true);
                    }
				}else{
					$table = JTable::getInstance('Design', 'JSNMobilizeTable');
                    $table->bind(array('design_id' => isset($listData['mobilize-custom-css-code']) ? intval($listData['mobilize-custom-css-code']) : 0, 'profile_id' => $profileId, 'name' => 'mobilize-custom-css-code', 'value' => ''));
                    if (!$table->store()) {
                        $this->setError($table->getError());
                        return false;
                    }
                    if ($check) {
                        $fileName = "custom_css_profile_" . (int) $profileId . ".css";
                        $file = JPath::clean($urlFolder . "/" . $fileName);
                        JFile::write($file, $post['mobilize_custom_css_code'], true);
                    }
				}
                $table = JTable::getInstance('Design', 'JSNMobilizeTable');
                $table->bind(array('design_id' => isset($listData['mobilize-css']) ? intval($listData['mobilize-css']) : 0, 'profile_id' => $profileId, 'name' => 'mobilize-css', 'value' => $getStyle));
                if (!$table->store()) {
                    $this->setError($table->getError());
                    return false;
                }
            }
            foreach ($post['jsnmobilize'] as $key => $data) {
                if (is_array($data)) {
                    $dataItem = new stdClass;
                    foreach ($data as $item) {
                        if (!empty($item)) {
                            $item = !empty($item) ? (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($item) : $item : '';
                            $items = json_decode($item);
                            $itemValue = isset($items) ? key($items) : '';
                            $itemType = isset($items->$itemValue) ? $items->$itemValue : '';
                            $dataItem->$itemValue = $itemType;
                        }
                    }
                    $data = json_encode($dataItem);
                } else {
                    $data = !empty($data) ? (get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true) ? stripslashes($data) : $data : '';
                }
                $table = JTable::getInstance('Design', 'JSNMobilizeTable');
                $table->bind(array('design_id' => isset($listData[$key]) ? intval($listData[$key]) : 0, 'profile_id' => $profileId, 'name' => $key, 'value' => $data));
                if (!$table->store()) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }
    }

    /**
     * Save OS Support
     *
     * @param   array  $post  Data form
     *
     * @return boolean
     */
    public function saveOSSupport($post) {
        $profileId = $this->getState($this->getName() . '.id');
        $db = $this->getDbo();
        $db->setQuery("DELETE FROM #__jsn_mobilize_os_support WHERE profile_id={$profileId}");
        $db->execute();

        if (!empty($post["ossupport"])) {
            foreach ($post["ossupport"] as $os) {
                $table = JTable::getInstance('Ossupport', 'JSNMobilizeTable');
                $table->bind(array('profile_id' => $profileId, 'os_id' => $os));
                if (!$table->store()) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }
    }

    /**
     * Get Data design.
     *
     * @return        string        The default menu type
     *
     * @since        1.6
     */
    public function getDataDesign() {
        $profileId = $this->getState($this->getName() . '.id');
        // Create a new query object.
        if (!empty($profileId)) {
            $db = $this->getDbo();
            $query = $db->getQuery(true)->select('*')->from('#__jsn_mobilize_design')->where('profile_id=' . intval($profileId));
            $db->setQuery($query);
            return $db->loadObjectList();
        }
    }

    /**
     * Get Data design.
     *
     * @return        string        The default menu type
     *
     * @since        1.6
     */
    public function getDataOSSupport() {
        $profileId = $this->getState($this->getName() . '.id');
        // Create a new query object.
        if (!empty($profileId)) {
            $db = $this->getDbo();
            $query = $db->getQuery(true)->select('*')->from('#__jsn_mobilize_os_support')->where('profile_id=' . intval($profileId));
            $db->setQuery($query);
            return $db->loadObjectList();
        }
    }
    /**
     * Get Data OS.
     *
     * @return        string        The default menu type
     *
     * @since        1.6
     */
    public function OrderDataOS() {
        $db = $this->getDbo();
        $query = $db->getQuery(true)->select('*')->from('#__jsn_mobilize_os')->order('os_id ASC');
        $db->setQuery($query);
        $getOs = $db->loadObjectList();
        foreach ( $getOs as $os ) {
            if($os->os_title == 'iOS 8.x'){
                $ios8=1;
            }
        }
        if( !isset($ios8) ){
            $db->setQuery($query);
            $columns = array('os_id', 'os_value', 'os_type', 'os_title', 'os_order');
            $values = array(12, $db->quote('{"ios":["8"]}'), $db->quote('ios'),$db->quote('iOS 8.x'), 3);
            $query = $db->getQuery(true);
            $query  ->insert($db->quoteName('#__jsn_mobilize_os'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',',$values));
            $db->setQuery($query);
            $db->execute();
            $this->updateOsOrder($getOs);
        }else{
            $this->updateOsOrder($getOs);
        }
    }
    /**
     * Update Order Data OS.
     *
     * @param   array  &$getOs 
     *
     * @since        1.6
     */
    public function updateOsOrder($getOs){
        foreach ( $getOs as $os ) {
            $order = 4;
            if($os->os_title == 'iOS 6.x and bellow'){
                $order=1;
            }elseif($os->os_title == 'iOS 7.x'){
                $order=2;
            }elseif($os->os_title == 'iOS 8.x'){
                $order=3;
            }
            $db = $this->getDbo();
            $db->setQuery("UPDATE #__jsn_mobilize_os SET `os_order` = " .$order. " WHERE `os_id` = ".$os->os_id);
            $db->execute();
        }
    }
    
    /**
     * Get Data OS.
     *
     * @return        string        The default menu type
     *
     * @since        1.6
     */
    public function getDataOS() {
        $this->OrderDataOS();
        $db = $this->getDbo();
        $query = $db->getQuery(true)->select('*')->from('#__jsn_mobilize_os')->order('os_order ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Override delete method to also delete form fields that associated
     *
     * @param   array  &$pks  id form
     *
     * @return boolean
     */
    public function delete(&$pks) {
        $pks = (array) $pks;

        if (count($pks)) {
            foreach ($pks as $id) {

                $this->_db->setQuery('DELETE FROM #__jsn_mobilize_design where profile_id = ' . (int) $id);
                if (!$this->_db->execute()) {
                    return false;
                }
                $this->_db->setQuery('DELETE FROM #__jsn_mobilize_profiles where profile_id = ' . (int) $id);
                if (!$this->_db->execute()) {
                    return false;
                }
                $this->_db->setQuery('DELETE FROM #__jsn_mobilize_os_support where profile_id = ' . (int) $id);
                if (!$this->_db->execute()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get menu type.
     *
     * In the absence of better information, this is the first menu ordered by title.
     *
     * @return        string        The default menu type
     *
     * @since        1.6
     */
    public function getMenuType() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true)->select('*')->from('#__menu_types')->order('title');
        $db->setQuery($query);
        $menuType = $db->loadObjectList();
        return $menuType;
    }

}
