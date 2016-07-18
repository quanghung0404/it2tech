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
 * Helper for currency
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserCurrencyHelper
{
	/**
	 * Get label for currency.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	JRegistry	The parameter object.
	 * @param	string		The type of string to lookup for.
	 * @return	string
	 */
	public static function getLabel( &$params , $type )
	{
		// Get the currency to use.
		$text		= 'PLG_FIELDS_CURRENCY_' . strtoupper( $params->get( 'format' ) ) . '_' . strtoupper( $type );

		return JText::_( $text );
	}
}
