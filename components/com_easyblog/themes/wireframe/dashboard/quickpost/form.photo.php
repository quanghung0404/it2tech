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
?>
<div class="tab-pane <?php echo $active=='photo' ? 'active' : ''; ?>" id="photo" data-quickpost-form data-type="photo">
	<form class="eb-quick-photo form-horizontal">
		<div class="eb-quick-photo-options">
		    <ul class="eb-quick-photo-tab reset-list" role="tablist">
				<li class="active" data-quickpost-photo-tab-upload data-quickpost-photo-tab data-type="upload">
					<a href="#home" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_UPLOAD_PHOTO');?></a>
				</li>
				<li data-quickpost-photo-tab-webcam data-quickpost-photo-tab data-type="webcam">
					<a href="#profile" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_TAKE_PHOTO');?></a>
				</li>
		    </ul>
			<div class="eb-quick-photo-tab-content tab-content">

				<div class="tab-pane fade in active" id="home">

					<div id="dropzone" class="eb-quick-photo-uploader input-drop"
						data-photo-upload-container
						data-plupload
						data-plupload-url="<?php echo JRoute::_('index.php?option=com_easyblog&task=quickpost.uploadPhoto&tmpl=component&' . EB::getToken() . '=1');?>"
						data-plupload-max-file-size="<?php echo '10mb'; // TODO: Get from config ?>"
						data-plupload-file-data-name="image">

						<span class="eb-plupload-btn">
							<div id="input-drop-container" data-plupload-browse-button data-plupload-drop-element>
								<i class="fa fa-photo"></i>
								<div><?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_CLICK_TO_UPLOAD');?></div>
							</div>
						</span>
					</div>

					<div class="eb-quick-photo-uploader-preview upload-preview hidden" data-photo-upload-preview>
					</div>

					<br>

					<a href="javascript:void(0);" class="hidden eb-quick-photo-uploader-reupload btn btn-success btn-block" data-photo-upload-reupload>
						<i class="fa fa-refresh"></i>
						<?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_REUPLOAD');?>
					</a>
				</div>

				<div class="tab-pane fade" id="profile">
					<div class="eb-quick-photo-camera">
						<div id="camera" class="eb-responsive-video" data-photo-camera-canvas></div>

						<div data-photo-camera-preview class="eb-quick-photo-camera-preview hidden"></div>
					</div>

					<br />

					<a href="javascript:void(0);" class="eb-quick-photo-camera-recapture btn btn-success btn-block hidden" data-photo-camera-recapture>
						<i class="fa fa-refresh"></i>
						<?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_RECAPTURE');?>
					</a>

					<a href="javascript:void(0);" class="eb-quick-photo-camera-capture btn btn-danger btn-block" data-photo-camera-capture>
						<i class="fa fa-camera"></i>
						<?php echo JText::_('COM_EASYBLOG_QUICKPOST_PHOTO_CAPTURE');?>
					</a>
				</div>
			</div>

			<br>
			<div class="form-group">
				<div class="col-md-12">
					<input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_MICROBLOG_TITLE_REQUIRED');?>" data-quickpost-title />
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<textarea class="form-control" rows="10" placeholder="<?php echo $this->html('string.escape', JText::_('COM_EASYBLOG_MICROBLOG_STANDARD_CONTENT_PLACEHOLDER', true));?>" data-quickpost-content></textarea>
				</div>
			</div>
		</div>
		<input type="hidden" name="filename" data-photo-filename value="" />
		<?php echo $this->output('site/dashboard/quickpost/form.more'); ?>
	</form>
</div>
