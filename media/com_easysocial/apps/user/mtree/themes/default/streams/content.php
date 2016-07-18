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
<div class="stream-kunena mt-10 mb-10">
	<div class="media">


		<div class="media-object pull-left mr-15">
			<a href="<?php echo $link->hyperlink;?>"><img src="<?php echo $link->link_avatar;?>" width="120" /></a>
		</div>


		<div class="media-body">
			<h4 class="es-stream-content-title">
				<a href="<?php echo $link->hyperlink;?>"><?php echo $link->link_name;?></a>
			</h4>

			<div>
				<a href="<?php echo $link->categoryLink;?>"><?php echo $link->category;?></a>
			</div>

			<p><?php echo $link->link_desc;?></p>

			<div>
				<a href="<?php echo $link->hyperlink;?>" class="btn btn-es btn-sm"><?php echo JText::_('APP_USER_MTREE_VIEW_LISTING');?></a>
			</div>
		</div>
	</div>
</div>
