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

class EasyBlogNotification extends EasyBlog
{
	/**
	 * Retrieves the emails based on the settings on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAdminNotificationEmails(&$emails = array())
	{
		// If the site owner configures to send emails to custom email addresses defined at the back end.
		if ($this->config->get('custom_email_as_admin')) {
			$this->getCustomEmails($emails);
			return;
		}

		$this->getAdminEmails($emails);
		return;
	}

	/**
	 * Retrieves a list of moderator emails
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAdminEmails(&$emails = array())
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('name') . ',' . $db->qn('email');
		$query[] = 'FROM ' . $db->qn('#__users');

		$superAdmins = EB::getSAUsersIds();

		if (!$superAdmins) {
			return false;
		}

		$query[] = 'WHERE ' . $db->qn('id') . ' IN(' . implode(',', $superAdmins) . ')';
		$query[] = 'AND ' . $db->qn('sendEmail') . '=' . $db->Quote(1);

		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		foreach ($result as $row) {

			$obj = new stdClass();
			$obj->unsubscribe = false;
			$obj->email = $row->email;

			$emails[$row->email] = $obj;
		}
	}

	/**
	 * Retrieves a list of subscribers for a particular blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlogSubscriberEmails(&$emails = array(), $blogId)
	{
		$post = EB::post($blogId);

		// Get subscribers
		$subscribers = $post->getSubscribers();

		if (!$subscribers) {
			return $emails;
		}

		foreach ($subscribers as $row) {
			$obj = new stdClass();

			if (!isset($row->type)) {
				$row->type	= 'subscription';
			}

			$obj->unsubscribe = EB::getUnsubscribeLink( $row , true );
			$obj->email = $row->email;

			$emails[$row->email] = $obj;
		}
	}

	public function getTeamAdminEmails( &$emails = array() , $teamId )
	{
		$db 	= EasyBlogHelper::db();

		$query  = 'select `email` from `#__users` as a inner join `#__easyblog_team_users` as b on a.`id` = b.`user_id`';
		$query  .= ' where b.`team_id` = ' . $db->Quote( $teamId );
		$query  .= ' and b.isadmin = ' . $db->Quote('1');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $row )
		{
			$obj 				= new StdClass();
			$obj->unsubscribe	= false;
			$obj->email 		= $row->email;

			$emails[ $row->email ]	= $obj;
		}
	}

	public function getCustomEmails( &$emails = array() )
	{
		$config 	= EasyBlogHelper::getConfig();

		$customEmails	= $config->get( 'notification_email' );
		$customEmails 	= trim( $customEmails );

		if( !empty($customEmails ) )
		{
			$customEmails 	= explode( ',' , $customEmails );

			foreach( $customEmails as $email )
			{
				$obj 				= new StdClass();
				$obj->unsubscribe	= false;
				$obj->email 		= $email;

				$emails[ $email ]	= $obj;
			}
		}

	}

	public function getAllEmails( &$emails = array() )
	{
		$config 	= EasyBlogHelper::getConfig();

		// Get every email address of the users on the site.
		if( !class_exists( 'EasyBlogModelSubscription' ) )
		{
			JLoader::import( 'subscription' , EBLOG_ROOT . DIRECTORY_SEPARATOR . 'models' );
		}

		$model 			= EB::model( 'Subscription' );
		$subscribers	= $model->getMembersAndSubscribers();

		foreach( $subscribers as $subscriber )
		{
			if( !array_key_exists( $subscriber->email , $emails ) )
			{
				$obj 				= new stdClass();
				$obj->unsubscribe	= false;
				$obj->email 		= $subscriber->email;

				if( $subscriber->type != 'member' )
				{
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
				}
				$emails[ $subscriber->email ]	= $obj;
			}
		}
	}

	public function getSubscriberEmails( &$emails = array() , $blog )
	{
		$config 	= EasyBlogHelper::getConfig();

		// @rule: Send to subscribers that subscribe to the bloggers
		if( $config->get( 'notification_blogsubscriber' ) )
		{
			self::getBloggerSubscriberEmails( $emails , $blog );
		}

		// @rule: Send to subscribers that subscribed to the category
		if( $config->get( 'notification_categorysubscriber' ) )
		{
			self::getCategorySubscriberEmails( $emails , $blog );
		}

		// @rule: Send to subscribers that subscribed to a team
		if( $config->get( 'notification_teamsubscriber' ) )
		{
			self::getTeamSubscriberEmails( $emails , $blog );
		}

		// @rule: Send notification to all site's subscribers
		if($config->get('notification_sitesubscriber') )
		{
			self::getSiteSubscriberEmails( $emails , $blog );
		}
	}

	public function getBloggerSubscriberEmails( &$emails = array() , $blog )
	{
		$model 			= EB::model( 'Blogger' );

		// @TODO: Fix this
		$subscribers	= $model->getBlogggerSubscribers( $blog->created_by );

		if( !empty( $subscribers ) )
		{
			foreach( $subscribers as $subscriber )
			{
				if( !array_key_exists( $subscriber->email , $emails ) )
				{
					$obj 				= new stdClass();
					$obj->email 		= $subscriber->email;
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
					$emails[$subscriber->email]	= $obj;
				}
			}
		}
	}

	public function getCategorySubscriberEmails( &$emails = array() , $blog )
	{
		$model 			= EB::model( 'Category' );
		$subscribers	= $model->getCategorySubscribers( $blog->category_id );

		if( !empty( $subscribers ) )
		{
			foreach( $subscribers as $subscriber )
			{
				if( !array_key_exists( $subscriber->email , $emails ) )
				{
					$obj 				= new stdClass();
					$obj->email 		= $subscriber->email;
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
					$emails[$subscriber->email]	= $obj;
				}
			}
		}
	}


	public function getTeamUserEmails( &$emails = array() , $teamId )
	{
		$db 	= EasyBlogHelper::db();

		$query  = 'select `email` from `#__users` as a inner join `#__easyblog_team_users` as b on a.`id` = b.`user_id`';
		$query  .= ' where b.`team_id` = ' . $db->Quote( $teamId );

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $row )
		{
			if( !array_key_exists( $row->email , $emails ) )
			{
				$obj 				= new StdClass();
				$obj->unsubscribe	= false;
				$obj->email 		= $row->email;

				$emails[ $row->email ]	= $obj;
			}
		}
	}

	public function getTeamSubscriberEmails( &$emails = array() , $blog )
	{
		$model 			= EB::model('TeamBlogs');

		// @rule: See if blog post is tied to any group
		$db 			= EB::db();

		$teamId = ($blog->source_type == EASYBLOG_POST_SOURCE_TEAM) ? $blog->source_id : '';

		$subscribers 	= $model->getTeamSubscribers( $teamId );

		if( !empty( $subscribers ) )
		{
			foreach( $subscribers as $subscriber )
			{
				if( !array_key_exists( $subscriber->email , $emails ) )
				{
					$obj 				= new stdClass();
					$obj->email 		= $subscriber->email;
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
					$emails[$subscriber->email]	= $obj;
				}
			}
		}
	}

	public function getSiteSubscriberEmails( &$emails = array() )
	{
		$model 			= EB::model( 'Subscription' );
		$subscribers	= $model->getSiteSubscribers();

		if( !empty( $subscribers ) )
		{
			foreach( $subscribers as $subscriber )
			{
				if( !array_key_exists( $subscriber->email , $emails ) )
				{
					$obj 				= new stdClass();
					$obj->email 		= $subscriber->email;
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
					$emails[$subscriber->email]	= $obj;
				}
			}
		}
	}

	/**
	 * Send to a list of subscribers on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sendSubscribers($emailTitle, $template, $data, EasyBlogPost $post, $ignoreEmails = array())
	{
		$db = EB::db();
		$config = EB::config();

		$app = JFactory::getApplication();
		$jConfig = EB::getJConfig();

		$defaultEmailFrom = $jConfig->get('mailfrom');
		$defaultFromName = $jConfig->get('fromname');

		$fromEmail = $config->get('notification_from_email', $defaultEmailFrom);
		$fromName = $config->get('notification_from_name', $defaultFromName );

		// Override the from email address if necessary
		if (empty($fromEmail)) {
			$fromEmail = $defaultEmailFrom;
		}

		// Override the from name if necessary
		if (empty($fromName)) {
			$fromName = $defaultFromName;
		}

		// Check if this is to notify team subscribers
		$model = EB::model('TeamBlogs');
		$contribution = $model->getBlogContributed($post->id);

		if ($contribution) {
			$team = EB::table('TeamBlog');
			$team->load($contribution->team_id);

			$contribution->access = $team->access;
		} else {
			$contribution = new stdClass();
			$contribution->access = EBLOG_TEAMBLOG_ACCESS_EVERYONE;
		}

		$jsonData = json_encode($data);
		$insertQuery = array();

		$insertDate = EB::date()->toMySQL();
		$mainQuery = '';
		$queryHeader = 'insert into `#__easyblog_mailq` (`mailfrom`,`fromname`,`recipient`,`subject`,`created`,`status`,`template`,`data`,`param`) ';

		if (!$post->send_notification_emails) {
			return;
		}

		$query = '';

		// When the post is posted into a team, send it to the team
		if ($contribution && $config->get('notification_teamsubscriber') && isset($contribution->team_id)) {
			// teamblog subscribers
			$query .= 'select a.`email`';
			$query .= ' FROM `#__easyblog_subscriptions` as a';
			$query .= ' WHERE a.`uid` = ' . $db->Quote($contribution->team_id);
			$query .= ' AND a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG);

			// teamblog members
			$query .= ' UNION ';
			$query .= 'select a1.`email`';
			$query .= ' from `#__users` as a1 inner join `#__easyblog_team_users` as b1 on a1.`id` = b1.`user_id`';
			$query .= ' where b1.`team_id` = ' . $db->Quote( $contribution->team_id );
		}

		// @task: Only send emails to these group of users provided that, it is not a team posting or private team posting.
		if (!$contribution || $contribution->access != EBLOG_TEAMBLOG_ACCESS_MEMBER) {
			// @rule: Get all email addresses for the whole site.
			if ($config->get( 'notification_allmembers' )) {

				// all superadmins user id
				$saUsersIds	= EasyBlogHelper::getSAUsersIds();

				$collation = '';
				if (! EB::isCollationValid()) {
					$collation = 'COLLATE utf8_general_ci';
				}

				$query .= 'select a.`email` ' . $collation . ' AS `email` from `#__users` as a';
				$query .= ' where a.`block` = 0';
				$query	.= ' and a.`id` NOT IN (' . implode(',', $saUsersIds) . ')';

				// guest subscribers
				$query .= ' UNION ';
				$query .= 'select a1.`email` COLLATE utf8_general_ci AS `email` FROM `#__easyblog_subscriptions` as a1';
				$query .= ' WHERE a1.`user_id` = ' . $db->Quote('0');

			} else {

				if ($config->get('notification_blogsubscriber') || $config->get('notification_categorysubscriber') || $config->get('notification_sitesubscriber')) {

					$command = array();
					if ($config->get('notification_blogsubscriber')) {
						$command[] = '(' . $db->qn('a.uid') . ' = ' . $db->Quote($post->created_by) . ' and ' . $db->qn('a.utype') . ' = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER) . ')';
					}

					if ($config->get('notification_categorysubscriber')) {
						$command[] = '(' . $db->qn('a.uid') . ' IN (select ' . $db->qn('pc.category_id') . ' from ' . $db->qn('#__easyblog_post_category') . ' as pc where ' . $db->qn('pc.post_id') . ' = ' . $db->Quote($post->id) . ') and ' . $db->qn('a.utype') . ' = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY) . ')';
					}

					if ($config->get('notification_sitesubscriber')) {
						$command[] = '(' . $db->qn('a.uid') . ' = ' . $db->Quote('0') . ' and ' . $db->qn('a.utype') . ' = ' . $db->Quote(EBLOG_SUBSCRIPTION_SITE) . ')';
					}

					$query = 'select distinct ' . $db->qn('a.email');
					$query .= '	from ' . $db->qn('#__easyblog_subscriptions') . ' as a';
					$query .= ' where 1 = 1';
					$query .= ' and (';
					$query .= implode(' OR ', $command);
					$query .= ')';
				}

			}

		}

		if ($query) {
			$mainQuery = $queryHeader;
			$mainQuery .= 'SELECT ' . $db->Quote($fromEmail) . ' as `mailfrom`,' . $db->Quote($fromName) . ' as `fromname`, x.`email` as `recipient`,';
			$mainQuery .= $db->Quote($emailTitle) . ' as `subject`,' . $db->Quote($insertDate) . ' as `created`, 0 as `status`, ' . $db->Quote($template) . ' as `template`';
			$mainQuery .= ', ' . $db->Quote($jsonData) . ' as `data`';
			$mainQuery .= ', concat(\'{"email":"\', x.email, \'","unsubscribe":"1"}\') as `param`';
			$mainQuery .= ' FROM (' . $query . ') as x';

			// exclude these emails if there are any
			if ($ignoreEmails) {

				$tmpQuery = '';

				if (count($ignoreEmails) == 1) {
					$tmpQuery = ' where x.email != ' . $db->Quote($ignoreEmails[0]);
				} else {
					$emails = '';
					foreach ($ignoreEmails as $ignore) {
						$emails .= ($emails) ? ',' . $db->Quote($ignore) : $db->Quote($ignore);
					}
					$tmpQuery = ' where x.email NOT IN (' . $emails . ')';
				}

				$mainQuery .= $tmpQuery;
			}

			// insert records into mailq here.
			$db->setQuery($mainQuery);
			$db->query();
		}

		return true;
	}

	/**
	 * prepare unsubscribe links
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string - email address
	 *          strig - format - accept html, array
	 * @return
	 */
	public function getUnsubscribeLinks($email) {

		$db = EB::db();

		$query = 'select *,';
		$query .= '	(case';
		$query .= '		when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_SITE) . ' then ' . $db->Quote('sitesubscription');
		$query .= '		when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER) . ' then ' . $db->Quote('bloggersubscription');
		$query .= '		when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY) . ' then ' . $db->Quote('categorysubscription');
		$query .= '		when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG) . ' then ' . $db->Quote('teamsubscription');
		$query .= '		when `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY) . ' then ' . $db->Quote('subscription');
		$query .= '	end) as `type` from ' . $db->qn('#__easyblog_subscriptions');
        $query .= " WHERE `email` = " . $db->Quote($email);


        $db->setQuery($query);
        $results = $db->loadObjectList();

        $links = array();
        $types = array();
        $created = '';

        if ($results) {
        	foreach($results as $item) {
				$link = EasyBlogHelper::getUnsubscribeLink($item, true);
				$links[$item->type] = $link;

				$types[$item->type] = $item->id . '|' . $item->created;
        	}

        	if (count($links) > 1) {
        		// lets generate a 'all' type unsubscribe link.
				$link = EasyBlogHelper::getUnsubscribeLink($types, true, true, $email);
				$links['all'] = $link;
        	}
        }

		return $links;
	}

	/**
	 * Adds an email address to the queue
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function send($emails , $emailTitle , $template , $data)
	{
		$jConfig = EB::jConfig();

		// Ensure that the title is translated.
		EB::loadLanguages();

		$emailTitle = JText::_($emailTitle);

		// Get the sender's name and email
		$fromEmail = $this->config->get('notification_from_email', $jConfig->get('mailfrom'));
		$fromName = $this->config->get('notification_from_name', $jConfig->get('fromname'));

		// Ensure that all the emails are unique and we do not send any duplicates.
		foreach ($emails as $email => $obj) {

			// Ensure that the unsubscribe link is passed into the template
			if ($obj->unsubscribe && !isset($data['unsubscribeLink'])) {
				$data['unsubscribeLink'] = $obj->unsubscribe;
			}

			$mailq = EB::table('MailQueue');
			$mailq->mailfrom = $fromEmail;
			$mailq->fromname = $fromName;
			$mailq->recipient = $obj->email;
			$mailq->subject = $emailTitle;

			// We only get the contents of the body later when we are dispatching the emails
			$mailq->body = '';
			$mailq->created = EB::date()->toSql();
			$mailq->template = $template;
			$mailq->data = json_encode($data);

			$mailq->store();
		}
	}

	/**
	 * Retrieves the template contents.
	 *
	 **/
	public function getTemplateContents( $template, $data )
	{
		// Load front end's language file.
		EB::loadLanguages();

		// We only want to show unsubscribe link when the user is really subscribed to the blogs
		if( !isset( $data[ 'unsubscribeLink' ] ) )
		{
			$data[ 'unsubscribeLink' ]		= '';
		}

		$config = EasyBlogHelper::getConfig();

		// @rule: Detect what type of emails that we should process.
		$type = $config->get( 'main_mailqueuehtmlformat' ) ? 'html' : 'text';
		$theme = EB::template();

		// Fetch the child theme first.
		foreach ($data as $key => $val) {
			$theme->set($key, $val);
		}

		$namespace = 'site/emails/html/' . $template;
		$contents = $theme->output($namespace);

		// @rule: Now we need to process the main template holder.
		$title	= $config->get('notifications_title');

		$theme->set('unsubscribe', $data['unsubscribeLink']);
		$theme->set('emailTitle', $title);
		$theme->set('contents', $contents);

		$output = $theme->output('site/emails/html/template');
		return $output;
	}
}
