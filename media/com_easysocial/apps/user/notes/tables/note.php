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

FD::import( 'admin:/tables/table' );

class NotesTableNote extends SocialTable
{
	public $id 		= null;
	public $user_id	= null;
	public $title 	= null;
	public $alias	= null;
	public $content	= null;
	public $created	= null;
	public $params	= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__social_notes' , 'id' , $db );
	}

	public function store( $updateNulls = false )
	{
		// @TODO: Automatically set the alias
		if( !$this->alias )
		{

		}

		$state 	= parent::store();

		return $state;
	}

	public function getAppId()
	{
		return $this->getApp()->id;
	}

	public function getApp()
	{
		static $app;

		if (empty($app)) {
			$app = FD::table('app');
			$app->load(array('type' => SOCIAL_TYPE_APPS, 'group' => SOCIAL_APPS_GROUP_USER, 'element' => 'notes'));
		}

		return $app;
	}

	/**
	 * Formats the content of a note
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getContent()
	{
		// Apply e-mail replacements
		$content 		= FD::string()->replaceEmails( $this->content );

		// Apply hyperlinks
		$content 		= FD::string()->replaceHyperlinks( $content );

		// Apply bbcode
		$content		= FD::string()->parseBBCode( $content , array( 'code' => true , 'escape' => false ) );

		return $content;
	}

	/**
	 * Creates a new stream record
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createStream( $verb )
	{
		// Add activity logging when a friend connection has been made.
		// Activity logging.
		$stream				= FD::stream();
		$streamTemplate		= $stream->getTemplate();

		// Set the actor.
		$streamTemplate->setActor( $this->user_id , SOCIAL_TYPE_USER );

		// Set the context.
		$streamTemplate->setContext( $this->id , 'notes' );

		// Set the verb.
		$streamTemplate->setVerb( $verb );

		$streamTemplate->setAccess( 'core.view' );

		// Create the stream data.
		$stream->add( $streamTemplate );
	}

	/**
	 * Overrides parent's delete behavior
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete( $pk = null )
	{
		$state	= parent::delete( $pk );

		// Delete streams that are related to this note.
		$stream 	= FD::stream();
		$stream->delete( $this->id , 'notes' );

		return $state;
	}

	/**
	 * Shorthand to get the permalink of this note.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  boolean   $external True of the link should be external ready.
	 * @return string              The permalink of the note.
	 */
	public function getPermalink($external = false, $xhtml = true, $sef = true)
	{
		return $this->getApp()->getCanvasUrl(array('cid' => $this->id, 'userid' => FD::user($this->user_id)->getAlias(), 'external' => $external, 'sef' => $sef), $xhtml);

	}
}
