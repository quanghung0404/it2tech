<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import necessary Joomla libraries
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Sample data installation
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplWidgetSample extends JSNTplWidgetBase
{
	/**
	 * Template detailed information
	 * @var array
	 */
	protected $template = array();

	/**
	 * Temporary path where sample data package is extracted to.
	 *
	 * @var  string
	 */
	protected $temporary_path = '';

	/**
	 * Display agreement screen to ensure start sample
	 * data installation process
	 *
	 * @return  void
	 */
	public function confirmAction ()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		// Make sure current template is not out-of-date
		$update = new JSNTplWidgetUpdate;

		$update->checkUpdateAction();

		$update = $update->getResponse();

		// Render confirm view
		$this->render('confirm', array(
			'template'	=> $this->template,
			'update'	=> $update
		));
	}

	/**
	 * Render installation screen to display all steps
	 * we will walking through for install sample data
	 *
	 * @return  void
	 */
	public function installAction ()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$sampleVersion	= substr($sampleVersion = JSNTplHelper::getJoomlaVersion(2, false), 0, 1) == '3' ? '30' : $sampleVersion;
		$fileUrl		= 'http://www.joomlashine.com/joomla-templates/'
						. str_replace('_', '-', $this->template['name']) . '-sample-data-j' . $sampleVersion . '.zip';

		// Render confirm view
		$this->render('install', array(
			'template'	=> $this->template,
			'fileUrl'	=> $fileUrl
		));
	}

	/**
	 * This method will be install sample data from
	 * uploaded package
	 *
	 * @return  void
	 */
	public function uploadInstallAction ()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		try
		{
			// Move uploaded file to temporary folder
			if (isset($_FILES['package']))
			{
				$package = $_FILES['package'];
				$config  = JFactory::getConfig();
				$tmpPath = $config->get('tmp_path');
				$destination = $tmpPath . '/' . $this->template['name'] . '_sampledata.zip';

				if ( ! preg_match('/.zip$/i', $package['name']))
				{
					throw new Exception(JText::_('JSN_TPLFW_ERROR_UPLOAD_SAMPLE_DATA_PACKAGE_TYPE'));
				}

				if (move_uploaded_file($package['tmp_name'], $destination))
				{
					// Import library
					jimport('joomla.filesystem.archive');

					$this->temporary_path = pathinfo($destination, PATHINFO_DIRNAME) . '/' . pathinfo($destination, PATHINFO_FILENAME);

					JPath::clean($this->temporary_path);
					JArchive::extract($destination, $this->temporary_path);

					$this->installDataAction();
				}
			}

			$response = json_encode(array(
				'type' => 'success',
				'data' => $this->getResponse()
			));

			echo "<script type=\"text/javascript\">window.parent.uploadSampleDataCallback({$response})</script>";
			jexit();
		}
		catch (Exception $e)
		{
			$response = json_encode(array(
				'type' => 'error',
				'data' => $e->getMessage()
			));

			echo "<script type=\"text/javascript\">window.parent.uploadSampleDataCallback({$response})</script>";
			jexit();
		}
	}

	/**
	 * Sample data package will be downloaded to temporary
	 * folder in this action
	 *
	 * @return  void
	 */
	public function downloadPackageAction ()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		JSNTplHelper::isDisabledFunction('set_time_limit') OR set_time_limit(0);

		$config = JFactory::getConfig();
		$tmpPath = $config->get('tmp_path');
		$template = JSNTplTemplateRecognization::detect($this->template['name']);
		$fileUrl = 'http://www.joomlashine.com/joomla-templates/jsn-'
				 . strtolower($template->name . '-' . preg_replace('/\s(STANDARD|UNLIMITED)$/', '', $template->edition))
				 . '-sample-data-j' . (substr($version = JSNTplHelper::getJoomlaVersion(2, false), 0, 1) == '3' ? '30' : $version) . '.zip';

		// Download file to temporary folder
		try
		{
			$response = JSNTplHttpRequest::get($fileUrl, $tmpPath . "/{$this->template['name']}_sampledata.zip");
		}
		catch (Exception $e)
		{
			throw $e;
		}

		// Check download response headers
		if ($response['header']['content-type'] != 'application/zip')
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_DOWNLOAD_CANNOT_LOCATED_FILE'));
		}

		$listExtensions = $this->_extractExtensions($tmpPath . "/{$this->template['name']}_sampledata.zip");
		$this->setResponse($listExtensions);
	}

	/**
	 * Action to execute queries from sample data file
	 *
	 * @return  void
	 */
	public function installDataAction ()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		// Disable execution timeout
		if ( ! JSNTplHelper::isDisabledFunction('set_time_limit'))
		{
			set_time_limit(0);
		}

		try
		{
			// Create a backup of Joomla database
			$this->_backupDatabase();

			// Initialize variables
			if ( ! isset( $this->temporary_path ) || empty( $this->temporary_path ) )
			{
				$config  = JFactory::getConfig();

				$this->temporary_path = $config->get('tmp_path') . "/{$this->template['name']}_sampledata.zip";
				$this->temporary_path = pathinfo( $this->temporary_path, PATHINFO_DIRNAME )
					. '/' . pathinfo( $this->temporary_path, PATHINFO_FILENAME );

				JPath::clean( $this->temporary_path );
			}

			$xmlFiles = glob("{$this->temporary_path}/*.xml");

			if (empty($xmlFiles))
			{
				throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_SAMPLE_DATA_PACKAGE'));
			}

			// Load XML document
			$xml		= simplexml_load_file(current($xmlFiles));
			$version	= (string) $xml['version'];
			$joomla_ver	= (string) $xml['joomla-version'];

			// Compare versions
			$templateVersion 	= JSNTplHelper::getTemplateVersion($this->template['name']);
			$joomlaVersion 		= new JVersion;
			$databaseVersion 	= $this->dbo->getVersion();
			$databaseVersion	= preg_match('/[0-9]\.[0-9]+\.[0-9]+/', $databaseVersion, $dbMatch);
			$databaseVersion	= @$dbMatch[0];
			
			if(count($databaseVersion))
			{
				$expDatabaseVersion = explode('.', $databaseVersion);
				if (isset($expDatabaseVersion[2]))
				{
					if ((int) $expDatabaseVersion[2] < 10)
					{
						$expDatabaseVersion[2] = $expDatabaseVersion[2] * 10;
						$databaseVersion = implode('.', $expDatabaseVersion);
					}
				}
			}
			
			if (version_compare($templateVersion, $version, '<'))
			{
				throw new Exception(JText::sprintf('JSN_TPLFW_ERROR_SAMPLE_DATA_OUT_OF_DATED', $templateVersion), 99);
			}

			if ( ! empty($joomla_ver) AND version_compare($joomlaVersion->getShortVersion(), $joomla_ver, '<'))
			{
				throw new Exception(JText::sprintf('JSN_TPLFW_ERROR_JOOMLA_OUT_OF_DATE', $joomlaVersion->getShortVersion()), 99);
			}

			$thirdComponents 		= array();
			$thirdComponentErrors 	= array();
			// Looping to each extension type=component to get information and dependencies
			foreach ($xml->xpath('//extension[@author="3rd_party"]') as $component)
			{
				if (isset($component['author']) && $component['author'] == '3rd_party')
				{
					$attrs				= (array) $component->attributes();
					$attrs				= $attrs['@attributes'];

					$componentType 		= (string) $attrs['type'];
					$namePrefix			= array('component' => 'com_', 'module' => 'mod_');

					$componentName	= isset($namePrefix[(string) $attrs['type']])
					? $namePrefix[$componentType] . $attrs['name']
					: (string) $attrs['name'];

					$state = $this->_getExtensionState($componentName, (string) $attrs['version'], true, (string) $attrs['type']);
					$thirdComponents [] = array('id' => $attrs['name'], 'state' => $state, 'full_name' => (string) $attrs['full_name'], 'version' => (string) $attrs['version'], 'type' => $componentType);
				}

			}

			if (count($thirdComponents))
			{
				foreach ($thirdComponents as $thirdComponent)
				{
					if ($thirdComponent['state'] == 'install')
					{
						$thirdComponentErrors [] = 	JText::sprintf('JSN_TPLFW_ERROR_THIRD_EXTENSION_NOT_INSTALLED', strtoupper($thirdComponent['full_name']) . ' ' . $thirdComponent['type'], $thirdComponent['version']);
					}
					elseif ($thirdComponent['state'] == 'update')
					{
						$thirdComponentErrors [] = 	JText::sprintf('JSN_TPLFW_ERROR_THIRD_EXTENSION_NEED_TO_INSTALLED', strtoupper($thirdComponent['full_name']) . ' ' . $thirdComponent['type'], $thirdComponent['version']);
					}
					elseif($thirdComponent['state'] == 'unsupported')
					{
						$thirdComponentErrors [] = 	JText::sprintf('JSN_TPLFW_ERROR_THIRD_EXTENSION_NOT_SUPPORTED', strtoupper($thirdComponent['full_name']) . ' ' . $thirdComponent['type'], $thirdComponent['version']);
					}
					else
					{
						//do nothing
					}
				}
			}

			if (count($thirdComponentErrors))
			{
				$strThirdComponentError = '<ul>';
				foreach ($thirdComponentErrors as $thirdComponentError)
				{
					$strThirdComponentError .= '<li>' . $thirdComponentError . '</li>';
				}
				$strThirdComponentError .= '</ul>';
				throw new Exception(JText::sprintf('JSN_TPLFW_ERROR_THIRD_EXTENSION', $strThirdComponentError), 99);
			}

			// Temporary backup data
			$this->_backupThirdPartyModules();
			$this->_backupThirdPartyAdminModules();
			$this->_backupThirdPartyMenus();

			// Delete admin modules
			$this->_deleteThirdPartyAdminModules();

			// Loop each extension to execute queries
			$attentions = array();

			foreach ($xml->xpath('//extension') AS $extension)
			{
				// Get sample data queries.
				$queries = $extension->xpath('task[@name="dbinstall"]/parameters/parameter');

				// Verify extension.
				$canInstall    = true;
				$extensionType = (string) $extension['type'];
				$namePrefix    = array('component' => 'com_', 'module' => 'mod_');
				$extensionName = isset($namePrefix[(string) $extension['type']])
					? $namePrefix[$extensionType] . $extension['name']
					: (string) $extension['name'];

				if (isset($extension['author']) && $extension['author'] == 'joomlashine')
				{
					// Check if JoomlaShine extension is installed
					$canInstall = JSNTplHelper::isInstalledExtension($extensionName, $extensionType);

					if ($canInstall == false AND $extensionType == 'component')
					{
						// Add to attention list when extension is not installed
						$attentions[] = array(
							'id'   => (string) $extension['name'],
							'name' => (string) $extension['description'],
							'url'  => (string) $extension['producturl']
						);
					}
				}
				elseif ( $extension['type'] == 'component' )
				{
					// Check if 3rd-party component is installed.
					$canInstall = JSNTplHelper::isInstalledExtension($extensionName);

					if ( ! $canInstall )
					{
						// Add to attention list.
						$attentions[] = array(
							'id'      => ( string ) $extension['name'],
							'name'    => ( string ) $extension['title'],
							'modules' => $extension->xpath('modules/module'),
							'plugins' => $extension->xpath('plugins/plugin'),
							'display' => count( $queries ) ? true : false,
							'version' => JText::sprintf('JSN_TPLFW_SAMPLE_DATA_SUGGEST_SUPPORTED_VERSION', ( string ) $extension['version']),
						);
					}
					else
					{
						// Verify version of the installed 3rd-party component.
						$state = $this->_getExtensionState( $extensionName, ( string ) $extension['version'], true );

						if ( 'update' == $state )
						{
							// Add to attention list.
							$attentions[] = array(
								'id'      => (string) $extension['name'],
								'name'    => (string) $extension['title'],
								'modules' => $extension->xpath('modules/module'),
								'plugins' => $extension->xpath('plugins/plugin'),
								'message' => JText::sprintf(
									'JSN_TPLFW_UPDATE_3RD_PARTY_EXTENSION_FIRST',
									$this->_getExtensionVersion( $extensionName ),
									( string ) $extension['version']
								),
								'display' => count( $queries ) ? true : false,
								'outdate' => true,
							);

							$canInstall = false;
						}
						if ( 'supported' == $state )
						{
							// Add to attention list.
							$attentions[] = array(
									'id'      => (string) $extension['name'],
									'name'    => (string) $extension['title'],
									'modules' => $extension->xpath('modules/module'),
									'plugins' => $extension->xpath('plugins/plugin'),
									'message' => JText::sprintf(
											'JSN_TPLFW_SUPPORTED_3RD_PARTY_EXTENSION_FIRST',
											ucfirst( $extension['name'] ) ,
											$this->_getExtensionVersion( $extensionName ),
											( string ) $extension['version']
											),
									'display' => count( $queries ) ? true : false,
									'supported' => true,
							);
						
							$canInstall = true;
						}
						elseif ( 'unsupported' == $state )
						{
							// Add to attention list.
							$attentions[] = array(
								'id'      => (string) $extension['name'],
								'name'    => (string) $extension['title'],
								'modules' => $extension->xpath('modules/module'),
								'plugins' => $extension->xpath('plugins/plugin'),
								'message' => JText::sprintf(
									'JSN_TPLFW_UNSUPPORTED_3RD_PARTY_EXTENSION_VERSION',
									$this->_getExtensionVersion( $extensionName ),
									( string ) $extension['version']
								),
								'display' => count( $queries ) ? true : false,
								'outdate' => true,
							);

							$canInstall = false;
						}
						else
						{
							// Make sure all required modules are installed also.
							$missing = array();

							foreach ( $extension->xpath('modules/module') as $module )
							{
								if ( ! @is_dir( JPATH_ROOT . '/modules/' . ( string ) $module ) )
								{
									if ( ( string ) $module['title'] != '' )
									{
										$missing[] = ( string ) $module['title'];
									}
								}
							}

							foreach ( $extension->xpath('plugins/plugin') as $plugin )
							{
								if ( ! @is_dir( JPATH_ROOT . '/plugins/' . ( string ) $plugin['group'] . '/' . ( string ) $plugin ) )
								{
									if ( ( string ) $plugin['title'] != '' )
									{
										$missing[] = ( string ) $plugin['title'];
									}
								}
							}

							if ( count( $missing ) )
							{
								// Add to attention list.
								$attentions[] = array(
									'id'      => (string) $extension['name'],
									'name'    => (string) $extension['title'],
									'modules' => $extension->xpath('modules/module'),
									'plugins' => $extension->xpath('plugins/plugin'),
									'missing' => $missing,
									'message' => JText::_('JSN_TPLFW_MISSING_3RD_PARTY_EXTENSION_DEPENDENCIES'),
									'display' => count( $queries ) ? true : false,
								);

								$canInstall = false;
							}
						}
					}
				}

				if ( $canInstall )
				{
					// Execute sample data queries
					foreach ($queries AS $query)
					{
						
						// Find remote assets then download to local system
						if (preg_match_all('#(http://demo.joomlashine.com/[^\s\t\r\n]+/media/joomlashine/)[^\s\t\r\n]+\.(js|css|bmp|gif|ico|jpg|png|svg|ttf|otf|eot|woff)#', $query, $matches, PREG_SET_ORDER))
						{
							foreach ($matches AS $match)
							{
								$keepAsIs = false;

								if ( ! isset($this->mediaFolder))
								{
									// Detect a writable folder to store demo assets
									foreach (array('media', 'cache', 'tmp') AS $folder)
									{
										$folder = JPATH_ROOT . "/{$folder}";

										if (is_dir($folder) AND is_writable($folder) AND JFolder::create("{$folder}/joomlashine"))
										{
											$this->mediaFolder = "{$folder}/joomlashine";

											break;
										}
									}
								}

								if (isset($this->mediaFolder))
								{
									// Generate path to store demo asset
									$mediaFile = str_replace($match[1], "{$this->mediaFolder}/", $match[0]);

									// Download demo asset only once
									if ( ! is_file($mediaFile))
									{
										try
										{
											JSNTplHttpRequest::get($match[0], $mediaFile);
										}
										catch (Exception $e)
										{
											$keepAsIs = true;
										}
									}

									// Alter sample data query
									if ( ! $keepAsIs)
									{
										$query = str_replace($match[0], str_replace(JPATH_ROOT . '/', '', $mediaFile), $query);
									}
								}
							}
						}
						
						if (version_compare($databaseVersion, '5.5.30', '<'))
						{
							$tmpQuery = (string) $query;
							$query = str_replace('utf8mb4_unicode_ci', 'utf8_general_ci', $tmpQuery);
							$query = str_replace('utf8mb4', 'utf8', $query);
						}
						
						
						// Execute query
						$this->dbo->setQuery((string) $query);

						if ( ! $this->dbo->{$this->queryMethod}())
						{
							throw new Exception($this->dbo->getErrorMsg());
						}
					}

					// Update component ID for linked menu items.
					if ( $extension['type'] == 'component' )
					{
						// Get component ID.
						$query = $this->dbo->getQuery( true );

						$query->select( 'extension_id' )->from( '#__extensions' )
							->where( 'type = ' . $query->quote( 'component' ) )
							->where( 'element = ' . $query->quote( $extensionName ) );

						$this->dbo->setQuery( $query );

						$component_id = $this->dbo->loadResult();

						// Update component ID for all menu items that link to this component.
						$query = $this->dbo->getQuery( true );

						$query->update( '#__menu' )->set( 'component_id = ' . $query->quote( $component_id ) )
							->where( 'type = ' . $query->quote( 'component' ) )
							->where( "link LIKE 'index.php?option={$extensionName}&%'" );

						$this->dbo->setQuery( $query );

						if ( ! $this->dbo->{$this->queryMethod}() )
						{
							throw new Exception( $this->dbo->getErrorMsg() );
						}
					}

					// Copy images if has.
					if ( isset( $extension['images'] ) && @is_dir( $this->temporary_path . '/' . ( string ) $extension['images'] ) )
					{
						// Fix move advportfoliopro folder images
						if (( string ) $extension['images'] === 'images/advportfoliopro/images' && !is_dir( JPATH_ROOT . '/' . ( string ) $extension['images'] ))
						{
							JFolder::create(JPATH_ROOT . '/' . ( string ) $extension['images']);
						}
						
						// Backup existing folder.
						JFolder::move(
							JPATH_ROOT . '/' . ( string ) $extension['images'],
							JPATH_ROOT . '/' . ( string ) $extension['images'] . '-backup-at-' . date('y-m-d_H-i-s')
						);

						// Delete current folder.
						JFolder::delete( JPATH_ROOT . '/' . ( string ) $extension['images'] );

						// Move sample images folder.
						JFolder::move(
							$this->temporary_path . '/' . ( string ) $extension['images'],
							JPATH_ROOT . '/' . ( string ) $extension['images']
						);
					}

					// Download and install extended style if has.
					if ( isset( $extension['ext-style-package'] ) )
					{
						// Download extended style package.
						$template = JSNTplTemplateRecognization::detect($this->template['name']);
						$fileUrl  = 'http://www.joomlashine.com/index.php?option=com_lightcart&controller=remoteconnectauthentication'
							. '&task=authenticate&tmpl=component&upgrade=yes&identified_name=ext_style&edition='
							. '&joomla_version=' . JSNTplHelper::getJoomlaVersion(2)
							. '&file_attr='
							. '{"identified_template_name":"tpl_' . strtolower($template->name) . '"'
							. ',"ext_style":"' . ( string ) $extension['ext-style-package'] . '"}';

						// Download file to temporary folder.
						try
						{
							$response = JSNTplHttpRequest::get(
								$fileUrl,
								$this->temporary_path . '/' . ( string ) $extension['ext-style-package'] . '_ext_style.zip'
							);
						}
						catch (Exception $e)
						{
							throw $e;
						}

						// Check download response headers.
						if ($response['header']['content-type'] != 'application/zip')
						{
							throw new Exception(JText::_('JSN_TPLFW_ERROR_DOWNLOAD_CANNOT_LOCATED_FILE'));
						}

						// Install extended style.
						JArchive::extract(
							$this->temporary_path . '/' . ( string ) $extension['ext-style-package'] . '_ext_style.zip',
							JPATH_ROOT . '/' . ( string ) $extension['ext-style-path']
						);

						// Fix for old extended style package of OS Property.
						if ( false !== strpos( ( string ) $extension['ext-style-path'], '/com_osproperty/' ) )
						{
							$template = 'jsn_' . strtolower($template->name);

							if ( @file_exists( JPATH_ROOT . '/' . ( string ) $extension['ext-style-path'] . "/{$template}/template.xml" ) )
							{
								JFile::copy(
									JPATH_ROOT . '/' . ( string ) $extension['ext-style-path'] . "/{$template}/template.xml",
									JPATH_ROOT . '/' . ( string ) $extension['ext-style-path'] . "/{$template}/{$template}.xml"
								);
							}
						}
					}

					// Manipulate data for K2.
					if ( 'com_k2' == $extensionName )
					{
						// Update user mapping for K2 items table.
						$user  = JFactory::getUser();
						$query = $this->dbo->getQuery( true );

						$query->update( '#__k2_items' )->set( 'created_by = ' . $query->quote( $user->id ) );

						$this->dbo->setQuery( $query );

						if ( ! $this->dbo->{$this->queryMethod}() )
						{
							throw new Exception( $this->dbo->getErrorMsg() );
						}

						// Update user mapping for K2 users table.
						$query = $this->dbo->getQuery( true );

						$query->update( '#__k2_users' )->set( 'userID = ' . $query->quote( $user->id ) );

						$this->dbo->setQuery( $query );

						if ( ! $this->dbo->{$this->queryMethod}() )
						{
							throw new Exception( $this->dbo->getErrorMsg() );
						}
						
						// Update user mapping for K2 comments table.
						$query = $this->dbo->getQuery( true );
						
						$query->update( '#__k2_comments' )->set( 'userID = ' . $query->quote( $user->id ) );
						
						$this->dbo->setQuery( $query );
						
						if ( ! $this->dbo->{$this->queryMethod}() )
						{
							throw new Exception( $this->dbo->getErrorMsg() );
						}
						
						// Update user mapping for K2 items table.
						$query = $this->dbo->getQuery( true );
						
						$query->update( '#__k2_items' )->set( 'modified_by = ' . $query->quote( $user->id ) );
						
						$this->dbo->setQuery( $query );
						
						if ( ! $this->dbo->{$this->queryMethod}() )
						{
							throw new Exception( $this->dbo->getErrorMsg() );
						}
					}

					// Manipulate data for OS Property.
					if ( 'com_osproperty' == $extensionName )
					{
						// Update user mapping for agents table.
						$user  = JFactory::getUser();
						$query = $this->dbo->getQuery( true );

						$query->update( '#__osrs_agents' )->set( 'user_id = ' . $query->quote( $user->id ) );

						$this->dbo->setQuery( $query );

						if ( ! $this->dbo->{$this->queryMethod}() )
						{
							throw new Exception( $this->dbo->getErrorMsg() );
						}
					}

					// Unpublish menu item replacement published before.
					if ( isset( $extension['menu-replacement'] ) )
					{
						$items = array_map( 'intval', explode( ',', ( string ) $extension['menu-replacement'] ) );
						$query = $this->dbo->getQuery(true);

						$query->update('#__menu')->set('published = 0')
							->where('id IN (' . implode( ', ', $items ) . ')' )
							->where('published = 1');

						$this->dbo->setQuery($query);

						if ( ! $this->dbo->{$this->queryMethod}())
						{
							throw new Exception($this->dbo->getErrorMsg());
						}
					}

					// Unpublish module replacement published before.
					if ( isset( $extension['module-replacement'] ) )
					{
						$items = array_map( 'intval', explode( ',', ( string ) $extension['module-replacement'] ) );
						$query = $this->dbo->getQuery(true);

						$query->update('#__modules')->set('published = 0')
							->where('id IN (' . implode( ', ', $items ) . ')' )
							->where('published = 1');

						$this->dbo->setQuery($query);

						if ( ! $this->dbo->{$this->queryMethod}())
						{
							throw new Exception($this->dbo->getErrorMsg());
						}
					}
				}
				else
				{
					// Check if sample data contains menu item replacement for use when extension is missing.
					if ( isset( $extension['menu-replacement'] ) )
					{
						if ( isset( $menu_replacement ) )
						{
							$menu_replacement = array_merge(
								$menu_replacement,
								array_map( 'intval', explode( ',', ( string ) $extension['menu-replacement'] ) )
							);
						}
						else
						{
							$menu_replacement = array_map( 'intval', explode( ',', ( string ) $extension['menu-replacement'] ) );
						}
					}

					// Check if sample data contains module replacement for use when extension is missing.
					if ( isset( $extension['module-replacement'] ) )
					{
						if ( isset( $module_replacement ) )
						{
							$module_replacement = array_merge(
								$module_replacement,
								array_map( 'intval', explode( ',', ( string ) $extension['module-replacement'] ) )
							);
						}
						else
						{
							$module_replacement = array_map( 'intval', explode( ',', ( string ) $extension['module-replacement'] ) );
						}
					}
				}
			}

			// Disable default template
			$query = $this->dbo->getQuery(true);

			$query->update('#__template_styles');
			$query->set('home = 0');
			$query->where('client_id = 0');
			$query->where('home = 1');

			$this->dbo->setQuery($query);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}

			// Set installed template the default one
			$query = $this->dbo->getQuery(true);

			$query->update('#__template_styles');
			$query->set('home = 1');
			$query->where('id = ' . (int) $this->request->getInt('styleId'));

			$this->dbo->setQuery($query);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}

			// Publish menu item replacement.
			if ( isset( $menu_replacement ) )
			{
				$query = $this->dbo->getQuery(true);

				$query->update('#__menu')->set('published = 1')->where('id IN (' . implode( ', ', $menu_replacement ) . ')' );

				$this->dbo->setQuery($query);

				if ( ! $this->dbo->{$this->queryMethod}())
				{
					throw new Exception($this->dbo->getErrorMsg());
				}
			}

			// Publish module replacement.
			if ( isset( $module_replacement ) )
			{
				$query = $this->dbo->getQuery(true);

				$query->update('#__modules')->set('published = 1')->where('id IN (' . implode( ', ', $module_replacement ) . ')' );

				$this->dbo->setQuery($query);

				if ( ! $this->dbo->{$this->queryMethod}())
				{
					throw new Exception($this->dbo->getErrorMsg());
				}
			}
			
			// Update all created_by and modified_by by userId installing 
			$user  = JFactory::getUser();
			$query = $this->dbo->getQuery(true);
		
			$query->update('#__content');
			$query->set('created_by = ' . $user->id);
			$query->set('modified_by = ' . $user->id);
			
			$this->dbo->setQuery($query);
		
			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}
			
			// Update all created_user_id and modified_user_id by userId installing in categories table
			$query = $this->dbo->getQuery(true);
			$query->update('#__categories');
			$query->set('created_user_id = ' . $user->id);
			$query->set('modified_user_id = ' . $user->id);
				
			$this->dbo->setQuery($query);
			
			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}
			
			// Update current template style ID for megamenu
			$query = $this->dbo->getQuery(true);
			
			$query->update('#__jsn_tplframework_megamenu');
			$query->set('style_id = ' . (int) $this->request->getInt('styleId'));
						
			$this->dbo->setQuery($query);
			
			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}
			
		}
		catch (Exception $e)
		{
			$error = $e;
		}

		// Restore backed up data
		$this->_restoreThirdPartyData();
		$this->_rebuildMenus();
		
		//Truncate #__advancedmodules Table
		$this->_truncateAdvancedmodulesTable();
		
		// Clean up temporary data
		JInstallerHelper::cleanupInstall("{$this->temporary_path}.zip", $this->temporary_path);

		// Clean up junk data for extension that is not installed
		if (count($attentions))
		{
			foreach ($attentions AS $i => $attention)
			{
				// Clean up junk data imported during sample data installation.
				$this->_cleanJunkData(
					'com_' . $attention['id'],
					isset( $attention['modules'] ) ? $attention['modules'] : null,
					isset( $attention['plugins'] ) ? $attention['plugins'] : null
				);
				
				// Clean up menu data if component out date.
				if (isset($attention['outdate']))
				{
					$this->_cleanJunkMenuData('com_' . $attention['id']);
				}
				// Make sure extension has name defined.
				if ( ! isset( $attention['name'] ) || empty( $attention['name'] ) )
				{
					unset( $attentions[ $i ] );
				}
				elseif ( isset( $attention['display'] ) && ! $attention['display'] )
				{
					unset( $attentions[ $i ] );
				}
				else
				{
					// Remove data that are not necessary any more.
					if ( isset( $attention['modules'] ) )
						unset( $attentions[ $i ]['modules'] );

					if ( isset( $attention['plugins'] ) )
						unset( $attentions[ $i ]['plugins'] );
				}
			}
		}

		// Check if there is any error catched?
		if ( isset( $error ) )
		{
			throw $error;
		}

		// Set final response
		$this->setResponse(array('attention' => array_values($attentions)));
	}

	/**
	 * Action to handle install extension request.
	 *
	 * @param   string  $id  Identified name of the extension to be installed.
	 *
	 * @return  void
	 */
	public function installExtensionAction($id = null)
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		JSNTplHelper::isDisabledFunction('set_time_limit') OR set_time_limit(0);

		// Get necessary variables
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		$tmpPath = $config->get('tmp_path');

		if (empty($id))
		{
			$id = $this->request->getString('id');
		}

		// Disable debug system
		$config->set('debug', 0);

		// Path to sample data file
		$xmlFiles = glob("{$tmpPath}/{$this->template['name']}_sampledata/*.xml");

		if (empty($xmlFiles))
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_SAMPLE_DATA_PACKAGE'));
		}

		// Load XML document
		$xml = simplexml_load_file(current($xmlFiles));
		$extensions = $xml->xpath("//extension[@identifiedname=\"{$id}\"]");

		if ( ! empty($extensions))
		{
			$extension = current($extensions);
			$name = (string) $extension['name'];
			$type = (string) $extension['type'];

			switch ($type)
			{
				case 'component':
					$name = 'com_' . $name;
				break;

				case 'module':
					$name = 'mod_' . $name;
				break;
			}

			$this->_cleanJunkData($name);

			// Install JSN Extension Framework first if not already installed
			if ($type == 'component')
			{
				// Get details about JSN Extension Framework
				$extfw = $xml->xpath('//extension[@identifiedname="ext_framework"]');

				if ( ! empty($extfw))
				{
					$extfw = current($extfw);

					if ($this->_getExtensionState((string) $extfw['name'], (string) $extfw['version'], false, 'plugin-system') != 'installed')
					{
						// Install JSN Extension Framework
						try
						{
							$this->installExtensionAction('ext_framework');
						}
						catch (Exception $e)
						{
							throw $e;
						}
					}
				}
			}
		}

		// Download package from lightcart
		try
		{
			$packageFile = JSNTplApiLightcart::downloadPackage($id, 'FREE', null, null, "{$tmpPath}/{$this->template['name']}_sampledata/");
		}
		catch (Exception $e)
		{
			throw $e;
		}

		if ( ! is_file($packageFile))
		{
			throw new Exception("Package file not found: {$packageFile}");
		}

		// Load extension installation library
		jimport('joomla.installer.helper');

		// Rebuild menu structure
		$this->_rebuildMenus();

		// Extract downloaded package
		$unpackedInfo = JInstallerHelper::unpack($packageFile);
		$installer = JInstaller::getInstance();

		if (empty($unpackedInfo) OR ! isset($unpackedInfo['dir']))
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_EXTENSION_PACKAGE_FILE'));
		}

		// Install extracted package
		$installResult = $installer->install($unpackedInfo['dir']);

		if ($installResult === false)
		{
			foreach (JError::getErrors() AS $error)
			{
				throw $error;
			}
		}

		// Clean up temporary data
		JInstallerHelper::cleanupInstall($packageFile, $unpackedInfo['dir']);

		$this->_activeExtension(
			array(
				'type' => $type,
				'name' => $name
			)
		);

		// Rebuild menu structure
		$this->_rebuildMenus();
	}

	/**
	 * Action to clean files & database for install failure extension
	 *
	 * @return  void
	 */
	public function cleanUpAction ()
	{
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$id = $this->request->getString('id');

		// Retrieve temporary path
		$config		= JFactory::getConfig();
		$tmpPath	= $config->get('tmp_path');

		// Path to sample data file
		$xmlFiles	= glob("{$tmpPath}/{$this->template['name']}_sampledata/*.xml");

		if (empty($xmlFiles))
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_SAMPLE_DATA_PACKAGE'));

		// Load XML document
		$xml = simplexml_load_file(current($xmlFiles));

		// Retrieve extension information
		$extensions = $xml->xpath("//extension[@identifiedname=\"{$id}\"]");

		if (empty($extensions)) {
			return;
		}

		$extension = current($extensions);
	}

	/**
	 * Auto enable extension after installed
	 *
	 * @param   array  $extension  Extension information that will enabled
	 *
	 * @return  void
	 */
	private function _activeExtension ($extension)
	{
		$namePrefix		= array('component' => 'com_', 'module' => 'mod_', 'plugin' => '');
		$extensionName	= $extension['name'];

		if (isset($namePrefix[$extension['type']]))
		{
			$extensionName = $namePrefix[$extension['type']] . $extension['name'];
		}

		$extensionFolder = '';

		if (preg_match('/^plugin-([a-z0-9]+)$/i', $extension['type'], $matched))
		{
			$extensionFolder = $matched[1];
		}

		$q = $this->dbo->getQuery(true);

		$q->update('#__extensions');
		$q->set('enabled = 1');
		$q->where('element = ' . $q->quote($extensionName));
		$q->where('folder = ' . $q->quote($extensionFolder));

		$this->dbo->setQuery($q);

		if ( ! $this->dbo->{$this->queryMethod}())
		{
			throw new Exception($this->dbo->getErrorMsg());
		}
	}

	/**
	 * Parse extension list can installation from sample data
	 * package
	 *
	 * @param   string  $packageFile  Sample data package
	 * @return  array
	 */
	private function _extractExtensions ($packageFile)
	{
		// Import library
		jimport('joomla.filesystem.archive');

		$this->temporary_path = pathinfo($packageFile, PATHINFO_DIRNAME) . '/' . pathinfo($packageFile, PATHINFO_FILENAME);

		JPath::clean($this->temporary_path);
		JArchive::extract($packageFile, $this->temporary_path);

		// Find extracted files
		$sampleDataFiles = glob("{$this->temporary_path}/*.xml");

		if ( ! is_array($sampleDataFiles) OR count($sampleDataFiles) == 0)
		{
			throw new Exception(JText::_('JSN_TPLFW_ERROR_CANNOT_EXTRACT_SAMPLE_DATA_PACKAGE'));
		}

		// Load XML file
		$sampleData	= simplexml_load_file(current($sampleDataFiles));
		$components = array();

		// Looping to each extension type=component to get information and dependencies
		foreach ($sampleData->xpath('//extension[@author="joomlashine"][@type="component"]') AS $component)
		{
			$attrs				= (array) $component->attributes();
			$attrs				= $attrs['@attributes'];
			$attrs['name']		= sprintf('com_%s', $attrs['name']);
			$attrs['state']		= $this->_getExtensionState($attrs['name'], $attrs['version']);
			$attrs['depends']	= array();

			foreach ($component->dependency->parameter AS $name)
			{
				$dependency = $sampleData->xpath("//extension[@name=\"{$name}\"]");

				if ($name == 'jsnframework' OR empty($dependency))
				{
					continue;
				}

				$dependency					= current($dependency);
				$dependencyAttrs			= (array) $dependency->attributes();
				$dependencyAttrs			= $dependencyAttrs['@attributes'];
				$dependencyAttrs['state']	= $this->_getExtensionState($dependencyAttrs['name'], $dependencyAttrs['version'], false, 'plugin');

				if ($dependencyAttrs['type'] == 'module')
				{
					$dependencyAttrs['name'] = sprintf('mod_%s', $dependencyAttrs['name']);
				}

				$attrs['depends'][] = $dependencyAttrs;
			}

			$components[] = $attrs;
		}

		return $components;
	}

	/**
	 * Determine installation state of an extension.
	 *
	 * - Return "install"   if extension does not installed.
	 * - Return "update"    if extension is installed but outdated.
	 * - Return "installed" if extension is installed and up to date.
	 *
	 * @param   string  $name          The name of extension.
	 * @param   string  $version       Version number that used to determine state.
	 * @param   string  $isThirdParty  Whether this is a 3rd-party extension.
	 * @param   string  $type          Either 'component', 'module' or 'plugin'.
	 *
	 * @return  string
	 */
	private function _getExtensionState( $name, $version, $isThirdParty = false, $type = 'component' )
	{
		$installedExtensions = JSNTplHelper::findInstalledExtensions();

		if ( 'plugin' == $type )
		{
			// Find first plugin that matchs the given name.
			foreach ( $installedExtensions as $_type => $exts )
			{
				if ( 0 === strpos( $_type, 'plugin' ) && isset( $installedExtensions[ $_type ][ $name ] ) )
				{
					$installedExtension = $installedExtensions[ $_type ][ $name ];
				}
			}
		}
		elseif ( isset( $installedExtensions[ $type ][ $name ] ) )
		{
			$installedExtension = $installedExtensions[ $type ][ $name ];
		}

		if ( ! isset( $installedExtension ) )
		{
			return 'install';
		}
		
		if (!$isThirdParty)
		{
			if ( version_compare( $installedExtension->version, $version, '<' ) )
			{
				return 'update';
			}
		}
		else 
		{
			$state = '';
			if ( version_compare( $installedExtension->version, $version, '=' ) )
			{
				return 'installed';
			}
			else 
			{
				$state = 'supported';
			}
			
			$installedVersion = explode('.', $installedExtension->version);
			$installedVersion = $installedVersion[0] . '.' . $installedVersion[1];
			
			$supportedVersion = explode('.', $version);
			$supportedVersion = $supportedVersion[0] . '.' . $supportedVersion[1];
			
			if ( version_compare( $installedVersion, $supportedVersion, '<' ) )
			{
				$state = 'update';
			}
				

			if ( version_compare( $installedVersion, $supportedVersion, '>' ) )
			{
				$state = 'unsupported';
			}
			return $state;
			
		}
		return 'installed';
	}

	/**
	 * Get version of installed extension
	 *
	 * @param   string  $name  The name of extension.
	 *
	 * @return  string
	 */
	private function _getExtensionVersion( $name, $type = 'component' )
	{
		$installedExtensions = JSNTplHelper::findInstalledExtensions();
		
		if ( ! isset( $installedExtensions [ $type ][ $name ] ) )
			return null;
		
		return $installedExtensions [ $type ][ $name ]->version;
	}

	/**
	 * Backup data for third party extensions
	 * before install sample data
	 *
	 * @return void
	 */
	private function _backupThirdPartyModules ()
	{
		$builtInModules = array(
			'mod_login', 'mod_stats', 'mod_users_latest',
			'mod_footer', 'mod_stats', 'mod_menu', 'mod_articles_latest', 'mod_languages', 'mod_articles_category',
			'mod_whosonline', 'mod_articles_popular', 'mod_articles_archive', 'mod_articles_categories',
			'mod_articles_news', 'mod_related_items', 'mod_search', 'mod_random_image', 'mod_banners',
			'mod_wrapper', 'mod_feed', 'mod_breadcrumbs', 'mod_syndicate', 'mod_custom', 'mod_weblinks'
		);

		$query = $this->dbo->getQuery(true);
		$query->select('*')
			->from('#__modules')
			->where(sprintf('module NOT IN (\'%s\')', implode('\', \'', $builtInModules)))
			->where('id NOT IN (2, 3, 4, 6, 7, 8, 9, 10, 12, 13, 14, 15, 70)')
			->order('client_id ASC');
		$this->dbo->setQuery($query);
		$this->temporaryModules = $this->dbo->loadAssocList();
	}

	/**
	 * Backup menu assignment for 3rd-party admin modules before install sample data.
	 *
	 * @return void
	 */
	private function _backupThirdPartyAdminModules ()
	{
		$builtInModules = array(
			'mod_login', 'mod_stats', 'mod_users_latest',
			'mod_footer', 'mod_stats', 'mod_menu', 'mod_articles_latest', 'mod_languages', 'mod_articles_category',
			'mod_whosonline', 'mod_articles_popular', 'mod_articles_archive', 'mod_articles_categories',
			'mod_articles_news', 'mod_related_items', 'mod_search', 'mod_random_image', 'mod_banners',
			'mod_wrapper', 'mod_feed', 'mod_breadcrumbs', 'mod_syndicate', 'mod_custom', 'mod_weblinks'
		);

		$query = $this->dbo->getQuery(true);

		$query->select('id');
		$query->from('#__modules');
		$query->where('module NOT IN ("' . implode('", "', $builtInModules) . '")');
		$query->where('client_id = 1');

		$this->dbo->setQuery($query);

		if ($results = $this->dbo->loadColumn())
		{
			$query = $this->dbo->getQuery(true);

			$query->select('*');
			$query->from('#__modules_menu');
			$query->where('moduleid IN ("' . implode('", "', $results) . '")');

			$this->dbo->setQuery($query);

			$this->temporaryAdminModules = $this->dbo->loadAssocList();
		}
	}

	/**
	 * Backup menus data for third party extensions
	 *
	 * @return  void
	 */
	private function _backupThirdPartyMenus ()
	{
		$query = $this->dbo->getQuery(true);
		$query->select('*')
			->from('#__menu')
			->where('client_id=1')
			->where('parent_id=1')
			->order('id ASC');

		$this->dbo->setQuery($query);
		$this->temporaryMenus = array();

		foreach ($this->dbo->loadAssocList() as $row)
		{
			// Fetch children menus
			$query = $this->dbo->getQuery(true);
			$query->select('*')
				->from('#__menu')
				->where('client_id=1')
				->where('parent_id=' . $row['id'])
				->order('lft'); // Add order to correctly insert back those menu items with order later on

			$this->dbo->setQuery($query);
			$childrenMenus = $this->dbo->loadAssocList();

			// Save temporary menus data
			$this->temporaryMenus[] = array(
				'data' => $row,
				'children' => $childrenMenus
			);
		}
	}

	/**
	 * Remove all third party modules in administrator
	 *
	 * @return void
	 */
	private function _deleteThirdPartyAdminModules ()
	{
		$q = $this->dbo->getQuery(true);

		$q->delete('#__modules');
		$q->where('id NOT IN (2, 3, 4, 8, 9, 10, 12, 13, 14, 15)');
		$q->where('client_id = 1');

		$this->dbo->setQuery($q);

		if ( ! $this->dbo->{$this->queryMethod}())
		{
			throw new Exception($this->dbo->getErrorMsg());
		}
	}

	/**
	 * Restore data for third party extensions
	 * after install sample data
	 *
	 * @return void
	 */
	private function _restoreThirdPartyData()
	{
		// Preset an array to hold module id mapping
		$moduleIdMapping = array();

		// Restore 3rd-party modules
		foreach ($this->temporaryModules AS $module)
		{
			// Store old module id
			$oldModuleId = $module['id'];

			// Unset old module id to create new record
			unset($module['id']);

			$tblModule = JTable::getInstance('module');
			$tblModule->bind($module);

			// Disable all restored front-end modules
			$tblModule->client_id == 1 OR $tblModule->published = 0;

			if ( ! $tblModule->store())
			{
				throw new Exception($tblModule->getDbo()->getErrorMsg());
			}

			// Map new id to old module id
			$moduleIdMapping[$oldModuleId] = isset($tblModule->id) ? $tblModule->id : $this->dbo->insertid();
		}

		// Restore menu assignment for 3rd-party admin modules
		foreach ($this->temporaryAdminModules AS $module)
		{
			if (isset($moduleIdMapping[$module['moduleid']]))
			{
				$q = $this->dbo->getQuery(true);

				$q->insert('#__modules_menu');
				$q->columns('moduleid, menuid');
				$q->values($moduleIdMapping[$module['moduleid']] . ', ' . $module['menuid']);

				$this->dbo->setQuery($q);

				try
				{
					$this->dbo->{$this->queryMethod}();
				}
				catch (Exception $e)
				{
					// Do nothing
				}
			}
		}

		// Restore administrator menu
		foreach ($this->temporaryMenus as $menu)
		{
			unset($menu['data']['id']);

			$mainmenu = JTable::getInstance('menu');
			$mainmenu->setLocation(1, 'last-child');
			$mainmenu->bind($menu['data']);

			if ( ! $mainmenu->store())
			{
				throw new Exception($mainmenu->getDbo()->getErrorMsg());
			}

			if ( ! empty($menu['children']))
			{
				foreach ($menu['children'] AS $children)
				{
					$children['id'] = null;
					$children['parent_id'] = $mainmenu->id;

					$submenu = JTable::getInstance('menu');
					$submenu->setLocation($mainmenu->id, 'last-child');
					$submenu->bind($children);

					if ( ! $submenu->store())
					{
						throw new Exception($submenu->getDbo()->getErrorMsg());
					}
				}
			}
		}
	}

	/**
	 * Rebuild menu structure
	 *
	 * @return boolean
	 */
	private function _rebuildMenus ()
	{
		$table 	= JTable::getInstance('Menu', 'JTable');

		if (!$table->rebuild())
			throw new Exception($table->getDbo()->getErrorMsg());

		$query = $this->dbo->getQuery(true);
		$query->select('id, params')
			->from('#__menu')
			->where('params NOT LIKE ' . $this->dbo->quote('{%'))
			->where('params <> ' . $this->dbo->quote(''));

		$this->dbo->setQuery($query);
		$items = $this->dbo->loadObjectList();

		if ($error = $this->dbo->getErrorMsg())
			throw new Exception($error);

		foreach ($items as &$item)
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);

			$q = $this->dbo->getQuery(true);

			$q->update('#__menu');
			$q->set('params = ' . $q->quote((string) $registry));
			$q->where('id = ' . (int) $item->id);

			$this->dbo->setQuery($q);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}

			unset($registry);
		}

		// Clean the cache
		$this->_cleanCache('com_modules');
		$this->_cleanCache('mod_menu');

		return true;
	}

	/**
	 * Clean up junk data related to the missing component.
	 *
	 * @param   string  $name     The component name.
	 * @param   array   $modules  Additional modules to be removed.
	 * @param   array   $plugins  Additional plugins to be removed.
	 *
	 * @return  void
	 */
	private function _cleanJunkData ($name, $modules = null, $plugins = null)
	{
		// Only clean-up junk data if component is really missing.
		if ( ! JSNTplHelper::isInstalledExtension( $name ) )
		{
			// Get all menu items associated with the missing component.
			$q = $this->dbo->getQuery(true);

			$q->select('id')->from('#__menu')->where("type = 'component'");
			$q->where("link LIKE '%option=" . $name . "%'");

			$this->dbo->setQuery($q);

			$items = $this->dbo->loadColumn();

			if ( count( $items ) )
			{
				// Get all modules associated with all menu items of the missing component.
				$q = $this->dbo->getQuery(true);

				$q->select('moduleid')->from('#__modules_menu')->where('menuid IN (' . implode(', ', $items) . ')');

				$this->dbo->setQuery($q);

				$mods = $this->dbo->loadColumn();

				// Clean up menu table.
				$q = $this->dbo->getQuery(true);

				$q->delete('#__menu')->where('id IN (' . implode(', ', $items) . ')');

				$this->dbo->setQuery($q);

				if ( ! $this->dbo->{$this->queryMethod}())
				{
					throw new Exception($this->dbo->getErrorMsg());
				}

				// Clean up menu item alias also.
				$q = $this->dbo->getQuery(true);

				$q->delete('#__menu')
					->where("type = 'alias'")
					->where('(params LIKE \'%"aliasoptions":"' . implode('"%\' OR params LIKE \'%"aliasoptions":"', $items) . '"%\')');

				$this->dbo->setQuery($q);

				if ( ! $this->dbo->{$this->queryMethod}())
				{
					throw new Exception($this->dbo->getErrorMsg());
				}

				// Clean up module menu mapping table.
				$q = $this->dbo->getQuery(true);

				$q->delete('#__modules_menu')->where('menuid IN (' . implode(', ', $items) . ')');

				$this->dbo->setQuery($q);

				if ( ! $this->dbo->{$this->queryMethod}())
				{
					throw new Exception($this->dbo->getErrorMsg());
				}
			}

			if ( isset( $mods ) && count( $mods ) )
			{
				// Make sure queried modules does not associate with menu items of other component.
				$q = $this->dbo->getQuery(true);

				$q->select('moduleid')->from('#__modules_menu')->where('moduleid IN (' . implode(', ', $mods) . ')');

				$this->dbo->setQuery($q);

				if ($items = $this->dbo->loadColumn())
				{
					$mods = array_diff($mods, $items);
				}

				// Clean up modules table.
				if ( count( $mods ) )
				{
					$q = $this->dbo->getQuery(true);

					$q->delete('#__modules')->where('id IN (' . implode(', ', $mods) . ')');

					$this->dbo->setQuery($q);

					if ( ! $this->dbo->{$this->queryMethod}())
					{
						throw new Exception($this->dbo->getErrorMsg());
					}
				}
			}

			// Clean up modules associated with the missing component but not associated with its menu items.
			$q = $this->dbo->getQuery(true);

			$q->delete('#__modules')->where("params LIKE '%\"moduleclass_sfx\":\"%jsn-demo-module-for-{$name}\"%'");

			$this->dbo->setQuery($q);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}

			// Clean up assets table.
			$q = $this->dbo->getQuery(true);

			$q->delete('#__assets')->where('name = ' . $q->quote($name));

			$this->dbo->setQuery($q);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}

			// Clean up extensions table.
			$q = $this->dbo->getQuery(true);

			$q->delete('#__extensions')->where('element = ' . $q->quote($name));

			$this->dbo->setQuery($q);

			if ( ! $this->dbo->{$this->queryMethod}())
			{
				throw new Exception($this->dbo->getErrorMsg());
			}
		}

		// Clean up additional modules if specified.
		if ( $modules && @count( $modules ) )
		{
			foreach ( $modules as $module )
			{
				// Only clean-up junk data if module is really missing.
				if ( ! @is_dir( JPATH_ROOT . '/modules/' . ( string ) $module ) )
				{
					// Clean up modules table.
					$q = $this->dbo->getQuery(true);

					$q->delete('#__modules')->where( 'module = ' . $q->quote( ( string ) $module ) );

					$this->dbo->setQuery($q);

					if ( ! $this->dbo->{$this->queryMethod}())
					{
						throw new Exception($this->dbo->getErrorMsg());
					}

					// Clean up extensions table.
					$q = $this->dbo->getQuery(true);

					$q->delete('#__extensions')->where("type = 'module'");
					$q->where( 'element = ' . $q->quote( ( string ) $module ) );

					$this->dbo->setQuery($q);

					if ( ! $this->dbo->{$this->queryMethod}())
					{
						throw new Exception($this->dbo->getErrorMsg());
					}
				}
			}
		}

		// Clean up additional plugins if specified.
		if ( $plugins && @count( $plugins ) )
		{
			foreach ( $plugins as $plugin )
			{
				// Only clean-up junk data if plugin is really missing.
				if ( ! @is_dir( JPATH_ROOT . '/plugins/' . ( string ) $plugin['group'] . '/' . ( string ) $plugin ) )
				{
					// Clean up extensions table.
					$q = $this->dbo->getQuery(true);

					$q->delete('#__extensions')->where("type = 'plugin'");
					$q->where('folder = ' . $q->quote((string) $plugin['group']));
					$q->where('element = ' . $q->quote((string) $plugin));

					$this->dbo->setQuery($q);

					if ( ! $this->dbo->{$this->queryMethod}())
					{
						throw new Exception($this->dbo->getErrorMsg());
					}
				}
			}
		}
	}

	/**
	 * Clean cache data for an extension
	 *
	 * @param   string  $extension  Name of extension to clean cache
	 * @return  void
	 */
	private function _cleanCache ($extension)
	{
		$conf = JFactory::getConfig();
		$options = array(
			'defaultgroup' 	=> $extension,
			'cachebase'		=> $conf->get('cache_path', JPATH_SITE . '/cache')
		);

		jimport('joomla.cache.cache');

		$cache = JCache::getInstance('callback', $options);
		$cache->clean();
	}

	/**
	 * Method to backup current Joomla database
	 *
	 * @return  void
	 */
	private function _backupDatabase()
	{
		// Get Joomla config
		$config = JFactory::getConfig();

		// Preset backup buffer
		$buffer = '';

		// Generate file path to write SQL backup
		$file = $config->get('tmp_path') . '/' . $this->template['name'] . '_original_site_data.sql';
		$numb = 1;

		// Get object for working with Joomla database
		$db = JFactory::getDbo();

		// Get all tables in Joomla database
		$tables = $db->getTableList();

		// Loop thru all tables to backup table structure and data
		foreach ($tables AS $table)
		{
			// Create drop table statement
			$buffer .= (empty($buffer) ? '' : "\n\n") . "DROP TABLE IF EXISTS `{$table}`;";

			// Re-create create table statement
			$create = $db->getTableCreate($table);

			$buffer .= "\n" . array_shift($create) . ';';

			// Get all table columns
			$columns = '`' . implode('`, `', array_keys($db->getTableColumns($table, false))) . '`';

			// Get the number of data row in this table
			$q = $db->getQuery(true);

			$q->select('COUNT(*)');
			$q->from($table);
			$q->where('1');

			$db->setQuery($q);

			if ($max = (int) $db->loadResult())
			{
				for ($offset = 0, $limit = 50; $max - $offset > 0; $offset += $limit)
				{
					// Query for all table data
					$q = $db->getQuery(true);

					$q->select('*');
					$q->from($table);
					$q->where('1');

					$db->setQuery($q, $offset, $limit);

					if ($rows = $db->loadRowList())
					{
						$data = array();

						foreach ($rows AS $row)
						{
							$tmp = array();

							// Prepare data for creating insert statement for each row
							foreach ($row AS $value)
							{
								$tmp[] = $db->quote($value);
							}

							$data[] = implode(', ', $tmp);
						}

						// Create insert statement for fetched rows
						$q2 = $db->getQuery(true);

						$q2->insert($table);
						$q2->columns($columns);
						$q2->values($data);

						// Store insert statement
						$insert = "\n" . str_replace('),(', "),\n(", (string) $q2) . ';';

						// Write generated SQL statements to file if reached 2MB limit
						if (strlen($buffer) + strlen($insert) > 2097152)
						{
							if ( ! JFile::write($file, $buffer))
							{
								throw new Exception(JText::_('JSN_TPLFW_CANNOT_CREATE_BACKUP_FILE'));
							}

							// Rename current backup file if neccessary
							if ($numb == 1)
							{
								JFile::move($file, substr($file, 0, -4) . '.01.sql');
							}

							// Increase number of backup file
							$numb++;

							// Generate new backup file name
							$file = $config->get('tmp_path') . '/' . $this->template['name'] . '_original_site_data.' . ($numb < 10 ? '0' : '') . $numb . '.sql';

							// Reset backup buffer
							$buffer = trim($insert);
						}
						else
						{
							$buffer .= $insert;
						}
					}
					else
					{
						break;
					}
				}
			}
		}

		if ( ! JFile::write($file, $buffer))
		{
			throw new Exception(JText::_('JSN_TPLFW_CANNOT_CREATE_BACKUP_FILE'));
		}

		// Get list of backup file
		$files = glob($config->get('tmp_path') . '/' . $this->template['name'] . '_original_site_data.*');

		foreach ($files AS $k => $file)
		{
			// Create array of file name and content for making archive later
			$files[$k] = array(
				'name' => basename($file),
				'data' => JFile::read($file)
			);
		}

		// Create backup archive
		$archiver = new JSNTplArchiveZip;
		$zip_path = JPATH_ROOT . '/templates/' . $this->template['name'] . '/backups/' . date('y-m-d_H-i-s') . '_original_site_data.zip';

		if ($archiver->create($zip_path, $files))
		{
			// Remove all SQL backup file created previously in temporary directory
			foreach ($files AS $file)
			{
				JFile::delete($config->get('tmp_path') . '/' . $file['name']);
			}
		}
	}
	
	/**
	 * Truncate all data of #__advancedmodules table
	 * 
	 * @void
	 */
	private function _truncateAdvancedmodulesTable()
	{
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		$query->select('enabled');
		$query->from('#__extensions');
		$query->where('element=' . $db->quote('com_advancedmodules') . ' AND type=' . $db->quote('component'));
		$db->setQuery($query);
		
		if ((int) $db->loadResult())
		{
			$query->clear();
			$query->delete($db->quoteName('#__advancedmodules'));
			$query->where('1');	
			$db->setQuery($query);
			
			try 
			{
				$db->execute();
			} 
			catch (Exception $e) 
			{
				
			}
		}
	}
	
	/**
	 * Clean up junk data related to the out date component.
	 *
	 * @param   string  $name     The component name.
	 *
	 * @return  void
	 */
	private function _cleanJunkMenuData ($name)
	{
		// Only clean-up junk data if component is really missing.
		if ( JSNTplHelper::isInstalledExtension( $name ) )
		{
			// Get all menu items associated with the missing component.
			$q = $this->dbo->getQuery(true);
	
			$q->select('id')->from('#__menu')->where("menutype = 'mainmenu'");
			$q->where("link LIKE '%option=" . $name . "%'");
			$q->where("client_id = 0");
	
			$this->dbo->setQuery($q);
	
			$items = $this->dbo->loadColumn();
	
			if ( count( $items ) )
			{
				// Get all modules associated with all menu items of the missing component.
				$q = $this->dbo->getQuery(true);
	
				$q->select('moduleid')->from('#__modules_menu')->where('menuid IN (' . implode(', ', $items) . ')');
	
				$this->dbo->setQuery($q);
	
				$mods = $this->dbo->loadColumn();
	
				// Clean up menu table.
				$q = $this->dbo->getQuery(true);
	
				$q->delete('#__menu')->where('id IN (' . implode(', ', $items) . ')');
	
				$this->dbo->setQuery($q);
	
				if ( ! $this->dbo->{$this->queryMethod}())
				{
					throw new Exception($this->dbo->getErrorMsg());
				}
			}
		}
	}
}
