<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsnisid.php 8411 2011-09-22 04:45:10Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
class JFormFieldJsnisid extends JFormField
{
	protected function getInput()
	{
		if (empty($this->value)) {
			$this->value = time();
		}

		$html = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';

		return $html;
	}
}
?>