<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form class="eb-composer-actions" data-eb-composer-form="actions">
    <div class="row-table">
        <div class="col-cell">
            <div class="btn-group dropup">
                <a class="btn btn-default eb-composer-save-post-button" data-eb-composer-save-post-button>
                    <i class="fa fa-save"></i>
                    <span>&nbsp; <?php echo JText::_('COM_EASYBLOG_SAVE_FOR_LATER');?></span>
                </a>

                <a class="btn btn-default dropdown-toggle_" data-bp-toggle="dropdown" aria-expanded="true"><i class="fa fa-chevron-up" style="margin: 0;"></i></a>

                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">

                    <li class="eb-composer-preview-post-button" data-eb-composer-preview-post-button>
                        <a href="javascript:void(0);">
                            <i class="fa fa-list-alt"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_PREVIEW_POST'); ?>
                        </a>
                    </li>

                    <?php if ($post->isPending() && $post->canModerate()) { ?>
                    <li class="eb-composer-save-template-button" data-eb-composer-reject-post-button>
                        <a href="javascript:void(0);">
                            <?php echo JText::_('COM_EASYBLOG_COMPOSER_REJECT_POST_BUTTON'); ?>
                        </a>
                    </li>
                    <?php } ?>

                    <?php if (! $post->isPending()) { ?>

                    <li class="divider"></li>
                    <li class="eb-composer-update-template-button" data-eb-composer-update-template-button>
                        <a href="javascript:void(0);">
                            <i class="fa fa-save"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_UPDATE_TEMPLATE'); ?>
                        </a>
                    </li>

                    <li class="eb-composer-save-template-button" data-eb-composer-save-template-button>
                        <a href="javascript:void(0);">
                            <i class="fa fa-save"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SAVE_AS_NEW_TEMPLATE'); ?>
                        </a>
                    </li>
                    <?php } ?>

                    <li class="divider"></li>

                    <?php if ($post->isPublished()) { ?>
                    <li class="eb-composer-unpublish-post-button" data-eb-composer-unpublish-post-button>
                        <a href="javascript:void(0);">
                            <i class="fa fa-eraser"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_UNPUBLISH_POST_BUTTON'); ?>
                        </a>
                    </li>
                    <?php } ?>

                    <li class="eb-composer-delete-post-button" data-eb-composer-delete-post-button>
                        <a href="javascript:void(0);">
                            <i class="fa fa-trash-o"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DELETE_POST'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-cell pl-10">
            <a class="btn btn-default eb-composer-apply-post-button" data-eb-composer-apply-post-button>
                <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
                <i class="fa fa-send"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_APPLY_BUTTON'); ?>
            </a>
        </div>

        <div class="col-cell pl-10">
            <a class="btn btn-success eb-composer-primary-button eb-composer-publish-post-button" data-eb-composer-publish-post-button>
                <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
                <i class="fa fa-send"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_PUBLISH_POST'); ?>
            </a>

            <a class="btn btn-primary eb-composer-primary-button eb-composer-update-post-button" data-eb-composer-update-post-button>
                <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
                <i class="fa fa-send"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_UPDATE_POST'); ?>
            </a>

            <a class="btn btn-warning eb-composer-primary-button eb-composer-submit-post-button" data-eb-composer-submit-post-button>
                <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
                <i class="fa fa-send"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL'); ?>
            </a>

            <a class="btn btn-success eb-composer-primary-button eb-composer-approve-post-button" data-eb-composer-approve-post-button>
                <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
                <i class="fa fa-send"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_APPROVE_AND_PUBLISH_POST'); ?>
            </a>
        </div>
    </div>

    <input type="hidden" name="template_id" value="<?php echo $postTemplate ? $postTemplate->id : '';?>" data-eb-composer-template-id />
    <input type="hidden" name="uid" value="<?php echo $post->uid; ?>" data-eb-composer-post-uid />
    <input type="hidden" name="id" value="<?php echo $post->id; ?>" data-eb-composer-post-id />
    <input type="hidden" name="revision_id" value="<?php echo $post->revision->id; ?>" data-eb-composer-revision-id />
    <input type="hidden" name="published" value="<?php echo $post->published; ?>" data-eb-composer-published-field />
</form>
