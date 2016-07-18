<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: images_sources_default.php 12585 2012-05-11 08:17:16Z hiennv $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
class JSNImagesSourcesDefault extends JSNImagesSources
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$showlistTable = JTable::getInstance('showlist', 'Table');

		if ($config['showlistID']) {
			$showlistTable->load($config['showlistID']);
		}

		$this->_showlistTable = $showlistTable;

		$this->getLimitEdition();
	}

	public function getLimitEdition()
	{
		$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
		return $this->_limitEdition = $objJSNUtils->checkLimit();
	}

	public function getSourceName()
	{
		return (isset($this->_source['sourceDefine']->name)) ? $this->_source['sourceDefine']->name : '';
	}

	public function getListSources()
	{
		if (!isset ($this->_listSource))
		{
			$objJSNSource = JSNISFactory::getObj('classes.jsn_is_source');
			$this->_listSource = $objJSNSource->getListSources('active');
		}

		return $this->_listSource;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $config
	 */
	public function getImages($config = array())
	{
		if (!isset($this->_data['images']))
		{
			$objJSNImages 	= JSNISFactory::getObj('classes.jsn_is_images');
			$images 		= $objJSNImages->getImagesByShowlistID($this->_showlistTable->showlist_id);
			$this->_data['images']  = $images;
		}

		$this->addOriginalInfo();

		return $this->_data['images'];
	}

	public function saveImages($config = array())
	{
		// limit images by edition
		if ($this->getLimitEdition() == true)
		{
			$objJSNImages 	= JSNISFactory::getObj('classes.jsn_is_images');
			$countImages  	= $objJSNImages->countImagesShowlist($config['showlistID']);

			if (is_numeric($countImages[0]) && $countImages[0] >= 10) {
				$config['imgExtID'] = array();
			} else if (is_numeric($countImages[0])) {
				$count 			= 10 - $countImages[0];
				$config['imgExtID'] = array_slice($config['imgExtID'], 0, $count);
			}
		}

		$this->_data['saveImages'] = $config;
	}

	public function removeImages($config = array())
	{
		$imgExtID   = $config['imgExtID'];
		$showlistID = $config['showlistID'];
			
		if (is_array($imgExtID) and count($imgExtID))
		{

			for ($i = 0 ; $i < count($imgExtID); $i++)
			{
				$query = 'DELETE FROM #__imageshow_images
						  WHERE image_extid='.$this->_db->quote($imgExtID[$i]).'
						  AND showlist_id='.(int)$showlistID;

				$this->_db->setQuery($query);
				$result = $this->_db->query();
			}

			if ($result) {
				return true;
			}

			return false;
		}

		return false;
	}

	public function removeAllImages($config = array())
	{
		$showlistID = $config['showlist_id'];

		if (!empty($showlistID))
		{
			$query 	= 'DELETE FROM #__imageshow_images WHERE showlist_id='.(int) $showlistID;
			$this->_db->setQuery($query);

			if (!$this->_db->query()) {
				return false;
			}

			return true;
		}

		return false;
	}

	public function getCategories($config = array()){}

	public function updateImages($config = array()){}

	public function onSelectSource($config = array()){}

	public function getProfileTitle(){}

	public function loadImages($config = array()){}

	public function addOriginalInfo(){}

	/**
	 * @param showlist_id
	 * @param limitEdition true/false
	 * @param URL local url
	 */
	public function getImages2JSON($config = array())
	{
		if (!isset($this->_data['images']))
		{
			$objJSNImages 	= JSNISFactory::getObj('classes.jsn_is_images');
			$images 		= $objJSNImages->getImagesByShowlistID($this->_showlistTable->showlist_id);
			$this->_data['images'] = $images;
		}

		return $this->_data['images'];
	}

	public function getImageSrc($config = array('image_big' => '', 'URL' => '')){}

	public function removeShowlist()
	{
		$this->removeAllImages(array('showlist_id' => $this->_source['showlistID']));

		if ($this->_showlistTable->load($this->_source['showlistID']))
		{
			$this->_showlistTable->delete();
		}
	}

	public function renderScriptcheckThumb() {
		return ' JSNISImageShow.checkThumbCallBack(); ';
	}

	public function loadScript(){ return true;}
}
