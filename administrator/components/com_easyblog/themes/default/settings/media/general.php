<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_UPLOADER_TITLE');?></b>
                <div class="panel-info">
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_UPLOADER_INFO');?>
                </div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_ALLOWED_EXTENSIONS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_ALLOWED_EXTENSIONS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_ALLOWED_EXTENSIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<div class="input-group">
    						<input type="text" class="form-control" value="<?php echo $this->config->get( 'main_media_extensions' );?>" id="media_extensions" name="main_media_extensions" data-media-extensions />
    						<span class="input-group-btn">
    							<button type="button" class="btn btn-default" data-reset-extensions><?php echo JText::_( 'COM_EASYBLOG_RESET_DEFAULT' );?></button>
    						</span>
    					</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_MAX_FILESIZE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_MAX_FILESIZE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_MAX_FILESIZE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<div class="row">
    						<div class="col-sm-6">
    							<div class="input-group">
    								<input type="text" name="main_upload_image_size" class="form-control text-center" value="<?php echo $this->config->get('main_upload_image_size', '0' );?>" />
    								<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_MEGABYTES');?></span>
    							</div>
    						</div>
    					</div>


    					<div><?php echo JText::sprintf( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_UPLOAD_PHP_MAXSIZE' , ini_get( 'upload_max_filesize') ); ?></div>
    					<div><?php echo JText::sprintf( 'COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_UPLOAD_PHP_POSTMAXSIZE' , ini_get( 'post_max_size') ); ?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $options = array();

                            for( $i = 0; $i <= 100; $i += 10 )
                            {
                                $message    = $i;
                                $message    = $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
                                $message    = $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
                                $message    = $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
                                $options[]  = JHTML::_('select.option', $i , $message );
                            }

                            echo JHTML::_('select.genericlist', $options, 'main_image_quality', 'class="form-control"', 'value', 'text', $this->config->get('main_image_quality' ) );
                        ?>
                        <div class="help-block">
                        <?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_STORAGE_TITLE');?></b>
                <div class="panel-info">
                    <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_STORAGE_INFO');?>
                </div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_ARTICLE_PATH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_ARTICLE_PATH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_ARTICLE_PATH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" name="main_articles_path" class="form-control" value="<?php echo $this->config->get('main_articles_path', 'images/easyblog_articles/' );?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_PATH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_PATH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_IMAGE_PATH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<input type="text" name="main_image_path" class="form-control" value="<?php echo $this->config->get('main_image_path', 'images/easyblog_images/' );?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_SHARED_PATH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_SHARED_PATH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_SHARED_PATH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<input type="text" name="main_shared_path" class="form-control" value="<?php echo $this->config->get('main_shared_path', 'media/com_easyblog/shared/' );?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_AVATAR_PATH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_AVATAR_PATH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_AVATAR_PATH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<input type="text" name="main_avatarpath" class="form-control" value="<?php echo $this->config->get('main_avatarpath', 'images/eblog_avatar/' );?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_CATEGORY_PATH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_CATEGORY_PATH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_CATEGORY_PATH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<input type="text" name="main_categoryavatarpath" class="form-control" value="<?php echo $this->config->get('main_categoryavatarpath', 'images/eblog_cavatar/' );?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_TEAMBLOG_PATH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_TEAMBLOG_PATH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_TEAMBLOG_PATH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<input type="text" name="main_teamavatarpath" class="form-control" value="<?php echo $this->config->get('main_teamavatarpath', 'images/eblog_tavatar/' );?>" />
                    </div>
                </div>
            </div>
        </div>
	</div>

	<div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_ORIGINAL_IMAGE_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_ORIGINAL_IMAGE_DESC');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_RESIZE_ORIGINAL_IMAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_RESIZE_ORIGINAL_IMAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_RESIZE_ORIGINAL_IMAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_resize_original_image', $this->config->get('main_resize_original_image')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<div class="row">
                    		<div class="col-sm-6">
                    			<div class="input-group">
    								<input type="text" name="main_original_image_width" class="form-control text-center" value="<?php echo $this->config->get('main_original_image_width');?>" />
    								<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?></span>
    							</div>
    						</div>
    					</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<div class="row">
                    		<div class="col-sm-6">
                    			<div class="input-group">
    								<input type="text" name="main_original_image_height" class="form-control text-center" value="<?php echo $this->config->get('main_original_image_height');?>" />
    								<span class="input-group-addon"><?php echo JText::_('COM_EASYBLOG_PIXELS'); ?></span>
    							</div>
    						</div>
    					</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
    					<?php
      						$options = array();

      						for( $i = 0; $i <= 100; $i += 10 )
      						{
      							$message	= $i;
      							$message	= $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
      							$message	= $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
      							$message	= $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
      							$options[]	= JHTML::_('select.option', $i , $message );
      						}

    						echo JHTML::_('select.genericlist', $options, 'main_original_image_quality', 'class="form-control"', 'value', 'text', $this->config->get('main_original_image_quality' ) );
    					?>
    					<div class="help-block">
    					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
    					</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_THUMBNAILS_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_THUMBNAILS_DESC');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_WIDTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<div class="row">
                    		<div class="col-sm-6">
                    			<div class="input-group">
    								<input type="text" name="main_image_thumbnail_width" class="form-control text-center" value="<?php echo $this->config->get('main_image_thumbnail_width');?>" />
    								<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?></span>
    							</div>
    						</div>
    					</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_MAXIMUM_HEIGHT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                    	<div class="row">
                    		<div class="col-sm-6">
                    			<div class="input-group">
    								<input type="text" name="main_image_thumbnail_height" class="form-control text-center" value="<?php echo $this->config->get('main_image_thumbnail_height');?>" />
    								<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_PIXELS' ); ?></span>
    							</div>
    						</div>
    					</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_QUALITY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
    					<?php
      						$options = array();

      						for( $i = 0; $i <= 100; $i += 10 )
      						{
      							$message	= $i;
      							$message	= $i == 0 ? JText::sprintf( 'COM_EASYBLOG_LOWEST_QUALITY_OPTION' , $i ) : $message;
      							$message	= $i == 50 ? JText::sprintf( 'COM_EASYBLOG_MEDIUM_QUALITY_OPTION' , $i ) : $message;
      							$message	= $i == 100 ? JText::sprintf( 'COM_EASYBLOG_HIGHEST_QUALITY_OPTION' , $i ) : $message;
      							$options[]	= JHTML::_('select.option', $i , $message );
      						}

    						echo JHTML::_('select.genericlist', $options, 'main_image_thumbnail_quality', 'class="form-control"', 'value', 'text', $this->config->get('main_image_thumbnail_quality' ) );
    					?>
    					<div class="help-block">
    					<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_IMAGE_UPLOAD_QUALITY_HINTS' );?>
    					</div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
