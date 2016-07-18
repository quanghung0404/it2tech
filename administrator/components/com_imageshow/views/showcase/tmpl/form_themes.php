<?php
/**
 * @version    $Id: form_themes.php 16551 2012-10-01 03:44:56Z haonv $
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

$task			  = JRequest::getVar('task');
$objShowcaseTheme = JSNISFactory::getObj('classes.jsn_is_showcasetheme');
$objJSNTheme	  = JSNISFactory::getObj('classes.jsn_is_themes');
$objJSNUtils	  = JSNISFactory::getObj('classes.jsn_is_utils');
$objJSNLightCart  = JSNISFactory::getObj('classes.jsn_is_lightcart');
$errorCode		  = $objJSNLightCart->getErrorCode('customer_verification');
$baseURL 		  = $objJSNUtils->overrideURL();
$lists			  = $this->needUpdateList;
$random			  = uniqid('') . rand(1, 99);
$divTabID         = 'mod-jsncc-sliding-tab-select-theme' . $random;
$moduleID         = 'mod-jsncc-container-select-theme' . $random;
$buttonPreviousID = 'mod-jsncc-button-previous-select-theme' . $random;
$buttonNextID     = 'mod-jsncc-button-next-select-theme' . $random;
$itemPerSlide 	  = 3;
$uri			  = JFactory::getURI();
$tmpl			  = JRequest::getVar('tmpl','');
$tmpl			  = ($tmpl!='')?'&tmpl=' . $tmpl : '';
$return 		  = base64_encode($uri->toString() . $tmpl);
if(count($lists))
{
	?>
<div class="jsn-showcase-theme-select">
	<h3 class="jsn-section-header">
	<?php echo JText::_('SHOWCASE_INSTALL_SELECT_THEME'); ?>
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
					$updateElementID = 'jsn-showcasetheme-update-showcasetheme-process-' . $i;
					?>
					<div class="jsn-item" style="width:<?php echo (100/$countLists).'%;'; ?>">
						<?php
						$objInfoUpdate = new stdClass();
						$objInfoUpdate->identify_name 		= $rows->identified_name;
						$objInfoUpdate->edition 			= '';
						$objInfoUpdate->update 				= true;
						$objInfoUpdate->install 			= false;
						$objInfoUpdate->error_code 			= $errorCode;
						$objInfoUpdate->wait_text 			= JText::_('SHOWCASE_INSTALL_THEME_WAIT_TEXT', true);
						$objInfoUpdate->process_text 		= JText::_('SHOWCASE_INSTALL_THEME_PROCESS_TEXT', true);
						$objInfoUpdate->download_element_id	= $updateElementID;
						$objInfoUpdate = json_encode($objInfoUpdate);

						/*if ($rows->needUpdate)
						{
							$actionLink			= JSN_IMAGESHOW_UPDATE_LINK . '&return=' . $return;
							$actionClass = ' jsn-showcase-theme-update ';
							$actionRel 	 = '';
							$onclick = 'target="_blank"';
							$overlayTextClass 	= 'jsn-showcasetheme-update-overlay-download';
							$itemClass = ' jsn-item-container ';
						}
						else
						{*/
							if ($task == 'edit')
							{
								$cid				= JRequest::getVar( 'cid', array(0), 'request', 'array' );
								$themeProfile 		= $objShowcaseTheme->getThemeProfile($cid[0]);
								if (is_null($themeProfile))
								{
									$actionLink  = 'index.php?option=com_imageshow&controller=showcase&task=switchtheme&subtask=edit&cid[]=' . $cid[0] . '&theme=' . $rows->identified_name . $tmpl;
								}
							}
							else
							{
								$actionLink  = 'index.php?option=com_imageshow&controller=showcase&task=switchtheme&subtask=add&theme=' . $rows->identified_name;
							}
							$actionClass 	= '';
							$actionRel 		= '';
							$onclick 		= 'onclick="JSNISImageShow.switchShowcaseTheme(this); return false;"';
							$overlayTextClass = '';
							$itemClass = ' jsn-item-container ';
						//}
						?>
							<div class="jsn-item-inner<?php echo $itemClass;?>">
								<a href="<?php echo $actionLink; ?>"
									class="<?php echo $actionClass; ?>" <?php echo $onclick; ?>
									rel="<?php echo $actionRel; ?>"> <img
									class="jsn-showcasetheme-install-thumb"
									src="<?php echo dirname($baseURL); ?>/plugins/jsnimageshow/<?php echo $rows->identified_name; ?>/assets/images/jsn_theme_thumbnail.jpg" />
									<div
										class="jsn-showcasetheme-install-overlay <?php echo $overlayTextClass; ?>">
										<span class="jsn-showcasetheme-install-loading"><img
											id="jsn-list-theme-ajax-loader-lite"
											src="<?php echo dirname($baseURL) . '/administrator/components/com_imageshow/assets/images/icons-24/icon-24-loading-circle.gif';?>" />
										</span>
										<p
											class="jsn-showcasetheme-install-overlay-text jsn-showcasetheme-install-showcasetheme">
											<?php echo JText::_('SHOWCASE_UPDATE_THEME');?>
										</p>
										<p id="<?php echo $updateElementID;?>"
											class="jsn-showcasetheme-install-overlay-text jsn-showcasetheme-install-download">
											<?php echo JText::_('SHOWCASE_INSTALL_THEME_DOWNLOAD');?>
											<br /> <span></span>
										</p>
										<p
											class="jsn-showcasetheme-install-overlay-text jsn-showcasetheme-install-installing">
											<?php echo JText::_('SHOWCASE_INSTALL_THEME_INSTALLING');?>
										</p>
									</div> </a>
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