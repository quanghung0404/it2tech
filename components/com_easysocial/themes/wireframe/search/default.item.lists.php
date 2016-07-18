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

$listURL = ( $item->link ) ? $item->link : FRoute::friends( array( 'listid' => $item->uid ) );

?>
<li data-search-item
	data-search-item-id="<?php echo $item->id; ?>"
	data-search-item-type="<?php echo $item->utype; ?>"
	data-search-item-typeid="<?php echo $item->uid; ?>"
	>
	<div class="es-item">
		<div class="pull-left">
			<a href="<?php echo $item->link; ?>">
				<i class="icon-jar jar-users"></i>
			</a>
		</div>
		<div class="es-item-body">

			<div class="es-item-detail">
				<ul class="fd-reset-list">
					<li>
						<span class="es-item-title">
							<a href="<?php echo $listURL; ?>">
								<?php echo FD::get('String')->escape($item->title); ?>
							</a>
						</span>
					</li>
					<li>
						<a class="" href="<?php echo $item->link; ?>">
							<?php echo JText::_( 'COM_EASYSOCIAL_SEARCH_RESULT_GOTO_LISTS' ); ?>
						</a>
					</li>
				</ul>
			</div>
		</div>

	</div>

</li>
