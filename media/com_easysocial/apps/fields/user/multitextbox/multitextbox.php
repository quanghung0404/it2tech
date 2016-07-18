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
defined('_JEXEC') or die( 'Unauthorized Access');

// Include the fields library
FD::import('admin:/includes/fields/fields');

class SocialFieldsUserMultitextbox extends SocialFieldItem
{
	public function onRegister(&$post, &$registration)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		$error	= $registration->getErrors( $this->inputName );

		$this->set( 'error', $error );

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
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $this->value;

		$error = $this->getError($errors);

		$this->set('error', $error);

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
		return $this->display();
	}

	public function onDisplay($user)
	{
		if (empty($this->value))
		{
			return;
		}

		if( !$this->allowedPrivacy( $user ) )
		{
			return;
		}

		$value = FD::json()->decode($this->value);

		if (!is_array($value) || empty($value))
		{
			return;
		}

		$this->set('value', $value);
		$this->set('field', $this->field);

		return $this->display();
	}

	private function onOutput($value)
	{
		$count = 0;

		if (!empty($value))
		{
			$value = FD::json()->decode($value);

			$count = count($value);
		}
		else
		{
			$value = array();
		}

		$this->set('value', $value);

		$this->set('count', $count);

		$limit = $this->params->get('max', 0);

		$this->set('limit', $limit);

		return $this->display();
	}

	private function onValidate(&$data)
	{
		if (!$this->isRequired())
		{
			return true;
		}

		if (empty($data))
		{
			$this->setError(JText::_('PLG_FIELDS_MULTITEXTBOX_VALIDATION_REQUIRED_FIELD'));
			return false;
		}

		$json = FD::json();

		$value = $json->decode($data);

		if (!is_array($value) || empty($value))
		{
			$this->setError(JText::_('PLG_FIELDS_MULTITEXTBOX_VALIDATION_REQUIRED_FIELD'));
			return false;
		}

		foreach ($value as $v)
		{
			$v = trim($v);

			if (!empty($v))
			{
				return true;
			}
		}

		$this->setError(JText::_('PLG_FIELDS_MULTITEXTBOX_VALIDATION_REQUIRED_FIELD'));
		return false;
	}

	private function onBeforeSave(&$post)
	{
		if (empty($post[$this->inputName]))
		{
			unset($post[$this->inputName]);
			return true;
		}

		$json = FD::json();

		$value = $json->decode($post[$this->inputName]);

		if (!is_array($value) || empty($value))
		{
			unset($post[$this->inputName]);
			return true;
		}

		$result = array();

		foreach ($value as $v)
		{
			$v = trim($v);
			if (!empty($v))
			{
				$result[] = $v;
			}
		}

		if (empty($result))
		{
			unset($post[$this->inputName]);
			return true;
		}

		$post[$this->inputName] = $json->encode($result);

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

	public function getValue()
	{
		$container = $this->getValueContainer();

		$json = FD::json();

		if ($json->isJsonString($container->raw)) {
			$container->data = $json->decode($container->raw);

			$container->value = implode(', ', $container->data);
		}

		return $container;
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
