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
<div class="eb-composer-fieldset">
    <div class="eb-composer-field eb-revisions-current-field" data-eb-revisions-current-field>
        <div class="eb-composer-revision-item is-published is-single is-current is-primary">
            <div class="row-table">
                <div class="col-cell cell-tight">
                    <div class="eb-revision-number"><?php echo $workingRevision->ordering;?></div>
                </div>
                <div class="col-cell">
                    <div class="eb-revision-details">
                        <div class="eb-revision-title">
                            <div class="row-table">
                                <div class="col-cell cell-ellipse">
                                    <b><?php echo $workingRevision->getTitle();?></b>
                                </div>
                                <?php if ($workingRevision->isCurrent($post) && $post->isPublished()) { ?>
                                <div class="col-cell cell-tight">
                                    <span class="eb-revision-published"><?php echo JText::_('COM_EASYBLOG_REVISION_PUBLISHED');?></span>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="eb-revision-meta text-muted">

                            <?php if ($workingRevision->isCurrent($post) && $post->isUnpublished()) { ?>
                                <span><?php echo JText::_('COM_EASYBLOG_REVISION_UNPUBLISHED');?></span>
                                &middot;
                            <?php } ?>

                            <?php if ($workingRevision->isDraft()) { ?>
                                <span><?php echo JText::_('COM_EASYBLOG_REVISION_DRAFT');?></span>
                                &middot;
                            <?php } ?>

                            <span><?php echo ucfirst($workingRevision->getAuthor()->getName());?></span>
                            &middot;
                            <span><?php echo $workingRevision->getCreationDate()->format(JText::_('d M Y, h:ia'));?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="eb-composer-field style-bordered-outline">
        <div class="eb-composer-field eb-revisions-list-field" data-eb-revisions-list-field>
            <div class="eb-revision-listing" data-eb-revisions-list>
                <?php echo $this->output('site/composer/revisions/list', array('revisions' => $revisions)); ?>
            </div>
        </div>
    </div>
</div>