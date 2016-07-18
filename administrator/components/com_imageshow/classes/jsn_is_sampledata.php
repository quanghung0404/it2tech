<?php
defined('_JEXEC') or die( 'Restricted access' );
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_sampledata.php 16204 2012-09-20 04:31:14Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');
class JSNISSampledata
{
	public static function getInstance()
	{
		static $instanceSampleData;
		if ($instanceSampleData == null)
		{
			$instanceSampleData = new JSNISSampledata();
		}
		return $instanceSampleData;
	}

	/**
	 * Define link download, name of zip file, name of json file & prefix folder will be created in ../tmp
	 * $infor get from parse com_imageshow.xml
	 */
	function getPackageVersion($infor)
	{
		define("FILE_XML", 'jsn_'.$infor.'_sample_data.xml');
		define("PREFIX_FOLDER_NAME", 'jsn_'.$infor.'_sample_data_');
	}

	/**
	 *  Check environment allow to upload , zip file
	 */
	function checkEnvironment()
	{
		if (!(bool) ini_get('file_uploads'))
		{
			$this->returnError('false', JText::_('MAINTENANCE_SAMPLE_DATA_ENABLE_UPLOAD_FUNCTION'));
		}

		if (!extension_loaded('zlib'))
		{
			$this->returnError('false', JText::_('MAINTENANCE_SAMPLE_DATA_ENABLE_ZLIB'));
		}
		return true;
	}

	/**
	 * Check upload_max_file
	 * Check upload file have been selected & correct format
	 */
	function checkFileUpload()
	{
		$params 	= JComponentHelper::getParams('com_media');
		$max_size 	= (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);
		$user_file 	= JRequest::getVar('install_package', null, 'files', 'array');

		if ($user_file['name'] == '')
		{
			$this->returnError('false', JText::_('MAINTENANCE_SAMPLE_DATA_SAMPLE_DATA_NOT_SELECTED'));
		}

		if (trim(strtolower(JFile::getExt($user_file['name']))) != 'zip')
		{
			$this->returnError('false', JText::_('MAINTENANCE_SAMPLE_DATA_SAMPLE_DATA_INCORRECT_FORMAT'));
		}

		if($user_file['size'] >= $max_size)
		{
			$this->returnError('false', JText::_('MAINTENANCE_SAMPLE_DATA_AMOUNT_UPLOAD_ALLOW').' '. ($params->get('upload_maxsize', 0)).'M');
		}
		return true;

	}

	/**
	 * Upload package from local
	 */
	function getPackageFromUpload()
	{
		$this->checkFileUpload();
		$user_file 	= JRequest::getVar('install_package', null, 'files', 'array');
		$tmp_dest 	= JPath::clean(JPATH_ROOT.DS.'tmp'.DS.$user_file['name']);
		$tmp_src	= $user_file['tmp_name'];

		if (!$user_file['size'])
		{
			$this->returnError('false', JText::_('MAINTENANCE_SAMPLE_DATA_LARGE_UPLOAD_FILE'));
		}

		if (!JFile::upload($tmp_src, $tmp_dest))
		{
			$this->returnError('false', '');
		}
		return 	$user_file['name'];
	}

	/**
	 * Extract package
	 */
	function unpackPackage($p_file)
	{
		$tmp_dest 		= JPATH_ROOT.DS.'tmp';
		$tmpdir			= uniqid(PREFIX_FOLDER_NAME);
		$archive_name 	= $p_file;
		$extract_dir 	= JPath::clean($tmp_dest.DS.dirname($p_file).DS.$tmpdir);
		$archive_name 	= JPath::clean($tmp_dest.DS.$archive_name);
		$result 		= JArchive::extract( $archive_name, $extract_dir);

		if ($result)
		{
			$path = $tmp_dest.DS.$tmpdir;
			return $path;
		}
		return false;
	}

	function executeInstallSampleData($data)
	{
		$link 		= 'index.php?option=com_imageshow&controller=maintenance&type=data&tab=0';
		$db			= JFactory::getDBO();
		$queries 	= array();

		foreach ($data as $rows)
		{
			/*$indentityInsertOn	= $rows->indentityInsertOn;
			 if (count($indentityInsertOn))
			 {
				foreach ($indentityInsertOn as $value)
				{
				$db->setQuery($value);
				$db->query();
				}
				}*/
			$truncate 	= $rows->truncate;
			if (count($truncate))
			{
				foreach ($truncate as $value)
				{
					$queries [] = $value;
				}
			}
			$install 	= $rows->install;

			if (count($install))
			{
				foreach ($install as $value)
				{
					$queries [] = $value;
				}
			}

			/*$indentityInsertOff	= $rows->indentityInsertOff;
			 if (count($indentityInsertOff))
			 {
				foreach ($indentityInsertOff as $value)
				{
				$db->setQuery($value);
				$db->query();
				}
				}*/
		}

		if (count($queries) != 0)
		{
			foreach ($queries as $query)
			{
				$query = str_replace('`', '', trim($query));
				if ($query != '')
				{
					$db->setQuery($query);
					if (!$db->query())
					{
						$msg = JText::_('MAINTENANCE_SAMPLE_DATA_ERROR_QUERY_DATABASE', true);
						echo json_encode(array('install' => false, 'message'=>$msg, 'redirect_link'=>$link));
						exit();
					}
				}
			}
			return true;
		}
		return false;
	}

	function deleteTempFolderISD($path)
	{
		$path = JPath::clean($path);
		if (JFolder::exists($path))
		{
			JFolder::delete($path);
			return true;
		}
		return false;
	}

	function deleteISDFile($file)
	{
		$path = JPATH_ROOT.DS.'tmp'.DS.$file;

		if (JFile::exists($path))
		{
			JFile::delete($path);
			return true;
		}
		return false;
	}

	function returnError($result, $msg)
	{
		global $mainframe;

		if (is_array($msg))
		{
			foreach ($msg as $value)
			{
				JError::raiseWarning(100,JText::_($value));
			}
		}
		else
		{
			if ($msg != '')
			{
				JError::raiseWarning(100,JText::_($msg));
			}
		}
		$methodInstallSampleData = JRequest::getVar('method_install_sample_data');
		if ($methodInstallSampleData != '')
		{
			$method='&method_install_sample_data='.$methodInstallSampleData;
		}
		else
		{
			$method = '';
		}
		$mainframe->redirect('index.php?option=com_imageshow&view=maintenance&s=maintenance&g=data#data-sample-installation');
		return $result;
	}

	function checkFolderPermission()
	{
		$folderpath = JPATH_ROOT.DS.'tmp';
		if (is_writable($folderpath) == false)
		{
			$this->returnError('false','');
			return false;
		}
		return true;
	}

	// convert json sampledata to object data
	function jsonSampleDataToObject($path)
	{
		$path 		 = JPath::clean($path);
		$objJNSUtils = JSNISFactory::getObj('classes.jsn_is_utils');
		if (!$jsonString = $objJNSUtils->readFileToString($path))
		{
			JError::raiseWarning(100,JText::_('MAINTENANCE_SAMPLE_DATA_NOT_FOUND_INSTALLATION_FILE_IN_SAMPLE_DATA_PACKAGE'));
			return false;
		}

		return $dataObj = json_decode($jsonString);

	}
}