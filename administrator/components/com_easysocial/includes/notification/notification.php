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
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/dependencies.php');

class SocialNotification extends JObject
{
	/**
	 * Holds a copy of SocialNotification object.
	 * @var SocialNotification
	 */
	static $instance 	= null;

	/**
	 * The notification class is always a singleton object.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {

			// Just to be sure that the language files on the front end is loaded
			FD::language()->loadSite();

			self::$instance	= new self();
		}

		return self::$instance;
	}

	/**
	 * Creates a new notification item.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $notification	= FD::getInstance( 'Notification' );
	 *
	 * // Creates a new notification item.
	 * $notification->create( $options );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of key / value options that is to be binded to the ORM.
	 * @return	bool
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function create(SocialNotificationTemplate $template)
	{
		// Load the Notification table
		$table = ES::table('Notification');

		// Notification aggregation will only happen if there is the same `uid`,`type`
		if ($template->aggregate) {

			// Load any existing records to see if it exists.
			$type = $template->type;
			$uid = $template->uid;
			$targetId = $template->target_id;
			$targetType = $template->target_type;
			$contextType = $template->context_type;

			$exists = $table->load( array( 'uid' => $uid , 'type' => $type , 'target_id' => $targetId , 'target_type' => $targetType, 'context_type' => $contextType ) );

			// If it doesn't exist, go through the normal routine of binding the item.
			if (!$exists) {
				$table->bind($template);
			} else {

				if (!empty($template->title)) {
					$table->title = $template->title;
				}

				if (!empty($template->content)) {
					$table->content = $template->content;
				}

				// Reset to unread state since this is new.
				$table->state = SOCIAL_NOTIFICATION_STATE_UNREAD;
			}

			// Update this item to the latest since we want this to appear in the top of the list.
			$table->created	= FD::date()->toMySQL();
		} else {
			$table->bind($template);
		}

		$state = $table->store();

		if (!$state) {
			$this->setError($table->getError());
			return false;
		}

		return true;
	}

	/**
	 * Generates a new notification object template.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getTemplate()
	{
		$template 	= new SocialNotificationTemplate();

		return $template;
	}

	/**
	 * Marks an item as read
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique notification id.
	 * @return	bool
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function read( $id )
	{
		$table	= FD::table( 'Notification' );
		$table->load( $id );

		return $table->markAsRead();
	}

	/**
	 * Deletes a notification item from the site.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique notification id.
	 * @return	bool
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function delete( $id )
	{
		$table	= FD::table( 'Notification' );
		$table->load( $id );

		return $table->delete();
	}

	/**
	 * Hide's notification item but not delete. Still visible when viewing all notification items.
	 *
	 * Example:
	 * <code>
	 * <?php
	 *
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique notification id.
	 * @return	bool
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function hide( $id )
	{
		$table	= FD::table( 'Notification' );
		$table->load( $id );

		return $table->markAsHidden();
	}

	/**
	 * Retrieves the notification output.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The current user's id
	 * @return	string	The html output of the notifications list.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function toHTML( $userId )
	{
		$model = FD::model('Notifications');

		// Get the list of notification items
		$options = array('user_id' => $userId);
		$items = $model->getItems($options);

		if (!$items) {
			return false;
		}

		// Retrieve applications and trigger onNotificationLoad
		$dispatcher = FD::getInstance('Dispatcher');

		$result = array();

		// Trigger apps
		foreach ($items as $item) {

			$type = $item->type;
			$args = array(&$item);

			// @trigger onNotificationLoad from user apps
			$dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onNotificationLoad', $args, $type);

			// @trigger onNotificationLoad from group apps
			$dispatcher->trigger(SOCIAL_APPS_GROUP_GROUP, 'onNotificationLoad', $args, $type);

			// @trigger onNotificationLoad from event apps
			$dispatcher->trigger(SOCIAL_APPS_GROUP_EVENT, 'onNotificationLoad', $args, $type);

			// If an app lets us know that they want to exclude the stream, we should exclude it.
			if (isset($item->exclude) && $item->exclude) {
				continue;
			}

			$result[] = $item;
		}

		$theme = FD::themes();
		$theme->set('items', $result);

		return $theme->output('site/notifications/default');
	}

	/**
	 * Retrieves a list of notification items.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	To aggregate the notification items or not.
	 * @return	Array	An array of @SocialTableNotification
	 */
	public function getItems($options = array())
	{
		$model = ES::model('Notifications');
		$items = $model->getItems($options);

		if (!$items) {
			return false;
		}

		// Retrieve applications and trigger onNotificationLoad
		$dispatcher = ES::dispatcher();

		$result = array();

		// Trigger apps
		foreach ($items as $item) {

			// Add a `since` column to the result so that user's could use the `since` time format.
			$item->since= FD::date($item->created)->toLapsed();

			$args = array(&$item);

			// @trigger onNotificationLoad
			$dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onNotificationLoad', $args);

			// @trigger onNotificationLoad
			$dispatcher->trigger(SOCIAL_APPS_GROUP_GROUP, 'onNotificationLoad', $args );

			// @trigger onNotificationLoad
			$dispatcher->trigger(SOCIAL_APPS_GROUP_EVENT, 'onNotificationLoad', $args);

			// If an app lets us know that they want to exclude the stream, we should exclude it.
			if (isset($item->exclude) && $item->exclude) {
				continue;
			}

			// Let's format the item title.
			$this->formatItem($item);

			$result[] = $item;
		}

		// Group up items.
		if (isset($options['group']) && $options['group'] == SOCIAL_NOTIFICATION_GROUP_ITEMS) {
			$result = $this->group($result);
		}

		return $result;
	}

	/**
	 * Format the notification title
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return
	 */
	public function formatItem( &$item )
	{
		// Escape the original title first.
		$item->title = FD::string()->escape( $item->title );

		// We have our own custom tags
		$item->title = $this->formatKnownTags( $item->title );

		// Replace actor first.
		$item->title = $this->formatActor( $item->title , $item->actor_id , $item->actor_type );

		// Replace target.
		$item->title = $this->formatTarget( $item->title , $item->target_id , $item->target_type );

		// Replace variables from parameters.
		$item->title = $this->formatParams( $item->title , $item->params );

		// Get the icon of this app if needed.
		$item->icon = $this->getIcon($item);

		// Set the actor
		$item->user = FD::user($item->actor_id);
	}

	public function formatKnownTags( $title )
	{
		$title 	= str_ireplace( '{b}' , '<b>' , $title );
		$title 	= str_ireplace( '{/b}' , '</b>' , $title );

		return $title;
	}

	/**
	 * Retrieves the icon for this notification item.
	 *
	 * Example:
	 * <code>
	 * <?php
	 *
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The icon's absolute url.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getIcon( &$item )
	{
		$obj 	= FD::makeObject( $item->params );

		if( isset( $obj->icon ) )
		{
			return $obj->icon;
		}

		// @TODO: Return a default notification icon.

		return false;
	}

	/**
	 * Replaces {ACTOR} with the proper actor data.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function formatParams( $content , $params )
	{
		$obj 	= FD::makeObject( $params );

		if( $obj )
		{
			$keys 	= get_object_vars( $obj );

			if( $keys )
			{
				foreach( $keys as $key => $value )
				{
					$content 	= str_ireplace( '{%' . $key . '%}' , $value , $content );
				}
			}
		}


		return $content;
	}

	/**
	 * Replaces {ACTOR} with the proper actor data.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function formatActor( $content , $actorId , $actorType = SOCIAL_TYPE_USER )
	{
		// @TODO: Actor might not necessarily be a user.
		$actor 	= FD::user( $actorId , true );


		$theme 		= FD::themes();
		$theme->set( 'title', $actor->getName() );
		$theme->set( 'link'	, $actor->getPermalink() );

		$content 	= str_ireplace( '{ACTOR}' , $theme->output( 'site/notifications/actor' ) , $content );

		return $content;
	}

	/**
	 * Replaces {TARGET} with the proper actor data.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function formatTarget( $content , $targetId , $targetType = SOCIAL_TYPE_USER )
	{
		$output 	= '';

		// Get the current logged in user.
		if( $targetType == SOCIAL_TYPE_USER )
		{
			$target 	= FD::user( $targetId );

			$theme 		= FD::themes();
			$theme->set( 'title', $target->getStreamName() );
			$theme->set( 'link'	, $target->getPermalink() );

			$output 	= $theme->output( 'site/notifications/target' );
		}

		$content 	= str_ireplace( '{TARGET}' , $output , $content );

		return $content;
	}

	/**
	 * Group up items by days
	 *
	 * @since	1.0
	 * @access	private
	 * @param	Array	An array of @SocialTableNotification items.
	 * @return	Array	An array of aggregated items.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	private function group( &$items , $dateFormat = '')
	{
		$result	= array();

		foreach ($items as $item) {

			$today = FD::date();
			$date = FD::date($item->created);

			if ($today->format('j/n/Y') == $date->format('j/n/Y')) {
				$index = JText::_('COM_EASYSOCIAL_NOTIFICATION_TODAY');
			} else {
				$index = $date->format(JText::_('COM_EASYSOCIAL_NOTIFICATION_DATE_FORMAT'));
			}

			if (!isset($result[$index])) {
				$result[$index] = array();
			}

			$result[$index][] = $item;
		}

		return $result;
	}

	/**
	 * Retrieves the notification output in JSON format.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The JSON string.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function toJSON()
	{

	}
}
