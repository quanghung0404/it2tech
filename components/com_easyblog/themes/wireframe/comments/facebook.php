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
<div class="comments-facebook">
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/<?php echo $language[0];?>_<?php echo JString::strtoupper( $language[1] );?>/sdk.js#xfbml=1&appId=<?php echo $this->config->get('main_facebook_like_appid');?>&version=v2.0";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <div class="fb-comments" data-numposts="10" 
        data-colorscheme="<?php echo $this->config->get('comment_facebook_colourscheme');?>"
        data-width="100%"
        data-href="<?php echo EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $blog->id, false, true);?>"
    >
    </div>
</div>