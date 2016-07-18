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
<?php if ($post->hasLocation() && $post->category->getParam('post_location', true)) { ?>
<div class="eb-entry-location">
	<h4 class="eb-section-heading reset-heading"><?php echo JText::_('COM_EASYBLOG_BLOG_LOCATION');?></h4>
	<?php echo EB::maps()->html($post); ?>
</div>
<?php } ?>