<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script type="text/javascript">
EasyBlog.require()
.script('ticker')
.done(function($) {
	$('#js-ticker').ticker({
        titleText: "<?php echo JText::_('MOD_EASYBLOGTICKER_HEADLINE_TEXT', true);?>"
    });
});
</script>
<div id="fd" class="eb eb-mod mod_easyblogticker<?php echo $params->get('moduleclass_sfx'); ?>">
	<ul id="js-ticker" class="js-hidden" data-mod-ticker-items>
		<?php foreach ($items as $item) { ?>
			<li class="news-item">
				<a href="<?php echo $item->getPermalink();?>"><?php echo $item->title; ?></a>
			</li>
		<?php } ?>
	</ul>
</div>
