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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import('admin:/tables/table');

class SocialTablePolls extends SocialTable
{
	public $id = null;
	public $element = null;
	public $uid = null;
	public $title = null;
	public $multiple = null;
	public $cluster_id = null;
	public $locked = null;
	public $created = null;
	public $created_by = null;
	public $expiry_date = null;

	public function __construct($db)
	{
		parent::__construct('#__social_polls', 'id', $db);
	}

	public function isVoted($userId)
	{
		$model = ES::model('Polls');
		return $model->isVoted($this->id, $userId);
	}

	public function getItems()
	{
		$model = ES::model('Polls');
		$items = $model->getItems($this->id);

		return $items;
	}

	public function delete($pk = null)
	{
	    $state  = parent::delete($pk);

	    if ($state) {
	        // Delete stream item
	        $model = ES::model('Polls');
	        $model->deletePollStreams($this->id);
	    }

	    return $state;
	}

	public function updateStreamPrivacy($streamId)
	{

		$privacy = FD::table('Privacy');
		$state = $privacy->load(array('type'=>'polls', 'rule'=>'view'));

		if ($state) {
			$model = ES::model('Polls');
			$model->updateStreamPrivacy($streamId, $privacy->id);
		}

		return true;
	}


}
