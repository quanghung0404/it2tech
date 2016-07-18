<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_show.php 8418 2011-09-22 08:18:02Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );

class JSNISShow
{
	var $_db = null;

	function JSNISShow()
	{
		if ($this->_db == null)
		{
			$this->_db = JFactory::getDBO();
		}
	}

	public static function getInstance()
	{
		static $instanceShow;
		if ($instanceShow == null)
		{
			$instanceShow = new JSNISShow();
		}
		return $instanceShow;
	}

	function getArticleAlternate($showlistID)
	{
		$query 	= 'SELECT c.introtext, c.fulltext
				   FROM #__imageshow_showlist sl
				   INNER JOIN #__content c ON sl.alter_id = c.id
				   WHERE sl.showlist_id='.(int)$showlistID;
		$this->_db->setQuery($query);

		return $this->_db->loadAssoc();
	}

	function getModuleAlternate($showlistID)
	{
		$query = 'SELECT m.*
				  FROM #__imageshow_showlist sl
				  INNER JOIN #__modules m ON sl.alter_module_id = m.id
				  WHERE sl.showlist_id = '.(int)$showlistID;

		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	function getModuleSEO($showlistID)
	{
		$query = 'SELECT m.*
				  FROM #__imageshow_showlist sl
				  INNER JOIN #__modules m ON sl.seo_module_id = m.id
				  WHERE sl.showlist_id = '.(int)$showlistID;

		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	function getArticleSEO($showlistID)
	{
		$query = 'SELECT c.introtext, c.fulltext
		          FROM #__imageshow_showlist sl
		          INNER JOIN #__content c ON sl.seo_article_id = c.id
		          WHERE sl.showlist_id='.(int)$showlistID;
		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	function getArticleAuth($showlistID)
	{
		$query 	= 'SELECT c.introtext, c.fulltext
				   FROM #__imageshow_showlist sl
				   INNER JOIN #__content c ON sl.alter_autid = c.id
				   WHERE sl.showlist_id='.(int)$showlistID;
		$this->_db->setQuery($query);

		return $this->_db->loadAssoc();
	}

	function getModuleByID($ID)
	{
		$query = 'SELECT id, title, module, position, content, showtitle, params FROM #__modules WHERE id = '.(int)$ID;
		$this->_db->setQuery($query);
		$row 			= $this->_db->loadObject();
		$file 			= $row->module;
		$custom         = substr($file, 0, 4) == 'mod_' ?  0 : 1;
		$row->user      = $custom;
		$row->name      = $custom ? $row->title : substr($file, 4);
		return $row;
	}

	function renderAlternativeImage($path)
	{
		jimport('joomla.filesystem.file');

		$rootPath 	= JPATH_ROOT;
		$imagePath 	= $rootPath.DS.str_replace('/', DS, $path);
		$dimension	= array();

		if (JFile::exists($imagePath))
		{
			list($width, $height) = @getimagesize($imagePath);
			$dimension ['width']  = $width;
			$dimension ['height'] = $height;
		}
		return $dimension;
	}

	function renderAlternativeListImages($imagesData = array(), $showlistInfo = array())
	{
		$html = '';

		if (count( $imagesData ))
		{
			$html .= '<div>';
			$html .= '<p>'.htmlspecialchars(html_entity_decode($showlistInfo['showlist_title'])).'</p>';
			$html .= '<p>'.htmlspecialchars(html_entity_decode($showlistInfo['description'])).'</p>';
			$html .= '<ul>';

			foreach ($imagesData as $image)
			{
				$html .= '<li>';

				if ($image->image_title !='')
				{
					$html .= '<p>'.htmlspecialchars(html_entity_decode($image->image_title)).'</p>';
				}

				if ($image->image_description !='')
				{
					$html .= '<p>'.htmlspecialchars(html_entity_decode($image->image_description)).'</p>';
				}

				if ($image->image_link !='')
				{
					$html .= '<p><a href="'.htmlspecialchars(html_entity_decode($image->image_link)).'">'.htmlspecialchars(html_entity_decode($image->image_link)).'</a></p>';
				}

				$html .= '</li>';
			}
			$html .= '</ul></div>';
		}

		return $html;
	}
}