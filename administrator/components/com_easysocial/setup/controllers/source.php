<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class EasySocialControllerSource
{
	/**
	 * Validates an API key
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate()
	{
		$key 	= JRequest::getVar( 'key' , '' );
		$result = new stdClass();

		if( empty( $key ) )
		{
			$result->state	= 400;

			header('Content-type: text/x-json; UTF-8');
			echo json_encode( $result );
			exit;
		}

		$post 		= array( 'apikey' => $key , 'product' => 'easysocial' );
		$resource 	= curl_init();

		curl_setopt( $resource , CURLOPT_URL , ES_VERIFIER );
		curl_setopt( $resource , CURLOPT_POST , true );
		curl_setopt( $resource , CURLOPT_TIMEOUT , 120 );
		curl_setopt( $resource , CURLOPT_POSTFIELDS , $post );
		curl_setopt( $resource , CURLOPT_RETURNTRANSFER , true );

		$result 	= curl_exec( $resource );
		curl_close( $resource );

		// Determine if the user has more than 1 license
		if( $result )
		{
			$result		= json_decode( $result );

			if( $result->state == 400 )
			{
				header('Content-type: text/x-json; UTF-8');
				echo json_encode( $result );
				exit;
			}

			$total 		= count( $result->licenses );

			// If the user has more than 1 license, we want them to be able to select their license
			if( $total > 1 )
			{
				$result->state	= 201;

				ob_start();
				?>
					<select class="input input-xxlarge" name="license" data-source-license>
					<?php foreach( $result->licenses as $license ){ ?>
						<option value="<?php echo $license->reference;?>"><?php echo $license->title;?> - <?php echo $license->reference; ?></option>
					<?php } ?>
					</select>
				<?php
				$output 	= ob_get_contents();
				ob_end_clean();
			}
			else
			{
				$license 	= $result->licenses[ 0 ];
				ob_start();
				?>
				<input type="hidden" name="license" value="<?php echo $license->reference;?>" />
				<?php
				$output 	= ob_get_contents();
				ob_end_clean();
			}

			$result->html 	= $output;

			$result 		= json_encode( $result );
		}

		header('Content-type: text/x-json; UTF-8');
		echo $result;
		exit;
	}
}
