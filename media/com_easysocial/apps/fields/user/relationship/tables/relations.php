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
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/includes/fields/dependencies');

class SocialFieldTableUserRelations extends JTable
{
	public $id			= null;
	public $actor		= null;
	public $target		= null;
	public $type		= null;
	public $state		= null;
	public $created		= null;

	// Extended data
	private $typeInfo	= null;

	public function __construct(& $db)
	{
		parent::__construct('#__social_relationship_status', 'id', $db);
	}

	/**
	 * Stores the relationship data.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @param  boolean   $updateNulls True to update fields even if they are null.
	 * @return boolean                True on success.
	 */
	public function store($updateNulls = false)
	{
		$db	= FD::db();

		if (is_null($this->created)) {
			$this->created	= FD::date()->toSql();
		}

		return parent::store($updateNulls);
	}

	/**
	 * Checks if the user is the actor in this relationship.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @param  integer    $userid The user id to check.
	 * @return boolean            True if the user is the actor.
	 */
	public function isActor($userid = null)
	{
		if (is_null($userid)) {
			$userid = FD::user()->id;
		}

		return $this->actor == $userid;
	}

	/**
	 * Checks if the user is the target in this relationship.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @param  integer    $userid The user id to check.
	 * @return boolean            True if the user is the target.
	 */
	public function isTarget($userid = null)
	{
		if (is_null($userid)) {
			$userid = FD::user()->id;
		}

		return $this->target == $userid;
	}

	/**
	 * Returns the user object of the actor in this relationship.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return SocialUser    The actor user object
	 */
	public function getActorUser()
	{
		// If no actor, then we use the current user
		if (empty($this->actor)) {
			return FD::user();
		}

		return FD::user($this->actor);
	}

	/**
	 * Returns the user object of the target in this relationship.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return SocialUser    The target user object
	 */
	public function getTargetUser()
	{
		// If no target, then we use guest user
		if (empty($this->target)) {
			return FD::user(0);
		}

		return FD::user($this->target);
	}

	/**
	 * Returns the opposite side based on the user id passed in is actor or target.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @param  integer    $userid The user id to check.
	 * @return SocialUser         The opposite user object.
	 */
	public function getOppositeUser($userid = null)
	{
		$oppositeId = $this->isActor() ? $this->target : $this->actor;

		if (empty($oppositeId))
		{
			return false;
		}

		$oppositeUser = FD::user($oppositeId);

		return $oppositeUser;
	}

	/**
	 * Checks if this relationship is in pending state.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return boolean   True if this relationship is in pending state.
	 */
	public function isPending()
	{
		return $this->state == 0;
	}

	/**
	 * Checks if this relationship has been approved.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return boolean   True if this relationship has been approved.
	 */
	public function isApproved()
	{
		return !$this->isPending();
	}

	/**
	 * Checks if this relationship involves a 2nd party.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return boolean   True if this relationship involves a 2nd party.
	 */
	public function isConnect()
	{
		$type = $this->getType();

		if (!$type) {
			return false;
		}

		return $type->connect;
	}

	/**
	 * Returns the label string of the relationship type if this relationship involves a 2nd party.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return Mixed    String of the label if 2nd party is involved, false otherwise.
	 */
	public function getLabel()
	{
		$type = $this->getType();

		if (!$type) {
			return false;
		}

		return $type->label;
	}

	/**
	 * Returns the connecting word of the relationship type if this relationship involves a 2nd party.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return Mixed    String of the connecting word if 2nd party is involved, false otherwise.
	 */
	public function getConnectWord()
	{
		$type = $this->getType();

		if (!$type)
		{
			return false;
		}

		return $type->connectword;
	}

	/**
	 * Returns the coupling table object of this relationship if it involves 2nd party.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return Mixed    SocialFieldTableUserRelations if this relationship involves 2nd party, false otherwise.
	 */
	public function getOppositeTable()
	{
		if (!$this->isConnect() || empty($this->actor) || empty($this->target)) {
			return false;
		}

		$table = JTable::getInstance('relations', 'SocialFieldTableUser');
		$state = $table->load(array('actor' => $this->target, 'target' => $this->actor));

		if (!$state) {
			return false;
		}

		return $table;
	}

	/**
	 * Proxy function to store the relationship and carry out any necessary action on requesting a relationship.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return boolean    True if success.
	 */
	public function request()
	{
		$this->state = $this->isConnect() && !empty($this->target) ? 0 : 1;

		$state = $this->store();

		if (!$state) {
			return false;
		}

		// Send request notification if the relationship state is 0 (not approved)
		if (!$this->state) {
			$this->notify('request', $this->actor, $this->target);
		}

		return true;

	}

	/**
	 * Proxy function to store the relationship and carry out any necessary action on approving a relationship.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return boolean    True if success.
	 */
	public function approve()
	{
		$this->state = 1;

		$state = $this->store();

		if (!$state) {
			return false;
		}

		// After approval, we need to:
		// 1. clear relationship status for target user as an actor
		// 2. delete all previously created stream
		$db = FD::db();
		$sql = $db->sql();

		$sql->delete($this->getTableName())
			->where('actor', $this->target);

		$db->setQuery($sql);
		$db->query();

		// Then we need to:
		// 1. get all other request by other people
		// 2. send them a reject notification
		// 3. set target to 0 for requests by other people
		$sql->clear();

		// Get all other request
		$sql->select($this->getTableName())
			->column('actor')
			->where('actor', $this->actor, '<>')
			->where('target', $this->target);

		$db->setQuery($sql);
		$requesters = $db->loadObjectList();

		$target = FD::user($this->target);

		// Send a reject notification to all requesters
		foreach ($requesters as $requester) {
			$this->notify('reject', $this->target, $requester);
		}

		// Set target to 0 for request by other people
		$sql->clear();
		$sql->update($this->getTableName())
			->set('target', 0)
			->set('state', 1)
			->where('actor', $this->actor, '<>')
			->where('target', $this->target);

		$db->setQuery($sql);
		$db->query();

		// After clearing all other relationship status, we need to create the same relationship status for target user as an actor
		$table = JTable::getInstance('relations', 'SocialFieldTableUser');
		$table->actor = $this->target;
		$table->target = $this->actor;
		$table->state = 1;
		$table->type = $this->type;

		$state = $table->store();

		if ($state) {
			// Send notification to the original actor
			$this->notify('approve', $this->target, $this->actor);

			// Create a stream item
			$stream = FD::stream();
			$streamTemplate = $stream->getTemplate();

			$streamTemplate->setActor($this->actor, SOCIAL_TYPE_USER);
			$streamTemplate->setTarget($this->target, SOCIAL_TYPE_USER);
			$streamTemplate->setContext($this->id, 'relationship');
			$streamTemplate->setVerb('approve');
			$streamTemplate->setParams((object) array('type' => $this->type));

			$streamTemplate->setAccess('core.view');
			$stream->add($streamTemplate);
		}

		return true;
	}

	/**
	 * Proxy function to store the relationship and carry out any necessary action on rejecting a relationship.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return boolean    True if success.
	 */
	public function reject()
	{
		// Keep a reference of the original target first
		$originalTarget = $this->target;

		$this->target = 0;
		$this->state = 1;

		$state = $this->store();

		// Send reject notification to the original actor
		if ($state) {
			$this->notify('reject', $originalTarget, $this->actor);
		}

		return true;
	}

	/**
	 * Proxy function to remove the relationship and carry out any necessary action on removing a relationship.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return boolean    True if success.
	 */
	public function remove()
	{
		$state = $this->delete();

		if (!$state) {
			return false;
		}

		// Send notification here

		// Delete stream
		// It is possible that there are 2 records that needs to be searched
		// TODO: need verb
		FD::stream()->delete($this->id, 'relationship');

		$opposite = $this->getOppositeTable();
		if ($opposite) {
			FD::stream()->delete($opposite->id, 'relationship');
		}

		return true;
	}

	/**
	 * Returns the information of this relationship type.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.0
	 * @access public
	 * @return Object    Standard class that contains information regarding this relationship type.
	 */
	public function getType($type = null)
	{
		static $types = null;

		if (is_null($types)) {
			$app = FD::table('app');
			$state = $app->loadByElement('relationship', SOCIAL_APPS_GROUP_USER, SOCIAL_APPS_TYPE_FIELDS);

			if (!$state) {
				return false;
			}

			$types = $app->getManifest('config')->relationshiptype->option;

			$total = count($types);
			for ($i = 0; $i < $total; $i++) {
				$types[$i]->label = JText::_($types[$i]->label);
				$types[$i]->connectword = JText::_('PLG_FIELDS_RELATIONSHIP_CONNECT_WORD_' . strtoupper($types[$i]->value));
			}
		}

		// If $type is not empty means we are trying to manually get a type
		if (!empty($type)) {
			foreach ($types as $t) {
				if ($type == $t->value) {
					return $t;
				}
			}

			return false;
		}

		if (!isset($this->typeInfo)) {
			$this->typeInfo = false;

			foreach ($types as $t) {
				if ($this->type == $t->value) {
					$this->typeInfo = $t;
					break;
				}
			}
		}

		return $this->typeInfo;
	}

	/**
	 * Shorthand function to send notification for various actions.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access private
	 * @param  string    $verb   The action string.
	 * @param  integer   $actor  The acting user id.
	 * @param  integer   $target The targeted user id.
	 */
	private function notify($verb, $actor, $target)
	{
		$actor = FD::user($actor);
		$target = FD::user($target);

		$emailOptions = array(
			'title'		=> 'PLG_FIELDS_RELATIONSHIP_EMAIL_TITLE_' . strtoupper($verb),
			'template'	=> 'fields/user/relationship/' . $verb,
			'actor' => $actor->getName(),
			'posterName' => $actor->getName(),
			'posterAvatar' => $actor->getAvatar(),
			'posterLink' => $actor->getPermalink(true, true),
			'recipientName' => $target->getName(),
			'type' => $this->type,
			'link' => FRoute::profile(array('layout' => 'edit', 'external' => true), true)
		);

		$systemOptions = array(
			'uid'		=> $this->id,
			'actor_id'	=> $actor->id,
			'type'		=> 'relationship',
			// Translate this on the hook instead for multilanguage site purpose
			'title'		=> 'APP_USER_RELATIONSHIP_NOTIFICATION_TITLE_' . strtoupper($verb),
			'url'		=> FRoute::profile(array('layout' => 'edit', 'sef' => false)),
			'image'		=> $actor->getAvatar(SOCIAL_AVATAR_LARGE),
			'context_type' => 'apps.user.relationship.' . $verb
		);

		// relationship.request
		// relationship.approve
		// relationship.reject
		FD::notify('relationship.' . $verb, array($target->id), $emailOptions, $systemOptions);
	}
}
