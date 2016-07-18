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

class SocialFieldsUserPermalinkHelper
{
	/**
	 * Ensures that the user doesn't try to use a permalink from a menu alias
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function allowed($permalink)
	{
		$jConfig = ES::jConfig();

		// If sef isn't enabled, we shouldn't really need to worry about this.
		if (!$jConfig->getValue('sef')) {
			return true;
		}

		// Find any menu alias on the site which uses similar alias
		if (self::menuAliasExists($permalink)) {
			return false;
		}

		return true;
	}

	public static function menuAliasExists($permalink)
	{
		$db = ES::db();
		$query = $db->sql();

		$query->select('#__menu');
		$query->column('COUNT(1)');
		$query->where('client_id', 0);
		$query->where('published', 1);
		$query->where('alias', $permalink);

		$db->setQuery($query);
		$exists = $db->loadResult() > 0 ? true : false;

		return $exists;
	}

	/**
	 * Determines if the permalink is a valid permalink
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function valid($permalink, $params)
	{
		$invalid = preg_match("#[<>\"'%;()\!&_ @\.]#i", $permalink);
		
		if (!$permalink || $invalid) {
			return false;
		}

		// Get a list of forbidden permalinks
		$forbidden = $params->get('forbidden');

		if (!$forbidden) {
			return true;
		}

		$words = explode(',', $forbidden);

		// Trim the forbidden words
		foreach ($words as $word) {
			$word = JString::trim($word);

			if ($word && JString::stristr($permalink, $word) !== false) {
				return false;
			}
		}

		return true;
	}

	public static function exists( $permalink, $current = '' )
	{
		$db 	= FD::db();

		$sql	= $db->sql();

		$sql->select( '#__social_users' );
		$sql->where( 'permalink' , JFilterOutput::stringURLSafe( $permalink ) );

		if (!empty($current)) {
			$sql->where('permalink', $current, '!=');
		}

		$db->setQuery( $sql->getTotalSql() );

		$total	= $db->loadResult();

		if( $total > 0 )
		{
			return true;
		}

		// Do not allow them to use any "views"
		$views 	= JFolder::folders( JPATH_ROOT . '/components/com_easysocial/views' );

		if( in_array( $permalink , $views ) )
		{
			return true;
		}

		return false;
	}
}
