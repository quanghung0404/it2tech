<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_exifinternalsource.php 13759 2012-07-04 04:31:41Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
class JSNISExifInternalSource
{
	var $flashData = array();

	function JSNISExifInternalSource()
	{
		$this->_setFlashData();
	}

	function _setFlashData()
	{
		$this->flashData = array('0'=>'Flash did not fire', '1'=>'Flash fired', '5'=>'Strobe return light not detected', '7'=>'Strobe return light detected',
							 '8'=>'On, Flash did not fire', '9'=>'Flash fired, compulsory flash mode', '13'=>'Flash fired, compulsory flash mode, return light not detected',
							 '15'=>'Flash fired, compulsory flash mode, return light detected', '16'=>'Flash did not fire, compulsory flash mode',
							 '20'=>'Off, Flash did not fire, return light not detected', '24'=>'Flash did not fire, auto mode',
							 '25'=>'Flash fired, auto mode', '29'=>'Flash fired, auto mode, return light not detected',
							 '31'=>'Flash fired, auto mode, return light detected', '32'=>'No flash function',
							 '48'=>'Off, no flash function', '65'=>'Flash fired, red-eye reduction mode',
							 '69'=>'Flash fired, red-eye reduction mode, return light not detected', '71'=>'Flash fired, red-eye reduction mode, return light detected',
							 '73'=>'Flash fired, compulsory flash mode, red-eye reduction mode', '77'=>'Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected',
							 '79'=>'Flash fired, compulsory flash mode, red-eye reduction mode, return light detected', '80'=>'Off, red-eye reduction mode',
							 '88'=>'Flash did not fire, red-eye reduction mode', '89'=>'Flash fired, auto mode, red-eye reduction mode',
							 '93'=>'Flash fired, auto mode, return light not detected, red-eye reduction mode', '95'=>'Flash fired, auto mode, return light detected, red-eye reduction mode');
	}

	function renderData($file)
	{
		$exifData 	 = $this->readData($file);
		$tmpExifData = array();
		if (count($exifData))
		{
			if (isset($exifData['IFD0']['Model']) && $exifData['IFD0']['Model'] != '' && isset($exifData['IFD0']['Make']) && $exifData['IFD0']['Make'] != '')
			{
				$tmpExifData [] = @$exifData['IFD0']['Make'].'/'.@$exifData['IFD0']['Model'];
			}
			if (isset($exifData['EXIF']['ExposureTime']) && $exifData['EXIF']['ExposureTime'] != '')
			{
				$tmpExposureTime =  $this->convertFractionToDecimal($exifData['EXIF']['ExposureTime']);
				$tmpExposureTime =  $this->convertDecimalToFraction($tmpExposureTime);
				$tmpExifData [] = '1/'.$tmpExposureTime;
			}
			if (isset($exifData['COMPUTED']['ApertureFNumber']) && $exifData['COMPUTED']['ApertureFNumber'] != '')
			{
				$tmpExifData [] = $exifData['COMPUTED']['ApertureFNumber'];
			}
			if (isset($exifData['EXIF']['FocalLength']) && $exifData['EXIF']['FocalLength'] != '')
			{
				$tmpExifData [] = $this->convertFractionToDecimal($exifData['EXIF']['FocalLength']).'mm';
			}
			if (isset($exifData['EXIF']['ISOSpeedRatings']) && $exifData['EXIF']['ISOSpeedRatings'] != '')
			{
				$tmpExifData [] = 'ISO-'.(int) $exifData['EXIF']['ISOSpeedRatings'];
			}
			if (isset($exifData['EXIF']['Flash']) && $exifData['EXIF']['Flash'] != '')
			{
				$tmpExifData [] = @$this->flashData[$exifData['EXIF']['Flash']];
			}

			if (count($tmpExifData))
			{

				return implode(', ', $tmpExifData);
			}
			else
			{
				return '';
			}
		}
	}

	function readData($file)
	{
		$fileExtension 		= strtolower(JFile::getExt($file));
		$validExtensions 	= array('jpg', 'jpeg', 'jpe');
		$exifArray       	= array();
		if(in_array($fileExtension, $validExtensions))
		{
			/*if(!extension_loaded('exif'))
			{
				$phpVersion = phpversion();
				if (version_compare($phpVersion, '5.0.0') <= -1)
				{
					return array();
				}
				else
				{
					if ($this->detect())
					{
						$exifArray 	= $this->readDataDisabledExif($file);
					}
					else
					{
						return array();
					}
				}
			}*/
			if (extension_loaded('exif'))
			{
				$exifArray = $this->readDataEnabledExif($file);
			}

			if(!$exifArray)
			{
				return array();
			}
			else
			{
				return $exifArray;
			}
		}
	}

	function readDataEnabledExif($file)
	{
		return @exif_read_data(JPath::clean(JPATH_ROOT.DS.$file), 'COMPUTED, EXIF, IFD0, GPS', true);
	}

	function convertFractionToDecimal($fraction)
	{
		$fractionsplit	= preg_split('/\//', $fraction);
		$numerator 		= @$fractionsplit[0];
		$denominator 	= @$fractionsplit[1];
		$converted 		= $numerator / $denominator;
		return $converted;
	}

	function convertDecimalToFraction($decimal)
	{
		$data = 1/$decimal;
		return ceil($data);
	}

	function readDataDisabledExif($file)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'phpexif'.DS.'PelDataWindow.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'phpexif'.DS.'PelJpeg.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'phpexif'.DS.'PelTiff.php');
		$file 		= JPath::clean(JPATH_ROOT.DS.$file);
		$jpeg 		= new PelJpeg($file);
		$exif 		= $jpeg->getExif();
		$exifArray	= array();
		if ($exif != null)
		{
			$tiff = $exif->getTiff();
			$ifd0 = $tiff->getIfd();
			$exif = $ifd0->getSubIfd(PelIfd::EXIF);
			if (!method_exists($exif,'getEntry')) return $exifArray;
			$model 									= $ifd0->getEntry(PelTag::MODEL);
			$make 									= $ifd0->getEntry(PelTag::MAKE);
			$exposureTime 							= $exif->getEntry(PelTag::EXPOSURE_TIME);
			$Fnumber			 					= $exif->getEntry(PelTag::FNUMBER);
			$focalLength			 				= $exif->getEntry(PelTag::FOCAL_LENGTH);
			$iso			 						= $exif->getEntry(PelTag::ISO_SPEED_RATINGS);
			$flash			 						= $exif->getEntry(PelTag::FLASH);
			if ($model != null && $make != null)
			{
				$exifArray['IFD0']['Model']  			= $model->getValue();
				$exifArray['IFD0']['Make']   			= $make->getValue();
			}
			if ($exposureTime != null)
			{
				$exposureTime							= $exposureTime->getValue();
				$exposureTime							= $exposureTime[0].'/'.$exposureTime[1];
				$exifArray['EXIF']['ExposureTime']   	= $exposureTime;
			}

			if ($Fnumber != null)
			{
				$exifArray['COMPUTED']['ApertureFNumber']   	= $Fnumber->getText();
			}

			if ($focalLength != null)
			{
				$focalLength									= $focalLength->getValue();
				$focalLength									= $focalLength[0].'/'.$focalLength[1];
				$exifArray['EXIF']['FocalLength']   			= $focalLength;
			}

			if ($iso != null)
			{
				$exifArray['EXIF']['ISOSpeedRatings']   		= $iso->getValue();
			}

			if ($flash != null)
			{
				$exifArray['EXIF']['Flash']   					= $flash->getValue();
			}

			return $exifArray;
		}
		return false;
	}

	function detect()
	{
		$funcList = get_extension_funcs('calendar');
		if ($funcList)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}