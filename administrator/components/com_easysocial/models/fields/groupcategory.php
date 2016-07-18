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

// Include foundry
require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );

class JFormFieldEasySocial_GroupCategory extends JFormField
{
	protected $type 	= 'EasySocial_User';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   1.6
	 */
	protected function getInput()
	{
		// Load the language file.
		FD::language()->loadAdmin();
		FD::language()->loadSite();

		// Render the headers
		FD::page()->start();

		// Attach dialog's css file.
		JFactory::getDocument()->addStylesheet( rtrim( JURI::root() , '/' ) . '/administrator/components/com_easysocial/themes/default/styles/style.css' );

		$theme 	= FD::themes();

		$label 	= (string) $this->element[ 'label' ];
		$name 	= (string) $this->name;

		if( $this->value )
		{
			$category 	= FD::table( 'GroupCategory' );
			$category->load( $this->value );

			$label 		= $category->get( 'title' );
		}

		$theme->set( 'name'		, $name );
		$theme->set( 'id'		, $this->id );
		$theme->set( 'value'	, $this->value );
		$theme->set( 'label'	, $label );

		$output	= $theme->output( 'admin/jfields/groupcategory' );

		// We do not want to process stylesheets on Joomla 2.5 and below.
		$options	= array();

		if( FD::version()->getVersion() < 3 )
		{
			$options[ 'processStylesheets' ]	= false;
		}

		FD::page()->end( $options );

		return $output;
	}
}
