<?php
/**
 * @version     $Id: layout.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  libraries
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * JSNUniform layout Libraries
 *
 * @package     Joomla.Administrator
 * @subpackage  com_uniform
 * @since       1.6
 */
class JSNUniformLayout
{
	// Path to layouts folder
	private $_path = null;

	// Name of current layout
	private $_layout = 'default';

	// SimpleXML instance that loaded
	private $_layoutXml = null;

	// Contain generated html content
	private $_layoutOutput = null;

	/**
	 * Class constructor
	 * 
	 * @param   string  $path  Path layout
	 */
	public function __construct($path)
	{
		$this->_path = $path;
	}

	/**
	 * Load layout from file
	 * 
	 * @param   string  $name  Name
	 * 
	 * @return boolean
	 */
	public function load($name)
	{
		$this->_layout = $name;
		return $this->_generate();
	}

	/**
	 * Generate layout from manifest file to html
	 * 
	 * @return void
	 */
	private function _generate()
	{
		$htmlFile	 = "{$this->_path}/{$this->_layout}/layout.html";
		$manifestFile = "{$this->_path}/{$this->_layout}/uniform.xml";

		if (is_file($htmlFile) && filemtime($htmlFile) >= filemtime($manifestFile))
		{
			return ($this->_layoutOuput = file_get_contents($htmlFile));
		}

		$this->_layoutXml = simplexml_load_file($manifestFile);
		$rows = $this->_layoutXml->xpath('/layout/structure/row');

		$this->_layoutOutput = '';
		$hasDefault = false;

		foreach ($rows as $row)
		{
			$columns	  = $row->children();
			$columnSpans  = $this->_getColumnSizes((string) $row['columnSize'], count($columns));
			$columnOutput = '';
			$columnIndex  = 0;

			foreach ($columns as $column)
			{
				$columnName	= @$column['name'];
				$columnStyle   = 'form-region';
				$columnSpan	= (isset($columnSpans[$columnIndex])) ? "span{$columnSpans[$columnIndex]}" : "span1";
				$columnDefault = isset($column['default']) && $column['default'] == 'true' && $hasDefault == false;

				if (isset($column['style']))
				{
					$columnStyle .= "form-{$column['style']}";
				}

				if ($columnDefault)
				{
					$hasDefault = true;
					$columnStyle .= ' form-default';
				}

				$columnOutput .= "\r\n\t<div class=\"form-column {$columnSpan}\" data-column-name=\"{$columnName}\">\r\n\t\t<div class=\"{$columnStyle}\"></div>\r\n\t</div>\r\n";
				$columnIndex ++;
			}

			$this->_layoutOutput .= "<div class=\"form-row row-fluid\">{$columnOutput}</div>\r\n";
		}

		file_put_contents($htmlFile, $this->_layoutOutput);
		return $this->_layoutOutput;
	}

	/**
	 * Return span number based on bootstrap grid layout
	 * 
	 * @param   string  $styles       Style
	 * 
	 * @param   int     $columnCount  Count number
	 * 
	 * @return array
	 */
	private function _getColumnSizes($styles, $columnCount)
	{
		$spans	 = explode('-', $styles);
		$spanCount = count($spans);

		if ($spanCount < $columnCount)
		{
			$spans = array_merge($spans, array_fill(0, $columnCount - $spanCount, 1));
		}
		elseif ($spanCount > $columnCount)
		{
			$spans = array_slice($spans, 0, $columnCount);
		}

		$spanSum  = array_sum($spans);
		$lastSpan = array_pop($spans);
		$ratio	= 12 / $spanSum;

		foreach ($spans as $index => $span)
		{
			$spans[$index] = ceil($span * $ratio);
		}

		$spans[] = 12 - array_sum($spans);
		return $spans;
	}
}
