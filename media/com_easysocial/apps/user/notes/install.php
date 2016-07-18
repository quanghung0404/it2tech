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

/**
 * Notes application installer.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialUserAppsNotesInstaller implements SocialAppInstaller
{
	/**
	 * This is executed during the initial installation after the files are copied over.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	bool	True to proceed with installation, false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function install()
	{
		/*
		 * Run something here if necessary
		 */
		 return true;
	}

	/**
	 * This is executed during the uninstallation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	bool	True to proceed with uninstall, false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function uninstall()
	{
		/*
		 * Run something here if necessary
		 */
		 return true;
	}

	/**
	 * This is executed when user upgrades the application.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	bool	True to proceed with the upgrade, false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function upgrade()
	{
		/*
		 * Run something here if necessary
		 */
		 return true;
	}

	/**
	 * This is executed when there is an error during the installation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	bool	True to proceed with the process, false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function error()
	{
		/*
		 * Run something here if necessary
		 */
		 return true;
	}

	/**
	 * This is executed when installation is successfull.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string	Message to be displayed to the user.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function success()
	{
		ob_start();
	?>
	<h6>Thank you for installing Notes!</h6>
	<p>
		Notes is an application that allows site users to create, share or store notes on the site. Once the application is installed, you will need to publish the application.
	</p>

	<ul class="list-unstyled">

		<li>
			<div>Like us on Facebook</div>
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
			fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<div class="fb-like" data-href="http://www.facebook.com/StackIdeas" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>

		</li>

		<li style="margin-top:20px;">
			<div>Follow us on Twitter</div>
			<a href="https://twitter.com/stackideas" class="twitter-follow-button" data-show-count="false">Follow @stackideas</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</li>
	</ul>
	<?php

		$message = ob_get_contents();
		@ob_end_clean();


		return $message;
	}
}
