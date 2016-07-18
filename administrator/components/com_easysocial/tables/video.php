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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import('admin:/tables/table');
FD::import('admin:/includes/indexer/indexer');

class SocialTableVideo extends SocialTable implements ISocialIndexerTable
{
	public $id = null;
	public $title = null;
	public $description = null;
	public $user_id = null;
	public $uid = null;
	public $type = null;
	public $created = null;
	public $state = null;
	public $featured = null;
	public $category_id = null;
	public $hits = null;
	public $duration = null;
	public $size = null;
	public $params = null;
	public $storage = 'joomla';
	public $path = null;
	public $original = null;
	public $file_title = null;
	public $source = 'link';
	public $thumbnail = null;
	
	public function __construct($db)
	{
		parent::__construct('#__social_videos', 'id', $db);
	}

	public function syncIndex()
	{
	}

	public function deleteIndex()
	{		
	}

	/**
	 * Determines if this video is an upload source
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function isUpload()
	{
		return $this->source == SOCIAL_VIDEO_UPLOAD;
	}

	/**
	 * Determines if this video is a link source
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function isLink()
	{
		return $this->source == SOCIAL_VIDEO_LINK;
	}

    /**
     * Constructs the alias for this photo
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAlias($withId = true)
    {
        $title = $this->title;
        $title = JFilterOutput::stringURLSafe($title);
        $alias = $title;

        if ($withId) {
        	$alias = $this->id . ':' . $alias;
        }

        return $alias;
    }


	/**
	 * Retrieves the permalink of a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPermalink($xhtml = true)
	{
		$options = array('id' => $this->getAlias(), 'layout' => 'item');

		if ($this->uid && $this->type && $this->type != SOCIAL_TYPE_USER) {
			$cluster = ES::cluster($this->type, $this->uid);

			$options['uid'] = $cluster->getAlias();
			$options['type'] = $this->type;
		}

		$url = FRoute::videos($options, $xhtml);

		return $url;
	}

	/**
	 * Retrieves the external permalink of a video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getExternalPermalink()
	{
		$url = FRoute::videos(array('id' => $this->id, 'layout' => 'item', 'external' => true), false);

		return $url;
	}

	/**
	 * Allows caller to set the duration
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setDuration(SocialVideoDuration $duration)
	{
		$this->duration = $duration->raw();

		// Save the video object
		return $this->store();
	}

	/**
	 * Allows the caller to set the state to processing
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function processing()
	{
		$this->state = SOCIAL_VIDEO_PROCESSING;

		return $this->store();
	}


	/**
	 * Determines if the video item is unfeatured
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isUnfeatured()
	{
		return !$this->isFeatured();
	}

	/**
	 * Determines if the video item is featured
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFeatured()
	{
		return $this->featured == SOCIAL_VIDEO_PUBLISHED;
	}
}
