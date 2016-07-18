<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyBlogMailboxMapPdf extends EasyBlogMailboxMapAbstract
{
    public function map(&$file, $index, &$contents)
    {
        // Store the file to a temporary location
        $file['tmp_name'] = $this->tmp . '/' . md5($file['name']);
        JFile::write($file['tmp_name'], $file['data']);

        // Load up media manager now
        $mm = EB::mediamanager();
        $result = $mm->upload($file, 'user:' . $this->authorId);

        $title = $file['name'];
        $url = $this->absoluteUrl . '/' . $file['name'];

        // Get the properties from media manager result
        if (is_object($result) && property_exists($result, 'title')) {
            $title = $result->title;
            $url = $result->url;
        }

        // Once the attachment is already uploaded, we want to delete the temporary file now
        JFile::delete($file['tmp_name']);

        // Check if a file id is provided in the email
        if (isset($file['id']) && !empty($file['id'])) {

            $fileId = $file['id'];
            $fileId = str_replace('<', '', $fileId);
            $fileId = str_replace('>', '', $fileId);

            $patterns = array('/<div><img[^>]*src="[A-Za-z0-9:^>]*' . $fileId . '"[^>]*\/><\/div>/si', '/<img[^>]*src="[A-Za-z0-9:^>]*' . $fileId . '"[^>]*\/>/si');
            $replace = array('', '');

            $contents = preg_replace($patterns, $replace, $contents);
        }

        // Now we need to insert the pdf links into the content
        $template = EB::template();
        $template->set('title', $title);
        $template->set('url', $url);

        $output = $template->output('site/mailpublishing/template.pdf');

        $contents .= $output;
    }
}
