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

class JFormFieldTags extends JFormField
{
	protected $type = 'Tags';

	function getInput()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('t.id, t.name');
		$query->from('#__k2_tags AS t');
		$query->where('t.published = 1');
		$query->order('t.name');

		$db->setQuery($query);
		$tags = $db->loadAssocList();

		// Add select all option
		array_unshift($tags, array('id'=>'','name'=>JText::_('MOD_RAXO_ALLMODE_K2_TAGS_ALL')));

		// Initialize some field attributes
		$attr  = $this->multiple ? ' multiple="multiple"' : '';
		$attr .= count($tags) >= 20 ? ' size="20"' : (count($tags) <= 5 ? ' size="5"' : ' size="10"');
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		return JHTML::_('select.genericlist', $tags, $this->name, $attr, 'id', 'name', $this->value);
	}
}