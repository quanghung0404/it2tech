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
<div class="eb-block-audio" style="padding: 0 20px;">
     <div class="audiojs-track" data-audio-infobox>
        <div class="pull-right">
            <a href="<?php echo $url;?>" target="_blank" data-audio-download><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_DOWNLOAD');?></a>
        </div>
        <div class="overflow-no">
            <i class="fa fa-music text-muted"></i>
            <span>
                <b data-audio-artist><?php echo JText::_('COM_EASYBLOG_BLOCKS_AUDIO_ARTIST');?></b>
                <span data-audio-track-separator>-</span>
                <span data-audio-track><?php echo $file;?></span>
            </span>
        </div>
    </div>
    <audio id="<?php echo $id;?>" src="<?php echo $url;?>" preload="none" />
</div>