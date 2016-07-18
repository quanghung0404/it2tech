<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-social-button pinterest">
    <a href="//pinterest.com/pin/create/button/?url=<?php echo $url;?>&media=<?php echo $media;?>&description=<?php echo $title;?>" 
        data-pin-do="buttonPin" 
        data-pin-config="<?php echo $size == 'small' ? 'beside' : 'above';?>" 
        data-pin-color="white" 
        target="_blank"
    >
        <img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_white_20.png" />
    </a>
</div>