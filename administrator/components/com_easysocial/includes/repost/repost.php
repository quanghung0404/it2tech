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

class SocialRepost
{
	var $uid 		= null;
	var $element 	= null;
	var $group 		= null;
	var $cluster_id = null;
	var $cluster_type = null;

	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct($uid, $element, $group = SOCIAL_APPS_GROUP_USER, $clusterId = 0, $clusterType = '')
	{
		$this->uid = $uid;
		$this->element = $element;
		$this->group = $group;
		$this->cluster_id = $clusterId;
		$this->cluster_type = $clusterType;
	}

	public static function factory( $uid, $element, $group = SOCIAL_APPS_GROUP_USER )
	{
		return new self( $uid, $element, $group );
	}

	public function debug()
	{
		var_dump( $this->uid );
		var_dump( $this->element );
		var_dump( $this->group );
		exit;
	}

	public function setCluster( $clusterId, $clusterType )
	{
		$this->cluster_id 	= $clusterId;
		$this->cluster_type = $clusterType;
	}


	public function add( $userId = null, $content = null )
	{
		if( empty( $userId ) )
		{
			$userId = FD::user()->id;
		}

		$model = FD::model( 'Repost' );
		$state = $model->add( $this->uid, $this->formKeys( $this->element, $this->group ), $userId, $content );

		return $state;
	}

	public function delete( $userId = null )
	{
		if( empty( $userId ) )
		{
			$userId = FD::user()->id;
		}

		$model = FD::model( 'Repost' );
		$state = $model->delete( $this->uid, $this->formKeys( $this->element, $this->group ), $userId );

		return $state;
	}

	public function isShared( $userId )
	{
		$element = $this->formKeys( $this->element, $this->group );

		$table = FD::table( 'Share' );
		$table->load( array( 'uid' => $this->uid, 'element' => $element, 'user_id' => $userId ) );

		if( $table->id )
		{
			// already shared before. js return true.
			return true;
		}

		return false;
	}

	private function formKeys( $element, $group )
	{
		return $element . '.' . $group;
	}

	public function getCount()
	{
		$model 	= FD::model( 'Repost' );
		$cnt 	= $model->getCount( $this->uid, $this->formKeys( $this->element, $this->group ) );

		return $cnt;
	}

	/**
	 * Alias method for getButton
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	A custom label
	 * @return	string	The html codes for the repost link
	 */
	public function button( $label = null )
	{
		return $this->getButton( $label );
	}

	/**
	 * Retrieves the repost link
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	A custom label for the repost text.
	 * @return	string	The html string containing the repost link
	 */
	public function getButton($label = null)
	{
		$my = FD::user();

		if (!$label) {
			$label = JText::_('COM_EASYSOCIAL_REPOST');
		}

		$themes = FD::get('Themes');
		$themes->set('text', $label);
		$themes->set('my', $my);
		$themes->set('uid', $this->uid);
		$themes->set('element', $this->element);
		$themes->set('group', $this->group);
		$themes->set('clusterId', $this->cluster_id);
		$themes->set('clusterType', $this->cluster_type);

 		$html = $themes->output('site/repost/action');
 		return $html;
	}

	/*
	 * alias for funcion getHTML.
	 */

	public function toHTML()
	{
		return $this->getHTML();
	}

	/**
	 * Displays the sharing code on the page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getHTML()
	{
		// Get the count.
		$count 	= 0;
		$text 	= '';

		$count 	= $this->getCount();

		// $text 	= JText::sprintf( 'COM_EASYSOCIAL_REPOST_COUNT_SHARED', $count );
		$cntPluralize 	= FD::get( 'Language' )->pluralize( $count, true )->getString();
		$text 			= JText::sprintf( 'COM_EASYSOCIAL_REPOST' . $cntPluralize, $count );

		$themes 	= FD::get( 'Themes' );
		$themes->set( 'text'		, $text );
		$themes->set( 'uid', $this->uid );
		$themes->set( 'element', $this->element );
		$themes->set( 'group', $this->group );
		$themes->set( 'count', $count );

 		$html = $themes->output( 'site/repost/item' );
		return $html;
	}

	/**
	 * Retrieves the preview item
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preview()
	{
		$file 		= dirname( __FILE__ ) . '/helpers/'.$this->element.'.php';

		if( JFile::exists( $file ) )
		{
			require_once( $file );

			// Get class name.
			$className 	= 'SocialRepostHelper' . ucfirst( $this->element );

			// Instantiate the helper object.
			$helper		= new $className( $this->uid, $this->group, $this->element );

			$content 	= $helper->getContent();
			$title 		= $helper->getTitle();

			$themes 	= FD::get( 'Themes' );
			$themes->set( 'title'	, $title );
			$themes->set( 'content'	, $content );
	 		$html = $themes->output( 'site/repost/preview' );

	 		return $html;
		}

		return false;
	}

}
