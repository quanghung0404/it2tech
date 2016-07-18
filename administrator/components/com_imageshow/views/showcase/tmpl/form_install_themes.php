<?php
/**
 * @version    $Id: form_install_themes.php 16533 2012-09-28 05:16:54Z haonv $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die( 'Restricted access' );

$objJSNTheme	  = JSNISFactory::getObj('classes.jsn_is_themes');
$objJSNUtils	  = JSNISFactory::getObj('classes.jsn_is_utils');
$objJSNLightCart  = JSNISFactory::getObj('classes.jsn_is_lightcart');
$errorCode		  = $objJSNLightCart->getErrorCode('customer_verification');
$baseURL 		  = $objJSNUtils->overrideURL();
$lists	     	  = $this->needInstallList;
$random			  = uniqid('') . rand(1, 99);
$divTabID         = 'mod-jsncc-sliding-tab-new-theme' . $random;
$moduleID         = 'mod-jsncc-container-new-theme' . $random;
$buttonPreviousID = 'mod-jsncc-button-previous-new-theme' . $random;
$buttonNextID     = 'mod-jsncc-button-next-new-theme' . $random;
$itemPerSlide 	  = 3;
$uri			  = JFactory::getURI();
$return 		  = base64_encode($uri->toString());
if(count($lists))
{
	$modContentClipsSlidingTab = 'modContentClipsSlidingTabNewTheme' . $random;
	?>
<div class="jsn-showcase-theme-select">
	<h3 class="jsn-section-header">
	<?php echo JText::_('SHOWCASE_INSTALL_NEW_THEME'); ?>
	</h3>
	<div id="<?php echo $moduleID; ?>">
	<?php if (count($lists) > $itemPerSlide) { ?>
		<script type="text/javascript" charset="utf-8">
			(function($){
				$(document).ready(function() {
					$('#<?php echo $divTabID; ?>').JSNISSlider('<?php echo $buttonPreviousID; ?>', '<?php echo $buttonNextID; ?>','<?php echo $itemPerSlide;?>','<?php echo count($lists);?>');
				});
			})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
		</script>
<?php } ?>
		<div
			class="jsn-showcase-theme-slide jsn-showcase-theme-classic-bright">
			<div class="navigation-button clearafter">
				<span id="<?php echo $buttonPreviousID; ?>"
					class="jsn-showcase-theme-slide-arrow <?php echo (count($lists) > $itemPerSlide)?'slide-arrow-pre':'';?>"></span>
				<span id="<?php echo $buttonNextID; ?>"
					class="jsn-showcase-theme-slide-arrow <?php echo (count($lists) > $itemPerSlide)?'slide-arrow-next':'';?>"></span>
			</div>
			<div id="<?php echo $divTabID; ?>" class="sliding-content">
				<div class="clearafter" style="width:<?php echo (count($lists) < $itemPerSlide)?'100':(count($lists)/$itemPerSlide)*100; ?>%;">
				<?php
				$countLists = count($lists);
				if($countLists < $itemPerSlide)
				{
					$itemPerSlide = $countLists;
				}

				for($i = 0; $i < $countLists; $i++)
				{
					$rows = $lists[$i];
					$downloadElementID = 'jsn-showcasetheme-install-showcasetheme-process-' . $i;
					?>
					<div class="jsn-item" style="width:<?php echo (100/$countLists).'%;'; ?>">
						<?php
						$objInfoUpdate = new stdClass();
						$objInfoUpdate->identify_name 		= $rows->identified_name;
						$objInfoUpdate->edition 			= '';
						$objInfoUpdate->update 				= false;
						$objInfoUpdate->install 			= true;
						$objInfoUpdate->error_code 			= $errorCode;
						$objInfoUpdate->wait_text 			= JText::_('SHOWCASE_INSTALL_THEME_WAIT_TEXT', true);
						$objInfoUpdate->process_text 		= JText::_('SHOWCASE_INSTALL_THEME_PROCESS_TEXT', true);
						$objInfoUpdate->download_element_id	= $downloadElementID;
						$objInfoUpdate = json_encode($objInfoUpdate);
						$addHTML = '';
						$itemClass = ' jsn-item-container ';
						if ((boolean) $rows->authentication != true)
						{
							$actionLink  = 'javascript:void(0);';
							$actionClass = ' jsn-showcase-theme-install ';
							$actionRel 	= '';
							$onclick 	= ' onclick="JSNISInstallShowcaseThemes.install(this, ' . $this->escape($objInfoUpdate) . '); return false;" ';

							if (!$this->canAutoDownload) {
								$actionLink  = 'index.php?option=com_imageshow&controller=installer&task=manualInstall&layout=form_manual_install&identify_name=' . $rows->identified_name . '&name=' . $rows->name . '&tmpl=component&type=theme';
								$actionClass = ' jsn-showcase-theme-install modal ';
								$actionRel 	 = '{handler: \'iframe\', size: {x: 450, y: 330}}';
								$onclick 	 = '';
							}
						}
						else
						{
							$actionLink 	= 'index.php?option=com_imageshow&controller=showcase&task=authenticate&layout=form_login&identify_name=' . $rows->identified_name . '&tmpl=component&return=' . $return;
							$actionClass 	= ' modal jsn-showcase-theme-install ';
							$actionRel 		= '{handler: \'iframe\', size: {x: 450, y: ' . ((count($rows->related_products) > 0) ? '500' : '300') . '}}';
							$onclick 		= ' onclick="JSNISInstallShowcaseThemes.setOptions(this, ' . $this->escape($objInfoUpdate) . ');" ';
						}
						$overlayTextClass = 'jsn-showcasetheme-install-overlay-install';
						?>
							<div class="jsn-item-inner<?php echo $itemClass;?>">
								<a href="<?php echo $actionLink; ?>"
									class="<?php echo $actionClass; ?>" <?php echo $onclick; ?>
									rel="<?php echo $actionRel; ?>"> <img
									class="jsn-showcasetheme-install-thumb"
									src="<?php echo $rows->thumbnail ;?>" />
									<div
										class="jsn-showcasetheme-install-overlay <?php echo $overlayTextClass; ?>">
										<span class="jsn-showcasetheme-install-loading"><img
											id="jsn-install-theme-ajax-loader-lite"
											src="<?php echo dirname($baseURL) . '/administrator/components/com_imageshow/assets/images/icons-24/icon-24-loading-circle.gif';?>" />
										</span>
										<p
											class="jsn-showcasetheme-install-overlay-text jsn-showcasetheme-install-showcasetheme">
											<?php echo JText::_('SHOWCASE_INSTALL_THEME');?>
										</p>
										<p id="<?php echo $downloadElementID;?>"
											class="jsn-showcasetheme-install-overlay-text jsn-showcasetheme-install-download">
											<?php echo JText::_('SHOWCASE_INSTALL_THEME_DOWNLOAD');?>
											<br /> <span></span>
										</p>
										<p
											class="jsn-showcasetheme-install-overlay-text jsn-showcasetheme-install-installing">
											<?php echo JText::_('SHOWCASE_INSTALL_THEME_INSTALLING');?>
										</p>
									</div> </a>
									<?php echo $addHTML; ?>
							</div>
							<div class="jsn-source-name">
							<?php
							echo ($rows->name) ? $rows->name : JText::_('N/A');
							?>
							</div>
						</div>
						<?php
				}
				?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
}
?>