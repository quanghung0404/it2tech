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
<div class="wrapper accordion">
	<div class="tab-box tab-box-alt">
		<div class="tabbable">
			<ul class="nav nav-tabs nav-tabs-icons">
				<?php $i = 0; ?>
				<?php foreach( $tabs as $key => $tab ) { ?>

						<li<?php if( $i === 0 ) { echo ' class="active"'; } ?>>
							<a data-bs-toggle="tab" href="#<?php echo $key; ?>"><?php echo $tab['title']; ?></a>
						</li>

					<?php $i++; ?>
				<?php } ?>
			</ul>

			<div class="tab-content">
				<?php $i = 0; ?>
				<?php foreach( $tabs as $key => $tab ) { ?>

					<div id="<?php echo $key; ?>" class="tab-pane<?php if( $i === 0 ) { echo ' active in'; } ?>">
						<?php echo $tab['content']; ?>
					</div>

					<?php $i++ ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
