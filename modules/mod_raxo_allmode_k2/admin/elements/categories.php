<?php
/**
 * =============================================================
 * RAXO All-mode K2 J3.x - Element
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @copyright	Copyright (C) 2009-2014 RAXO Group
 * @license		GNU General Public License v2.0
 * 				http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://www.raxo.org
 * =============================================================
 */


defined('_JEXEC') or die;

jimport('joomla.form.formfield');

class JFormFieldCategories extends JFormField
{
	protected $type = 'Categories';

	function getInput()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('c.id, c.name, c.parent');
		$query->from('#__k2_categories AS c');
		$query->where('c.trash = 0');
		$query->order('parent, ordering');

		$db->setQuery($query);
		$categories = $db->loadAssocList();

		$children	= array();
		$cat_items	= array();

		foreach ($categories as $c) {
			$children[$c['parent']][] = $c;
		}

		$tree = self::checkParent(0, '', array(), $children, 9999, 0);

		// Add select all option
		$cat_items[] = JHTML::_('select.option', '', JText::_('MOD_RAXO_ALLMODE_K2_SOURCE_CAT_ALL'));
		foreach ($tree as $item) {
			$cat_items[] = JHTML::_('select.option',  $item['id'], $item['tree_name'] );
		}

		// Initialize some field attributes
		$attr  = $this->multiple ? ' multiple="multiple"' : '';
		$attr .= count($categories) >= 19 ? ' size="20"' : ' size="10"';
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		return JHTML::_('select.genericlist', $cat_items, $this->name, $attr, 'value', 'text', $this->value, $this->id);
	}


	function checkParent($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0)
	{
		if (@$children[$id] && $level <= $maxlevel) {
			foreach ($children[$id] as $v) {
				$id = $v['id'];
				$list[$id] = $v;
				$list[$id]['tree_name'] = ($v['parent'] == 0) ? $v['name'] : $indent .'&mdash; '. $v['name'];

				$list = self::checkParent($id, $indent .'&nbsp;&nbsp;', $list, $children, $maxlevel, $level + 1);
			}
		}
		return $list;
	}
}