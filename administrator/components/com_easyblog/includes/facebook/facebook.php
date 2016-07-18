<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogFacebook extends EasyBlog
{
	public static function getImage(EasyBlogPost &$blog, $rawIntroText = '')
    {
        // First, we try to search to see if there's a blog image. If there is already, just ignore the rest.
        if ($blog->hasImage()) {
            return $blog->getImage('large', true, true);
        }

        // For image posts.
        if (isset($blog->images[0])) {
            return $blog->images[0];
        }

        $fullcontent = $blog->getContent('entry');
        $source = EB::string()->getImage($fullcontent);

        if ($source !== false) {
            return $source;
        }

        // Default post image if the blog post doesn't contain any image
        $app = JFactory::getApplication();
        $override = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easyblog/images/placeholder-facebook.png';

        if (JFile::exists($override)) {
            $source = rtrim(JURI::root(), '/') . '/templates/' . $app->getTemplate() . '/html/com_easyblog/images/placeholder-facebook.png';
        } else {
            $source = rtrim(JURI::root(), '/') . '/components/com_easyblog/themes/wireframe/images/placeholder-facebook.png';
        }

        return $source;
    }

	/**
	 * Attaches the open graph tags in the header
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public function addOpenGraphTags(EasyBlogPost &$post)
    {
        if (!$this->config->get('main_facebook_opengraph')) {
            return;
        }

        // Get the absolute permalink for this blog item.
        $url = $post->getExternalPermalink();

        // Get the image of the blog post.
        $image  = self::getImage($post);

        // Add the blog image.
        $this->doc->addCustomTag( '<meta property="og:image" content="' . $image . '"/> ');

        // If app id is provided, attach it on the head
        $appId = $this->config->get('main_facebook_like_appid');
        $adminId = $this->config->get('main_facebook_like_admin');

        if ($appId) {
            $this->doc->addCustomTag( '<meta property="fb:app_id" content="' . $appId . '"/> ');
        }

        if ($adminId) {
            $this->doc->addCustomTag( '<meta property="fb:admins" content="' . $adminId . '"/>' );
        }

        // Convert double quotes to html entities.
        $title = htmlspecialchars($post->title, ENT_QUOTES);

        // Add the title tag
        $this->doc->addCustomTag('<meta property="og:title" content="' . $title . '" />');

        // Load any necessary meta data for the blog
        $meta = $post->loadMeta();

        // If there's a meta set for the blog, use the stored meta version
        $description = !empty($post->description) ? $post->description : $post->getIntro();

        // Remove unwanted tags
        $description = EB::stripEmbedTags($description);

        // Remove newlines
        $description = str_ireplace("\r\n", "", $description);

        // Replace &nbsp; with spaces
        $description = JString::str_ireplace('&nbsp;', ' ', $description);

        // Remove any html tags
        $description = strip_tags($description);

        // Ensure that newlines wouldn't affect the header
        $description = trim($description);

        // Replace htmlentities with the counterpert
        // Perhaps we need to explicitly replace &nbsp; with a space?
        $description = html_entity_decode($description);

        // Remove any quotes (") from the content
        $description = str_ireplace('"', '', $description);

        // If there's a maximum length specified, we should respect it.
        $max = $this->config->get('integrations_facebook_blogs_length');

        if ($max) {
            if (JString::strlen($description) > $max) {
                $description = JString::substr($description, 0, $max) . JText::_('COM_EASYBLOG_ELLIPSES');
            }
        }
        

        $this->doc->addCustomTag('<meta property="og:description" content="' .  $description . '" />');
        $this->doc->addCustomTag('<meta property="og:type" content="article" />');
        $this->doc->addCustomTag('<meta property="og:url" content="' . $url . '" />');

        // Get the author of the post
        $author = $post->getAuthor();
        $authorParams = $author->getParams();

        // Determines if we should add the article:author meta data
        $facebookProfileUrl = $authorParams->get('facebook_profile_url', '');

        if ($facebookProfileUrl && $this->config->get('main_facebook_ogauthor')) {
            $this->doc->addCustomTag('<meta property="article:author" content="' . $facebookProfileUrl . '" />');
        }

        return true;
    }
}
