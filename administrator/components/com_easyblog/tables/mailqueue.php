<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/table.php');

class EasyBlogTableMailQueue extends EasyBlogTable
{
	public $id = null;
	public $mailfrom = null;
	public $fromname = null;
	public $recipient = null;
	public $subject = null;
	public $body = null;
	public $created = null;
	public $param = null;
	public $data = null;
	public $template = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_mailq', 'id', $db);
	}

	/**
	 * Retrieves the body of the email.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBody()
	{
		// if this object is not valid, do not futher process this item.
		if (!$this->id) {
			return false;
		}

		$body = $this->body;

		// If the body is not empty, we should just use this
		if (!empty($this->body) && !$this->template) {
			return $body;
		}

		// this is new way of handling mailq content.
		$notti = EB::notification();

		// data and params need to be in array form.
		$data = json_decode($this->data);
		$param = json_decode($this->param);

		if ($data && !is_array($data)) {
			$data = get_object_vars($data);
		}

		if ($param && !is_array($param)) {
			$param = get_object_vars($param);
		}

		if ($data) {

			$unsubscribe = 1;

			if ($param && isset($param['unsubscribe'])) {
				$unsubscribe = $param['unsubscribe'];
			}

			// we need to generate the unsubscribe links for this email.
			if ($unsubscribe) {
				$data['unsubscribeLink' ] = $notti->getUnsubscribeLinks($this->recipient);
			}

			$body = $notti->getTemplateContents( $this->template , $data );
		}

		return $body;
	}

}
