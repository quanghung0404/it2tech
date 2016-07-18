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

$view = ($type=='image') ? 'tile' : 'list';
$limit = 48;
$count = count($files);
?>
<div class="eb-mm-filegroup type-<?php echo $type; ?> <?php echo empty($count) ? 'is-empty' : ''; ?>" data-eb-mm-filegroup data-limit="<?php echo $limit; ?>">

    <div class="eb-mm-filegroup-header" data-eb-mm-filegroup-header>
        <i class="fa fa-angle-up"></i>
        <div class="eb-mm-filegroup-title"><?php echo JText::_('COM_EASYBLOG_MM_FILEGROUP_TYPE_' . strtoupper($type)); ?></div>
    </div>

    <div class="eb-mm-filegroup-body">

        <div class="eb-mm-filelist view-<?php echo $view; ?>" data-eb-mm-filelist>
            <?php foreach($files as $i => $file) { ?>
                <?php if ($count > $limit && $i==$limit) { ?>
                <div class="eb-mm-filegroup-show-all-button" data-eb-mm-filegroup-show-all-button>
                    <?php echo JText::_('COM_EASYBLOG_MM_SHOW_ALL');?> <span data-eb-mm-filegroup-count><?php echo $count; ?></span> items</div>
                <div class="eb-mm-filegroup-more">
                <?php } ?>

                <?php // For performance reasons (~150ms faster), this is hardcoded. An almost identical copy of can be found at 'site/mediamanager/file' used by renderFile(). ?>

                <div class="eb-mm-file type-<?php echo $file->type; ?><?php echo empty($file->extension) ? '' : ' ext-' . $file->extension; ?>"
                     data-eb-mm-file
                     data-key="<?php echo $file->key; ?>"
                     data-type="<?php echo $file->type; ?>">
                    <i class="<?php echo $file->icon; ?>"
                       <?php if (isset($file->thumbnail)) { ?>
                           data-thumbnail="<?php echo $file->thumbnail; ?>"
                       <?php } ?>></i>
                    <div>
                        <span data-eb-mm-file-title><?php echo $file->title; ?></span>
                    </div>
                    <?php if ($type=='folder') { ?>
                        <b class="fa fa-angle-right"></b>
                    <?php } ?>
                </div>

                <?php if ($count > $limit && $i==$count - 1) { ?>
                </div>
                <?php } ?>
            <?php } ?>
        </div>

    </div>

</div>
