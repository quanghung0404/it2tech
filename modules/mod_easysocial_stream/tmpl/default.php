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
<div id="fd" class="es es-responsive mod-es-stream module-social<?php echo $suffix;?>">
    <!-- to simulate side menu so that the stream can update the content correctly -->
    <div class="active" data-dashboardSidebar-menu data-type="module" data-id=""></div>

	<?php echo $stream->html();?>

	<div class="pull-right mb-20">
		<a href="<?php echo $readmoreURL; ?>"><?php echo $readmoreText; ?></a>
	</div>
</div>
