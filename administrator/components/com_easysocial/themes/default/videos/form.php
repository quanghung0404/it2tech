<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" id="adminForm" method="post" name="adminForm" data-table-grid data-videos-form>
	<div class="wrapper accordion">
		<div class="tab-box tab-box-alt">
			<div class="tabbable">
				
				<ul id="videosForm" class="nav nav-tabs">
					<li class="tabItem active">
						<a data-bs-toggle="tab" href="#general" data-form-tabs data-item="general">
							<span class="help-block"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_TAB_PROFILE_GENERAL');?></span>
						</a>
					</li>
				</ul>

				<div class="tab-content">
					<div id="general" class="tab-pane active in">
						<div class="row">
							<div class="col-md-6">
								<div class="panel">
									<div class="panel-head">
										<b><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_GENERAL');?></b>
										<p class="panel-info"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_GENERAL_INFO');?></p>
									</div>

									<div class="panel-body">
				                        <div class="form-group">
											<label for="title" class="col-md-4">
												<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_AUTHOR');?>
											</label>
											<div class="col-md-8">
												<input type="text" class="form-control input-sm" value="<?php echo $video->getAuthor()->getName();?>" disabled="true" />
											</div>
				                        </div>

										<div class="form-group">
											<label for="title" class="col-md-4">
												<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FORM_CATEGORY');?>
												<i class="icon-es-help pull-right"
													<?php echo $this->html('bootstrap.popover' , 'COM_EASYSOCIAL_VIDEOS_FORM_CATEGORY', '', 'bottom'); ?>
												></i>
											</label>
											<div class="col-md-8">
												<select name="category_id" class="form-control input-sm">
													<option value=""><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FORM_SELECT_CATEGORY');?></option>
													<?php foreach ($categories as $category) { ?>
													<option value="<?php echo $category->id;?>" <?php echo $table->category_id == $category->id ? ' selected="selected"' : '';?>><?php echo JText::_($category->title);?></option>
													<?php } ?>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="title" class="col-md-4">
												<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FORM_TITLE');?>
												<i class="icon-es-help pull-right"
													<?php echo $this->html('bootstrap.popover' , 'COM_EASYSOCIAL_VIDEOS_FORM_TITLE', '', 'bottom'); ?>
												></i>
											</label>
											<div class="col-md-8">
												<input type="text" name="title" id="title" class="form-control input-sm" value="<?php echo $table->title;?>"/>
											</div>
										</div>

										<div class="form-group">
											<label for="title" class="col-md-4">
												<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FORM_DESCRIPTION');?>
												<i class="icon-es-help pull-right"
													<?php echo $this->html('bootstrap.popover' , 'COM_EASYSOCIAL_VIDEOS_FORM_DESCRIPTION', '', 'bottom'); ?>
												></i>
											</label>
											<div class="col-md-8">
												<textarea name="description" class="form-control input-sm" placeholder="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FORM_DESCRIPTION_PLACEHOLDER');?>"><?php echo $table->description;?></textarea>
											</div>
										</div>

				                        <div class="form-group">
				                            <label for="es-fields-85" class="col-md-4 control-label">
				                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_TAG');?>
												<i class="icon-es-help pull-right"
													<?php echo $this->html('bootstrap.popover' , 'COM_EASYSOCIAL_VIDEOS_VIDEO_TAG', '', 'bottom'); ?>
												></i>
				                            </label>
				                            <div class="col-md-8">
				                                <div class="textboxlist disabled" data-mentions>

				                                    <?php if ($tags) { ?>
				                                        <?php foreach ($tags as $tag) { ?>
				                                            <div class="textboxlist-item" data-id="<?php echo $tag->getEntity()->id; ?>" data-title="<?php echo $tag->getEntity()->getName(); ?>" data-textboxlist-item>
				                                                <span class="textboxlist-itemContent" data-textboxlist-itemContent>
				                                                    <img width="16" height="16" src="<?php echo $tag->getEntity()->getAvatar(SOCIAL_AVATAR_SMALL);?>" />
				                                                    <?php echo $tag->getEntity()->getName(); ?>
				                                                    <input type="hidden" name="items" value="<?php echo $tag->getEntity()->id; ?>" />
				                                                </span>
				                                                <a class="textboxlist-itemRemoveButton" href="javascript: void(0);" data-textboxlist-itemRemoveButton>
				                                                    <i class="fa fa-remove"></i>
				                                                </a>
				                                            </div>
				                                        <?php } ?>
				                                    <?php } ?>

				                                    <input type="text" autocomplete="off"
				                                        disabled
				                                        class="textboxlist-textField"
				                                        data-textboxlist-textField
				                                        placeholder="<?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_START_TYPING');?>"
				                                    />
				                                </div>
				                            </div>
				                        </div>

				                        <div class="form-group">
				                            <label for="es-fields-85" class="col-md-4 control-label">
				                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_LOCATION');?>
				                            </label>
				                            <div class="col-md-8">
				                                <?php echo $this->html('form.location', 'location', $video->getLocation()); ?>
				                            </div>
				                        </div>

				                        <div class="form-group">
				                            <label for="es-fields-85" class="col-md-4 control-label">
				                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_PRIVACY');?>
				                            </label>
				                            <div class="col-md-8">
				                                <div class="pull-left">
				                                    <?php echo $privacy->form($video->id, SOCIAL_TYPE_VIDEOS, $this->my->id, 'videos.view', true, null, array()); ?>
				                                </div>
				                            </div>
				                        </div>


									</div>
								</div>


							</div>

							<div class="col-md-6">
								<div class="panel">
									<div class="panel-head">
										<b><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_VIDEO_SOURCE');?></b>
										<p class="panel-info"><?php echo JText::_('COM_EASYSOCIAL_PROFILES_FORM_VIDEO_SOURCE_INFO');?></p>
									</div>

									<div class="panel-body">
										<?php if ($video->isPendingProcess() || $video->isProcessing()) { ?>
										<div class="form-group">
										<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FORM_VIDEO_IS_PENDING_OR_PROCESSING'); ?>
										</div>

										<input type="hidden" name="source" value="upload" />
										<?php } else { ?>
										<div class="form-group">
											<label for="title" class="col-md-4">
												<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FORM_VIDEO_TYPE');?>
												<i class="icon-es-help pull-right"
													<?php echo $this->html('bootstrap.popover' , 'COM_EASYSOCIAL_VIDEOS_FORM_CATEGORY', '', 'bottom'); ?>
												></i>
											</label>
											<div class="col-md-8">
				                                <label for="video-link" class="radio-inline">
				                                    <input id="video-link" type="radio" name="source" value="link" data-video-source <?php echo $video->isLink() ? ' checked="checked"' : '';?>/>
				                                    <span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_TYPE_EXTERNAL');?></span>
				                                </label>

				                                <label for="video-uploads" class="radio-inline">
				                                    <input id="video-uploads" type="radio" name="source" value="upload" data-video-source <?php echo $video->isUpload() ? ' checked="checked"' : '';?>/>
				                                    <span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_TYPE_UPLOAD');?></span>
				                                </label>
											</div>
										</div>

				                        <div class="form-group<?php echo $video->isUpload() ? ' hide' : '';?>" data-form-link data-form-source>
				                            <label for="video-link-source" class="col-md-4 control-label">
				                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_LINK');?>
				                            </label>
				                            <div class="col-md-8">
				                                <input type="text" class="form-control input-sm" name="link" 
				                                    placeholder="<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_LINK_PLACEHOLDER');?>" 
				                                    value="<?php echo $video->isLink() ? $video->path : '';?>"
				                                    id="video-link-source"
				                                />
				                            </div>
				                        </div>

				                        <div class="form-group<?php echo $video->isLink() ? ' hide' : '';?>" data-form-upload data-form-source>
				                            <label class="col-md-4 control-label">
				                                <?php echo JText::_('COM_EASYSOCIAL_VIDEOS_VIDEO_FILE');?>
												<i class="icon-es-help pull-right"
													<?php echo $this->html('bootstrap.popover' , 'COM_EASYSOCIAL_VIDEOS_FORM_CATEGORY', '', 'bottom'); ?>
												></i>
				                            </label>
				                            <div class="col-md-8">
				                                <input type="file" name="video" />
				                            </div>
				                        </div>
				                        <?php } ?>

									</div>
								</div>
							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'videos'); ?>
	<input type="hidden" name="id" value="<?php echo $video->id;?>" />
</form>
