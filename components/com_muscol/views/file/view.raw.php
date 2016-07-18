<?php

/** 
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.path');

class ArtistsViewFile extends JViewLegacy
{
	public function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$user = JFactory::getUser();
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$id = JRequest::getInt('id');

		//new zip
		$zip = JRequest::getInt('zip');

		if(!$zip){
		
			if(!$id)
			{
				JError::raiseError(404, 'Page Not Found');
				return;	
			}
			
			if( (!$user->id && $params->get('allowsongdownload') == 1 ) || $params->get('allowsongdownload') == 0)
			{
				JError::raiseError(401, 'You must be logged in to download this content');
				return;	
			}
				
			$model = $this->getModel('file');
			//$model->hit();
			
			$data		= $model->getData();
			$fileName	= $data->filename;
			
			//new plugin access
			$dispatcher	= JDispatcher::getInstance();
			$plugin_ok = JPluginHelper::importPlugin('muscol');
			$results = $dispatcher->trigger('onDownloadSong', array ($data->id));
			
			if($params->get('registersongdownloads')) $this->get('RegisterHit');
			
			$this->fileName = $fileName ;
			
			$dirname = $params->get('songspath');
			if(substr($dirname, 0, 1) == "/") $dirname = substr($dirname, 0);
			
			if($dirname == "") $filePath	= JPath::clean( JPATH_SITE.DS.$fileName );
			else $filePath	= JPath::clean( JPATH_SITE.DS.$dirname.DS.$fileName );
			
			if( !JFile::exists( $filePath ) )
			{
				JError::raiseError(404, 'Page Not Found');
				return;
			}
			
			$this->download($filePath);
			die();
		
		}
		else{ //else ZIP
			$model = $this->getModel('file');
		
			$thefiles		= $model->getAlbumSongs();
			
			$db = JFactory::getDBO();
			$query = " SELECT name FROM #__muscol_albums WHERE id = ". $id ;
			$db->setQuery($query);
			$album_name = $db->loadResult();
		
			//its a zip - dynamic producer	
			$dirname = $params->get('songspath');
			if(substr($dirname, 0, 1) == "/") $dirname = substr($dirname, 0);
			
			if($dirname == "") $filePath_base	= JPath::clean( JPATH_SITE.DS );
			else $filePath_base	= JPath::clean( JPATH_SITE.DS.$dirname.DS );
			
			$filePath_base = str_replace("/", DS, $filePath_base) ;
			$filePath_base = str_replace("\\", DS, $filePath_base) ;
			
			if(!empty($thefiles)){
				foreach($thefiles as $file){
					
					$path = $filePath_base . $file->filename ;
					
					$path = str_replace("/", DS, $path) ;
					$path = str_replace("\\", DS, $path) ;
					
					$files[] = $path ;
					$names[] = $file->filename  ;
				}
			}
			
			//print_r($files);die;
			
			$destination = $filePath_base.$album_name.'_'.time().'.zip';
			$folder = $filePath_base ;
			
			//new version
			
			if($this->create_zip($files,$names,$destination )){
			//echo $destination;die;
				$this->send_file($destination,$album_name . ".zip"); 
				unlink($destination);
			}
			
			
		}
	}

	// Send zipped file
	function send_file($file,$download_filename='download.zip') {
	
		header('Content-Description: File Transfer');
		header('Content-type: application/zip');
		header('Content-Length: '.filesize($file));
		header('Content-Disposition: attachment; filename="'.$download_filename.'"');
		header('Expires: 0');
		while(ob_end_clean());
		flush();
		readfile($file);
	
	}
	
	// Zip files
	function zip_files($files,$destination, $folder) {
		
		/*$cache_dir = dirname($destination).'/'.basename($destination,'.zip').'/';
		$cwd = getcwd();
		// Create temp directory to minimize collision
		mkdir($cache_dir);
		// Check if path is valid
		$valid_files = '';
		foreach($files as $file) {
			if(file_exists($file)) {
				copy($file,$cache_dir.basename($file));
				$valid_files .= '"'.basename($file).'" ';
			}
		}
		
		chdir($cache_dir);
		$command = '/usr/bin/zip "'.$destination.'" '.$valid_files;
		exec($command);
		exec('rm -r "'.$cache_dir.'"');
		chdir($cwd);
		*/
		
		// Check if path is valid
		$valid_files = '';
		foreach($files as $file) {
			if(file_exists($file)) {
				$valid_files .= '"'.basename($file).'" ';
			}
		}
		
		
		chdir($folder);
		$command = '/usr/bin/zip "'.$destination.'" '.$valid_files;
		//echo $command ;die;
		exec($command);
		
		return true;
		//exec('rm -r "'.$cache_dir.'"');
		
		
		//print_r($files);
		//print_r($destination);
		//die;
		/*
		$zip_file = new JArchiveZip();
		
		return $zip_file->create($destination, $files);
	*/
	}
	
	/* creates a compressed zip file */
		function create_zip($files = array(),$names = array(),$destination = '',$overwrite = false) {
		  //if the zip file already exists and overwrite is false, return false
		  if(file_exists($destination) && !$overwrite) { return false; }
		  //vars
		  $valid_files = array();
		  //if files were passed in...
		  if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
			  //make sure the file exists
			  if(file_exists($file)) {
				$valid_files[] = $file;
			  }
			}
		  }
		  //if we have good files...
		  if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			  return false;
			}
			//add the files
			$i = 0;
			foreach($valid_files as $file) {
				
			  $zip->addFile($file,$names[$i]);
			  $i++;
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			
			//close the zip -- done!
			$zip->close();
			
			//check to make sure the file exists
			return file_exists($destination);
		  }
		  else
		  {
			return false;
		  }
		}
	
	function download($filePath, $inline = false)
    {
		// Fix [3164]
		while (@ob_end_clean());
		
		$this->mime = $this->filenameToMIME($this->fileName, false);

		$fsize = @filesize($filePath);
		$mod_date = date('r', filemtime( $filePath ) );

		$cont_dis = $inline ? 'inline' : 'attachment';

		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))  {
			ini_set('zlib.output_compression', 'Off');
		}

        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");

        header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
			. ' filename="' . $this->fileName . '";'
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize .';'
			); //RFC2183
        header("Content-Type: "    . $this->mime );			// MIME type
        header("Content-Length: "  . $fsize);

        if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
		    @set_time_limit(0);
        }

 		// No encoding - we aren't using compression... (RFC1945)
		//header("Content-Encoding: none");
		//header("Vary: none");


        $this->readfile_chunked($filePath);
        // The caller MUST 'die();'
    }

    function readfile_chunked($filename,$retbytes=true)
    {
   		$chunksize = 1*(1024*1024); // how many bytes per chunk
   		$buffer = '';
   		$cnt =0;
   		$handle = fopen($filename, 'rb');
   		if ($handle === false) {
       		return false;
   		}
   		while (!feof($handle)) {
       		$buffer = fread($handle, $chunksize);
       		echo $buffer;
			@ob_flush();
			flush();
       		if ($retbytes) {
           		$cnt += strlen($buffer);
       		}
   		}
       $status = fclose($handle);
   	   if ($retbytes && $status) {
       		return $cnt; // return num. bytes delivered like readfile() does.
   		}
   		return $status;
	}
	
	function filenameToMIME($filename, $unknown = true)
    {
        $pos = strlen($filename) + 1;
        $type = '';

        $map =$this->_getMimeExtensionMap();
        for ($i = 0;
            $i <= $map['__MAXPERIOD__'] &&
            strrpos(substr($filename, 0, $pos - 1), '.') !== false;
            $i++) {
            $pos = strrpos(substr($filename, 0, $pos - 1), '.') + 1;
        }
        $type = $this->extToMIME(substr($filename, $pos));

        if (empty($type) ||
                (!$unknown && (strpos($type, 'x-extension') !== false))) {
            return 'application/octet-stream';
        } else {
            return $type;
        }
    }
	
	function extToMIME($ext)
    {
        if (empty($ext)) {
            return 'application/octet-stream';
        } else {
            $ext = strtolower($ext);
            $map =$this->_getMimeExtensionMap();
            $pos = 0;
            while (!isset($map[$ext]) && $pos !== false) {
                $pos = strpos($ext, '.');
                if ($pos !== false) {
                    $ext = substr($ext, $pos + 1);
                }
            }

            if (isset($map[$ext])) {
                return $map[$ext];
            } else {
                return 'x-extension/' . $ext;
            }
        }
    }
	
	function &_getMimeExtensionMap()
    {
        static $mime_extension_map;

        if (!isset($mime_extension_map)) {
            require JPATH_SITE . DS . 'components' . DS . 'com_muscol' . DS . 'helpers' . DS . 'mime.mapping.php';
        }

        return $mime_extension_map;
    }
}
