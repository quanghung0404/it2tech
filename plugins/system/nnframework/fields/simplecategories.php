<?php
/**
 * @package         NoNumber Framework
 * @version         16.1.9037
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once dirname(__DIR__) . '/helpers/functions.php';
require_once dirname(__DIR__) . '/helpers/field.php';
require_once dirname(__DIR__) . '/helpers/html.php';

class JFormFieldNN_SimpleCategories extends NNFormField
{
	public $type = 'SimpleCategories';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		$size = (int) $this->get('size');
		$attr = $this->get('onchange') ? ' onchange="' . $this->get('onchange') . '"' : '';

		$categories = $this->getOptions();
		$options    = parent::getOptions();

		if ($this->get('show_none', 1))
		{
			$options[] = JHtml::_('select.option', '', '- ' . JText::_('JNONE') . ' -', 'value', 'text');
		}

		if ($this->get('show_new', 1))
		{
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('NN_NEW_CATEGORY') . ' -', 'value', 'text', false);
		}

		$options = array_merge($options, $categories);

		if (!$this->get('show_new', 1))
		{
			return JHtml::_('select.genericlist',
				$options,
				$this->name,
				trim($attr),
				'value',
				'text',
				$this->value,
				$this->id
			);
		}

		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/nnframework/js/simplecategories.min.js');

		$html = array();

		$html[] = '<div class="nn_simplecategory">';

		$html[] = '<input type="hidden" class="nn_simplecategory_value" id="' . $this->id . '" name="' . $this->name . '" value="' . $this->value . '" checked="checked" />';

		$html[] = '<div class="nn_simplecategory_select">';
		$html[] = NNHtml::selectlistsimple(
			$options,
			$this->getName($this->fieldname . '_select'),
			$this->value,
			$this->getId('', $this->fieldname . '_select'),
			$size,
			false
		);
		$html[] = '</div>';

		$html[] = '<div id="' . rand(1000000, 9999999) . '___' . $this->fieldname . '_select.-1" class="nntoggler nntoggler_nofx" style="display:none;">';
		$html[] = '<div class="nn_simplecategory_new">';
		$html[] = '<input type="text" id="' . $this->id . '_new" value="" placeholder="' . JText::_('NN_NEW_CATEGORY_ENTER') . '" />';
		$html[] = '</div>';
		$html[] = '</div>';

		$html[] = '</div>';

		return implode('', $html);
	}

	protected function getOptions()
	{
		$table = $this->get('table');

		if (!$table)
		{
			return array();
		}

		// Get the user groups from the database.
		$query = $this->db->getQuery(true)
			->select(array(
				$this->db->quoteName('category', 'value'),
				$this->db->quoteName('category', 'text'),
			))
			->from($this->db->quoteName('#__' . $table))
			->where($this->db->quoteName('category') . ' != ' . $this->db->quote(''))
			->group($this->db->quoteName('category'))
			->order($this->db->quoteName('category') . ' ASC');
		$this->db->setQuery($query);

		return $this->db->loadObjectList();
	}
}
