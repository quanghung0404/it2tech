<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ROOT . '/components/com_easyblog/views/views.php');
require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/mediamanager/mediamanager.php');

class EasyBlogViewMediamanager extends EasyBlogView
{
    public function __construct()
    {
        // Ensure that the user is logged in
        EB::requireLogin();

        parent::__construct();
    }

    /**
     * Renders the media?
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function media()
    {
        // Get the key to lookup for
        $key = $this->input->getRaw('key');

        // Get the uri
        $uri = EBMM::getUri($key);

        // Get the media
        $media = EBMM::getMedia($uri);

        return $this->ajax->resolve($media);
    }

    /**
     * Display a success message that the item is already moved
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function fileMoved()
    {
        $theme = EB::template();

        $output = $theme->output('site/mediamanager/dialog.file.moved');

        return $this->ajax->resolve($output);
    }

    /**
     * Displays a confirmation dialog to move an item
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function confirmMoveItem()
    {
        $source = $this->input->get('source', '', 'default');
        $source = EBMM::getUri($source);

        $target = $this->input->get('target', '', 'default');
        $target = EBMM::getUri($target);

        $sourceFile = EBMM::getMedia($source);
        $targetFolder = EBMM::getMedia($target);

        $theme = EB::template();
        $theme->set('fileName', $sourceFile->meta->title);
        $theme->set('folderName', $targetFolder->meta->title);

        $output = $theme->output('site/mediamanager/dialog.move.confirm');

        return $this->ajax->resolve($output);
    }

    /**
     * Retrieves contents of a folder
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
	public function folder()
	{
        // Ensure that the user has access
        EB::requireLogin();

        // Get the key to lookup for
		$key = $this->input->getRaw('key');

        // Get the uri
		$uri = EBMM::getUri($key);

        // Generate the html codes for the folder that is being accessed
		$html = EBMM::renderFolder($uri);

		return $this->ajax->resolve($html);
	}

    /**
     * Renders the folder tree
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function tree()
    {
        $key = $this->input->getRaw('key');
        $uri = EBMM::getUri($key);

        $html = EBMM::renderTree($uri);

        return $this->ajax->resolve($html);
    }

    /**
     * Retrieves a list of article folders on the site for admin navigation
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function posts()
    {
        $html = EBMM::renderPosts();

        return $this->ajax->resolve($html);
    }

    /**
     * Displays the flickr login form
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function flickr()
    {
        // Ensure that the user is logged in
        EB::requireLogin();

        $key = $this->input->getRaw('key');

        $html = EBMM::renderFlickr($key);

        return $this->ajax->resolve($html);
    }

    /**
     * Retrieves a list of available authors on the site for admin navigation
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function users()
    {
        // Ensure that this user is really allowed here
        if (!EB::isSiteAdmin()) {
            return EB::exception('COM_EASYBLOG_NOT_ALLOWED_TO_PERFORM_ACTION');
        }

        // Get a list of authors on the site.
        $output = EBMM::renderUsers();

        return $this->ajax->resolve($output);
    }

    /**
     * Retrieves property about a file or folder
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function info()
    {
        $key = $this->input->get('key', '', 'raw');
        $uri = EBMM::getUri($key);

        $mm = EB::mediamanager();
        $file = $mm->getItem($uri);

        $html = EBMM::renderInfo($uri);

        return $this->ajax->resolve($html, $file);
    }

    /**
     * Displays the delete folder dialog confirmation
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteFolderDialog()
    {
        // Ensure that the user is logged in
        EB::requireLogin();

        $theme = EB::template();

        $output = $theme->output('site/mediamanager/dialog.delete.folder');

        return $this->ajax->resolve($output);
    }

    /**
     * Displays the delete folder dialog confirmation
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteFileDialog()
    {
        $file = $this->input->get('file', '', 'default');

        $theme = EB::template();
        $theme->set('file', $file);
        $output = $theme->output('site/mediamanager/dialog.delete.file');

        return $this->ajax->resolve($output);
    }

    /**
     * Displays the rename file dialog confirmation
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function renameFileDialog()
    {
        // Ensure that the user is logged in
        EB::requireLogin();

        // Get the current file name
        $current = $this->input->get('current', '', 'default');

        $theme = EB::template();
        $theme->set('current', $current);

        $output = $theme->output('site/mediamanager/dialog.rename.file');

        return $this->ajax->resolve($output);
    }

    /**
     * Displays the rename folder dialog confirmation
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function renameFolderDialog()
    {
        // Ensure that the user is logged in
        EB::requireLogin();

        // Get the current file name
        $current = $this->input->get('current', '', 'default');

        $theme = EB::template();
        $theme->set('current', $current);

        $output = $theme->output('site/mediamanager/dialog.rename.folder');

        return $this->ajax->resolve($output);
    }

    /**
     * Displays the create dialog form
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function createFolderDialog()
    {
        // Ensure that the user is logged in
        EB::requireLogin();

        $theme = EB::template();

        $output = $theme->output('site/mediamanager/dialog.create.folder');

        return $this->ajax->resolve($output);
    }

    /**
     * Creates a new folder on the site
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function createFolder()
    {
        // Ensure that the user is logged in
        EB::requireLogin();

        $key = $this->input->getRaw('key');
        $uri = EBMM::getUri($key);
        $folder = $this->input->get('folder', '', 'cmd');

        $media = EB::mediamanager();
        $uri = $media->createFolder($uri, $folder);

        if ($uri instanceof EasyBlogException) {
            return $this->ajax->reject($item);
        }

        $html = EBMM::renderFile($uri);

        return $this->ajax->resolve($html);
    }

    /**
     * Allows caller to rename a file
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function rename()
    {
        $key = $this->input->get('key', '', 'raw');
        $source = EBMM::getUri($key);
        $target = dirname($source) . '/' . $this->input->get('filename', '', 'default');

        $media = EB::mediamanager();
        $media->rename($source, $target);

        $fileHtml = $media->renderFile($target);
        $infoHtml = $media->renderInfo($target);

        $folderHtml = $media->renderFolder($target);

        return $this->ajax->resolve($fileHtml, $infoHtml, $folderHtml);
    }

    /**
     * Moves an item given the source and destination path
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function move()
    {
        $source = $this->input->get('source', '', 'default');
        $source = EBMM::getUri($source);

        $target = $this->input->get('target', '', 'default');
        $target = EBMM::getUri($target);

        $media = EB::mediamanager();
        $item = $media->move($source, $target);

        if ($item instanceof EasyBlogException) {
            return $this->ajax->reject($item);
        }

        $fileHtml = $media->renderFile($item->uri);
        $infoHtml = $media->renderInfo($item->uri);

        return $this->ajax->resolve($fileHtml, $infoHtml);
    }

    /**
     * Deletes an item
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function delete()
    {
        $key = $this->input->getRaw('key');
        $uri = EBMM::getUri($key);

        $media = EB::mediamanager();
        $state = $media->delete($uri);

        if ($state instanceof EasyBlogException) {
            return $this->ajax->reject($state);
        }

        return $this->ajax->resolve();
    }

    /**
     * Allows caller to rebuild a single variation
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function rebuildVariation()
    {
        $name = $this->input->get('name', '', 'cmd');
        $key = $this->input->get('key', '' ,'raw');

        // Get the uri
        $uri = EBMM::getUri($key);

        $media = EB::mediamanager();
        $state = $media->rebuildVariation($uri, $name);

        $info = EBMM::getMedia($uri);

        return $this->ajax->resolve($info);
    }

    /**
     * Allows caller to delete / remove a variation
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return  
     */
    public function deleteVariation()
    {
        $name = $this->input->get('name', '', 'cmd');
        $key = $this->input->get('key', '' ,'raw');

        // Get the uri
        $uri = EBMM::getUri($key);

        $media = EB::mediamanager();
        $state = $media->deleteVariation($uri, $name);

        if ($state instanceof EasyBlogException) {
            return $this->ajax->reject($state);
        }

        // Get the variations list again
        $info = EBMM::getMedia($uri);

        return $this->ajax->resolve($info);
    }

    /**
     * Creates a new variation on the site
     *
     * @since   5.0
     * @access  public
     * @param   string
     * @return
     */
    public function createVariation()
    {
        $name = $this->input->getCmd('name');
        $key = $this->input->getRaw('key');

        // Convert the key into uri
        $uri = EBMM::getUri($key);

        // Get the width and height
        $width = $this->input->get('width');
        $height = $this->input->get('height');

        $params = new stdClass();
        $params->width = $width;
        $params->height = $height;

        $media = EB::mediamanager();
        $item = $media->createVariation($uri, $name, $params);

        if ($item instanceof EasyBlogException) {
            return $this->ajax->reject($state);
        }

        // Response object is intended to also include
        // other properties like status message and status code.
        // Right now it only inclues the media item.
        $info = EBMM::getMedia($uri);

        return $this->ajax->resolve($info);
    }

}
