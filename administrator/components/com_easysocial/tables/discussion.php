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

/**
 * Object mapping for `#__social_discussions` table.
 *
 * @author	Mark Lee <mark@stackideas.com>
 * @since	1.2
 */
class SocialTableDiscussion extends SocialTable
{
	/**
	 * The unique id of the cluster
	 * @var int
	 */
	public $id			= null;

	/**
	 * The category id of the cluster.
	 * @var string
	 */
	public $parent_id	= null;

	/**
	 * Determines the cluster type
	 * @var string
	 */
	public $uid		= null;

	/**
	 * The owner type of this cluster
	 * @var string
	 */
	public $type 		= null;

	/**
	 * If this discussion has been answered, it should store the discussion id.
	 * @var int
	 */
	public $answer_id		= null;

	/**
	 * Determines the last replied discussion
	 * @var int
	 */
	public $last_reply_id		= null;

	/**
	 * The title of this cluster
	 * @var string
	 */
	public $title		= null;

	/**
	 * The content of this discussion
	 * @var string
	 */
	public $content 		= null;

	/**
	 * The creator of this discussion
	 * @var int
	 */
	public $created_by		= null;

	/**
	 * Total number of hits for this discussion
	 * @var int
	 */
	public $hits		= null;

	/**
	 * The state of this discussion.
	 * @var string
	 */
	public $state		= null;

	/**
	 * The creation date of this discussion
	 * @var datetime
	 */
	public $created		= null;

	/**
	 * Determines the last replied date
	 * @var datetime
	 */
	public $last_replied		= null;

	/**
	 * Determines the vote value of a discussion.
	 * @var string
	 */
	public $votes		= null;

	/**
	 * Determines the total number of replies for a discussion
	 * @var string
	 */
	public $total_replies		= 0;

	/**
	 * Determines if the discussion is locked.
	 * @var int
	 */
	public $lock		= null;

	/**
	 * JSON string that is used as params
	 * @var string
	 */
	public $params		= null;


	public function __construct(& $db )
	{
		parent::__construct( '#__social_discussions' , 'id' , $db );
	}

	/**
	 * Synchronizes the count for denormalized columns
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sync()
	{
		$model	= FD::model( 'Discussions' );

		// Get the total number of replies
		$this->total_replies 	= $model->getTotalReplies( $this->id );

		// Try to get the last reply item
		$reply 	= $model->getLastReply( $this->id );

		if( $reply )
		{
			// Update the last_reply_id
			$this->last_reply_id 	= $reply->id;

			// Set the last replied time
			$this->last_replied 	= $reply->created;
		}

		// Store it.
		$this->store();
	}

	/**
	 * Allows caller to set a reply as an answer
	 *
	 * @since	1.2
	 * @access	public
	 * @return	bool	True if success false otherwise
	 */
	public function setAnswered( SocialTableDiscussion $reply )
	{
		$this->answer_id 	= $reply->id;

		$state 	= parent::store();

		return $state;
	}

	/**
	 * Allows caller to unlock a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @return	bool	True if success false otherwise
	 */
	public function unlock()
	{
		$this->lock 	= false;

		$state 	= parent::store();

		return $state;
	}

	/**
	 * Allows caller to lock a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @return	bool	True if success false otherwise
	 */
	public function lock()
	{
		$this->lock 	= true;

		$state 	= parent::store();

		return $state;
	}

	/**
	 * Override parent's behavior to delete this discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int
	 * @return
	 */
	public function delete( $pk = null )
	{
		$state 	= parent::delete($pk);

		if( $state )
		{
			// Delete all the replies
			$model 	= FD::model( 'Discussions' );
			$model->deleteReplies( $this->id );

			// Delete all stream items related to this discussion.
			FD::stream()->delete( $this->id , 'discussions' );

			// Delete any files associated in #__social_discussions_files
			$model->deleteFiles( $this->id );

		}

		return $state;
	}

	/**
	 * Retrieve the permalink to the discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true, $external = false, $sef = true)
	{
		static $app 	= false;

		if (!$app) {
			$app 	= FD::table('App');
			$app->load(array('element' => 'discussions', 'group' => SOCIAL_TYPE_GROUP));
		}

		// Get the group
		$group 		= FD::group($this->uid);
		$permalink 	= $app->getPermalink('canvas', array('customView' => 'item', 'groupId' => $group->id, 'discussionId' => $this->id, 'external' => $external, 'sef' => $sef));

		return $permalink;
	}

	/**
	 * Get participants in a discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getParticipants( $exclude = array() )
	{
		$model 	= FD::model( 'Discussions' );

		$participants 	= $model->getParticipants( $this->id , array( 'exclude' => $exclude  ) );

		return $participants;
	}

	public function getContent()
	{
		// Escape html codes
		$content = $this->content;

		// Apply e-mail replacements
		$content = FD::string()->replaceEmails($content);

		// Apply gist replacements
		$content = FD::string()->replaceGist($content);

		// Apply hyperlinks
		$content = FD::string()->replaceHyperlinks($content);

		// Apply bbcode
		$content = FD::string()->parseBBCode($content, array( 'code' => true , 'escape' => false ) );

		// Remove files from the content
		$content = $this->replaceFiles($content);

		// // Apply line break to the message
		// $content 	= nl2br( $content );

		return $content;
	}

	public function removeFiles( $content )
	{
		$pattern 	= '/\[file(.*?)\](.*?)\[\/file\]/is';

		$content 	= preg_replace( $pattern , '' , $content );

		return $content;
	}

	/**
	 * Stores the list of files for this particular discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function mapFiles()
	{
		// Get a list of files from the content first.
		$files		= $this->getFiles();

		foreach( $files as $file )
		{
			$table 					= FD::table( 'DiscussionFile' );
			$table->file_id			= $file->id;
			$table->discussion_id	= $this->id;

			$table->store();
		}

		return true;
	}


	/**
	 * Replaces the files in the discussion with images
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function replaceFiles( $content )
	{
		// due to the htmlentities, now the quotes become &quot; and we need to change it back.
		// $content = JString::str_ireplace('&quot;', '"', $content);

		$pattern 	= '/\[file id="(.*?)"\](.*?)\[\/file\]/is';
		preg_match_all($pattern, $this->content, $matches);

		// If there are no matches, skip this altogether.
		if (!$matches && !$matches[0]) {
			return $content;
		}

		// Now we need to do a proper search / replace
		$total 	= count($matches[0]);

		for ($i = 0; $i < $total; $i++) {

			$search = $matches[0][$i];
			$fileId = $matches[1][$i];
			$title = $matches[2][$i];

			$file = FD::table('File');
			$file->load($fileId);

			// Perhaps the user is trying to exploit the system?
			if( !$file->id || ( $file->uid != $this->uid && $file->type != $this->type ) ) {
				continue;
			}

			$theme 		= FD::themes();
			$theme->set( 'file' , $file );

			if ($file->hasPreview()) {
				$replace 	= $theme->output('site/discussions/item.image');
			} else {
				$replace 	= $theme->output('site/discussions/item.file');
			}

			$content = JString::str_ireplace($search, $replace, $content);
		}

		return $content;
	}

	/**
	 * Determines if there are files in a discusison
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasFiles()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_discussions_files' );
		$sql->column('COUNT(1)', 'total');
		$sql->where( 'discussion_id' , $this->id );

		$db->setQuery($sql);
		$total	= $db->loadResult();

		return $total > 0;
	}

	/**
	 * Retrieves a list of files posted in this discussion
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFiles()
	{
		static $data 		= array();

		if( !isset( $data[ $this->id ] ) )
		{
			$pattern 	= '/\[file id="(.*?)"\](.*?)\[\/file\]/is';

			preg_match_all( $pattern , $this->content , $matches );

			if( !isset( $matches[ 1 ] ) || empty( $matches[ 1 ] ) )
			{
				return false;
			}

			$ids 	= $matches[ 1 ];
			$files 	= array();

			foreach( $ids as $id )
			{
				$file 	= FD::table( 'File' );
				$file->load( $id );

				// Perhaps the user is trying to exploit the system?
				if( !$file->id || ( $file->uid != $this->uid && $file->type != $this->type ) )
				{
					continue;
				}

				// If the user tries to use the same files twice, ignore this
				if( isset( $files[ $file->id ] ) )
				{
					continue;
				}

				$files[ $file->id ]	= $file;
			}

			$data[ $this->id ]	= $files;
		}

		return $data[ $this->id ];
	}






}
