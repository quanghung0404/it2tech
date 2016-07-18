<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogSubscription extends EasyBlog
{
	/**
	 * Retrieves the html codes for the ratings.
	 *
	 * @param	int	$uid	The unique id for the item that is being rated
	 * @param	string	$type	The unique type for the item that is being rated
	 * @param	string	$command	A textual representation to request user to vote for this item.
	 * @param	string	$element	A dom element id.
	 **/
	public function addMailQueue(EasyBlogSubscriptionItem $item)
	{
		// Build the variables for the template
		$data = array();
		$data['fullname'] = $item->ufullname;
		$data['target'] = $item->targetname;

		// To append joomla url inside the link.
		$domain = rtrim(JURI::root(), '/');
		if (stripos($item->targetlink, $domain) === false) {
			$item->targetlink = rtrim(JURI::root(), '/') . '/'. ltrim($item->targetlink, '/');
		}

		$data['targetlink'] = $item->targetlink;
		$data['type'] = $item->utype;

		$recipient = new stdClass();
		$recipient->email = $item->uemail;
		$recipient->unsubscribe = $this->getUnsubscribeLink($item, true);

		$title = JText::_('COM_EASYBLOG_SUBSCRIPTION_EMAIL_CONFIRMATION');

		$notification = EB::notification();
		return $notification->send(array($recipient), $title, 'subscription.confirmation', $data);
	}

	public function getTemplate()
	{
		$template = new EasyBlogSubscriptionItem();
		return $template;
	}

	/**
	 * Generates the unsubscribe link for the email
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUnsubscribeLink($data, $external = false)
	{
		$itemId = EBR::getItemId('latest');

		// Generate the unsubscribe hash
		$hash = base64_encode(json_encode($data->export()));

		$link = EBR::getRoutedURL('index.php?option=com_easyblog&task=subscription.unsubscribe&data=' . $hash . '&Itemid=' . $itemId, false, $external);

		return $link;
	}
}

class EasyBlogSubscriptionItem
{
	public $uid = null;
	public $utype = null;
	public $user_id = null;
	public $uemail = null;
	public $ufullname = null;
	public $ucreated = null;

	// eg. blog post title and link
	// eg. blogger name and link
	// eg. category name and link
	// and etc
	public $targetname 	= null;
	public $targetlink 	= null;

	public function export()
	{
		$data = array(
				'uid' => $this->uid,
				'utype' => $this->utype,
				'user_id' => $this->user_id,
				'created' => $this->ucreated
				);

		return $data;
	}
}
