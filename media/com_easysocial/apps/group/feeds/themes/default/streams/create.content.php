<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<h5>
	<a href="<?php echo FRoute::groups(array('id' => $group->getAlias() , 'appId' => $app->getAlias(), 'layout' => 'item')); ?>">
		<i class="fa fa-rss-square mr-5"></i> <?php echo $rss->get( 'title' );?>
	</a>
</h5>
<hr />
<p>
	<?php echo strip_tags($rss->description);?>
</p>
