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

class EasyBlogAUP
{
	public function __construct()
	{
		EB::loadLanguages();
	}

	public function enabled()
	{
		jimport('joomla.filesystem.file');

		$config 	= EB::config();
		$path 		= JPATH_ROOT . '/components/com_alphauserpoints/helper.php';

		// make sure the config is enabled
		if ($config->get('main_alpha_userpoint')) {

			if (JFile::exists($path)) {

				require_once($path);
				return true;
			}
		}

		return false;
	}

	private function loadHelper()
	{
		$file	= JPATH_ROOT .  '/components/com_alphauserpoints/helper.php';
		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );

		return true;
	}

	public function getPoints( $userId )
	{
		$config	= EasyBlogHelper::getConfig();

		if( !$config->get( 'main_alpha_userpoint_points' ) )
		{
			return false;
		}

		if(!$this->loadHelper() )
		{
			return false;
		}

		$info		= AlphaUserPointsHelper::getUserInfo( '' , $userId );

		if( !$info )
		{
			return '';
		}

		return JText::sprintf( 'COM_EASYBLOG_AUP_POINTS_EARNED' , $info->points );
	}

	public function assignPoints($cmd, $userId, $message)
	{
		if (!$this->enabled()) {
			return false;
		}

		// Get the user id
		$userId = AlphaUserPointsHelper::getAnyUserReferreID($userId);

		$state = AlphaUserPointsHelper::newpoints($cmd, $userId, '', $message);

		return $state;
	}

	public function assign($cmd, $msg, $cmdSingle, $text)
	{
		if (!$this->enabled()) {
			return false;
		}

		$state 	= AlphaUserPointsHelper::newpoints($cmd, $msg, $cmdSingle, $text);

		return $state;
	}

	public function getMedals( $userId )
	{
		$config	= EasyBlogHelper::getConfig();

		if (!$config->get('main_alpha_userpoint_medals')) {
			return false;
		}

		if (!$this->loadHelper()) {
			return false;
		}

		if (! method_exists('AlphaUserPointsHelper','getUserMedals')) {
		    return false;
		}

		$medals		= AlphaUserPointsHelper::getUserMedals( '' , $userId );

		$theme		= EB::template();
		$theme->set('medals', $medals);
		return $theme->output('site/aup/medals');
	}

	public function getRanks( $userId )
	{
		$config	= EasyBlogHelper::getConfig();

		if (!$config->get('main_alpha_userpoint_ranks')) {
			return false;
		}

		if (!$this->loadHelper()) {
			return false;
		}

		if (! method_exists('AlphaUserPointsHelper','getUserRank')) {
		    return false;
		}

		$rank		= AlphaUserPointsHelper::getUserRank( '' , $userId );
		$theme		= EB::template();
		$theme->set( 'rank' , $rank );
		return $theme->output( 'site/aup/ranks' );
	}
}
