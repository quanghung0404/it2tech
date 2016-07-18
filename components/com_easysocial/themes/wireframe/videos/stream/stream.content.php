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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<?php if ($stream->content) { ?>
<div class="stream-contents mb-10">
    <?php echo $stream->content;?>
</div>
<?php } ?>

<div class="stream-links">
    <h4 class="es-stream-content-title has-info">
        <a href="<?php echo $video->getPermalink();?>"><?php echo $video->title;?></a>
    </h4>

    <div class="links-content" data-video-wrapper>

        <div class="es-stream-preview fd-small">
            <div class="video-container">
                <?php echo $video->getEmbedCodes();?>
            </div>
            
            <p class="preview-desc"><?php echo $video->description;?></p>
        </div>
    </div>
</div>
