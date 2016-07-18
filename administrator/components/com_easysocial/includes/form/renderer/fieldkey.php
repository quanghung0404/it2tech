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

/**
 * Fieldkey Generator
 *
 * @since	1.2
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialFormRendererFieldkey
{
	public function render( &$field , &$params )
	{
		// Get a list of unique keys from all of the fields.
		$model 		= FD::model( 'Fields' );
		$keys 		= $model->getUniqueKeys();

		$theme 		= FD::themes();

		$theme->set( 'params', $params );
		$theme->set( 'field', $field );
		$theme->set( 'keys' , $keys );

		$field->output 	= $theme->output( 'admin/forms/types/fieldkey' );
	}
}
