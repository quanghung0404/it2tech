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

require_once(__DIR__ . '/table.php');

class EasyBlogTableOauth extends EasyBlogTable
{
	public $id = null;
	public $user_id	= null;
	public $type = null;
	public $auto = null;
	public $request_token = null;
	public $access_token = null;
	public $message	= null;
	public $created	= null;
	public $private	= null;
	public $params = null;
	public $system = null;
	public $expires = null;

	public function __construct( $db )
	{
		parent::__construct('#__easyblog_oauth' , 'id' , $db );
	}

	/**
	 * Deprecated. Use @load instead
	 *
	 * @deprecated	4.0
	 */
	public function loadSystemByType( $type )
	{
	    $db		= $this->getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'system' ) . '=' . $db->Quote( 1 );

	    $db->setQuery( $query );

	    $result = $db->loadResult();

	    if(empty($result))
	    {
	        $this->id	= 0;
			$this->type	= $type;
	        return $this;
	    }

		return parent::load($result);
	}

	public function loadByUser($id, $type)
	{
		$db = EB::db();

		$query	= 'SELECT * FROM ' . $db->quoteName($this->_tbl) . ' '
				. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $id ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'LIMIT 1';
	    $db->setQuery( $query );

	    $result = $db->loadObject();

	    if( !$result )
	    {
	    	return false;
	    }

	    return parent::bind( $result );
	}

	/**
	 * Pushes to the oauth site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function push(EasyBlogPost &$post)
	{
		// When there is no access token set on this oauth record, we shouldn't do anything
		if (!$this->access_token) {
			$this->setError(JText::sprintf('No access token available for autoposting on %1$s', $this->type));
			return false;
		}

		$config = EB::config();

		// Determines if this user is really allowed to auto post
		$author = $post->getAuthor();

		// Check the author's acl
		$acl = EB::acl($author->id);
		$rule = 'update_' . $this->type;

		if (!$acl->get($rule) && !EB::isSiteAdmin($post->created_by)) {
			$this->setError(JText::sprintf('No access to autopost on %1$s', $this->type));
			return false;
		}

		// we only check if the autopost on blog edit is disabled.
		if (! $config->get('integrations_' . $this->type . '_centralized_send_updates')) {
		// Check if the blog post was shared before.
			if ($this->isShared($post->id)) {
				$this->setError(JText::sprintf('Post %1$s has been shared to %2$s before.', $post->id, $this->type));
				return false;
			}
		}

		// Ensure that the oauth data has been set correctly
		$config = EB::config();
		$key = $config->get('integrations_' . $this->type . '_api_key');
		$secret = $config->get('integrations_' . $this->type . '_secret_key');

		// If either of this is empty, skip this
		if (!$key || !$secret) {
			return false;
		}

		// Set the callback URL
		$callback = JURI::base() . 'index.php?option=com_easyblog&task=oauth.grant&type=' . $this->type;

		// Now we do the real thing. Get the library and push
		$lib = EB::oauth()->getClient($this->type, $key, $secret, $callback);
		$lib->setAccess($this->access_token);

		// Try to share the post now
		$state = $lib->share($post, $this);

		if ($state === true) {
			$history = EB::table('OAuthPost');
			$history->load(array('oauth_id' => $this->id, 'post_id' => $post->id));

			$history->post_id = $post->id;
			$history->oauth_id = $this->id;
			$history->created = EB::date()->toSql();
			$history->modified = EB::date()->toSql();
			$history->sent = EB::date()->toSql();
			$history->store();

			return true;
		}

		return false;
	}

	/**
	 * Override parent's store method
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($updateNulls = false)
	{
		$db = EB::db();

		$query = array();

		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn($this->_tbl);
		$query[] = 'WHERE ' . $db->qn('user_id') . '=' . $db->q($this->user_id);
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->q($this->type);
		$query[] = 'AND ' . $db->qn('system') . '=' . $db->q($this->system);

		$db->setQuery($query);

		$exists = $db->loadResult();

		if ($exists) {
			return $db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
		}

		return $db->insertObject($this->_tbl, $this, $this->_tbl_key);
	}

	/**
	 * Retrieves the expiry date
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExpireDate()
	{
		$date 	= EB::date($this->expires);

		return $date;
	}

	/**
	 * Retrieves a key value from the access token object.
	 */
	public function getAccessTokenValue( $key )
	{
		$param 	= EB::registry($this->access_token);

		return $param->get($key);
	}

	public function getMessage()
	{
		$config = EB::config();
		$message	= !empty( $this->message ) ? $this->message : $config->get('integrations_' . $this->type . '_default_messsage' );
		return $message;
	}

	/**
	 * Determines whether a blog post has been shared before.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isShared($postId)
	{
		$model = EB::model('Oauth');

		return $model->isShared($postId, $this->id);
	}

	/*
	 * Get's the last shared date
	 *
	 * @param	int		$blogId	The respective blog id.
	 * @return	boolean	True if entry is shared previously.
	 */
	public function getSharedDate( $blogId )
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT ' . $db->nameQuote( 'sent' ) . ' FROM ' . $db->nameQuote( '#__easyblog_oauth_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'oauth_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $blogId );

	    $db->setQuery( $query );
		$result = $db->loadResult();

		return EasyBlogDateHelper::dateWithOffSet( $result )->toMySQL();
	}
}
