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

require_once( SOCIAL_LIB . '/template/template.php' );

class SocialStyle extends SocialTemplate
{
	public $extension = 'css';

	public $styleTag = false;
	public $openingTag = '<style type="text/css">';
	public $closingTag = '</style>';

	public $CDATA = false;

	public $header = '';
	public $footer = '';

	public static function factory()
	{
		return new self();
	}

	public function attach( $path = null )
	{
		// Keep current value
		$_styleTag  = $this->styleTag;
		$_CDATA     = $this->CDATA;

		// Keep original file value
		if (!is_null($path))
		{
			$_file		= $this->file;
			$this->file = FD::resolve($path . '.' . $this->extension );
		}

		// Reset to false
		$this->styleTag = false;
		$this->CDATA = false;

		$output = $this->parse();

		FD::page()->addInlineStylesheet($output);

		// Restore current value
		$this->styleTag  = $_styleTag;
		$this->CDATA     = $_CDATA;

		// Restore original file value
		if (!is_null($path)) {
			$this->file = $_file;
		}
	}

	public function parse( $vars=null )
	{
		$stylesheet = $this->header . parent::parse($vars) . $this->footer;

		ob_start();

			// Opening script tag
			if ($this->styleTag) echo $this->openingTag . "\n";

			// Opening CDATA tag
			if ($this->CDATA) echo '/*' . '<![CDATA[' . '*/' . "\n";

			echo $stylesheet;

			// Closing CDATA tag
			if ($this->CDATA) echo "\n" . '/*' . ']]>' . '*/';

			// Closing script tag
			if ($this->styleTag) echo "\n" . $this->closingTag;

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
