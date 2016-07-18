<?php
/**
 * @version    $Id: form_sources.php 16580 2012-10-01 10:44:35Z haonv $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$objJSNLightCart  = JSNISFactory::getObj('classes.jsn_is_lightcart');
$errorCode		  = $objJSNLightCart->getErrorCode('customer_verification');
$objJSNSource     = JSNISFactory::getObj('classes.jsn_is_source');
$objJSNUtils	  = JSNISFactory::getObj('classes.jsn_is_utils');
$baseURL 		  = $objJSNUtils->overrideURL();
$datas 			  = $objJSNSource->compareLocalSources();
$lists			  = $objJSNSource->getNeedUpdateList($datas);
$random			  = uniqid('').rand(1, 99);
$divTabID         = 'mod-jsncc-sliding-tab-'.$random;
$moduleID         = 'mod-jsncc-container-'.$random;
$buttonPreviousID = 'mod-jsncc-button-previous-'.$random;
$buttonNextID     = 'mod-jsncc-button-next-'.$random;
$itemPerSlide 	  = 3;
$showlistID 	  = JRequest::getVar('cid', array(0));
$showlistID 	  = $showlistID[0];
$uri			  = JFactory::getURI();
$return 		  = base64_encode($uri->toString());
$popup			  = JRequest::getInt('popup', 1);
$session 		  = JFactory::getSession();
$identifier		  = md5('jsn_imageshow_downloasource_identify_name');
$tmpIdentifyName  = $session->get($identifier, '', 'jsnimageshowsession');
$tmpl			  = JRequest::getVar('tmpl','');
$tmpl			  = ($tmpl!='')?'&tmpl='.$tmpl:'';
if(count($lists))
{
	?>
<script type="text/javascript">
var objISModal = null;

function JSNAutoOpenModalWindow()
{
	require(['jsn/libs/modal'], function (JSNModal) {
		var link = 'index.php?option=com_imageshow&controller=showlist&task=profile&layout=form_profile&tmpl=component&source_identify=<?php echo $tmpIdentifyName; ?>&image_source_type=external&showlist_id=<?php echo $showlistID;?>&return=<?php echo $return;?>';
		objISModal = new JSNModal({
			width: 450,
			height: 500,
			url: link,
			title: '<?php echo JText::_('SHOWLIST_PROFILE_SELECT_IMAGE_SOURCE_PROFILE', true);?>',
			scrollable: true,
			buttons: [
				          {
				        	  text: '<?php echo JText::_('JSN_IMAGESHOW_SAVE'); ?>',
				        	  click: function () {
									if (typeof gIframeOnSubmitFunc != 'undefined')
									{
										gIframeOnSubmitFunc($(this));
									}
									else
									{
										console.log('Iframe function not available');
									}
				        	  }
				          },
				          {
				        	  text: '<?php echo JText::_('JSN_IMAGESHOW_CLOSE'); ?>',
				        	  click: function () {
				        		  objISModal.close();
				        	  }
				          }
				    ]
		});
		objISModal.iframe.css('overflow', 'hidden');
		objISModal.show();

	});
}

function JSNAutoSelectSourceAfterInstallion()
{
	var url = 'index.php?option=com_imageshow&controller=showlist&task=onSelectSource&image_source_type=internal&source_identify=<?php echo $tmpIdentifyName; ?>&showlist_id=<?php echo $showlistID;?><?php echo $tmpl; ?>';
	window.location=url;
}
</script>
<div class="jsn-showlist-source-select">
	<h3 class="jsn-section-header">
	<?php echo JText::_('SHOWLIST_SELECT_IMAGE_SOURCE'); ?>
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
			class="jsn-showlist-source-slide jsn-showlist-source-classic-bright">
			<div class="navigation-button clearafter">
				<span id="<?php echo $buttonPreviousID; ?>"
					class="jsn-showlist-source-slide-arrow <?php echo (count($lists) > $itemPerSlide)?'slide-arrow-pre':'';?>"></span>
				<span id="<?php echo $buttonNextID; ?>"
					class="jsn-showlist-source-slide-arrow <?php echo (count($lists) > $itemPerSlide)?'slide-arrow-next':'';?>"></span>
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
					$text = JText::_('SHOWLIST_IMAGE_SOURCE_INSTALL_IMAGE_SOURCE');
					$rows = $lists[$i];
					$updateElementID = 'jsn-imagesource-update-id-'.$i;
					?>
					<div class="jsn-item" style="width:<?php echo (100/$countLists).'%;'; ?>">
						<?php
						$objInfoUpdate = new stdClass();
						$objInfoUpdate->identify_name		= $rows->identified_name;
						$objInfoUpdate->edition				= '';
						$objInfoUpdate->update 				= true;
						$objInfoUpdate->install 			= false;
						$objInfoUpdate->error_code 			= $errorCode;
						$objInfoUpdate->wait_text 			= JText::_('SHOWLIST_IMAGE_SOURCE_INSTALL_WAIT_TEXT', true);
						$objInfoUpdate->process_text 		= JText::_('SHOWLIST_IMAGE_SOURCE_INSTALL_PROCESS_TEXT', true);
						$objInfoUpdate->download_element_id	= $updateElementID;
						$objInfoUpdate = json_encode($objInfoUpdate);
						$addHTML = '';
						?>
						<?php
						$actionTitle				= '';
						/*if ($rows->needUpdate && $rows->identified_name != 'folder')
						{
							$actionLink			= JSN_IMAGESHOW_UPDATE_LINK . '&return=' . $return;
							$actionClass		= ' jsn-showlist-imagesource-update ';
							$actionRel			= '';
							$onclick			= 'target="_blank"';
							$overlayTextClass	= 'jsn-imagesource-update-overlay-download';
							$text				= JText::_('SHOWLIST_IMAGE_SOURCE_UPDATE_IMAGE_SOURCE');
							$itemClass			= ' jsn-item-container ';
						}
						else */
						if ($rows->type == 'external')
						{
							$actionLink			= 'index.php?option=com_imageshow&controller=showlist&task=profile&layout=form_profile&tmpl=component&source_identify='.$rows->identified_name.'&image_source_type='.$rows->type.'&showlist_id='.(int)$showlistID.'&return='.$return;
							$actionClass		= 'jsn-is-form-modal';
							$actionTitle			= JText::_('SHOWLIST_PROFILE_SELECT_IMAGE_SOURCE_PROFILE', true);
							$actionRel			= '{"size": {"x": 450, "y": 500}, "buttons": {"ok": true, "close": true}}';
							$onclick			= '';
							$overlayTextClass	= '';
							$itemClass			= ' jsn-item-container ';
							if ($tmpIdentifyName != '' && $tmpIdentifyName == $rows->identified_name && $popup)
							{
								echo '<script type="text/javascript">';
								echo 'window.addEvent("domready", function() {';
								echo 'JSNAutoOpenModalWindow();';
								echo '});';
								echo '</script>';
							}
						}
						else if (isset($rows->localInfo->componentInstall) && $rows->localInfo->componentInstall == false)
						{
							$actionLink			= '#';
							$actionClass		= 'jsn-showlist-imagesource-miss-component';
							$actionRel			= '';
							$onclick			= '';
							$overlayTextClass	= 'jsn-imagesource-install-overlay-miss-component';
							$addHTML			= '<p class="jsn-imagesource-install-overlay-text jsn-imagesource-install-miss-component">'. JText::sprintf('SHOWLIST_IMAGE_SOURCE_INSTALL_MISS_COMPONENT', $rows->localInfo->define->component_link) .'</p>';
							$itemClass			= '';
						}
						else
						{
							$actionLink			= 'index.php?option=com_imageshow&controller=showlist&task=onSelectSource&image_source_type='.$rows->type.'&source_identify='.$rows->identified_name.'&showlist_id='.(int)$showlistID.$tmpl;
							$actionClass		= '';
							$actionRel			= '';
							$onclick			= '';
							$overlayTextClass	= '';
							$itemClass			= ' jsn-item-container ';
							if ($tmpIdentifyName != '' && $tmpIdentifyName == $rows->identified_name && $popup)
							{
								echo '<script type="text/javascript">';
								echo 'window.addEvent("domready", function() {';
								echo 'JSNISImageShow._openModal();';
								echo 'JSNAutoSelectSourceAfterInstallion();';
								echo '});';
								echo '</script>';
							}
						}
						?>
							<div class="jsn-item-inner<?php echo $itemClass;?>">
								<a href="<?php echo $actionLink; ?>"
									class="<?php echo $actionClass; ?>" <?php echo $onclick; ?>
									rel='<?php echo $actionRel; ?>'
									title='<?php echo $actionTitle?>'>
									<div
										class="jsn-imagesource-install-overlay <?php echo $overlayTextClass; ?>">
										<span class="jsn-imagesource-install-loading"><img
											src="<?php echo dirname($baseURL).'/administrator/components/com_imageshow/assets/images/icons-24/icon-24-loading-circle.gif';?>" />
										</span>
										<p
											class="jsn-imagesource-install-overlay-text jsn-imagesource-install-imagesource">
											<?php echo $text;?>
										</p>
										<p id="<?php echo $updateElementID; ?>"
											class="jsn-imagesource-install-overlay-text jsn-imagesource-install-download">
											<?php echo JText::_('SHOWLIST_IMAGE_SOURCE_INSTALL_DOWNLOAD');?>
											<br /> <span></span>
										</p>
										<p
											class="jsn-imagesource-install-overlay-text jsn-imagesource-install-installing">
											<?php echo JText::_('SHOWLIST_IMAGE_SOURCE_INSTALL_INSTALLING');?>
										</p>
									</div> <img class="jsn-imagesource-install-thumb"
									src="<?php echo ($rows->identified_name == 'folder') ? dirname($baseURL).'/'.$rows->thumbnail : $rows->thumbnail ;?>" />
								</a>
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
<?php $session->set($identifier, '', 'jsnimageshowsession'); ?>
