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

// Include main model file.
FD::import( 'admin:/includes/model' );

class EasySocialModelHashtags extends EasySocialModel
{
	public function __construct()
	{
		parent::__construct( 'hashtags' );
	}

	/**
	 * Searches for a particular hash tag given the current keyword
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function search($keyword)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_stream_tags' , 'a');
		$sql->where('a.utype', 'hashtag');
		$sql->where('a.title', '%' . $keyword . '%', 'LIKE');
		$sql->group('a.title');

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		return $result;
	}

}
