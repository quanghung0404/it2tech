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
<li class="toolbarItem" data-toolbar-story  data-toolbar-item>
	<a href="javascript:void(0);" data-es-provide="tooltip" data-original-title="<?php echo JText::_( 'Share something' , true );?>" class="loadFormButton">
		<i class="icon-es-tb-stream"></i>
		<span class="visible-phone"><?php echo JText::_( 'Share something' );?></span>
	</a>
	<div class="dropdown-menu dropdown-menu-post">
		<div class="overview" style="top: 0px;">
			<div>
				<ul class="fd-reset-list">
					<li>
						<div class="">
							<h5><?php echo JText::_( 'Quick Post' ); ?></h5>
						</div>
					</li>
					<li class="divider"></li>
				</ul>
				<div class="post-wrapper">
					<form action="/index.php?option=com_easydiscuss&amp;view=index&amp;Itemid=479" method="post">
						<textarea class="full-width" rows="4" placeholder="<?php echo JText::_( 'Write something here...' , true );?>"></textarea>
					</form>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<a href="javascript:void(0);" class="btn btn-es-primary btn-medium" tabindex="104"><?php echo JText::_( 'Share This' ); ?></a>
		</div>
	</div>
</li>
