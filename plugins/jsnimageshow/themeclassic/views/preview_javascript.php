<?php
/**
 * @version    $Id: preview_javascript.php 17070 2012-10-16 04:34:57Z haonv $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die( 'Restricted access' );
$objJSNUtils 	= JSNISFactory::getObj('classes.jsn_is_utils');
$baseURL 		= $objJSNUtils->overrideURL();
$URLLocalFile	= dirname($baseURL).'/plugins/'.$this->_showcaseThemeType.'/'.$this->_showcaseThemeName.'/assets/images/sample-images';
$format 		= JRequest::getVar('view_format', 'temporary');
$showcaseID 	= $array = JRequest::getVar('cid', array(0), '', 'array');
?>
<div id="jsn-visual-object">
	<div class="jsn-preview">
		<div class="jsn-preview-container"
			id="jsn-preview-container-background">
			<div class="jsn-preview-caption-wrapper"
				id="jsn-preview-caption-wrapper">
				<div class="jsn-preview-caption"
					onclick="JSNISClassicTheme.openAccordion('js-info-panel',['general']);"></div>
				<p id="js-jsn-preview-title" class="jsn-preview-title"
					onclick="JSNISClassicTheme.openAccordion('js-info-panel',['title','general']);">Title
					Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
				<p id="js-jsn-preview-description" class="jsn-preview-description"
					onclick="JSNISClassicTheme.openAccordion('js-info-panel',['description','general']);">Description
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
					eiusmod tempor incididunt ut la et dolore magna aliqua.</p>
				<p id="js-jsn-preview-link" class="jsn-preview-link"
					onclick="JSNISClassicTheme.openAccordion('js-info-panel',['link','general']);">
					<a href="javascript:void(0);" id="js-jsn-preview-a-link"
						class="jsn-preview-a-link">http://joomlashine.com</a>
				</p>
			</div>
			<div id="jsn-preview-thumbnails-container"
				class="jsn-preview-thumbnails-container"
				onclick="JSNISClassicTheme.openAccordion('js-thumb-panel',['thumbnail','general']);">
				<div id="jsn-preview-thumb-nav-left"
					class="jsn-preview-thumb-nav-left"></div>
				<div class="jsn-preview-thumbnails-list">
					<div class="jsn-preview-thumbnails">
						<div id="jsn-preview-thumbnails-image"
							class="jsn-preview-thumbnails-image">
							<img class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-01.jpg'; ?>"
								alt="thumb-01.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-02.jpg'; ?>"
								alt="thumb-02.jpg"> <img
								id="jsn-preview-thumbnails-image-active" class="active"
								src="<?php echo $URLLocalFile.'/thumb/thumb-active.jpg'; ?>"
								alt="thumb-active.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-04.jpg'; ?>"
								alt="thumb-04.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-05.jpg'; ?>"
								alt="thumb-01.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-01.jpg'; ?>"
								alt="thumb-02.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-02.jpg'; ?>"
								alt="thumb-03.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-03.jpg'; ?>"
								alt="thumb-04.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-04.jpg'; ?>"
								alt="thumb-01.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-01.jpg'; ?>"
								alt="thumb-02.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-02.jpg'; ?>"
								alt="thumb-03.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-03.jpg'; ?>"
								alt="thumb-04.jpg"> <img
								class="jsn-preview-thumbnails-image-normal"
								src="<?php echo $URLLocalFile.'/thumb/thumb-04.jpg'; ?>"
								alt="thumb-01.jpg">
						</div>
					</div>
				</div>
				<div id="jsn-preview-thumb-nav-right"
					class="jsn-preview-thumb-nav-right"></div>
			</div>
			<div id="jsn-image-fit" class="jsn-preview-image"
				onclick="JSNISClassicTheme.openAccordion('js-image-panel',['image-presentation']);">
				<img id="jsn-image-fit-img" height="266px"
					src="<?php echo $URLLocalFile.'/sample-image.jpg'; ?>"
					alt="sample-image.jpg"> <img id="jsn-image-fit-no-thumbnail"
					width="430px"
					src="<?php echo $URLLocalFile.'/sample-image.jpg'; ?>"
					alt="sample-image.jpg">
			</div>
			<div id="jsn-image-expand" class="jsn-preview-image-expand">
				<img width="550px"
					src="<?php echo $URLLocalFile.'/sample-image.jpg'; ?>" alt="1234">
			</div>
			<div id="jsn-preview-background" class="jsn-preview-background"
				onclick="JSNISClassicTheme.checkImagePresentationMode();"></div>
			<div class="jsn-preview-image-nav" id="jsn-preview-image-nav">
				<div class="jsn-preview-image-nav-left"
					onclick="JSNISClassicTheme.openAccordion('js-toolbar-panel',['general']);"></div>
				<div class="jsn-preview-image-nav-right"
					onclick="JSNISClassicTheme.openAccordion('js-toolbar-panel',['general']);"></div>
			</div>
		</div>
	</div>
</div>

