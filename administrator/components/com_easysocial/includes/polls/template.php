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
 * Stream data template.
 * Example:
 *
 * <code>
 * </code>
 *
 * @since
 */
class SocialPollsTemplate extends JObject
{
	public $element = null;
	public $uid = null;
	public $title = null;
	public $multiple = null;
	public $locked = null;
	public $created = null;
	public $created_by = null;
	public $expiry_date = null;
	public $items = null;
	public $cluster_id = null;

	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct()
	{
		// Set the creation date to the current time.
		$date = FD::date();
		$this->created = $date->toMySQL();
		$this->title = '';
		$this->element = '';
		$this->uid = '0';
		$this->multiple = 0;
		$this->locked = 0;
		$this->created_by = '0';
		$this->expiry_date = '';
		$this->items = array();
		$this->cluster_id = null;
	}

	/**
	 *
	 * @since	1.4
	 * @access	public
	 * @param   title string
	 * @return  null
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 *
	 * @since	1.4
	 * @access	public
	 * @param   cluster Id int
	 * @return  null
	 */
	public function setCluster($clusterId)
	{
		$this->cluster_id = $clusterId;
	}

	/**
	 * Sets the creator of the poll.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The actor's id.
	 * @param	string	The actor's type.
	 */
	public function setCreator($userid)
	{
		// Set actors id
		$this->created_by 	= $userid;
	}

	/**
	 * Sets the context of this polls
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The uid
	 * @param	string	The element
	 */
	public function setContext($uid, $element)
	{
		// Set the context id.
		$this->uid = $uid;

		// Set the context type.
		$this->element = $element;
	}


	/**
	 * Sets if this polls allow multiple options selectin or not.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The uid
	 * @param	string	The element
	 */
	public function setMultiple($multiple = 1)
	{
		// Set the context id.
		$this->multiple = $multiple;
	}


	/**
	 * Sets if this polls has the expiry date.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The uid
	 * @param	string	The element
	 */
	public function setExpiry($expiry)
	{
		if ($expiry) {
			$parts = explode(' ', $expiry);
			if (count($parts) == 1) {
				$expiry = $expiry . ' 23:59:59';
			} else if (count($parts) > 1) {

				// since we know the expirey date that pass in already has the timezone. We need to reverse it.
				$offset = ES::date()->getOffSet();
				$newDate = new JDate($expiry, $offset);
				$expiry = $newDate->toSql();
			}
		}

		// Set the context id.
		$this->expiry_date = $expiry;
	}


	/**
	 * add polls options
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The uid
	 * @param	string	The element
	 */
	public function addOption($option)
	{
		if (is_array($option)) {
			$option = (object) $option;
		}

		$this->items[] = $option;
	}

}
