<?php
/**
 * @version    $Id: jsn_is_showcase.php 16563 2012-10-01 07:56:09Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Showcase Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JSNISShowcase
{
	private $_db = null;

	public function JSNISShowcase()
	{
		if ($this->_db == null)
		{
			$this->_db = JFactory::getDBO();
		}
	}

	public static function getInstance()
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}
		if (empty($instances['JSNISShowcase']))
		{
			$instance	= new JSNISShowcase;
			$instances['JSNISShowcase'] = &$instance;
		}

		return $instances['JSNISShowcase'];
	}

	public function getShowCaseTitle($showCaseID)
	{
		$query	= 'SELECT showcase_title FROM #__imageshow_showcase WHERE showcase_id=' . (int) $showCaseID;
		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	public function getShowcaseID()
	{
		$arrayID 	= array();
		$query		= 'SELECT showcase_id FROM #__imageshow_showcase';
		$this->_db->setQuery($query);
		$result 	= $this->_db->loadAssocList();

		if (count($result))
		{
			foreach ($result as $value)
			{
				$arrayID[] = $value['showcase_id'];
			}
			return $arrayID;
		}

		return false;
	}

	public function countShowcase()
	{
		$query	= 'SELECT COUNT(*) FROM #__imageshow_showcase';
		$this->_db->setQuery($query);
		return $this->_db->loadRow();
	}

	public function getTotalShowcase()
	{
		$query 	= 'SELECT COUNT(*) FROM #__imageshow_showcase';
		$this->_db->setQuery($query);
		return $this->_db->loadRow();
	}

	public function getLastestShowcase($limit = 1)
	{
		$query 	= 'SELECT showcase_title, showcase_id, showcase_title AS item_title, showcase_id AS item_id  FROM #__imageshow_showcase ORDER BY date_modified DESC';
		$this->_db->setQuery($query, 0, $limit);
		return $this->_db->loadObjectList();
	}

	public function renderShowcaseComboBox($ID, $elementText, $elementName, $parameters = '')
	{
		$this->_db 	= JFactory::getDBO();
		$query	= 'SELECT showcase_title AS text, showcase_id AS value
		FROM #__imageshow_showcase
		ORDER BY showcase_title ASC';
		$this->_db->setQuery($query);
		$data 	= $this->_db->loadObjectList();

		array_unshift($data, JHTML::_('select.option', '0', '- ' . JText::_($elementText) . ' -', 'value', 'text'));
		return JHTML::_('select.genericlist', $data, $elementName, $parameters, 'value', 'text', $ID);
	}

	public function getShowcase2JSON ($data, $URL)
	{
		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/json');

		$dataObj 		= new stdClass;
		$showcaseObject = new stdClass;

		$objJSNShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
		$showcaseTheme 		 = $objJSNShowcaseTheme->getShowcaseThemeByShowcaseID($data->showcase_id, $URL);

		if ($showcaseTheme == false)
		{
			$showcaseTheme = $objJSNShowcaseTheme->getDefaultThemeByThemeName($data->theme_name, $URL);
		}

		if (!empty($showcaseTheme))
		{

			foreach ($showcaseTheme as $key => $value)
			{
				$showcaseObject->$key = $value;
			}
		}

		$dataObj->{'showcase'} = $showcaseObject;

		return $dataObj;
	}

	public function getShowCaseByID($showcaseID, $published = true,  $resultType = 'loadObject')
	{
		$condition = '';

		if ($published == true)
		{
			$condition = ' published = 1 AND ';
		}

		$query 	= 'SELECT * FROM #__imageshow_showcase WHERE ' . $condition . ' showcase_id = ' . (int) $showcaseID;
		$this->_db->setQuery($query);

		return $this->_db->$resultType();
	}

	public function checkShowcaseLimition()
	{
		$objJSNUtils 		= JSNISFactory::getObj('classes.jsn_is_utils');
		$limitStatus		= $objJSNUtils->checkLimit();
		$count 				= $this->countShowcase();

		if (@$count[0] >= 3 && $limitStatus == true)
		{
			$msg = JText::sprintf('SHOWCASE_YOU_HAVE_REACHED_THE_LIMITATION_OF_3_SHOWCASES_IN_FREE_EDITION', '<a href="' . JSN_IMAGESHOW_UPGRADE_LINK . '" class="jsn-link-action">' . JText::_('UPGRADE_TO_PRO_EDITION') . '</a>');
			JError::raiseNotice(100, $msg);
		}
	}

	public function checkRecordShowcase()
	{
		$db 	= JFactory::getDBO();
		$query 	= 'SELECT COUNT(showcase_id) FROM #__imageshow_showcase';
		$db->setQuery($query);
		$result = $db->loadRow();
		if (count($result))
		{
			if ($result[0] != 0)
			{
				return true;
			}
		}
		return false;
	}
}
