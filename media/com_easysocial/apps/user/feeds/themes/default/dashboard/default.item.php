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
<li class="feed-item" data-feeds-item data-id="<?php echo $feed->id;?>">
	<div class="row">
		<div class="col-md-12">
			<div class="pull-left">
				<span class="btn-group mr-10">
					<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-dropdown">
						<i class="icon-es-dropdown"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-user messageDropDown small">
						<li>
							<a href="javascript:void(0);" class="fd-small" data-feeds-item-remove>
								<?php echo JText::_( 'APP_FEEDS_REMOVE_ITEM' );?>
							</a>
						</li>
					</ul>
				</span>

				<i class="fa fa-rss-square mr-5"></i>
				<a href="<?php echo $this->html( 'string.escape' , $feed->url );?>" target="_blank">
					<?php echo $feed->title; ?>
				</a>
			</div>

			<div class="feed-time">
				<i class="fa fa-clock-o-2 "></i> <?php echo FD::date( $feed->created )->toLapsed();?>
			</div>
		</div>
	</div>
</li>
