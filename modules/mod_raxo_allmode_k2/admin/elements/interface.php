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

class JFormFieldInterface extends JFormField
{
	protected $type = 'Interface';

	protected function getInput()
	{
		return null;
	}

	protected function getLabel()
	{
		JHtml::stylesheet($this->element['path'].'interface.css');
		JHtml::script($this->element['path'].'interface.js');

		return null;
	}
}