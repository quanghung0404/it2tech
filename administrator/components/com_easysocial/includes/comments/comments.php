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

class SocialComments
{
	static $instance = null;
	static $blocks = array();

	public $config = null;
	public $commentor = null;
	public $commentCount = null;


	public function __construct()
	{
		$this->config = 1;
		$this->commentor = array();
	}

	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function factory($uid = null, $element = null, $verb = 'null', $group = SOCIAL_APPS_GROUP_USER, $options = array(), $useStreamId = false)
	{
		if ($verb == SOCIAL_APPS_GROUP_USER || $verb == SOCIAL_APPS_GROUP_GROUP) {
			// now we know the caller still using old way of calling the api.
			// we need to manually re-assign the arguments.
			$options = $group;
			$group = $verb;
			$verb = 'null';
		}

		return new self($uid, $element, $verb, $group, $options, $useStreamId);
	}

	public function load( $uid, $element, $verb = 'null', $group = SOCIAL_APPS_GROUP_USER, $options = array(), $useStreamId = false )
	{
		if ($verb == SOCIAL_APPS_GROUP_USER || $verb == SOCIAL_APPS_GROUP_GROUP) {
			// now we know the caller still using old way of calling the api.
			// we need to manually re-assign the arguments.
			$options = $group;
			$group = $verb;
			$verb = 'null';
		}

		if (empty(self::$blocks[$group][$element][$verb][$uid])) {
			$class = new SocialCommentBlock($uid, $element, $verb, $group, $options, $useStreamId);

			self::$blocks[$group][$element][$verb][$uid] = $class;
		}

		self::$blocks[$group][$element][$verb][$uid]->loadOptions($options);

		return self::$blocks[$group][$element][$verb][$uid];
	}
}

class SocialCommentBlock
{
	public $uid 	= '';
	public $element = '';
	public $group 	= '';
	public $verb 	= '';
	public $stream_id = '';
	public $options = array();

	public function __construct( $uid, $element, $verb = 'null', $group = SOCIAL_APPS_GROUP_USER, $options = array(), $useStreamId = false )
	{
		$this->uid = $uid;
		$this->element = $element;
		$this->group = $group;
		$this->verb = $verb;
		$this->stream_id = ( $useStreamId ) ? $useStreamId : '';

		$this->config = ES::config();
		$this->my = ES::user();
		$this->input = JFactory::getApplication()->input;

		$this->loadOptions($options);
	}

	public function loadOptions( $options = array() )
	{
		if( !empty( $options['url'] ) )
		{
			$this->options['url'] = $options['url'];
		}
	}

	public function setOption( $key, $value )
	{
		$this->options[$key] = $value;
	}

	private function getElement()
	{
		$compositeKey = $this->element . '.' . $this->group . '.' . $this->verb;
		return $compositeKey;
	}

	/**
	 * Retrieves the comment count given the element and unique id
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @return	int		The total count of the comment block
	 */
	public function getCount()
	{
		$model 		= FD::model( 'Comments' );
		$options	= array( 'element' => $this->getElement() , 'uid' => $this->uid );

		if ($this->stream_id) {
			$options['stream_id'] = $this->stream_id;
		}

		$count 		= $model->getCommentCount($options);

		return $count;
	}

	/**
	 * Function to return HTML of 1 comments block
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array	$options	Various options to manipulate the comments
	 *
	 * @return	string	Html block of the comments
	 */
	public function getHtml($options = array())
	{
		// Ensure that language file is loaded
		FD::language()->loadSite();

		// Construct mandatory options
		$options['uid'] = $this->uid;
		$options['element'] = $this->getElement();
		$options['hideEmpty'] = isset( $options['hideEmpty'] ) ? $options['hideEmpty'] : false;
		$options['hideForm'] = isset( $options['hideForm'] ) ? $options['hideForm'] : false;
		$options['deleteable'] = isset($options['deleteable']) ? $options['deleteable'] : false;

		if ($this->stream_id) {
			$options['stream_id'] = $this->stream_id;
		}

		if ($this->my->isSiteAdmin()) {
			$options['deleteable']	= true;
		}

		// Check view mode (with childs or not)
		if (empty($options['fullview'])) {
			$options['parentid'] = 0;
		}

		// Get the model
		$model = ES::model('Comments');

		// Get the total comments first
		$total = $model->getCommentCount($options);

		// Construct bounderies
		if (!isset($options['limit'])) {
			$options['limit'] = $this->config->get('comments.limit', 5);
		}

		$options['start'] = max($total - $options['limit'], 0);

		// Construct ordering
		$options['order'] = 'created';
		$options['direction'] = 'asc';

		// Check if it is coming from a permalink
		$commentid = $this->input->get('commentid', 0, 'int');

		if ($commentid !== 0) {
			$options['commentid'] = $commentid;

			// If permalink is detected, then no limit is required
			$options['limit'] = 0;
		}

		$comments = array();
		$count = 0;

		if ($total) {
			$comments = $model->getComments($options);
			$count = count($comments);
		}

		// @trigger: onPrepareComments
		$dispatcher = FD::dispatcher();
		$args = array(&$comments);

		$dispatcher->trigger($this->group , 'onPrepareComments', $args);

		// Check for permalink
		if (!empty($options['url'])) {
			$this->options['url'] = $options['url'];
		}

		// Check for stream id
		if (!empty($options['streamid'])) {
			$this->options['streamid'] = $options['streamid'];
		} else if($this->stream_id) {
			$this->options['streamid'] = $this->stream_id;
		}

		$themes = FD::themes();

		$themes->set('deleteable', $options['deleteable']);
		$themes->set('hideEmpty', $options['hideEmpty'] );
		$themes->set('hideForm', $options['hideForm'] );
		$themes->set('element', $this->element);
		$themes->set('group', $this->group);
		$themes->set('verb', $this->verb);
		$themes->set('uid', $this->uid);
		$themes->set('total', $total);
		$themes->set('count', $count);
		$themes->set('comments', $comments);

		if (!empty($this->options['url'])) {
			$themes->set( 'url', $this->options['url'] );
		}

		if (!empty( $this->options['streamid'])) {
			$themes->set( 'streamid', $this->options['streamid'] );
		}

		if (isset($this->options['hideForm'])) {
			$themes->set('hideForm', $this->options['hideForm']);
		}

		$html = $themes->output('site/comments/frame');

		return $html;
	}

	public function delete()
	{
		$model = FD::model( 'comments' );

		$comments = $model->getComments( array(
			'element' => $this->getElement(),
			'uid' => $this->uid,
			'limit' => 0
		) );

		foreach ($comments as $comment) {
			$comment->delete();
		}

		return true;
	}

	// @TODO: Shift this to comment app
	public function parentItemDeleted()
	{
		$model = FD::model( 'comments' );
		$state = $model->deleteCommentBlock( $this->uid, $this->getElement() );

		return $state;
	}

	public function getParticipants( $options = array() , $userObject = true )
	{
		$model = FD::model( 'comments' );

		$result = $model->getParticipants( $this->uid, $this->getElement(), $options );

		$users = array();

		if( !$result )
		{
			return $users;
		}

		if( !$userObject )
		{
			return $result;
		}

		foreach( $result as $id )
		{
			$users[$id] = FD::user( $id );
		}

		return $users;
	}
}
