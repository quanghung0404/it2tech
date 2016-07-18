<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ThemesHelperForm
{
	/**
	 * Generates token for the form.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 */
	public static function token()
	{
		$token = FD::token();

		$theme = ES::themes();
		$theme->set('token', $token);

		$content = $theme->output('admin/html/form/token');

		return $content;
	}

	/**
	 * Allows caller to generically load up a form action which includes the generic data
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function action($controller, $task = '', $view = '')
	{
		$theme = ES::themes();

		$theme->set('controller', $controller);
		$theme->set('task', $task);
		$theme->set('view', $view);

		$output = $theme->output('admin/html/form/action');

		return $output;
	}

	/**
	 * Generates a location form
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function location($name, SocialLocation $location)
	{
		$theme = ES::themes();

		$address = $location->getAddress();
		$latitude = $location->getLatitude();
		$longitude = $location->getLongitude();

		$theme->set('name', $name);
		$theme->set('address', $address);
		$theme->set('latitude', $latitude);
		$theme->set('longitude', $longitude);

		$output = $theme->output('admin/html/form/location');

		return $output;
	}

	/**
	 * Generates the item id
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string
	 */
	public static function itemid( $itemid = null )
	{
		// Check for the current itemid in the request
		if( is_null( $itemid ) )
		{
			$itemid		= JRequest::getInt( 'Itemid' , 0 );
		}

		if( !$itemid )
		{
			return;
		}

		$theme	= FD::themes();

		$theme->set( 'itemid'	, $itemid );

		$content = $theme->output('admin/html/form/itemid');

		return $content;
	}


	/**
	 * Renders a WYSIWYG editor that is configured in Joomla
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function editor( $name , $value = '' , $id = '' , $editor = '' )
	{
		// Get the editor
		$editor = JFactory::getEditor('tinymce');

		$theme = FD::themes();

		$theme->set( 'editor'	, $editor );
		$theme->set( 'name'		, $name );
		$theme->set( 'content'	, $value );
		$content 	= $theme->output('admin/html/form/editor');

		return $content;
	}

	/**
	 * Renders a user group select list
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function usergroups( $name , $selected = '' )
	{
		$model = FD::model('Users');
		$groups = $model->getUserGroups();

		$theme = FD::themes();

		$theme->set( 'name', $name );
		$theme->set( 'selected', $selected );
		$theme->set( 'groups', $groups );

		$output = $theme->output('admin/html/form/usergroups');

		return $output;
	}

	/**
	 * Renders a calendar input
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The key name for the input.
	 * @param	string			The value of the selected item.
	 * @param	string			The id of the select list. (optional, will fallback to name by default)
	 * @param	string/array	The attributes to add to the select list.
	 *
	 * @return	string	The html output.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function calendar( $name , $value = '', $id = '' , $attributes = '' , $time = false , $format = '', $language = false )
	{
		if (is_array($attributes)) {
			$attributes	= implode( ' ' , $attributes );
		}

		$theme = FD::themes();
		$uuid = uniqid();

		if (!$language) {
			$language 	= JFactory::getDocument()->getLanguage();
		}

		$theme->set('language'		, $language);
		$theme->set( 'time'			, $time );
		$theme->set( 'uuid'			, $uuid );
		$theme->set( 'format'		, $format );
		$theme->set( 'name'			, $name );
		$theme->set( 'value'		, $value );
		$theme->set( 'id'			, $id );
		$theme->set( 'attributes'	, $attributes );

		return $theme->output('admin/html/form/calendar');
	}

	/**
	 * Renders a select list for editors on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The key name for the input.
	 * @param	string			The value of the selected item.
	 * @param	string			The id of the select list. (optional, will fallback to name by default)
	 * @param	string/array	The attributes to add to the select list.
	 *
	 * @return	string	The html output.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function editors( $name , $value = '', $id = '' , $attributes = '' )
	{
		if( is_array( $attributes ) )
		{
			$attributes	= implode( ' ' , $attributes );
		}

		$theme 	= FD::themes();

		// Get list of editors on the site first.
		$editors 	= self::getEditors();

		$theme->set( 'editors'		, $editors );
		$theme->set( 'name'			, $name );
		$theme->set( 'value'		, $value );
		$theme->set( 'id'			, $id );
		$theme->set( 'attributes'	, $attributes );

		return $theme->output('admin/html/form/editors');
	}

	/**
	 * Displays the text input
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function text($name, $id = null, $value = '', $options)
	{
		$class = 'form-control input-sm';
		$placeholder = '';
		$attributes = '';

		if (isset($options['attr']) && $options['attr']) {
			$attributes = $options['attr'];
		}

		if (isset($options['class']) && $options['class']) {
			$class = $options['class'];
		}

		if (isset($options['placeholder']) && $options['placeholder']) {
			$placeholder = JText::_($options['placeholder']);
		}

		$theme = ES::themes();
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		return $theme->output('admin/html/form/text');
	}

	/**
	 * Displays the textarea input
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function textarea($name, $id = null, $value = '', $options)
	{
		$class = 'form-control input-sm';
		$placeholder = '';

		if (isset($options['class']) && $options['class']) {
			$class = $options['class'];
		}

		if (isset($options['placeholder']) && $options['placeholder']) {
			$placeholder = JText::_($options['placeholder']);
		}

		$theme = ES::themes();
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		return $theme->output('admin/html/form/textarea');
	}


	/**
	 * Retrieve list of editors from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getEditors()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__extensions' );
		$sql->column( 'element' , 'value' );
		$sql->column( 'name' , 'text' );
		$sql->where( 'folder' , 'editors' );
		$sql->where( 'type' , 'plugin' );
		$sql->where( 'enabled' , SOCIAL_STATE_PUBLISHED );

		$db->setQuery( $sql );
		$editors 	= $db->loadObjectList();

		// Load the language file of each editors
		$lang 	= JFactory::getLanguage();

		foreach( $editors as &$editor )
		{
			$lang->load( $editor->text . '.sys' , JPATH_ADMINISTRATOR , null , false , false );

			$editor->text 	= JText::_( $editor->text );
		}

		return $editors;
	}

	/**
	 * Displays a pull down select list to select a profile type
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function profiles( $name , $id = '' , $selected = null , $attributes = array() )
	{
		// If the id is empty, we'll re-use the name as the id.
		$id 	= !$id ? $name : $id;

		// Get the list of profiles on the site
		$model 		= FD::model( 'Profiles' );
		$profiles	= $model->getProfiles();

		$multiple 	= isset($attributes['multiple']) ? $attributes['multiple'] : false;

		$attributes	= FD::makeArray( $attributes );
		$attributes	= implode( ' ' , $attributes );



		$theme		= FD::themes();
		$theme->set('multiple', $multiple);
		$theme->set('name'		, $name );
		$theme->set('attributes', $attributes );
		$theme->set('profiles' , $profiles );
		$theme->set('id'		, $id );
		$theme->set('selected'	, $selected );

		$output = $theme->output('admin/html/form/profiles');

		return $output;
	}

	/**
	 * Displays a list of menu forms
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function menus( $name , $selected , $menus = array() )
	{
		require_once realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

		$items 	= MenusHelper::getMenuLinks();

		// Build the groups arrays.
		foreach ($items as $menu)
		{
			// Initialize the group.
			$menus[$menu->menutype] = array();

			// Build the options array.
			foreach ($menu->links as $link)
			{
				$menus[$menu->menutype][] = JHtml::_( 'select.option' , $link->value , $link->text );
			}
		}

		$theme 	= FD::themes();

		$theme->set( 'name'		, $name );
		$theme->set( 'menus'	, $menus );
		$theme->set( 'selected' , $selected );
		$output = $theme->output('admin/html/form/menus');

		return $output;
	}
}



