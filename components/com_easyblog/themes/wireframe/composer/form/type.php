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
<?php if( $system->config->get( 'main_microblog' ) ){ ?>
<div class="eb-composer-fieldset">
    <select name="source" class="form-control">
        <option value=""<?php echo $post->posttype == '' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_STANDARD_POST' );?></option>
        <option value="photo"<?php echo $post->posttype == 'photo' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_PHOTO' );?></option>
        <option value="video"<?php echo $post->posttype == 'video' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_VIDEO' );?></option>
        <option value="quote"<?php echo $post->posttype == 'quote' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_QUOTE' );?></option>
        <option value="link"<?php echo $post->posttype == 'link' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_LINK' );?></option>
    </select>
</div>
<?php } ?>
