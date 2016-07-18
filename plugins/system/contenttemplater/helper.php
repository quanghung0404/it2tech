<?php
/**
 * Plugin Helper File
 *
 * @package         Content Templater
 * @version         5.1.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * System Plugin that places a Content Templater code block into the text
 */
class PlgSystemContentTemplaterHelper
{
	var $params = null;
	var $parameters = null;

	public function __construct(&$params)
	{
		$this->params = $params;

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$this->parameters = NNParameters::getInstance();
	}

	/**
	 * place content on page load
	 */
	function placeContent()
	{
		$buffer = JFactory::getDocument()->getBuffer('component');

		$editor = 0;
		if (preg_match('#CT_editor\s*=\s*"(.*?)"#', $buffer, $matches))
		{
			$editor = $matches['1'];
		}

		if (!$editor)
		{
			return;
		}

		$empty_editor = 0;
		$regex        = '#(<textarea[^>]*\sid="' . preg_quote($editor, '#') . '"[^>]*>)\s*(</textarea>)#s';
		if (preg_match($regex, $buffer))
		{
			$empty_editor = 1;
		}

		if (!$empty_editor)
		{
			return;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/assignments.php';
		$this->assignments = new NNFrameworkAssignmentsHelper;

		require_once JPATH_ADMINISTRATOR . '/components/com_contenttemplater/models/list.php';
		$list = new ContentTemplaterModelList;
		$list->setState('limit', 0);
		$list->setState('limitstart', 0);
		$items = $list->getItems();

		$default  = 0;
		$url_load = JFactory::getApplication()->input->get('ctid');

		foreach ($items as $item)
		{
			// not enabled if: not published
			if (!$item->published)
			{
				continue;
			}

			// check if template should be loaded by url
			if ($url_load && $url_load == $item->id
				&& $this->passChecks($item, 'url')
			)
			{
				$default = $item->id;
				break;
			}

			// check if template should be loaded by default
			if (!$default
				&& $this->passChecks($item, 'load')
			)
			{
				$default = $item->id;
				break;
			}
		}

		if ($default)
		{
			require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_contenttemplater/models/item.php';

			// Create a new class of classname and set the default task: display
			$model = new ContentTemplaterModelItem;
			$item  = $model->getItem($default, 0, 1);
			$model->replaceVars($item->content);

			if (preg_match($regex, $buffer))
			{
				$buffer = preg_replace($regex, '\1' . $item->content . '\2', $buffer);
				JFactory::getDocument()->setBuffer($buffer, 'component');
			}

			NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/contenttemplater/js/script.min.js');
			$script = '
				var CT_f_loaded = 0;
				jQuery(document).ready(function() {
					if( CT_f_loaded == 0 ) {
						CT_f_loaded = 1;
						jQuery( function() { ContentTemplater.getXML( ' . $default . ', \'' . $editor . '\', 1 ) } ).delay( ' . (int) ($this->params->url_delay * 1000) . ' );
					}
				});
			';
			JFactory::getDocument()->addScriptDeclaration($script);
		}
	}

	function passChecks(&$item, $type = 'button')
	{
		if (!$item->{$type . '_enabled'})
		{
			return false;
		}
		// not enabled if: not active in this area (frontend/backend)
		if (
			(JFactory::getApplication()->isAdmin() && $item->{$type . '_enable_in_frontend'} == 2)
			|| (JFactory::getApplication()->isSite() && $item->{$type . '_enable_in_frontend'} == 0)
		)
		{
			return false;
		}

		// return true if assignments are already checked
		if (isset($item->pass_assignments))
		{
			return $item->pass_assignments;
		}

		$ass                    = $this->assignments->getAssignmentsFromParams($item);
		$item->pass_assignments = $this->assignments->passAll($ass, $item->match_method);

		return $item->pass_assignments;
	}
}
