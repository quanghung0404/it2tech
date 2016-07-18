<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filter.filteroutput');

$jVersion	= EB::getJoomlaVersion();

if( $jVersion <= '3.1' )
{
	jimport( 'joomla.application.router' );
}
else
{
	jimport( 'joomla.libraries.cms.router' );
}


class EasyBlogJoomlaRouter extends JRouter
{
	public function encode($segments)
	{
		return parent::_encodeSegments($segments);
	}
}

class EBR extends EasyBlog
{
	static $posts = array();

	/**
	 * Retrieve all menu's from the site associated with EasyBlog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public static function getMenus($view, $layout = null, $id = null, $lang = null)
    {
        static $menus = null;
        static $selection = array();

        // Always ensure that layout is lowercased
        if (!is_null($layout)) {
            $layout = strtolower($layout);
        }

        // We want to cache the selection user made.
        // $key = $view . $layout . $id;
        $language = false;
        $languageTag = JFactory::getLanguage()->getTag();

        // If language filter is enabled, we need to get the language tag
        if (!JFactory::getApplication()->isAdmin()) {
            $language = JFactory::getApplication()->getLanguageFilter();
            $languageTag = JFactory::getLanguage()->getTag();
        }

        // var_dump($lang);
        if ($lang) {
            $languageTag = $lang;
        }

        $key = $view . $layout . $id . $languageTag;

        // Preload the list of menus first.
        if (is_null($menus)) {

            $model = EB::model('Menu');
            $result = $model->getAssociatedMenus();

            if (!$result) {
                return $result;
            }

            $menus = array();

            foreach ($result as $row) {

                // Remove the index.php?option=com_easyblog from the link
                $tmp = str_ireplace('index.php?option=com_easyblog', '', $row->link);

                // Parse the URL
                parse_str($tmp, $segments);

                // Convert the segments to std class
                $segments = (object) $segments;

                // if there is no view, most likely this menu item is a external link type. lets skip this item.
                if(!isset($segments->view)) {
                    continue;
                }

                $menu = new stdClass();
                $menu->segments = $segments;
                $menu->link = $row->link;
                $menu->view = $segments->view;
                $menu->layout = isset($segments->layout) ? $segments->layout : 0;

                if (!$menu->layout && $menu->view == 'entry') {
                    $menu->layout = 'entry';
                }

                $menu->id = $row->id;

                // var_dump($row->language);

                // this is the safe step to ensure later we will have atlest one menu item to retrive.
                $menus[$menu->view][$menu->layout]['*'][] = $menu;
                $menus[$menu->view][$menu->layout][$row->language][] = $menu;
            }

        }

        // Get the current selection of menus from the cache
        if (!isset($selection[$key])) {

            // Search for $view only. Does not care about layout nor the id
            if (isset($menus[$view]) && isset($menus[$view]) && is_null($layout)) {
                if (isset($menus[$view][0][$languageTag])) {
                    $selection[$key] = $menus[$view][0][$languageTag];
                } else if (isset($menus[$view][0]['*'])) {
                    $selection[$key] = $menus[$view][0]['*'];

                } else {
                    $selection[$key] = false;
                }

            }


            // Searches for $view and $layout only.
            if (isset($menus[$view]) && isset($menus[$view]) && !is_null($layout) && isset($menus[$view][$layout]) && (is_null($id) || empty($id)) ) {
                $selection[$key] = isset($menus[$view][$layout][$languageTag]) ? $menus[$view][$layout][$languageTag] : $menus[$view][$layout]['*'];
            }

            // // view=entry is unique because it doesn't have a layout
            // if ($view == 'entry') {
            //     dump($layout, $selection[$key]);
            // }

            // Searches for $view $layout and $id
            if (isset($menus[$view]) && !is_null($layout) && isset($menus[$view][$layout]) && !is_null($id) && !empty($id)) {
                // $tmp = $menus[$view][$layout];
                $tmp = isset($menus[$view][$layout][$languageTag]) ? $menus[$view][$layout][$languageTag] : $menus[$view][$layout]['*'];

                foreach ($tmp as $tmpMenu) {

                    // Backward compatibility support. Try to get the ID from the new alias style, ID:ALIAS
                    $parts = explode(':', $id);
                    $legacyId = null;

                    if (count($parts) > 1) {
                        $legacyId = $parts[0];
                    }

                    if (isset($tmpMenu->segments->id) && ($tmpMenu->segments->id == $id || $tmpMenu->segments->id == $legacyId)) {
                        $selection[$key] = array($tmpMenu);
                        break;
                    }
                }
            }

            // If we still can't find any menu, skip this altogether.
            if (!isset($selection[$key])) {
                $selection[$key] = false;
            }

            // Flatten the array so that it would be easier for the caller.
            if (is_array($selection[$key])) {
                $selection[$key] = $selection[$key][0];
            }
        }

        return $selection[$key];
    }

	/**
	 * Generates a permalink given a string
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function normalizePermalink($string)
	{
		$config = EB::config();
		$permalink = '';

		if (EBR::isSefEnabled() && $config->get('main_sef_unicode')) {
			$permalink = JFilterOutput::stringURLUnicodeSlug($string);
			return $permalink;
		}

		// Replace accents to get accurate string
		$string = EBR::replaceAccents($string);

		// no unicode supported.
		$permalink = JFilterOutput::stringURLSafe($string);

		// check if anything return or not. If not, then we give a date as the alias.
		if (trim(str_replace('-','',$permalink)) == '') {
			$date = EB::date();
			$permalink = $date->format("%Y-%m-%d-%H-%M-%S");
		}

		return $permalink;
	}

	/**
	 * Generates the query string for language selection.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLanguageQuery($concate = 'AND', $column = 'language')
	{
		$language = JFactory::getLanguage();
		$tag = $language->getTag();
		$query = '';

		$concate = (! $concate) ? 'AND' : $concate;
		$column = (! $column) ? 'language' : $column;


		if (!empty($tag) && $tag != '*') {
			$db = EB::db();
			$query = ' ' . $concate . ' (' . $db->qn($column) . '=' . $db->Quote($tag) . ' OR ' . $db->qn($column) . '=' . $db->Quote('') . ' OR ' . $db->qn($column) . '=' . $db->Quote('*') . ')';
		}

		return $query;
	}

	/**
	 * Assign a post statically so that we can retrieve it without loading
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function setPost(EasyBlogPost $post)
	{
		EBR::$posts[(int) $post->id] = $post;
	}


    public static function getSiteLanguageTag($langSEF)
    {
        static $cache = null;

        if (is_null($cache)) {
            $db = EB::db();

            $query = "select * from #__languages";
            $db->setQuery($query);

            $results = $db->loadObjectList();

            if ($results) {
                foreach($results as $item) {
                    $cache[$item->sef] = $item->lang_code;
                }
            }
        }

        if (isset($cache[$langSEF])) {
            return $cache[$langSEF];
        }

        return $langSEF;
    }



	/**
	 * Converts the non sef links to SEF links when necessary
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function _($url, $xhtml = true, $ssl = null, $search = false, $isCanonical = false, $jRouted = true)
	{
		static $cache = array();
		static $itemIds = array();

		// Cache index
		$key = $url . (int) $xhtml . (int) $isCanonical . (int) $jRouted;

		// If the url has already loaded previously, do not need to load it again.
		if (isset($cache[$key])) {
			return $cache[$key];
		}

		$config = EB::config();
		$app = JFactory::getApplication();
		$input = $app->input;

		// Parse the url
		parse_str($url, $query);

		// Get the view portion from the query string
		$view = isset($query['view']) ? $query['view'] : 'latest';
		$layout = isset($query['layout']) ? $query['layout'] : null;
		$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';
		$task = isset($query['task']) ? $query['task'] : '';
		$id = isset($query['id']) ? $query['id'] : null;
        $sort = isset($query['sort']) ? $query['sort'] : null;
        $lang = isset($query['lang']) ? $query['lang'] : null;
        $search = isset($query['search']) ? $query['search'] : null;

        if ($lang) {
            // we knwo the lang that we passed in is the short tag. we need to get the full tag. e.g. en-GB
            $lang = EBR::getSiteLanguageTag($lang);
        }

        $dropSegment = false;

		// Get routing behavior
		$behavior = $config->get('main_routing', 'default');

        // we no longer support currentactive in 5.0. lets set to 'default' if this is an upgrade from 3.9 .
        $behavior = ($behavior == 'currentactive') ? 'default' : $behavior;

		// Legacy settings for "use current active menu"
		// if ($behavior == 'currentactive' && !$isCanonical) {
		// 	// Get the current active menu
		// 	$active = $app->getMenu()->getActive();

		// 	if ($active) {
		// 		$itemId = $active->id;
		// 	}
		// }

		// settings for "use menu id"
		if ($behavior == 'menuitemid') {

			// Get the menu id from the settings
			$itemId = $config->get('main_routing_itemid');
            if (! $itemId) {
                // if admin did not specify any item id, lets fall back to default style.
                $behavior = 'default';
            }
		}

		// Default routing behavior
		if ($behavior == 'default') {

			// The default menu in the event we can't find anything for the url
			$defaultMenu = EBR::getMenus('latest', null, null, $lang);



			// Entry view needs to be treated differently.
			if ($view == 'entry' && !$layout) {

				// Respect which settings the user configured
				$respectView = $config->get('main_routing_entry');

				// Entry view has higher precedence over all
                $menu = EBR::getMenus('entry', 'entry', $id, $lang);


				if ($menu) {
					$dropSegment = true;
				} else {

					// Get the post data from the cache
					$postCache = EB::cache();
					$post = $postCache->get($id, 'post');

					// Get the category the post is created in
					if ($respectView == 'categories') {
						$menu = EBR::getMenus('categories', 'listings', $post->category_id, $lang);
					}

					if ($respectView == 'blogger') {
						$menu = EBR::getMenus('blogger', 'listings', $post->created_by, $lang);
					}

					if ($respectView == 'teamblog' && $post->source_type == EASYBLOG_POST_SOURCE_TEAM) {
						$menu = EBR::getMenus('teamblog', 'listings', $post->source_id, $lang);
					}
				}
			}

			// Get the default menu that the current view should use
			if ($view != 'entry' || ($view == 'entry' && $layout == 'preview')) {
				$menu = EBR::getMenus($view);

				// If there's a layout an id accompanying the view, we should search for a menu to a single item layout.
				if ($layout && $id) {
					$itemMenu = EBR::getMenus($view, $layout, $id);

					// If there's a menu item created on the site associated with this item, we need to drop the segment
					// to avoid redundant duplicate urls.
					// E.g:
					// menu alias = test
					// post alias = test
					// result = /test/test

					if ($itemMenu) {
						$menu = $itemMenu;
						$dropSegment = true;
					}
				} else if ($layout) {

					// this section here is to cater a view + layout page.
					// e.g dashboard/entries

					$itemMenu = EBR::getMenus($view, $layout);

					if ($itemMenu) {
						$menu = $itemMenu;
						$dropSegment = true;
					}
				}

				// If there is a menu created for the view, we just drop the segment
				if (!$layout && !$id && $menu) {
					$dropSegment = true;
				}

                // Some query strings may have "sort" in them.
                if ($sort) {
                    $dropSegment = false;
                }

                // Some query strings may have "search" in them.
                if ($search) {
                    $dropSegment = false;
                }
			}

			// If we still cannot find any menu, use the default menu :(
			if (!isset($menu) || !$menu) {
				$menu = $defaultMenu;
			}

			// Only proceed when there is at least 1 menu created on the site for EasyBlog
			if (isset($menu) && $menu) {
				$itemId = $menu->id;
			}

			// If this is a task, we shouldn't drop any segments at all
			if ($task) {
				$dropSegment = false;
			}
		}

		// If there's an item id located for the url, we need to intelligently apply it into the url.
		if ($itemId) {

			// We need to respect dropSegment to avoid duplicated menu and view name.
			// For instance, if a menu is called "categories" which links to the categories page, it would be /categories/categories
			if ($dropSegment && EBR::isSefEnabled()) {
				$url = 'index.php?Itemid=' . $itemId;
			} else {
				$url = EBR::appendItemIdToQueryString($url, $itemId);
			}
		}

		$cache[$key] = ($jRouted) ? JRoute::_($url, $xhtml, $ssl) : $url;

		return $cache[$key];
	}

	/**
	 * Appends a fragment to the url as it would intelligent detect if it should use & or ? to join the query string
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function appendFormatToQueryString($url, $format = null)
	{
		if (!$format) {
			return $url;
		}

		if (EBR::isSefEnabled()) {
			$url .= '?format=' . $format;

			return $url;
		}

		$url .= '&format=' . $format;

		return $url;
	}

	/**
	 * Fixes a URL if it contains anchor links
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function appendItemIdToQueryString($url, $itemId)
	{
		$itemId = '&Itemid=' . $itemId;
		$anchor = JString::strpos($url, '#');

		if ($anchor === false) {
			$url .= $itemId;

			return $url;
		}

		$url = JString::str_ireplace('#', $itemId . '#', $url);

		return $url;
	}

	/**
	 * Determiens if SEF is enabled on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isSefEnabled()
	{
		$jConfig = EB::jConfig();
		$isSef = false;
		$isSef = EBR::isSh404Enabled();

		// If sh404sef not enabled, we need to check if joomla has it enabled
		if (!$isSef) {
			$isSef = $jConfig->get('sef');
		}

		return $isSef;
	}

	/**
	 * Due to the fact that SH404 doesn't rewrite urls from the back end, we need to check if they exist
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isSh404Enabled()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';
		$enabled = false;

		if (defined('SH404SEF_AUTOLOADER_LOADED') && JFile::exists($file)) {
			require_once($file);

			if (class_exists('shRouter')) {
				$sh404Config = shRouter::shGetConfig();

				if ($sh404Config->Enabled) {
					$enabled = true;
				}
			}
		}

		return $enabled;
	}

	/**
	 * Retrieves the custom permalink
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public static function getCustomPermalink(EasyBlogPost $post)
    {
        $config = EB::config();
        $custom = $config->get('main_sef_custom');

        $date = EB::date($post->created);

        $postPermalink = $post->permalink;

        if ($config->get('main_sef_unicode') || !EBR::isSefEnabled()) {
            $postPermalink = $post->id . '-' . $postPermalink;
        }

        $fallback = $date->toFormat('%Y') . '/' . $date->toFormat( '%m' ) . '/' . $date->toFormat('%d') . '/' . $postPermalink;

        // If the user didn't enter any values for the custom sef, we'll just load the default one which is the 'date' based
        if (!$custom) {
            return $fallback;
        }

        // Break down parts of the url defined by the admin
        $pieces = explode('/', $custom);

        if (!$pieces) {
            return $fallback;
        }

        $result = array();

        foreach ($pieces as $piece) {

            $piece = str_ireplace('%year_num%', $date->format('Y'), $piece);
            $piece = str_ireplace('%month_num%', $date->format('m'), $piece);
            $piece = str_ireplace('%day_num%', $date->format('d'), $piece);
            $piece = str_ireplace('%day%', $date->format('A'), $piece);
            $piece = str_ireplace('%month%', $date->format('b'), $piece);
            $piece = str_ireplace('%blog_id%', $post->id, $piece);
            $piece = str_ireplace('%category%', $post->getPrimaryCategory()->getAlias(), $piece);
            $piece = str_ireplace('%category_id%', $post->getPrimaryCategory()->id, $piece);

            $result[] = $piece;
        }

        $url = implode('/', $result);
        $url .= '/' . $postPermalink;

        return $url;
    }


	/**
	 * Retrieves the external url
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getRoutedURL($url, $xhtml = false, $external = false, $isCanonical = false)
	{
		// If this is not an external link, just pass it to joomla's router
		if (!$external) {
			return EBR::_($url, $xhtml, null, false, $isCanonical);
		}

		$app = JFactory::getApplication();
		$uri = JURI::getInstance();
		$config = EB::jConfig();

		$dashboard = false;

		// Check if the current menu view is pointing to the dashboard view
		if (!$app->isAdmin()) {

			$menu = JFactory::getApplication()->getMenu()->getActive();

			if (isset($menu->link) && $menu->link) {
				$pos = strpos($menu->link, 'view=dashboard');

				if ($pos !== false) {
					$dashboard = true;
				}
			}
		}

		// Address issues with JRoute as it will include the /administrator/ portion in the url if this link
		// is being generated from the back end.
		if ($app->isAdmin() && EBR::isSefEnabled()) {

			$oriURL = $url;

            // We need to render our own router file.
            require_once(JPATH_ROOT . '/components/com_easyblog/router.php');

			if (! EB::isJoomla30()) {
				// below is required for joomla 2.5
				require_once(JPATH_ROOT . '/includes/router.php');
				require_once(JPATH_ROOT . '/includes/application.php');
			}

			// Here we are tricking Joomla to assume that we are on the front end now.
			JFactory::$application = JApplication::getInstance('site');

			$router = new JRouterSite(array('mode'=>JROUTER_MODE_SEF));

            $rootUri = JURI::root();

            // Get the languagefilter params
			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
			$params = new JRegistry();
			$params->loadString(empty($plugin) ? '' : $plugin->params);
			$langFilterRemoveLangCodeParams = is_null($params) ? 'null' : $params->get('remove_default_prefix', 'null');
				
			$rewrite = $config->get('sef_rewrite');

			// check if the languagefilter plugin enabled
			$pluginEnabled = JPluginHelper::isEnabled('system', 'languagefilter');

			// Check the 'Remove URL Language Code' option is disabled or not from the languagefilter plugin
            if ($pluginEnabled && !$langFilterRemoveLangCodeParams) {

                parse_str($url, $tmpQuery);

				$postId = isset($tmpQuery['id']) ? $tmpQuery['id'] : null;

                // TODO:: get site language
                $lang = JFactory::getLanguage();
                $languages = JLanguageHelper::getLanguages('lang_code');
                $langcode = $languages[$lang->getTag()]->sef;

                if (isset($postId)) {
	                // Get the current post language
	                $post = EB::post($postId);
	                $postLanguage = $post->language;

	                // need to check if the user select all language *
	                if ($postLanguage != '*') {
	                	$langcode = substr($postLanguage, 0, 2);
	                }                	
                }

                // siteDomain.com/index.php/en
                $rootUri = rtrim($rootUri, '/') . '/index.php/' . $langcode . '/';

                // Append the non-def lang into original url
                $oriURL = $oriURL . '&lang=' . $langcode;
            }

			$url = str_replace('/administrator', '/', EBR::_($oriURL, $xhtml, null, $dashboard, $isCanonical));

            if (strpos($url, 'option=com_easyblog') !== false) {
                // this mean someting is screwing up the jrouter. Lets use manual way to build.
                // we need to use $url because this url already has the Itemid that was added by EBR::_();
                $url = $router->build($url);
            }

			$url = rtrim($rootUri, '/') . '/' . ltrim(str_replace('/administrator/', '/', $url), '/');

			// When rewrite is enabled, we need to use the proper paths
			if ($rewrite) {
				$url = ltrim($url , '/');
				$url = str_replace('/index.php', '', $url);
			}

			$container = explode('/', $url);
			$container = array_unique($container);
			$url = implode('/', $container);

			// Update the "application" back so that it knows it's in the administrator area.
			JFactory::$application = JApplication::getInstance('administrator');

			return $url;
		}

		$url = EBR::_($url, $xhtml, null, $dashboard, $isCanonical);
		$url = str_replace('/administrator/', '/', $url);
		$url = ltrim($url, '/');

		// We need to use $uri->toString() because JURI::root() may contain a subfolder which will be duplicated
		// since $url already has the subfolder.
		return $uri->toString(array('scheme', 'host', 'port')) . '/' . $url;
	}

	/**
	 * Better method to replace accents rather than relying on JFilter
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function replaceAccents($string)
	{
        $a = array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß' , 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('AE', 'ae', 'OE', 'oe', 'UE', 'ue', 'ss', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');

        return str_replace($a, $b, $string);
	}

	public static function getItemIdByEntry( $blogId )
	{
		static $entriesItems	= null;

		if( !isset( $entriesItems[ $blogId ] ) )
		{
			$db		= EasyBlogHelper::db();

			// We need to check against the correct latest entry to be used based on the category this article is in
			$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ',' . $db->nameQuote( 'params') . ' FROM ' . $db->nameQuote( '#__menu' )
					. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=latest' )
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
					. EBR::getLanguageQuery();

			$db->setQuery( $query );
			$menus	= $db->loadObjectList();

			$blog	= EB::table('Blog');
			$blog->load( $blogId );

			if ($menus) {
				foreach ($menus as $menu) {

					$params 	= EB::registry($menu->params);
					$inclusion	= EasyBlogHelper::getCategoryInclusion( $params->get( 'inclusion' ) );

					if( empty( $inclusion ) )
					{
						continue;
					}

					if( !is_array( $inclusion ) )
					{
						$inclusion	= array( $inclusion );
					}

					if( in_array( $blog->category_id , $inclusion ) )
					{
						$entriesItems[ $blogId ]	= $menu->id;
					}
				}
			}

			// Test if there is any entry specific view as this will always override the latest above.
			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=entry&id='.$blogId ) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
					. EBR::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			if( $itemid )
			{
				$entriesItems[ $blogId ]    = $itemid;
			}
			else
			{
				// this is to check if we used category menu item from this post or not.
				// if yes, we do nothing. if not, we need to update the cache object so that the next checking will
				// not execute sql again.

				if (isset($entriesItems[ $blogId ])) {
					return $entriesItems[ $blogId ];
				} else
				{
					$entriesItems[ $blogId ] = '';
				}
			}

		}

		return $entriesItems[ $blogId ];
	}

	/**
	 * Retrieves the itemid associated with a dashboard layout
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getItemIdByDashboardLayout($layout)
	{
		static $items = array();

		if (!isset($items[$layout])) {
			$model = EB::model('Menu');
			$items[$layout] = $model->getMenus('dashboard', $layout);
		}

		return $items[$layout];
	}

	/**
	 * Retrieve the itemid associated with a team blog.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getItemIdByTeamBlog($id)
	{
		static $items = array();

		if (!isset($items[$id])) {
			$model = EB::model('Menu');
			$items[$id] = $model->getMenusByTeamId($id);
		}

		return $items[$id];
	}

	/**
	 * Retrieves the itemid based on the all categories listings
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getItemIdByAllCategories()
	{
		static $item = false;

		if (!$item) {
			$model = EB::model('Menu');
			$item = $model->getMenusByAllCategory();
		}

		return $item;
	}

	/**
	 * Retrieves the itemid based on the category id
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getItemIdByCategories($id)
	{
		static $items = array();

		if (!isset($items[$id])) {
			$model = EB::model('Menu');
			$items[$id] = $model->getMenusByCategoryId($id);
		}

		return $items[$id];
	}

	/**
	 * Retrieve menu id by specific blogger
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getItemIdByBlogger($id)
	{
		static $items = array();

		if (!isset($items[$id])) {
			$model = EB::model('Menu');
			$items[$id] = $model->getMenusByBloggerId($id);
		}

		return $items[$id];
	}

	/**
	 * Retrieves itemid associated with a tag id.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getItemIdByTag($id)
	{
		static $items = array();

		if (!isset($items[$id])) {
			$model = EB::model('Menu');
			$items[$id] = $model->getMenusByTagId($id);
		}

		return $items[$id];
	}

	public static function getItemId($view = '', $exactMatch = false)
	{
		static $items	= null;

		if( !isset( $items[ $view ] ) )
		{
			$db	= EasyBlogHelper::db();

			switch($view)
			{
				case 'archive':
					$view='archive';
					break;
				case 'blogger':
					$view='blogger';
					break;
				case 'calendar':
					$view='calendar';
					break;
				case 'categories':
					$view='categories';
					break;
				case 'dashboard':
					$view='dashboard';
					break;
				case 'myblog':
					$view='myblog';
					break;
				case 'profile';
					$view='dashboard&layout=profile';
					break;
				case 'subscription':
					$view='subscription';
					break;
				case 'tags':
					$view='tags';
					break;
				case 'teamblog':
					$view='teamblog';
					break;
				case 'search':
					$view='search';
					break;
				case 'latest':
				default:
					$view='latest';
					break;
			}

			$config 	= EasyBlogHelper::getConfig();
			// $routingBehavior    = $config->get( 'main_routing', 'currentactive');
            $routingBehavior    = $config->get( 'main_routing', 'default');

            // since 5.0, we no longer support currentactive menu item. lets default to 'default' if this is an upgrade from 3.9
            $routingBehavior    = ($routingBehavior == 'currentactive') ? 'default' : $routingBehavior;


			if( $routingBehavior == 'menuitemid' )
			{
				$routingMenuItem    = $config->get('main_routing_itemid','');

				$items[ $view ]	= $routingMenuItem;
			}
			else
			{
				$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
						. 'WHERE (' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view='.$view ) . ' '
						. 'OR ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easyblog&view='.$view.'&limit=%' ) . ') '
						. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
						. self::getLanguageQuery()
						. ' LIMIT 1';
				$db->setQuery( $query );
				$itemid = $db->loadResult();


				if( ! $exactMatch )
				{

					// @rule: Try to fetch based on the current view.
					if( empty( $itemid ) )
					{
						$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
								. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easyblog&view=' . $view . '%' ) . ' '
								. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
								. self::getLanguageQuery()
								. ' LIMIT 1';
						$db->setQuery( $query );
						$itemid = $db->loadResult();
					}

				}

				if(empty($itemid))
				{
					$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
							. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easyblog&view=latest' ) . ' '
							. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
							. self::getLanguageQuery()
							. ' LIMIT 1';
					$db->setQuery( $query );
					$itemid = $db->loadResult();
				}

				//last try. get anything view that from easyblog.
				if(empty($itemid))
				{
					$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
							. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easyblog&view=%' ) . ' '
							. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
							. self::getLanguageQuery()
							. ' ORDER BY `id` LIMIT 1';
					$db->setQuery( $query );
					$itemid = $db->loadResult();
				}

				// if still failed the get any item id, then get the joomla default menu item id.
				if( empty($itemid) )
				{
					$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
							. 'WHERE `home` = ' . $db->Quote( '1' ) . ' '
							. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' )
							. self::getLanguageQuery()
							. ' ORDER BY `id` LIMIT 1';
					$db->setQuery( $query );
					$itemid = $db->loadResult();
				}

				$items[ $view ]	= !empty($itemid)? $itemid : 1;
			}
		}
		return $items[ $view ];
	}

	/**
	 * Goes through Joomla's router to encode the segments
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function encodeSegments($segments)
	{
		$router = new EasyBlogJoomlaRouter();
		$segments = $router->encode($segments);

		return $segments;
	}

	/**
	 * Retrieves the blogger id given the menu id
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getBloggerIdFromMenu($id)
	{
		$model = EB::model('Menu');
		$link = $model->getMenuLink($id);

		parse_str($link, $queryStrings);

		return $queryStrings['id'];
	}

	/**
	 * Determines if the current URL is on blogger mode
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isBloggerMode()
	{
		return EB::isBloggerMode();
	}

	/**
	 * Determines if the menu is a standalone blogger mode
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isMenuABloggerMode($itemId)
	{
		$app = JFactory::getApplication();

		if ($app->isAdmin()) {
			return false;
		}

		$menu = $app->getMenu();
		$params = $menu->getParams($itemId);

		$isBloggerMode = $params->get('standalone_blog', false);

		return $isBloggerMode;
	}

	/**
	 * Determines if the given view is the current active menu item.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isCurrentActiveMenu($view, $id = 0)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu()->getActive();

		if (!$menu) {
			return false;
		}

		if ($id && strpos($menu->link, 'view=' . $view) !== false && strpos($menu->link, 'id=' . $id) !== false) {
			return true;
		}

		if (strpos($menu->link, 'view=' . $view) !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Provides translations for SEF links
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function translate($val)
	{
		$config = EB::config();

		if (!$config->get('main_url_translation', 0)) {
			return $val;
		}

        // Get default site language
        $langParams = JComponentHelper::getParams('com_languages');
        $defaultLang = $langParams->get('site');

		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT, $defaultLang);
		$new = JText::_('COM_EASYBLOG_SEF_' . strtoupper($val));

		// If translation fails, we try to use the original value instead.
		if (stristr($new, 'COM_EASYBLOG_SEF_') === false) {
			return $new;
		}

		return $val;
	}
}

// Deprecated @since 5.0 . Use @EBR instead.
class EasyBlogRouter extends EBR { }
