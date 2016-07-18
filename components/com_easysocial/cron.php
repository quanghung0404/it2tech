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

########################################
##### Configuration options.
########################################

// This should just contain fully qualified domain.
// E.g: stackideas.com or yourdomain.com
$host		= 'site.com';

########################################

if( md5( $host ) == '7aef11b08719dfb3b922e89eac4b8d78' )
{
	echo "Please change the \$host value in the cron.php file to your correct url";
	exit;
}

// Strip http:// and https:// from the $host
$host		= str_ireplace( array( 'http://' , 'https://' ) , '' , $host );

// Now we should open the connection to connect to the site.
$resource	= fsockopen( $host , 80 , $errorNumber , $errorString );

if( !$resource )
{
	echo 'There was an error connecting to the site.';
	exit;
}


function connect( $fp , $host, $url )
{
	$request 	= "GET /" . $url . " HTTP/1.1\r\n";
	$request 	.= "Host: " . $host . "\r\n";
	$request 	.= "Connection: Close\r\n\r\n";

	fwrite( $fp , $request );
}

connect( $resource , $host , 'index.php?option=com_easysocial&cron=true' );

// // Debug codes only to see the output of the cron
// while (($buffer = fgets($resource, 4096)) !== false)
// {
// 	echo $buffer;
// }

fclose( $resource );

echo "Cronjob processed.\r\n";
return;
