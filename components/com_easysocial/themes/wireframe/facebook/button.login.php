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
<span data-oauth-facebook>
	<a href="javascript:void(0);" class="btn btn-es-social btn-es-facebook" data-oauth-facebook-login data-oauth-facebook-appid="<?php echo $appId;?>" data-oauth-facebook-url="<?php echo $authorizeURL;?>"><i class="fa fa-facebook"></i> <?php echo $text; ?></a>
</span>
