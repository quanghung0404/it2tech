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

class SocialFieldsUserRelationship extends SocialFieldItem
{
	/*
	 * Provide boolean status for the result
	 */
	public function onValidate($post)
	{
	    return true;
	}

	/*
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @param
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		if (!$user->id) {
			return false;
		}

		$this->set('user', $user);

		$model = $this->model('relations');

		$relation = $model->getActorRelationship($user->id);

		$targetted = $model->getTargetRelationship($user->id, array('state' => 0));

		$this->set('relation', $relation);

		$this->set('pending', $targetted);

		$types = $this->getRelationshipTypes();

		$firstType = '';

		foreach ($types as $type) {
			$firstType = $type;
			break;
		}

		$this->set('firstType', $firstType);

		$this->set('types', $types);

		return $this->display();
	}

	/*
	 * Save trigger which is called after really saving the object.
	 */
	public function onEditAfterSave(&$post, &$user)
	{
		$json = FD::json();

		$params = $json->decode($post[$this->inputName]);

		if (!isset($params->type)) {
			return false;
		}

		// We set it as array here to follow the standard of textboxlist passing in target as array even for single target
		if (!isset($params->target)) {
			$params->target = array(0);
		}

		$model = $this->model('relations');

		$relation = $model->getActorRelationship($user->id);

		// If no relationship data is found, then we init a new one
		if ($relation === false) {
			$relation = $this->table('relations');
		}

		$origType = $relation->type;
		$origTarget = $relation->getTargetUser()->id;

		$currentType = $params->type;

		// Do not use $relation->isConnect because the type might change
		$typeInfo = $relation->getType($currentType);

		$currentTarget = $typeInfo && $typeInfo->connect ? $params->target[0] : 0;

		// Only process if there is a change in type or target
		if ($origType !== $currentType || $origTarget !== $currentTarget) {

			// If original target is not empty, we need to find the target's relationship and change it to empty target since this person is no longer tied to that target
			if (!empty($origTarget)) {
				$targetRel = $model->getActorRelationship($origTarget, array('target' => $user->id));

				if ($targetRel) {
					$targetRel->target = 0;
					$targetRel->state = 1;
					$targetRel->store();
				}
			}

			// If this relationship has an id, means it is from an existing record.
			// We need to delete and recreate it in order to have a new id.
			// When the target approves, the genereted stream needs to use the new id instead of the old id.

			if (!empty($relation->id)) {
				$relation->remove();
				$relation = $this->table('relations');
			}

			$relation->actor = $user->id;
			$relation->type = $currentType;
			$relation->target = $currentTarget;

			$state = $relation->request();

			if (!$state) {
				return false;
			}
		}

		return true;
	}

	/*
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @param
	 */
	public function onDisplay($user)
	{
		if (!$this->allowedPrivacy($user)) {
			return;
		}

		$model = $this->model('relations');
		$relation = $model->getActorRelationship($user->id);

		if (!$relation) {
			return;
		}

		// linkage to advanced search page.
		$field = $this->field;
		if ($field->type == SOCIAL_FIELDS_GROUP_USER && $field->searchable) {
			$params = array( 'layout' => 'advanced' );
			$params['criterias[]'] = $field->unique_key . '|' . $field->element;
			$params['operators[]'] = 'equal';
			$params['conditions[]'] = $relation->getType()->value;

			$advsearchLink = FRoute::search($params);
			$this->set( 'advancedsearchlink'	, $advsearchLink );
		}

		$this->set('relation', $relation);

		return $this->display();
	}

	public function onSample()
	{
		$types = $this->getRelationshipTypes();

		$this->set('types', $types);

		return $this->display();
	}

	private function getRelationshipTypes()
	{
		// get all the relationship types and key it with the name
		$allowedTypes = $this->params->get('relationshiptype', array());
		$types = $this->field->getApp()->getManifest('config')->relationshiptype->option;

		$result = array();

		foreach ($types as $type) {
			if (empty($allowedTypes) || (!empty($allowedTypes) && in_array($type->value, $allowedTypes))) {
				$type->label = JText::_($type->label);
				$type->connectword = JText::_('PLG_FIELDS_RELATIONSHIP_CONNECT_WORD_' . strtoupper($type->value));

				$result[$type->value] = $type;
			}
		}

		return $result;
	}

	public function onOAuthGetUserPermission(&$permissions)
	{
		$permissions[] = 'user_relationships';
	}

	public function onOAuthGetMetaFields(&$fields)
	{
		$fields[] = 'relationship_status';
	}
}
