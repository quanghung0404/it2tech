<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EASYBLOG_FOUNDRY . '/joomla/compiler.php');

class EasyBlogCompiler
{
    static $instance = null;
    public $resourceManifestFile;
    public $cli = false;
    public $version;

    public static $exclusion = array(
        "ui/draggable",
        "ui/sortable",
        "ui/droppable",
        "ui/datepicker",
        "ui/timepicker",
        "flot",
        "sparkline",
        "plupload",
        "redactor",
        "moment"
    );

    public function __construct()
    {
        if (defined('EASYBLOG_COMPONENT_CLI')) {
            $this->cli = true;
        }

        if ($this->cli) {
            $this->version = EASYBLOG_COMPONENT_VERSION;
        }

        if (!$this->cli) {
            $this->version = (string) EB::getLocalVersion();
        }

        $this->resourceManifestFile = EASYBLOG_RESOURCES . '/default-' . $this->version . '.json';
    }

    /**
     * Retrieves a single instance of the compiler
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Retrieves the new compiler object
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function getCompiler()
    {
        $compiler = new FD50_FoundryCompiler();

        return $compiler;
    }

    /**
     * Main compiler code that compiles javascript files.
     *
     * @since   1.0
     * @access  public
     * @param   bool    Determines if the compiler should also minify the javascript codes
     * @return
     */
    public function compile($section = 'site', $minify = false)
    {
        $compiler = $this->getCompiler();

        // Create a master manifest containing all the scripts
        $manifest = new stdClass();
        $manifest->adapter = 'EasyBlog';
        $manifest->script = array();

        // Get a list of all the js files in the "scripts" folder
        jimport('joomla.filesystem.folder');

        // Read the dependencies file
        $dependenciesFile = EASYBLOG_SCRIPTS . '/dependencies.json';
        $contents = JFile::read($dependenciesFile);
        $dependencies = json_decode($contents);

        // Nothing to compile so skip this altogether.
        if (!isset($dependencies->$section)) {
            return;
        }

        // Set compiler options
        $options = array(
            "static" => EASYBLOG_SCRIPTS . '/' . $section . '-' . $this->version . '.static',
            "optimized" => EASYBLOG_SCRIPTS . '/' . $section . '-' . $this->version . '.optimized',
            "minify" => $minify
        );

        $compiler->exclude = self::$exclusion;

        // Include everything in composer to speed up load time.
        // Doesn't matter if the static composer script is large.
        if ($section=='composer') {
            $compiler->exclude = array();
        }

        // Compiler scripts
        return $compiler->compile($dependencies->$section, $options);
    }


    public function purgeResources()
    {
        $files = JFolder::files( EASYBLOG_RESOURCES , '.' , true, true);

        foreach ($files as $file) {
            if (strpos($file, 'default') !== false) {
                continue;
            }

            $state = JFile::delete( $file );
        }
    }

}
