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

// Include the fields library
FD::import('admin:/includes/fields/fields');

class SocialFieldsUserMultidropdown extends SocialFieldItem
{
	public function getValue()
	{
		$container = $this->getValueContainer();

		$container->data = FD::makeObject($container->raw);

		$container->value = array();

		foreach( $container->data as $v )
		{
			$option = FD::table( 'fieldoptions' );
			$option->load( array( 'parent_id' => $this->field->id, 'key' => 'items', 'value' => $v ) );

			$container->value[$option->value] = $option->title;
		}

		return $container;
	}

	public function getOptions()
	{
		$options = $this->field->getOptions( 'items' );

		if( empty( $options ) )
		{
			return array();
		}

		$result = array();

		foreach( $options as $o )
		{
			$result[$o->value] = $o->title;
		}

		return $result;
	}

	public function onRegister(&$post, &$registration)
	{
		$error = $registration->getErrors($this->inputName);

		$this->set('error', $error);

		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->onOutput($value);
	}

	public function onRegisterValidate(&$post)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->onValidate($value);
	}

	public function onRegisterBeforeSave(&$post)
	{
		return $this->onBeforeSave($post);
	}

	public function onEdit(&$post, &$user, $errors)
	{
		$error = $this->getError($errors);

		$this->set('error', $error);

		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $this->value;

		return $this->onOutput($value);
	}

	public function onEditValidate(&$post)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->onValidate($value);
	}

	public function onEditBeforeSave(&$post)
	{
		return $this->onBeforeSave($post);
	}

	public function onSample()
	{
		$choices = array((object) array('value' => '', 'title' => JText::_($this->params->get('placeholder'))));

		$this->set('choices', $choices);

		return $this->display();
	}

	public function onDisplay($user)
	{
		if (empty($this->value)) {
			return;
		}

		if( !$this->allowedPrivacy($user)) {
			return;
		}

		$json = FD::json();

		$result = $json->decode($this->value);

		if (!is_array($result) || empty($result)) {
			return;
		}

		$field = $this->field;

		$advGroups = array(SOCIAL_FIELDS_GROUP_GROUP, SOCIAL_FIELDS_GROUP_USER);

		$addAdvLink = in_array($field->type, $advGroups) && $field->searchable;

		$values = array();

		foreach ($result as $r)
		{
			$r = trim($r);

			if (empty($r)) {
				continue;
			}

			$option = Foundry::table('fieldoptions');
			$option->load( array( 'parent_id' => $this->field->id, 'key' => 'items', 'value' => $r ) );

			if ($addAdvLink) {
				$params = array( 'layout' => 'advanced' );

				if ($field->type != SOCIAL_FIELDS_GROUP_USER) {
					$params['type'] = $field->type;
					$params['uid'] = $field->uid;
				}

				$params['criterias[]'] = $field->unique_key . '|' . $field->element;
				$params['operators[]'] = 'contain';
				$params['conditions[]'] = $r;

				$advsearchLink = FRoute::search($params);
				$option->advancedsearchlink = $advsearchLink;
			}

			$values[] = $option;
		}

		if (empty($values)) {
			return;
		}

		$this->set('values', $values);

		return $this->display();
	}

	private function onOutput($value)
	{
		$json = FD::json();

		$value = $json->decode($value);

		if (!is_array($value))
		{
			$value = array();
		}

		$choices = $this->params->get('items');

		if (!is_array($choices))
		{
			$choices = array();
		}

		array_unshift($choices, (object) array('value' => '', 'title' => JText::_($this->params->get('placeholder'))));

		$limit = $this->params->get('max');

		$count = count($value);

		$this->set(
			array(
				'choices' => $choices,
				'limit' => $limit,
				'count' => $count,
				'value' => $value
			)
		);

		return $this->display();
	}

	private function onValidate($data)
	{
		if (!$this->isRequired())
		{
			return true;
		}

		if (empty($data))
		{
			$this->setError(JText::_('PLG_FIELDS_MULTIDROPDOWN_VALIDATION_REQUIRED_FIELD'));
			return false;
		}

		$json = FD::json();

		$value = $json->decode($data);

		if (!is_array($value) || empty($value))
		{
			$this->setError(JText::_('PLG_FIELDS_MULTIDROPDOWN_VALIDATION_REQUIRED_FIELD'));
			return false;
		}

		foreach ($value as $v)
		{
			if (!empty($v))
			{
				return true;
			}
		}

		$this->setError(JText::_('PLG_FIELDS_MULTIDROPDOWN_VALIDATION_REQUIRED_FIELD'));
		return false;
	}

	private function onBeforeSave($post)
	{
		if (empty($post[$this->inputName])) {
			unset($post[$this->inputName]);
			return true;
		}

		$json = FD::json();

		$value = $json->decode($post[$this->inputName]);

		if (!is_array($value) || empty($value)) {
			unset($post[$this->inputName]);
			return true;
		}

		$result = array();

		foreach ($value as $v) {
			$v = trim($v);

			if (!empty($v)) {
				$result[] = $v;
			}
		}

		if (!empty($result)) {
			$post[$this->inputName] = $result;
		} else {
			unset($post[$this->inputName]);
		}

		return true;
	}

	/**
	 * Checks if this field is complete.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  SocialUser    $user The user being checked.
	 */
	public function onFieldCheck($user)
	{
		return $this->onValidate($this->value);
	}

	/**
	 * Trigger to get this field's value for various purposes.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  SocialUser    $user The user being checked.
	 * @return Mixed               The value data.
	 */
	public function onGetValue($user)
	{
		return $this->getValue();
	}

	/**
	 * Checks if this field is filled in.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3
	 * @access public
	 * @param  array		$data	The post data.
	 * @param  SocialUser	$user	The user being checked.
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		if (empty($this->value)) {
			return false;
		}

		$value = FD::makeObject($this->value);

		if (empty($value)) {
			return false;
		}

		return true;
	}
}

class SocialFieldsUserMultidropdownValue extends SocialFieldValue
{
	public function toString()
	{
		$values = array_values($this->value);

		return implode(', ', $values);
	}
}
