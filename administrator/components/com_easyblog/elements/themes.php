<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class JElementThemes extends JElement
{
	var	$_name = 'Themes';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$mainframe	= JFactory::getApplication();
		$doc 		= JFactory::getDocument();

		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
		$themes 	= EBLOG_THEMES;

		$themes 	= JFolder::folders( $themes );

		ob_start();
		?>
		<select name="<?php echo $control_name;?>[<?php echo $name;?>]">
			<option value="0"<?php echo $value == 0 ? ' selected="selected"' :'';?>><?php echo JText::_('Select a theme');?></option>
		<?php
		foreach($themes as $theme)
		{
			if( $theme != 'dashboard' )
			{
			$selected	= $theme == $value ? ' selected="selected"' : '';
		?>
			<option value="<?php echo $theme;?>"<?php echo $selected;?>><?php echo $theme;?></option>
		<?php
			}
		}
		?>
		</select>
		<?php
		$html	= ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
