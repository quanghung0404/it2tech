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
<div data-grid-pagination>
	<ul class="pagination">
		<?php if( $data->previous ){ ?>
		<li>
			<a href="javascript:void(0);" class="previousItem<?php echo !$data->previous->link ? ' disabled' : '';?>" data-limitstart="<?php echo $data->previous->base;?>">&laquo;</a>
		</li>
		<?php } ?>

		<?php foreach( $data->pages as $page ){ ?>
		<li>
			<a href="javascript:void(0);" class="pageItem<?php echo !$page->link ? ' active disabled' : '';?>" data-limitstart="<?php echo $page->base ? $page->base : 0;?>"><?php echo $page->text;?></a>
		</li>
		<?php } ?>

		<?php if( $data->next ){ ?>
		<li>
			<a href="javascript:void(0);" class="nextItem<?php echo !$data->next->link ? ' disabled' :'';?>" data-limitstart="<?php echo $data->next->base;?>">&raquo;</a>
		</li>
		<?php } ?>
	</ul>
	<input id="limitstart" name="limitstart" value="<?php echo $pagination->limitstart;?>" type="hidden" />
</div>
