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

/**
 * Hooks for Stream
 *
 * @since	1.3
 * @author	Sam <sam@stackideas.com>
 *
 */
class SocialCronHooksStream
{
	public function execute( &$states )
	{
		// Offload photos to remote location
		$states[] = $this->archiveStream();
	}


	/**
	 * archive stream items
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function archiveStream()
	{
		$config		= FD::config();

		if (! $config->get('stream.archive.enabled')) {
			return JText::_('Stream archive disabled.');
		}

		$months = $config->get('stream.archive.duration', '6');

		$model = FD::model('Stream');
		$ids = $model->getItemsToArchive( $months );

		if ($ids) {
			$state 		= $model->archive( $ids );

			if ($state) {
				return JText::_( 'COM_EASYSOCIAL_CRONJOB_STREAM_ARCHIVE_PROCESSED' );
			}
		}

		return JText::_( 'COM_EASYSOCIAL_CRONJOB_STREAM_ARCHIVE_NOTHING_TO_EXECUTE' );
	}
}
