<?php
/**
 * @version     $Id: index.php 19770 2012-12-28 08:26:19Z thailv $
 * @package     JSN_Mobilize
 * @subpackage  Template
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
error_reporting(0);
// Include helper class
require_once JPATH_ROOT . '/administrator/components/com_mobilize/mobilize.defines.php';
require_once dirname(__FILE__) . '/helpers/mobilize.php';
require_once JPATH_ROOT . '/administrator/components/com_mobilize/helpers/mobilize.php';

// Initialize variables
$app = JFactory::getApplication();
$get = $app->input->getArray($_GET);
$jCfg = JFactory::getConfig();
$device = $app->input->getCmd('_device');
$mCfg = JSNMobilizeTemplateHelper::getConfig($device, $app->input->getInt('jsn_mobilize_preview'));
$switch = $app->input->getVar('_request') . (strpos($app->input->getVar('_request'), '?') === false ? '?' : '&') . 'switch_to_desktop_ui=1';
$preview = $app->input->getInt('jsn_mobilize_preview');
$language = JFactory::getLanguage()->getTag();
$menuLanguages = $mCfg->get('menu-language');

if ($app->input->getInt('jsn_mobilize_preview') == '1')
{
	$menuLanguages = json_decode($menuLanguages);
}
// Get user selected style
JHtml::_('behavior.framework', true);
if (!empty($mCfg))
{
	$cssFile = $mCfg->get('css-file') ? $mCfg->get('css-file') : "";
	$cookieStyle = $mCfg->get('profile-style') ? $mCfg->get('profile-style') : "";
	$cookieStl = $mCfg->get('style') ? $mCfg->get('style') : "";
	$customCssFiles = $mCfg->get( 'custom-css-files' ) ? $mCfg->get( 'custom-css-files' ) : "";
	$customCssCode = $mCfg->get( 'custom-css-code' ) ? $mCfg->get( 'custom-css-code' ) : "";
	// Load header for HTML document
	?>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="IE=10">
	<?php
	$document = JFactory::getDocument();
	?>
	<jdoc:include type="head" />
	<?php
	//$document->addStyleSheet(JURI::root() . '/plugins/system/jsnframework/assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.9.0.custom.css');
	$document->addStyleSheet(JURI::root() . 'media/jui/css/bootstrap.min.css');
	$document->addStyleSheet(JURI::root() . 'media/jui/css/bootstrap-responsive.min.css');
	$document->addStyleSheet(JURI::root() . 'templates/jsn_mobilize/css/template.css');
	$document->addStyleSheet('//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css');
	$document->addStyleSheet(JURI::root() . 'plugins/system/jsnmobilize/assets/css/mobilize.css');
	if (!$cookieStyle)
	{
		if ($cssFile && JFolder::exists(JPath::clean(JPATH_ROOT . "/templates/jsn_mobilize/css/profiles" . $cssFile)))
		{
			$document->addStyleSheet(JURI::root() . '/templates/jsn_mobilize/css/profiles/' . $cssFile);
		}
		else
		{
			$css = $mCfg->get('css') ? $mCfg->get('css') : "";
			$document->addStyleDeclaration($css);
		}
	}
	else
	{
		$css = JSNMobilizeHelper::generateStyle($cookieStyle);
		$css = str_replace("\n", "", $css);
		$document->addStyleDeclaration($css);
	}
	if ( $customCssCode ) {
		$document->addStyleDeclaration( $customCssCode );
	}
	if ( ! empty( $customCssFiles ) ) {
		if ( is_string( $customCssFiles ) ) {
			$customCssFiles = json_decode( $customCssFiles );
		}

		if ( ! empty( $customCssFiles ) ) {
			foreach ( $customCssFiles as $file ) {
				$document->addStyleSheet( $file );
			}
		}
	}
	//$document->addScript(JURI::root() . '/templates/jsn_mobilize/js/mobilize.js');
	$logoAlignment = $mCfg->get('logo-alignment');
	if(JSNMobilizeTemplateHelper::checkMTREE()){
		$document->addStyleSheet(JURI::root() . '/templates/jsn_mobilize/ext/mosetstree/jsn_mosetstree.css');
	}
	if(JSNMobilizeTemplateHelper::checkK2()){
		$document->addStyleSheet(JURI::root() . '/templates/jsn_mobilize/ext/k2/jsn_ext_k2.css');
	}
	if(JSNMobilizeTemplateHelper::checkVM()){
		$document->addStyleSheet(JURI::root() . '/templates/jsn_mobilize/ext/vm/jsn_ext_vm.css');
	}
	?>

</head>
<?php
		$tpl = new JSNMobilizeTemplateHelper();
		echo $tpl->renderOpcity('',$preview,$cookieStyle,$cookieStl,'jsn_template');
?>
<body id="jsn-master" class="<?php echo $app->input->getInt('jsn_mobilize_preview') ? ' jsn-mobilize-preview' : ''; ?> jsn-preview-<?php echo $device; ?>">
	
<div id="jsn-page" class="jsn-mobile-layout">
	
<div>
	<div id="jsn-header">
		<div id="jsn-menu">
			<div class="row-fluid">
				<?php
				if ((int) $mCfg->get('menu') || count($menuLanguages))
				{
				?>
					<ul class="mobilize-menu nav nav-pills jsn-mainmenu">
						<?php
						// Get module ID
						if(count($menuLanguages) && $menuLanguages != 'null')
						{
							foreach($menuLanguages as $k => $menuLanguage)
							{
								if($menuLanguage->jsn_menu_language == 'all')
								{
									$id = $menuLanguage->jsn_menu_id;
								}
								else
								{
									if($menuLanguage->jsn_menu_language == $language)
									{
										$id = $menuLanguage->jsn_menu_id;
									}
								}
							}
						}
						else
						{
							$id = array_keys($mCfg->get('menu'));
							$id = array_pop($id);
						}

						// Get link status
						//$status = array_values($mCfg->get('menu'));
						//$status = array_pop($status);
						if ((int) $id)
						{
				?>
							<li class="dropdown">
								<span class="jsn-menu-toggle"><i class="icon-th-list"></i></span>
								<?php JSNMobilizeTemplateHelper::renderMenu((int) $id); ?>
							</li>
							<?php
						}
						?>
					</ul>
					<?php
				}
				if ((int) $mCfg->get('search') || (int) $mCfg->get('login'))
				{
					?>
					<ul class="mobilize-menu nav nav-pills jsn-sidetool">
						<?php
						foreach (array('login', 'search') AS $type)
						{
							// Get module ID
							$configMenu = $mCfg->get($type);
							if (!empty($configMenu))
							{
								$id = array_keys($configMenu);
								$id = array_pop($id);
								// Get link status
								$status = array_values($configMenu);
								$status = array_pop($status);
								$contentMenu = JSNMobilizeTemplateHelper::renderModule((int) $id, array(), true, true);
								if ($status && !empty($contentMenu))
								{
									?>
									<li class="dropdown">
										<?php
										if ($type == "login")
										{
											$type = "user";
										}
										?>
										<span class="jsn-menu-toggle"><i class="icon-<?php echo strtolower($type);?>"></i></span>

										<div class="jsn-tool">
											<?php echo $contentMenu;?>
										</div>
									</li>
									<?php
								}
							}
						}
						?>
					</ul>
					<?php
				}
				?>
				<?php
				if ($mCfg->get('logo'))
				{
					?>
					<div id="jsn-logo" class="jsn-text-<?php echo $logoAlignment ? $logoAlignment : "left"; ?>">
						<?php
						$urlLogo = JURI::root(true);
						
						// Get logo link
						$link = array_keys($mCfg->get('logo'));
						$link = array_pop($link);
						// Remove the "/" if it's at the begining of the link
						if ( strpos($link, '/') == 0 ) {
							$link = substr($link, 1);
						}
						// Get logo alternative text
						$alt = array_values($mCfg->get('logo'));
						$alt = array_pop($alt);
						
						$imgSrc = JURI::root() . $link;
						
						if ($hasLogo = is_readable(JPATH_ROOT . DS . $link))
						{
						?>
							<a href="<?php echo JURI::root(true); ?>" title="<?php echo JText::_($alt); ?>"><img src="<?php echo $imgSrc; ?>" alt="<?php echo JText::_($alt); ?>" /></a>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
				<div class="clearbreak"></div>
			</div>
		</div>
		<?php
		$tool = $tpl->renderOpcity(1,$preview,$cookieStyle,$cookieStl,'jsn_mobile_tool');
		JSNMobilizeTemplateHelper::renderHtmlBlock($tool,'mobile-tool', $device, $preview, 'jsn-mobile-tool'); ?>
	</div>
	<div id="jsn-body">
		<?php
		$contentTop = $tpl->renderOpcity(1,$preview,$cookieStyle,$cookieStl,'jsn_content_top');
		JSNMobilizeTemplateHelper::renderHtmlBlock($contentTop,'content-top', $device, $preview, 'jsn-content-top'); ?>
		
		<div id="jsn-mainbody">
			<jdoc:include type="component" />
		</div>
		<?php
		$userTop = $tpl->renderOpcity(1,$preview,$cookieStyle,$cookieStl,'jsn_user_top');
		JSNMobilizeTemplateHelper::renderHtmlBlock($userTop,'user-top', $device, $preview, 'jsn-user-top'); ?>
		
		<?php 
		$userbt = $tpl->renderOpcity(1,$preview,$cookieStyle,$cookieStl,'jsn_user_bottom');
		JSNMobilizeTemplateHelper::renderHtmlBlock($userbt,'user-bottom', $device, $preview, 'jsn-user-bottom'); ?>
		<?php JSNMobilizeTemplateHelper::renderHtmlBlock('side-content', $device, $preview, 'jsn-side-content'); ?>
		<?php 
		$contentbt = $tpl->renderOpcity(1,$preview,$cookieStyle,$cookieStl,'jsn_content_bottom');
		JSNMobilizeTemplateHelper::renderHtmlBlock($contentbt,'content-bottom', $device, $preview, 'jsn-content-bottom'); ?>
	</div>
	<?php 
	$footer = $tpl->renderOpcity(1,$preview,$cookieStyle,$cookieStl,'jsn_footer');
	JSNMobilizeTemplateHelper::renderHtmlBlock($footer,'footer', $device, $preview, 'jsn-footer');?>
	<div id="jsn-social">
		<div class="social_div">
			<?php
				if($preview == 1){
						$jssocial = $cookieStyle['social_input'];
				}else{
						$jssocial = json_decode($cookieStl['jsn_social']);
					}
				if (isset($jssocial) && !empty($jssocial)):
					foreach ($jssocial as $key=>$val):
						foreach ($val as $ky=>$vl){
							$arrVl[$ky] = $vl;
						}
						if($arrVl[0] !=''):
						foreach ($arrVl[1] as $k=>$v){
							$stt[$k] = $v;
						}
			?>
			<a href="<?=$arrVl[0]?>" id="<?=$key?>" class="font-icon <?=$stt[0]?>" target="_blank"><i class="fa <?=$key?>"></i></a>                                           
			<?php endif;endforeach;?>
				<input type="hidden" class="jsn-input-style" name="style[jsn_social]" value='<?=$social?>' id="social_input" />
			<?php else:?>
				<a href="https://facebook.com" target="_blank" id="fa-facebook" class="font-icon"><i class="fa fa-facebook"></i></a>                                                    
				<a href="https://plus.google.com" target="_blank" id="fa-google-plus" class="font-icon"><i class="fa fa-google-plus"></i></a>                                                    
				<a href="https://twitter.com" target="_blank" id="fa-twitter" class="font-icon"><i class="fa fa-twitter"></i></a>                                                    
				<input type="hidden" class="jsn-input-style" name="style[jsn_social]" value='{"fa-facebook":{"0":"","1":{"0":"choised","1":"none"}},"fa-twitter":{"0":"","1":{"0":"choised","1":"none"}},"fa-google-plus":{"0":"","1":{"0":"choised","1":"none"}},"fa-instagram":{"0":"","1":{"0":"choised","1":"none"}},"fa-youtube-play":{"0":"","1":{"0":"choised","1":"none"}},"fa-linkedin":{"0":"","1":{"0":"choised","1":"none"}},"fa-pinterest":{"0":"","1":{"0":"choised","1":"none"}},"fa-flickr":{"0":"","1":{"0":"choised","1":"none"}},"fa-tumblr":{"0":"","1":{"0":"choised","1":"none"}},"fa-vimeo-square":{"0":"","1":{"0":"choised","1":"none"}},"fa-deviantart":{"0":"","1":{"0":"choised","1":"none"}},"fa-digg":{"0":"","1":{"0":"choised","1":"none"}},"fa-dribbble":{"0":"","1":{"0":"choised","1":"none"}},"fa-behance":{"0":"","1":{"0":"choised","1":"none"}}}' id="social_input" />
			<?php endif;?>
		</div>
	</div>
	<?php
	$switcher = $mCfg->get('switcher');
	if (!empty($switcher))
	{
		foreach ($switcher as $key => $value)
		{
			if ($value == 1)
			{
				if (!empty($get['jsn_mobilize_preview']) && $get['jsn_mobilize_preview']==1)
				{
					$config = JFactory::getConfig();
					$switch = JURI::root() . $switch;
					if(!empty($get['jsn_mobilize_preview']) && $get['jsn_mobilize_preview']==1){
						$switch = $switch.'&jsn_mobilize_preview=1';
					}
				}
				?>
				<div id="jsn-switcher" class="jsn-mobilize-ui-switcher jsn-text-center">
					<button id="jsn-mobilize-ui-switcher" class="btn btn-primary" onclick="window.location.href='<?php echo $switch;?>'" type="button"><?php echo $key;?></button>
				</div>
				<?php
				break;
			}
			else
			{
				break;
			}
		}
	}
	?>
</div>
</div>		

<script src="<?php echo JURI::root() . '/templates/jsn_mobilize/js/mobilize.js';?>" type="text/javascript"></script>
</body>
</html>
<?php
}
