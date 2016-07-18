<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class ThemesHelperGrid
{
	/**
	 * Renders a checkbox for each row.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function id( $number , $id , $allowed = true , $checkedOut = false , $name = 'cid' )
	{
		$theme 	= FD::themes();

		$theme->set( 'allowed'		, $allowed );
		$theme->set( 'number'		, $number );
		$theme->set( 'name'			, $name );
		$theme->set( 'checkedOut'	, $checkedOut );
		$theme->set( 'id'			, $id );

		$contents 	= $theme->output( 'admin/html/grid.id' );

		return $contents;
	}

	/**
	 * Renders publish / unpublish icon.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	The object to check against.
	 * @param	string	The controller to be called.
	 * @param	string	The key for the object.
	 * @param	bool	Whether or not the key should be checked in a reverse state.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function userActivation( $allowed = true , $obj , $controllerName = '' )
	{
		// By default the key is the `state` column
		$key			= 'state';

		$publishTask	= 'publish';
		$unpublishTask	= 'unpublish';

		switch( $obj->$key )
		{
			case SOCIAL_USER_STATE_ENABLED:
				$class 		= 'publish';
				$tooltip	= JText::_( 'COM_EASYSOCIAL_GRID_USERS_ACTIVATE_USER' );
				break;

			case SOCIAL_USER_STATE_DISABLED:
				$class 	= 'unpublish';
				$tooltip	= JText::_( 'COM_EASYSOCIAL_GRID_USERS_DEACTIVATE_USER' );
				break;
		}

		if( !$allowed )
		{
			$tooltip = JText::_( 'You cannot block yourself' );
		}

		$task			= $obj->$key ? $unpublishTask : $publishTask;

		$theme 			= FD::get( 'Themes' );
		$theme->set( 'allowed'	, $allowed );
		$theme->set( 'class'	, $class );
		$theme->set( 'task'		, $task );
		$theme->set( 'tooltip'	, $tooltip );

		return $theme->output( 'admin/html/grid.published' );
	}

	/**
	 * Renders publish / unpublish icon.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	The object to check against.
	 * @param	string	The controller to be called.
	 * @param	string	The key for the object.
	 * @param	bool	Whether or not the key should be checked in a reverse state.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function userPublished( $allowed = true , $obj , $controllerName = '' )
	{
		// By default the key is the `state` column
		$key = 'block';

		$publishTask = 'publish';
		$unpublishTask = 'unpublish';

		switch( $obj->$key )
		{
			case SOCIAL_JOOMLA_USER_UNBLOCKED:
				$class = 'publish';
				$tooltip = FD::_('COM_EASYSOCIAL_GRID_TOOLTIP_UNPUBLISH_USER', true);
				break;

			case SOCIAL_JOOMLA_USER_BLOCKED:
				$class = 'unpublish';
				$tooltip = FD::_('COM_EASYSOCIAL_GRID_TOOLTIP_PUBLISH_USER', true);
				break;
		}

		if (!$allowed) {
			$tooltip = JText::_('COM_EASYSOCIAL_GRID_NOT_ALLOWED_TO_BLOCK_YOURSELF');
		}

		$task = !$obj->$key ? $unpublishTask : $publishTask;

		$theme = FD::get('Themes');
		$theme->set('allowed', $allowed);
		$theme->set('class', $class);
		$theme->set('task', $task);
		$theme->set('tooltip', $tooltip);

		return $theme->output('admin/html/grid.published');
	}

	/**
	 * Renders publish / unpublish icon.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	The object to check against.
	 * @param	string	The controller to be called.
	 * @param	string	The key for the object.
	 * @param	Array	An optional array of tasks
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function published( $obj , $controllerName = '' , $key = '' , $tasks = array() , $tooltips = array(), $classes = array() )
	{
		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key = !empty( $key ) ? $key : 'state';

		// array_replace is only supported php>5.3
		// While array_replace goes by base, replacement
		// Using + changes the order where base always goes last

		$classes += array(
			-1 => 'trash',
			0 => 'unpublish',
			1 => 'publish'
		);
		$tasks += array(
			-1 => 'publish',
			0 => 'publish',
			1 => 'unpublish'
		);
		$tooltips += array(
			-1 => 'COM_EASYSOCIAL_GRID_TOOLTIP_TRASHED_ITEM',
			0 => 'COM_EASYSOCIAL_GRID_TOOLTIP_PUBLISH_ITEM',
			1 => 'COM_EASYSOCIAL_GRID_TOOLTIP_UNPUBLISH_ITEM'
		);

		$class = isset($classes[$obj->$key]) ? $classes[$obj->$key] : '';
		$task = isset($tasks[$obj->$key]) ? $tasks[$obj->$key] : '';
		$tooltip = isset($tooltips[$obj->$key]) ? JText::_($tooltips[$obj->$key]) : '';

		// If task is empty, then we don't want this button to do anything
		$allowed = !empty($task);

		$theme = FD::themes();

		$theme->set('allowed', $allowed);
		$theme->set('tooltip', $tooltip);
		$theme->set('task', $task);
		$theme->set('class', $class);

		return $theme->output('admin/html/grid.published');
	}

	/**
	 * Renders the order save button in a grid
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function order($rows)
	{
		$count = count($rows);

		if (!$rows || !$count) {
			return '';
		}

		$theme 	= FD::themes();
		$theme->set('total', $count);

		$contents 	= $theme->output( 'admin/html/grid.order' );
		return $contents;
	}

	/**
	 * Renders the ordering in a grid
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function ordering( $total , $current , $showOrdering = true , $ordering = 0 )
	{
		$theme 	= FD::themes();

		$theme->set( 'current'	, $current );
		$theme->set( 'total'	, $total );
		$theme->set( 'ordering'	, $ordering );
		$theme->set( 'showOrdering' , $showOrdering );

		$contents 	= $theme->output( 'admin/html/grid.ordering' );

		return $contents;
	}

	/**
	 * Renders the ordering in a grid
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function sort( $column , $text , $currentOrdering , $direction )
	{
		$theme 	= FD::themes();

		// Ensure that the direction is always in lowercase because we will check for it in the theme file.
		$direction 			= JString::strtolower( $direction );
		$currentOrdering	= JString::strtolower( $currentOrdering );
		$column 			= JString::strtolower( $column );

		$theme->set( 'column'	, $column );
		$theme->set( 'text'		, $text );
		$theme->set( 'currentOrdering'	, $currentOrdering );
		$theme->set( 'direction'		, $direction );

		$contents 	= $theme->output( 'admin/html/grid.sort' );

		return $contents;
	}

	/**
	 * Renders feature / unfeature icon.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	The object to check against.
	 * @param	string	The controller to be called.
	 * @param	string	The key for the object.
	 * @param	Array	An optional array of tasks
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function featured($obj , $controllerName = '' , $key = '' , $task = '' , $allowed = true , $tooltip = array() )
	{
		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key	= !empty( $key ) ? $key : 'default';

		$task 	= !empty( $task ) ? $task : 'toggleDefault';

		switch( $obj->$key )
		{
			case SOCIAL_STATE_PUBLISHED:
				$class 		= 'featured';
				$tooltip 	= '';

				if( $allowed )
				{
					$tooltip	= isset( $tooltip[ 1 ] ) ? $tooltip[ 1 ] : FD::_('COM_EASYSOCIAL_GRID_TOOLTIP_UNFEATURE_ITEM', true);
				}


				break;

			default:
				$class 		= 'default';

				$tooltip	= isset( $tooltip[ 0 ] ) ? $tooltip[ 0 ] : FD::_('COM_EASYSOCIAL_GRID_TOOLTIP_FEATURE_ITEM', true);

				break;
		}

		$theme 			= FD::get( 'Themes' );
		$theme->set( 'task'		, $task );
		$theme->set( 'class'	, $class );
		$theme->set( 'tooltip'	, $tooltip );
		$theme->set( 'allowed'	, $allowed );

		return $theme->output( 'admin/html/grid.published' );
	}

	/**
	 * Renders a Yes / No input.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The key name for the input.
	 * @param	bool			The value of the current item. Should be either false / true.
	 * @param	string			The id of the input (Optional).
	 * @param	string/array	The attributes to add to the select list.
	 * @param	array			The tooltips data.
	 *
	 * @return	string	The html output.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function boolean( $name , $value , $id = '' , $attributes = '' , $tips = array() , $text = array() )
	{
		// Load language strings from back end.
		JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );

		// Ensure that id is set.
		$id 		= empty( $id ) ? $name : $id;

		// Determine if the input should be checked.
		$checked	= $value ? true : false;

		$theme 		= FD::get( 'Themes' );

		if( is_array( $attributes ) )
		{
			$attributes	= implode( ' ' , $attributes );
		}

		$onText		= JText::_( 'COM_EASYSOCIAL_GRID_YES' );
		$offText 	= JText::_( 'COM_EASYSOCIAL_GRID_NO' );

		if( isset( $text[ 'on' ] ) )
		{
			$onText 	= $text[ 'on' ];
		}

		if( isset( $text[ 'off' ] ) )
		{
			$offText 	= $text[ 'off' ];
		}

		$theme->set( 'onText'	, $onText );
		$theme->set( 'offText'	, $offText );
		$theme->set( 'attributes' , $attributes );
		$theme->set( 'tips'		, $tips );
		$theme->set( 'id'		, $id );
		$theme->set( 'name'		, $name );
		$theme->set( 'checked' 	, $checked );

		return $theme->output( 'admin/html/grid.boolean' );
	}

	/**
	 * Renders a Yes / No input.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The key name for the input.
	 * @param	bool	The value of the current item. Should be either false / true.
	 * @param	string	The id of the input (Optional).
	 *
	 * @return	string	The html output.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function checkbox( $key , $value , $label = '' , $id = '', $attributes = '' )
	{
		// Ensure that id is set.
		$id 		= empty( $id ) ? $key : $id;

		// Determine if the input should be checked.
		$checked	= $value ? true : false;

		if( is_array( $attributes ) )
		{
			$attributes	= implode( ' ' , $attributes );
		}

		$theme 	= FD::get( 'Themes' );

		$theme->set( 'id'			, $id );
		$theme->set( 'key'			, $key );
		$theme->set( 'label'		, $label );
		$theme->set( 'checked'		, $checked );
		$theme->set( 'attributes'	, $attributes );
		return $theme->output( 'admin/html/grid.checkbox' );
	}

	/**
	 * Renders a select list
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The key name for the input.
	 * @param	string			The value of the selected item.
	 * @param	array			The options to show in the select list.
	 * @param	string			The id of the select list. (optional, will fallback to name by default)
	 * @param	string/array	The attributes to add to the select list.
	 * @param	string			The key to look for the value in the options array.
	 * @param	string			The key to look for the text in the options array.
	 *
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public static function selectlist( $name, $selected, $arr, $id = '', $attributes = '', $key = 'value', $text = 'text' )
	{
		$options = array();

		// array should only contain option list.
		unset( $arr['attributes'] );


		if( count($arr) > 0 )
		{
		    foreach( $arr as $element )
		    {
				if( is_array( $element ) )
				{
				    $val    = ( isset( $element[$key] ) ) ? $element[$key] : '';
				    $txt    = ( isset( $element[$text] ) ) ? $element[$text] : '';
				}
				else
				{
				    $val    = $element->$key;
				    $txt    = $element->$text;
				}

				// ensure ampersands are encoded
				$val = JFilterOutput::ampReplace( $val );
				$txt = JFilterOutput::ampReplace( $txt );

				$options[$val] = $txt;
			}
		}

		$isMultiple = false;

		if( is_array( $attributes ) )
		{
			if( isset( $attributes['multiple'] ) )
			{
				$isMultiple = true;
				unset( $attributes['multiple'] );
			}

			$attributes	= implode( ' ' , $attributes );
		}

		$theme = FD::get( 'Themes' );
		$theme->set( 'options'		, $options );
		$theme->set( 'selected'		, $selected );
		$theme->set( 'name'			, $name );
		$theme->set( 'id'			, $id );
		$theme->set( 'attributes'	, $attributes );
		$theme->set( 'ismultiple'	, $isMultiple );
		return $theme->output( 'admin/html/grid.selectlist' );
	}

	/**
	 * Renders a input box
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
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public static function inputbox( $name, $value = '', $id = '', $attributes = '' )
	{
		if( is_array( $attributes ) )
		{
			$attributes	= implode( ' ' , $attributes );
		}

		// If value is an array, implode it with a comma as a separator
		if( is_array( $value ) )
		{
			$value 	= implode( ',' , $value );
		}

		$theme = FD::get( 'Themes' );
		$theme->set( 'name'			, $name );
		$theme->set( 'value'		, $value );
		$theme->set( 'id'			, $id );
		$theme->set( 'attributes'	, $attributes );

		return $theme->output( 'admin/html/grid.inputbox' );
	}

	/**
	 * Renders a textarea
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
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */

	public static function textarea( $name, $value = '', $id = '', $attributes = '' )
	{
		if( is_array( $attributes ) )
		{
			$attributes	= implode( ' ' , $attributes );
		}

		$theme = FD::get( 'Themes' );
		$theme->set( 'name'			, $name );
		$theme->set( 'value'		, $value );
		$theme->set( 'id'			, $id );
		$theme->set( 'attributes'	, $attributes );

		return $theme->output( 'admin/html/grid.textarea' );
	}

	/**
	 * Renders a hidden input
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
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public static function hidden( $name, $value = '', $id = '', $attributes = '' )
	{
		if( is_array( $attributes ) )
		{
			$attributes	= implode( ' ' , $attributes );
		}

		$theme = FD::get( 'Themes' );
		$theme->set( 'name'			, $name );
		$theme->set( 'value'		, $value );
		$theme->set( 'id'			, $id );
		$theme->set( 'attributes'	, $attributes );

		return $theme->output( 'admin/html/grid.hidden' );
	}

	/**
	 * Renders a date form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The key name for the input.
	 * @param	string			The date value.
	 * @param	string			The id of the date form. (optional, will fallback to name by default)
	 * @param	string/array	The attributes to add to the select list.
	 *
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public static function dateform( $name, $value = '', $id = '', $attributes = '', $withOffset = true)
	{
		if (is_array($attributes)) {
			$attributes	= implode( ' ' , $attributes );
		}

		if (empty($value)) {
			$value = FD::date('now', $withOffset);
		} else {
			$value = FD::date($value, $withOffset);
		}

		$theme = FD::get( 'Themes' );
		$theme->set( 'name'			, $name );
		$theme->set( 'value'		, $value );
		$theme->set( 'id'			, $id );
		$theme->set( 'attributes'	, $attributes );

		return $theme->output( 'admin/html/grid.dateform' );
	}

	public static function location($location, $options='')
	{
		$uid = uniqid();
		$classname = 'es-location-' . $uid;
		$selector  = '.' . $classname;

		if (empty($location)) {
			$location = FD::table( 'Location' );
		}

		$theme = FD::get( 'Themes' );
		$theme->set( 'uid'     		, $uid );
		$theme->set( 'classname'	, $classname );
		$theme->set( 'selector'		, $selector );
		$theme->set( 'location'     , $location );

		return $theme->output( 'admin/html/grid.location' );
	}
}
