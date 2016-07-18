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

class SocialException
{
	public $message = '';
	public $type = SOCIAL_MSG_ERROR;


	public function __construct($message='', $type=SOCIAL_MSG_ERROR)
	{
		$this->parse($message, $type);
	}

	private function parse($message='', $type=SOCIAL_MSG_ERROR)
	{
		switch ($type) {

			case SOCIAL_EXCEPTION_MESSAGE:
				// If an array was passed in, we treat it as
				// an exception that was serialized to an array.
				$this->set($message['message'], $message['type']);
				break;

			case SOCIAL_MSG_ERROR:
			case SOCIAL_MSG_INFO:
			case SOCIAL_MSG_SUCCESS:
				$this->set($message, $type);
				break;

			default:
				$this->filter($message, $type);
				break;
		}
	}

	private function set($message='', $type=SOCIAL_MSG_ERROR)
	{
		// Translate messages
		$this->message = JText::_($message);

		// Ensure exception type is always lowercased
		$this->type = strtolower($type);
	}

	private function filter($message, $type='exception') {

		$adapterFile = dirname(__FILE__) . '/adapters/' . $type . '.php';

		// If the file does not exist, return missing adapter file error.
		// e.g. Could not locate adapter file for exception type - file.'
		if(!JFile::exists($adapterFile)) {
			$this->set(JText::sprintf('COM_EASYSOCIAL_EXCEPTION_ADAPTER_INVALID_FILE_ERROR', $type));
			return;
		}

		// Load the adapter
		require_once($adapterFile);

		// Construct adapter classname
		$adapterClass = 'SocialException' . ucfirst($type);

		// If the file does not exist, return missing adapter class error
		// e.g. Could not locate adapter class for exception class - SocialExceptionFile.'
		if (!class_exists($adapterClass)) {
			$this->set(JText::sprintf('COM_EASYSOCIAL_EXCEPTION_ADAPTER_INVALID_CLASS_ERROR', $adapterClass));
			return;
		}

		// Ask adapter to parse message
		$exception 	= call_user_func_array( array( $adapterClass , 'filter' ) , array( $message ) );

		// If adapter did not return any exception
		if (empty($exception)) {

			// Set to the default adapter error of unkown adapter error,
			// e.g. Unknown file error.
			$this->set(JText::sprintf('COM_EASYSOCIAL_EXCEPTION_ADAPTER_UNKNOWN_ERROR', $type));

		// Parse exception
		} else {

			$this->parse($exception, SOCIAL_EXCEPTION_MESSAGE);
		}
	}

	public static function factory($message='', $type=SOCIAL_MSG_ERROR)
	{
		return new self($message, $type);
	}

	public function toArray()
	{
		return array(
			'type'    => $this->type,
			'message' => $this->message
		);
	}

}
