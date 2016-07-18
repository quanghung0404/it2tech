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

class EasySocialModDatingSearchHelper
{
	public static function getFields( &$params )
	{
		$db = FD::db();
		$sql = $db->sql();

		static $fields = null;

		if(! $fields )
		{
			// later we need to respect the module settings.
			$elements = array('address','birthday','gender','joomla_fullname', 'joomla_username');

			$db 	= FD::db();
			$sql 	= $db->sql();

			$query = 'select a.`unique_key`, a.`title`, b.`element`';
			$query .= ' from `#__social_fields` as a';
			$query .= ' inner join `#__social_fields_steps` as fs on a.`step_id` = fs.`id` and fs.`type` = ' . $db->Quote('profiles');
			$query .= ' inner join `#__social_profiles` as p on fs.`uid` = p.`id`';
			$query .= ' inner join `#__social_apps` as b on a.`app_id` = b.`id` and b.`group` = ' . $db->Quote( 'user' );
			$query .= ' where a.`searchable` = ' . $db->Quote( '1' );
			$query .= ' and a.`state` = ' . $db->Quote( '1' );
			$query .= ' and a.`unique_key` != ' . $db->Quote( '' );
			$query .= ' and p.`state` = ' . $db->Quote('1');

			$string = "'" . implode("','", $elements) . "'";
			$query .= ' and b.`element` IN (' . $string . ')';

			$sql->raw( $query );

			// echo $sql;exit;

			$db->setQuery( $sql );
			$results = $db->loadObjectList();

			// manual grouping / distinct
			if( $results )
			{
				foreach( $results as $result )
				{
					//$fields[ $result->unique_key ] = $result;
					$fields[ $result->element ] = $result;
				}
			}
		}

		return $fields;
	}
}
