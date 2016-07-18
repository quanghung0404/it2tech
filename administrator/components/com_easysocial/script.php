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

class com_EasySocialInstallerScript
{
	/**
	 * Triggers before the installers are copied
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function postflight()
	{
		ob_start();
		?>
		<style type="text/css">
			table{
				border-collapse: separate !important;
			}
			div#es-installer * {
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}
			div#es-installer{
				width: 860px;
			}
			div#es-installer,
			div#es-installer p,
			div#es-installer div
			{
				font-family: 'Lucida Grande', 'Gisha', 'Lucida Sans Unicode', 'Lucida Sans', Lucida, Arial, Verdana, sans-serif;
				font-size: 11px;
			}

			div#es-installer .clearfix,
			div#es-installer .box-hd,
			div#es-installer .box-bd {
				clear:none;display:block;
			}
			div#es-installer .clearfix:after,
			div#es-installer .box-hd,
			div#es-installer .box-bd {
				content:"";display:table;clear:both;
			}

			div#es-installer .box
			{
				background: #F9FAFC;
				border: 1px solid #D3D3D3;
				padding: 0px;
				margin-bottom: 20px;
				color: #777;

				-webkit-border-radius: 3px;
				-moz-border-radius: 3px;
				border-radius: 3px;
			}
			div#es-installer .box-hd {
				background: #F6F7F9;
				border-bottom: 1px solid #d3d3d3;
				width: 100%;
				padding: 8px 15px 3px;

				-webkit-border-radius: 3px 3px 0 0;
				-moz-border-radius: 3px 3px 0 0;
				border-radius: 3px 3px 0 0;
			}
			div#es-installer .box-hd .es-title {
				float: left;
			}
			div#es-installer .box-hd .es-logo {
				float: right;
				margin-right: 10px;
			}
			div#es-installer .box-hd .es-logo img {
				vertical-align: bottom;
			}
			div#es-installer .box-hd .es-social {
				float: right;
			}

			div#es-installer .box-bd {
				padding: 16px !important;
			}
			div#es-installer h1.es-title {
				font-size: 22px;
				line-height: 24px;
				color: #333;
			}

			div#es-installer .btn-install {
				font-size: 11px;
				padding: 6px 16px;

			    background-color: #8AD449;
			    background-image: linear-gradient(to bottom, #8AD449, #6CD107);
			    background-repeat: repeat-x;
			    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
			    color: #FFFFFF;
			    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
			}
			div#es-installer .btn-install:hover {
				background-position: 0 0;
			}

			div#es-installer .box p
			{
				font-weight: normal;
				text-align: left;
			}

			div#es-installer .box p img
			{
				padding: 0 25px 0 0;
			}

			div#es-installer .fb-like,
			div#es-installer .fb-like iframe{
				width: 85px !important;
				max-width: 85px !important;
			}
			div#es-installer .twitter-follow-button{
				margin-left: 5px;
			}

			div#es-installer .actions{
				margin-top: 30px;
				text-align: left !important;
			}
		</style>


		<div id="es-installer">
			<div class="box">
				<div class="box-hd">
					<div class="es-title">
						You are about to install <b>EasySocial</b>.
					</div>

					<div class="es-social socialize">
						<div id="fb-root"></div>
						<script>(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=406369119482668";
						fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>
						<div class="fb-like" data-href="http://www.facebook.com/StackIdeas" data-width="90" data-layout="button_count" data-show-faces="false" data-send="false"></div>
						<a href="https://twitter.com/stackideas" class="twitter-follow-button" data-show-count="false">Follow @stackideas</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					</div>

					<div class="es-logo">
						Another product by <a href="http://stackideas.com" target="_blank"><img src="http://stackideas.com/images/es-logo-stackideas.png" alt=""></a>
					</div>

				</div>
				<!-- box-hd -->
				<div class="box-bd">
					<h1 class="es-title">
						Thank you for your recent purchase of EasySocial.
					</h1>
					<p>
						Thank you for your recent purchase of EasySocial and congratulations on making the choice to use the Best Social Networking Extension for Joomla! Before you are able to access the EasySocial Component, you will need to proceed with the Initial Setup. If this is an Upgrade, please ignore this message and proceed with the Upgrade.
					</p>

					<div class="actions">
						<a href="index.php?option=com_easysocial&amp;install=true" class="btn btn-success btn-install">Proceed With Installation &raquo;</a>
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</div>
		<div style="clear:both"></div>
		<?php
		$contents 	= ob_get_contents();
		ob_end_clean();

		echo $contents;
	}

	/**
	 * Triggers after the installers are copied
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preflight()
	{
		// During the preflight, we need to create a new installer file in the temporary folder
		$file = JPATH_ROOT . '/tmp/easysocial.installation';

		// Determines if the installation is a new installation or old installation.
		$obj = new stdClass();
		$obj->new = false;
		$obj->step = 1;
		$obj->status = 'installing';

		$contents = json_encode($obj);

		if (!JFile::exists($file)) {
			JFile::write($file, $contents);
		}
	}

	/**
	 * Responsible to perform the installation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function install()
	{
	}

	/**
	 * Responsible to perform the uninstallation
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uninstall()
	{
		// @TODO: Disable modules

		// @TODO: Disable plugins
	}

	/**
	 * Responsible to perform component updates
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function update()
	{

	}
}
