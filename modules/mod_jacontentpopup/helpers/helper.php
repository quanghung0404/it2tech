<?php
/**
 * ------------------------------------------------------------------------
 * JA Content Popup Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter');
jimport('joomla.form.form');

if (!defined('_JA_NEWS_')) {
	define('_JA_NEWS_', 1);
	require_once (dirname(__FILE__) . '/jaimage.php');
	require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
	/**
	 * mod JA New Featured Helper class.
	 */
	class modJaNewsHelper extends JObject
	{
		/*
		 * @var string module name
		 */
		var $_module = null;
		/*
		 * @var object parametters of module
		 */
		var $_params = null;
		/**
		 * Constructor
		 *
		 * For php4 compatability we must not use the __constructor as a constructor for plugins
		 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
		 * This causes problems with cross-referencing necessary for the observer design pattern.
		 *
		 * @param	object	$module The object to observe
		 * @param	object 	$params	The object config of plugin
		 */
		function __construct($module, $params = null)
		{
			$this->_module = $module;
			$this->_params = $params;
		}
		/**
		 *
		 * Resize image in content
		 * @param object $row
		 * @param string $align
		 * @param int $autoresize
		 * @param int $maxchars
		 * @param int $showimage
		 * @param int $width
		 * @param int $height
		 * @param int $hiddenClasses
		 * @return string new image
		 */
		function replaceImage($row, $width = 0, $height = 0)
		{
			$regex = '#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im';
			
			if (!isset($row->introtext)) {
				$row->introtext="";				
			}
			if (!isset($row->text)) {
				$row->text = "";
			}
			
			//check to see if there is an  intro image or fulltext image  first
			$images = "";
			if (isset($row->images)) {
				$images = json_decode($row->images);
			}
			if((isset($images->image_fulltext) and !empty($images->image_fulltext)) || (isset($images->image_intro) and !empty($images->image_intro))){
				$image = (isset($images->image_intro) and !empty($images->image_intro))?$images->image_intro:((isset($images->image_fulltext) and !empty($images->image_fulltext))?$images->image_fulltext:"");
				
			}else{
				preg_match($regex, $row->introtext, $matches);
				if (!count($matches))
					preg_match($regex, $row->fulltext, $matches);
				$images = (count($matches)) ? $matches : array();
				$image = '';
				if (count($images))
					$image = trim($images[2]);
			}			
			
			if ($image){
				
				$width 		   = $this->_params->get('width','100');
				$height 	   = $this->_params->get('height','100');
				$thumbnailMode = $this->_params->get('thumbnail_mode', 'crop');
				$class		   = 'jaimage'; 
				if ($width >0 && $height >0) {
					$aspect = $this->get('use_ratio', '1');
					$crop = $thumbnailMode == 'crop' ? true : false;
					
					$jaimage = JAImage::getInstance();
					
					if ($thumbnailMode != 'none' && $jaimage->sourceExited($image)) {
						$imageURL = $jaimage->resize($image, $width, $height, $crop, $aspect);
						$image = $imageURL ? "<img class=\"$class\" src=\"" .$imageURL . "\" alt=\"{$row->title}\"  />" : "";
					} else {
						$width = $width ? "width=\"$width\"" : "";
						$height = $height ? "height=\"$height\"" : "";
						$image = "<img class=\"$class\" src=\"" . JURI::root().$image . "\" alt=\"{$row->title}\" $width $height />";
					}
				}else {
					$image = "<img class=\"$class\" src=\"" . JURI::root().$image . "\" alt=\"{$row->title}\" />";
				}
				
			}else {
				$class		   = 'jaimage'; 
				$image = '<img class="'.$class.' blank" src="'.JURI::root().'modules/mod_jacontentpopup/assets/img/blank.gif" />';
			}
			// clean up globals
			
			return $image;
		}
		/**
		 * trim string with max specify
		 *
		 * @param string $title
		 * @param integer $max.
		 */
		function trimString($title, $maxchars = 60, $includeTags = NULL)
		{
			
			if (!empty($includeTags)) {
				$title = $this->trimIncludeTags($title, $this->buildStrTags($includeTags));
			}
			if (function_exists('mb_substr')) {
				$doc = JDocument::getInstance();
				return SmartTrim::mb_trim(($title), 0, $maxchars, $doc->_charset);
			} else {
				return SmartTrim::trim(($title), 0, $maxchars);
			}
		}
		/**
		 *
		 * Build Tags
		 * @param unknown_type $strTags
		 * @return string
		 */
		public function buildStrTags($strTags = "")
		{
			$strOut = "";
			if (!empty($strTags) && !is_array($strTags)) {
				$arrStr = explode(",", $strTags);
				if (!empty($arrStr)) {
					foreach ($arrStr as $key => $item) {
						$strOut .= "<" . $item . ">";
					}
				}
			} elseif (!empty($strTags) && is_array($strTags)) {
				$strOut = implode(",", $strTags);
				$strOut = str_replace(",", "", $strOut);
			}
			
			return $strOut;
		}

		/**
		*
		* Clear space in tags
		* @param string $strContent
		* @param string $listTags
		* @return string the stripped string.
		*/
		function trimIncludeTags($strContent, $listTags = "")
		{
			$strOut = strip_tags($strContent, $listTags);
			return $strOut;
		}

		/**
		*/
		function getPagination($params,$module){
			$lang = jFactory::getLanguage();			
			$languages	= JLanguageHelper::getLanguages();
			
			$url_lang_code = '';
			
			foreach($languages as $l){
				if($l->lang_code == $lang->getTag()){
					$url_lang_code = $l->sef;
				}
			}
			
	    	$group  = $params->get('group_categories', 0);
			/*Get layout*/
			$layout = $params->get('layout', 'default');
			$callback = 'get'.str_replace(":","",$layout);
			
			$source = $params->get('source', 'JANewsHelper');
			$jacphelper =  new $source();
			
			if($group == 1){
				$total = $jacphelper->getCategories($params,$this,true);
			}else {
				$total = $jacphelper->getList($params,$this,true);
			}
			
			$limited = ceil($total/$params->get('limited', 10));
			
			$pagedata = array(
				'option' => ($source == 'JANewsHelper') ? 'com_content' : 'com_k2',
				'position' => $module->position,
				'modulesid' => $module->id,
				'Itemid' => JRequest::getInt('Itemid')
			);
			if($url_lang_code){
				$pagedata = array_merge($pagedata,array('lang'=>$url_lang_code));
			}
			$html = '';
			if ($total > $params->get('limited', 10)) {			
				for($i=0; $i < $limited; $i++){
					$class = '';
					if($i == 0){
						$class = ' class="active"';
					}
	
					$j = $i + 1;
	
					$pagedata['jalimitstart'] = $i * $params->get('limited', 10);
					$html .= '<li'.$class.'><span data-ref="' . htmlentities(json_encode($pagedata), ENT_QUOTES) . '">'.$j.'</span></li>';
				}
			}
			return $html ? ('<ul class="ja-cp-pagelist">' . $html . '</ul>') : $html;
			
		}
	}
}

if (!class_exists('SmartTrim')) {
	/**
	 * Smart Trim String Helper
	 *
	 */
	class SmartTrim
	{


		/**
		 *
		 * process string smart split
		 * @param string $strin string input
		 * @param int $pos start node split
		 * @param int $len length of string that need to split
		 * @param string $hiddenClasses show and redmore with property display: none or invisible
		 * @param string $encoding type of string endcoding
		 * @return string string that is smart splited
		 */
		public static function mb_trim($strin, $pos = 0, $len = 10000, $hiddenClasses = '', $encoding = 'utf-8')
		{
			mb_internal_encoding($encoding);
			$strout = trim($strin);

			$pattern = '/(<[^>]*>)/';
			$arr = preg_split($pattern, $strout, -1, PREG_SPLIT_DELIM_CAPTURE);
			$left = $pos;
			$length = $len;
			$strout = '';
			for ($i = 0; $i < count($arr); $i++) {
				/*$arr [$i] = trim ( $arr [$i] );*/
				if ($arr[$i] == '')
					continue;
				if ($i % 2 == 0) {
					if ($left > 0) {
						$t = $arr[$i];
						$arr[$i] = mb_substr($t, $left);
						$left -= (mb_strlen($t) - mb_strlen($arr[$i]));
					}

					if ($left <= 0) {
						if ($length > 0) {
							$t = $arr[$i];
							$arr[$i] = mb_substr($t, 0, $length);
							$length -= mb_strlen($arr[$i]);
							if ($length <= 0) {
								$arr[$i] .= '...';
							}

						} else {
							$arr[$i] = '';
						}
					}
				} else {
					if (SmartTrim::isHiddenTag($arr[$i], $hiddenClasses)) {
						if ($endTag = SmartTrim::getCloseTag($arr, $i)) {
							while ($i < $endTag)
								$strout .= $arr[$i++] . "\n";
						}
					}
				}
				$strout .= $arr[$i] . "\n";
			}
			//echo $strout;
			return SmartTrim::toString($arr, $len);
		}


		/**
		 *
		 * process simple string split
		 * @param string $strin string input
		 * @param int $pos start node
		 * @param int $len length of string that need to split
		 * @param string $hiddenClasses show and redmore with property display: none or invisible
		 * @return string
		 */
		public static function trim($strin, $pos = 0, $len = 10000, $hiddenClasses = '')
		{
			$strout = trim($strin);

			$pattern = '/(<[^>]*>)/';
			$arr = preg_split($pattern, $strout, -1, PREG_SPLIT_DELIM_CAPTURE);
			$left = $pos;
			$length = $len;
			$strout = '';
			for ($i = 0; $i < count($arr); $i++) {
				/*$arr [$i] = trim ( $arr [$i] );*/
				if ($arr[$i] == '')
					continue;
				if ($i % 2 == 0) {
					if ($left > 0) {
						$t = $arr[$i];
						$arr[$i] = substr($t, $left);
						$left -= (strlen($t) - strlen($arr[$i]));
					}

					if ($left <= 0) {
						if ($length > 0) {
							$t = $arr[$i];
							$arr[$i] = substr($t, 0, $length);
							$length -= strlen($arr[$i]);
							if ($length <= 0) {
								$arr[$i] .= '...';
							}

						} else {
							$arr[$i] = '';
						}
					}
				} else {
					if (SmartTrim::isHiddenTag($arr[$i], $hiddenClasses)) {
						if ($endTag = SmartTrim::getCloseTag($arr, $i)) {
							while ($i < $endTag)
								$strout .= $arr[$i++] . "\n";
						}
					}
				}
				$strout .= $arr[$i] . "\n";
			}
			//echo $strout;
			return SmartTrim::toString($arr, $len);
		}


		/**
		 * Check is Hidden Tag
		 * @param string tag
		 * @param string type of hidden
		 * @return boolean
		 */
		public static function isHiddenTag($tag, $hiddenClasses = '')
		{
			//By pass full tag like img
			if (substr($tag, -2) == '/>')
				return false;
			if (in_array(SmartTrim::getTag($tag), array('script', 'style')))
				return true;
			if (preg_match('/display\s*:\s*none/', $tag))
				return true;
			if ($hiddenClasses && preg_match('/class\s*=[\s"\']*(' . $hiddenClasses . ')[\s"\']*/', $tag))
				return true;
		}


		/**
		 *
		 * Get close tag from content array
		 * @param array $arr content
		 * @param int $openidx
		 * @return int 0 if find not found OR key of close tag
		 */
		public static function getCloseTag($arr, $openidx)
		{
			/*$tag = trim ( $arr [$openidx] );*/
			$tag = $arr[$openidx];
			if (!$openTag = SmartTrim::getTag($tag))
				return 0;

			$endTag = "<$openTag>";
			$endidx = $openidx + 1;
			$i = 1;
			while ($endidx < count($arr)) {
				if (trim($arr[$endidx]) == $endTag)
					$i--;
				if (SmartTrim::getTag($arr[$endidx]) == $openTag)
					$i++;
				if ($i == 0)
					return $endidx;
				$endidx++;
			}
			return 0;
		}


		/**
		 *
		 * Get tag in content
		 * @param string $tag
		 * @return string tag
		 */
		public static function getTag($tag)
		{
			if (preg_match('/\A<([^\/>]*)\/>\Z/', trim($tag), $matches))
				return ''; //full tag
			if (preg_match('/\A<([^ \/>]*)([^>]*)>\Z/', trim($tag), $matches)) {
				//echo "[".strtolower($matches[1])."]";
				return strtolower($matches[1]);
			}
			//if (preg_match ('/<([^ \/>]*)([^\/>]*)>/', trim($tag), $matches)) return strtolower($matches[1]);
			return '';
		}


		/**
		 *
		 * convert array to string
		 * @param array $arr
		 * @param int $len
		 * @return string
		 */
		public static function toString($arr, $len)
		{
			$i = 0;
			$stack = new JAStack();
			$length = 0;
			while ($i < count($arr)) {
				/*$tag = trim ( $arr [$i ++] );*/
				$tag = $arr[$i++];
				if ($tag == '')
					continue;
				if (SmartTrim::isCloseTag($tag)) {
					if ($ltag = $stack->getLast()) {
						if ('</' . SmartTrim::getTag($ltag) . '>' == $tag)
							$stack->pop();
						else
							$stack->push($tag);
					}
				} else if (SmartTrim::isOpenTag($tag)) {
					$stack->push($tag);
				} else if (SmartTrim::isFullTag($tag)) {
					//echo "[TAG: $tag, $length, $len]\n";
					if ($length < $len)
						$stack->push($tag);
				} else {
					$length += strlen($tag);
					$stack->push($tag);
				}
			}

			return $stack->toString();
		}


		/**
		 *
		 * Check is open tag
		 * @param string $tag
		 * @return boolean
		 */
		public static function isOpenTag($tag)
		{
			if (preg_match('/\A<([^\/>]+)\/>\Z/', trim($tag), $matches))
				return false; //full tag
			if (preg_match('/\A<([^ \/>]+)([^>]*)>\Z/', trim($tag), $matches))
				return true;
			return false;
		}


		/**
		 *
		 * Check is full tag
		 * @param string $tag
		 * @return boolean
		 */
		public static function isFullTag($tag)
		{
			//echo "[Check full: $tag]\n";
			if (preg_match('/\A<([^\/>]*)\/>\Z/', trim($tag), $matches))
				return true; //full tag
			return false;
		}


		/**
		 *
		 * Check is close tag
		 * @param string $tag
		 * @return boolean
		 */
		public static function isCloseTag($tag)
		{
			if (preg_match('/<\/(.*)>/', $tag))
				return true;
			return false;
		}
	}
}
if (!class_exists('JAStack')) {

	/**
	 * News Pro Module JAStack Helper
	 */
	class JAStack
	{
		/*
		 * array
		 */
		var $_arr = null;


		/**
		 * Constructor
		 *
		 * For php4 compatability we must not use the __constructor as a constructor for plugins
		 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
		 * This causes problems with cross-referencing necessary for the observer design pattern.
		 *
		 */
		function JAStack()
		{
			$this->_arr = array();
		}


		/**
		 *
		 * Push item value into array
		 * @param observe $item value of item that will input to stack
		 * @return unknown
		 */
		function push($item)
		{
			$this->_arr[count($this->_arr)] = $item;
		}


		/**
		 *
		 * Pop item value from array
		 * @param observe $item value of item that will pop from stack
		 * @return unknow value of item that is pop from array
		 */
		function pop()
		{
			if (!$c = count($this->_arr))
				return null;
			$ret = $this->_arr[$c - 1];
			unset($this->_arr[$c - 1]);
			return $ret;
		}


		/**
		 *
		 * Get value of last element in array
		 * @return unknown value of last element in array
		 */
		function getLast()
		{
			if (!$c = count($this->_arr))
				return null;
			return $this->_arr[$c - 1];
		}


		/**
		 *
		 * Convert array to string
		 * @return string
		 */
		function toString()
		{
			$output = '';
			foreach ($this->_arr as $item) {
				$output .= $item;
			}
			return $output;
		}
	}
}