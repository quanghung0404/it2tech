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

// Include main views file.
FD::import( 'admin:/views/views' );

class EasySocialViewMigrators extends EasySocialAdminView
{
	/**
	 * Default user listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		// Set page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MIGRATORS');


		// // ---------------------------------------
		// // debug - do not remove
		// $mnt = FD::maintenance();
		// $files = $mnt->getScriptFiles();
		// foreach($files as $file)
		// {
		// 	// var_dump( $file );
		//     $state = $mnt->runScript($file);
		// }

		// $mnt = FD::maintenance();
		// $file = '/Users/kfteh/Projects/solo/workbench/joomla25/administrator/components/com_easysocial/updates/1.3.0/GeoTest.php';
		// var_dump( $mnt->runScript($file) );
		// exit;
		// debug end here
		// // ---------------------------------------

		echo parent::display( 'admin/migrators/default' );
	}

	/**
	 * Displays the JomSocial migration form
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function jomsocial()
	{
		// Set page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS_JOMSOCIAL');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MIGRATORS_JOMSOCIAL');

		// Get the migrator library
		$migrator 	= FD::migrators( __FUNCTION__ );
		$installed	= $migrator->isInstalled();

		$version 	= $migrator->getVersion();

		if ($installed) {
			// Get custom fields from JomSocial
			$jsFields 	= $migrator->getCustomFields();

			// Get our own fields list
			$appsModel		= FD::model( 'Apps' );
			$fields			= $appsModel->getApps(array('type' => SOCIAL_APPS_TYPE_FIELDS, 'group' => SOCIAL_FIELDS_GROUP_USER));

			// lets reset the $fiels so that the index will be the element type.
			if( $fields )
			{
				$tmp = array();
				foreach( $fields as $field )
				{
					$tmp[ $field->element ] = $field;
				}
				$fields = $tmp;
			}

			$fieldsMap = $migrator->getFieldsMap();


			$this->set( 'fields'		, $fields );
			$this->set( 'jsFields'		, $jsFields );
			$this->set( 'fieldsMap'		, $fieldsMap );

			$this->displayPurgeButton('Jomsocial');
		}

		$this->set( 'installed'		, $installed );
		$this->set( 'version'		, $version );

		parent::display( 'admin/migrators/jomsocial' );
	}

	/**
	 * Displays the migration form for Community Builder
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function cb()
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS_CB');
		$this->setDescription('COM_EASYSOCIAL_HEADING_MIGRATORS_CB_DESC');

		// Get the migrator library
		$migrator 	= FD::migrators( __FUNCTION__ );
		$installed	= $migrator->isInstalled();
		$version 	= $migrator->getVersion();

		// Fetch available custom fields from CB
		if ($installed) {
			// Get custom fields from JomSocial
			$cbFields 	= $migrator->getCustomFields();

			// Get known field mapping
			$mapping		= $migrator->getFieldsMap();

			// Get our own fields list
			$appsModel		= FD::model( 'Apps' );
			$fields			= $appsModel->getApps(array('type' => SOCIAL_APPS_TYPE_FIELDS, 'group' => SOCIAL_FIELDS_GROUP_USER));

			// Reset the $fields so that the index will be the element type.
			if( $fields )
			{
				$tmp = array();
				foreach( $fields as $field )
				{
					$tmp[ $field->element ] = $field;
				}
				$fields = $tmp;
			}

			// Go through each of the cb fields
			foreach( $cbFields as &$cbField )
			{
				$mapped 	= isset( $mapping[ $cbField->type ] ) ? $mapping[ $cbField->type ] : '';
				$code 		= strtolower( $cbField->name );

				// For gender fields
				if( $mapped && ( $mapped == 'dropdown' || $mapped == 'checkbox' ) && strpos( $code , 'gender' ) !== false )
				{
					$mapped = 'gender';
				}

				// For full name field
				if( $mapped && $mapped == 'textbox' && ( strpos( $code , 'givenname' ) !== false || strpos( $code , 'familyname' ) !== false ) )
				{
					$mapped = 'joomla_fullname';
				}

				if( $mapped && $mapped == 'datetime' && ( strpos( $code , 'birthday' ) !== false || strpos( $code , 'birthdate' ) !== false ) )
				{
					$mapped = 'birthday';
				}

				// address
				if( $mapped && ($mapped == 'textarea' || $mapped == 'textbox')
					&& ( strpos( $code, 'cb_address' ) !== false
						|| strpos( $code, 'cb_street1' ) !== false
						|| strpos( $code, 'cb_street2' ) !== false ) )
				{
					$mapped = 'address';
				}

				if( $mapped && $mapped == 'textbox'
					&& ( strpos( $code, 'cb_state' ) !== false
						|| strpos( $code, 'cb_city' ) !== false
						|| strpos( $code, 'cb_zip' ) !== false ) )
				{
					$mapped = 'address';
				}

				if( $mapped && ($mapped == 'country' || $mapped == 'dropdown')
					&& ( strpos( $code, 'cb_country' ) !== false
						|| strpos( $code, 'cb_state' ) !== false) )
				{
					$mapped = 'address';
				}

				foreach( $fields as &$field )
				{
					$cbField->map_id 		= false;

					if( $mapped )
					{
						$cbField->map_id 	= $fields[ $mapped ]->id;
					}
				}

			}

			$this->set( 'fields'		, $fields );
			$this->set( 'cbFields'		, $cbFields );


			$this->displayPurgeButton('Cb');
		}

		$this->set( 'installed'		, $installed );
		$this->set( 'version'		, $version );

		parent::display( 'admin/migrators/cb' );
	}

	/**
	 * Displays the JomSocial's Group migration form
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function jomsocialgroup()
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS_JOMSOCIAL_GROUP');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MIGRATORS_JOMSOCIAL_GROUP');

		// Get the migrator library
		$migrator 	= FD::migrators( __FUNCTION__ );
		$installed	= $migrator->isInstalled();
		$version 	= $migrator->getVersion();

		if ($installed) {
			$this->displayPurgeButton('JomsocialGroup');
		}


		$this->set( 'installed'		, $installed );
		$this->set( 'version'		, $version );

		parent::display( 'admin/migrators/jomsocialgroup' );
	}

	/**
	 * Displays the JomSocial's event migration form
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function jomsocialevent()
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS_JOMSOCIAL_EVENT');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MIGRATORS_JOMSOCIAL_EVENT');

		// Get the migrator library
		$migrator 	= FD::migrators( __FUNCTION__ );
		$installed	= $migrator->isInstalled();
		$version 	= $migrator->getVersion();

		if ($installed) {
			$this->displayPurgeButton('JomsocialEvent');
		}

		$this->set( 'installed'		, $installed );
		$this->set( 'version'		, $version );

		parent::display( 'admin/migrators/jomsocialevent' );
	}

	/**
	 * Displays the EasyBlog migration form
	 *
	 * @since	1.1
	 * @access	public
	 */
	public function easyblog()
	{
		// Set page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS_EASYBLOG');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MIGRATORS_EASYBLOG');

		// Get the migrator library
		$migrator 	= FD::migrators( __FUNCTION__ );
		$installed	= $migrator->isInstalled();
		// $version 	= $migrator->getVersion();
		//
		if ($installed) {
			$this->displayPurgeButton('Easyblog');
		}

		$this->set( 'installed'		, $installed );
		// $this->set( 'version'		, $version );

		parent::display( 'admin/migrators/easyblog' );
	}

	/**
	 * Displays the Joomla migration form
	 *
	 * @since	1.1
	 * @access	public
	 */
	public function joomla()
	{
		// Set page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS_JOOMLA');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MIGRATORS_JOOMLA');

		// Get the migrator library
		$migrator 	= FD::migrators( __FUNCTION__ );

		$this->displayPurgeButton('Joomla');


		parent::display( 'admin/migrators/joomla' );
	}

	/**
	 * Displays the Kunena migration form
	 *
	 * @since	1.1
	 * @access	public
	 */
	public function kunena()
	{
		// Set page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS_KUNENA');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MIGRATORS_KUNENA');

		// Get the migrator library
		$migrator 	= FD::migrators( __FUNCTION__ );
		$installed	= $migrator->isInstalled();
		// $version 	= $migrator->getVersion();

		$this->set( 'installed'		, $installed );
		// $this->set( 'version'		, $version );
		//
		if ($installed) {
			$this->displayPurgeButton('Kunena');
		}

		parent::display( 'admin/migrators/kunena' );
	}

	/**
	 * Displays the JomSocial's event migration form
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function jomsocialvideo()
	{
		$this->setHeading('COM_EASYSOCIAL_HEADING_MIGRATORS_JOMSOCIAL_VIDEO');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_MIGRATORS_JOMSOCIAL_VIDEO');

		// Get the migrator library
		$migrator 	= FD::migrators( __FUNCTION__ );
		$installed	= $migrator->isInstalled();
		$version 	= $migrator->getVersion();
		$isLocalFiles = true;

		if ($installed) {
			$this->displayPurgeButton('JomsocialVideo');

			// get JomSocial config.
			require_once(JPATH_ROOT . '/components/com_community/libraries/core.php');
			$jsConfig = CFactory::getConfig();

			if ($jsConfig->get('enable_zencoder')) {
				$isLocalFiles = false;
			}
		}

		$this->set( 'installed', $installed );
		$this->set( 'version', $version );
		$this->set( 'isLocalFiles', $isLocalFiles );

		parent::display( 'admin/migrators/jomsocialvideo' );
	}


	public function purgeHistory($type)
	{
		FD::info()->set( $this->getMessage() );
		$this->redirect( 'index.php?option=com_easysocial&view=migrators&layout=' . $type );
	}

	private function displayPurgeButton($type)
	{
		// Add clear cache button here.
		$functionName = 'purge' . $type . 'History';
		$label = 'COM_EASYSOCIAL_TOOLBAR_BUTTON_PURGE_' . strtoupper($type) . '_HISTORY';
		JToolbarHelper::custom($functionName, 'trash', '', JText::_($label), false);
	}

}
