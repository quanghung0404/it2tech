<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialRouter
{
    /**
     * Stores itself to be used statically.
     * @var SocialRouter
     */
    public static $instances   = array();

    private $adapter    = null;

    /**
     * Creates a copy of it self and return to the caller.
     *
     * @since   1.0
     * @access  public
     * @param   null
     * @return  SocialParameter
     *
     */
    public static function getInstance($view)
    {
        if (!isset(self::$instances[$view])) {
            self::$instances[$view]   = new self( $view );
        }

        return self::$instances[$view];
    }

    /**
     * Class Constructur.
     *
     * @since   1.0
     * @access  public
     * @param   string  The type of routing object.
     */
    public function __construct( $view )
    {
        $file   = dirname( __FILE__ ) . '/adapters/' . $view . '.php';

        if( !JFile::exists( $file ) )
        {
            return false;
        }

        require_once( $file );

        $className      = 'SocialRouter' . ucfirst( $view );
        $this->adapter  = new $className( $view );
    }

    /**
     * Some desc
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function parse( &$segments )
    {
        if( is_null( $this->adapter) )
        {
            return array();
        }

        $vars   = $this->adapter->parse( $segments );

        return $vars;
    }

    /**
     * Some desc
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function build(&$menu, &$query)
    {
        if (is_null($this->adapter) || !method_exists($this->adapter, 'build')) {
            return array();
        }

        $segments = $this->adapter->build($menu, $query);

        return $segments;
    }

    /**
     * Some desc
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function route()
    {
        $args       = func_get_args();

        if( count( $args ) > 0 )
        {
            $options    = $args[0];

            $args[0]['ssl']        = isset( $options['ssl'] ) ? $options['ssl'] : null;
            $args[0]['tokenize']   = isset( $options['tokenize'] ) ? $options['tokenize'] : null;
            $args[0]['external']   = isset( $options['external']  ) ? $options['external'] : null;
            $args[0]['tmpl']       = isset( $options['tmpl'] ) ? $options['tmpl'] : null;
            $args[0]['controller'] = isset( $options['controller'] ) ? $options['controller'] : null;
            $args[0]['sef']        = isset( $options['sef'] ) ? $options['sef'] : null;
        }
        else
        {
            $args[0]               = array();
            $args[0]['ssl']        = null;
            $args[0]['tokenize']   = null;
            $args[0]['external']   = null;
            $args[0]['tmpl']       = null;
            $args[0]['controller'] = '';
            $args[0]['sef']        = null;
        }

        return call_user_func_array( array( $this->adapter , __FUNCTION__ ) , $args );
    }
}

abstract class SocialRouterAdapter
{
    static $base = 'index.php?option=com_easysocial';
    public $name;

    public function __construct($view)
    {
        ES::language()->loadSite();

        $this->doc = JFactory::getDocument();
        $this->name = $view;
    }

    /**
     * Translates a url
     *
     * @since   1.0
     * @access  public
     */
    public static function translate( $str )
    {
        FD::language()->loadSite();
        
        $str = JString::strtoupper($str);
        $text = 'COM_EASYSOCIAL_ROUTER_' . $str;

        return JText::_($text);
    }

    /**
     * Normalizes an array
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function normalize($arr, $index, $default = null)
    {
        if (isset($arr[$index])) {
            return $arr[$index];
        }

        return $default;
    }

    /**
     * Builds the URLs for apps view
     *
     * @since   1.0
     * @access  public
     * @param   array   An array of request arguments
     * @param   bool    Determines if the url should be xhtml compliant
     * @return  url     The string of the URL
     */
    public function route($options = array(), $xhtml = true)
    {
        $url        = self::$base . '&view=' . $this->name;

        // Custom options
        $ssl        = $options['ssl'];
        $tokenize   = $options['tokenize'];
        $external   = $options['external'];
        $tmpl       = $options['tmpl'];
        $sef        = $options['sef'];
        $layout     = isset( $options['layout'] ) ? $options['layout'] : '';
        $type     = isset( $options['type'] ) ? $options['type'] : '';

        // check if the current request is from feed page or not.
        // if yes, let set the external to always true.
        $pageFormat = FD::input()->get('format', '', 'var');

        if (! $external && $pageFormat == 'feed') {
            $external = true;
        }

        // Determines if this is a request to the controller
        $controller = $options['controller'];
        $data       = array();

        unset( $options['ssl'] , $options['tokenize'] , $options['external'] , $options['tmpl'] , $options['controller'], $options['sef'] );

        if ($options) {

            foreach ($options as $key => $value) {
                $data[] = $key . '=' . $value;
            }
        }

        $query      = $options;
        $options    = implode( '&' , $data );
        $join       = !empty( $options ) ? '&' : '';
        $url        = $url . $join . $options;

        // Try to get the url from the adapter
        $overrideUrl    = '';

        // Set temporary data
        $query['view']      = $this->name;
        $query['option']    = 'com_easysocial';

        // Ensure that all query values are lowercased
        $query      = array_map(array('JString', 'strtolower'), $query);

        // Let's find for a suitable menu
        $view       = $this->name;
        $layout     = isset($query['layout']) ? $query['layout'] : '';
        $id         = isset($query['id']) ? (int) $query['id'] : '';

        // For photos and albums, we want to fetch menu from "All Albums"
        if ($view == 'photos' || $view == 'albums') {
            $view   = 'albums';
            $layout = 'all';
            $id     = '';
        }

        $menuId     = FRoute::getItemId($view, $layout, $id, $type);

        if ($menuId) {

            $menu       = JFactory::getApplication()->getMenu()->getItem($menuId);

            if ($menu) {

                $current = $menu->query;
                if (isset($current['id']) && !empty($current['id'])) {
                    $current['id'] = (int) $current['id'];
                }

                if (isset($query['id'])) {
                    $query['id'] = (int) $query['id'];
                }

                $hasDiff    = array_diff($query, $current);

                // // If there's no difference in both sets of query, we can safely assume that there's already
                // // a menu for this link
                if (empty($hasDiff)) {
                    $overrideUrl    = 'index.php?Itemid=' . $menuId;
                }

                //$overrideUrl    = 'index.php?Itemid=' . $menuId;
            }
        }

        // If there are no overriden url's, we append our own item id.
        if ($overrideUrl) {
            $url    = $overrideUrl;
        } else {
            // If there is no getUrl method, we want to get the default item id.
            if ($menuId){
                $url    .= '&Itemid=' . $menuId;
            } else {
                $url    .= '&Itemid=' . FRoute::getItemId($view, $layout);
            }
        }

        return FRoute::_($url, $xhtml, array(), $ssl, $tokenize, $external, $tmpl, $controller, $sef);
    }

    /**
     * Retrieves the user id
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getUserId( $permalink )
    {
        static $loaded  = array();

        // Joomla always replaces the first : with a -
        $permalink  = str_ireplace( ':' , '-' , $permalink );

        if( !isset( $loaded[$permalink] ) )
        {
            $config     = FD::config();

            // Always test for the user's stored permalink first.
            $model      = FD::model( 'Users' );
            $id         = $model->getUserFromPermalink( $permalink );

            if( $id )
            {
                $loaded[$permalink]   = $id;

                return $loaded[$permalink];
            }

            // Always test for the user's stored permalink first.
            $id         = $model->getUserFromAlias( $permalink );

            if( $id )
            {
                $loaded[$permalink]   = $id;

                return $loaded[$permalink];
            }

            // If there's no permalink or alias found for the user, we know the syntax
            // by default would be ID:Username or ID:Full Name
            $loaded[$permalink]       = $this->getIdFromPermalink( $permalink );

            return $loaded[$permalink];
        }

        return $loaded[$permalink];
    }

    /**
     * Returns the user's permalink
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getUserPermalink( $fragment )
    {
        static $users = array();

        if( !isset( $users[$fragment] ) )
        {
            $config = FD::config();

            // Since id is always in ID:alias format.
            $id     = explode( ':' , $fragment );

            $segment    = '';

            if( count( $id ) == 1 )
            {
                $segment    = $id[0];
            }
            else
            {
                // Check whether this is a user alias.
                $permalink  = $id[1];

                // If this is an alias that the user set, just use it as is
                $model  = FD::model( 'Users' );
                if( $config->get( 'users.aliasName' ) == 'username' || $model->isValidUserPermalink( $permalink ) )
                {
                    $segment    = $permalink;
                }
                else
                {
                    // Otherwise, this is a real name and we have to always prepend the id.
                    $segment    = $id[0] . ':' . $permalink;
                }
            }

            $users[$fragment] = $segment;
        }

        return $users[$fragment];
    }

    /**
     * Retrieves the app id
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function getAppId( $alias )
    {
        $parts  = explode( '-' , $alias );

        if( count( $parts ) > 1 )
        {
            return $parts[0];
        }

        $app    = FD::table( 'App' );
        $app->load( array( 'alias' => $alias ) );

        return $app->id;
    }

    /**
     * Retrieves the id based on the permalink
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getIdFromPermalink($permalink, $type = '')
    {
        $id = $permalink;

        if (!empty($type) && $type == SOCIAL_TYPE_USER) {
            $id = $this->getUserId($permalink);

            return $id;
        }

        if (!empty($type) && $type == SOCIAL_TYPE_APPS) {
            $id = $this->getAppId($permalink);

            return $id;
        }

        if (strpos( $permalink , ':' ) !== false) {
            $parts = explode(':', $permalink , 2 );

            $id = $parts[0];
        }

        return $id;
    }

    /**
     * Retrieves a list of layouts from a particular view
     *
     * @since   1.0
     * @access  public
     * @param   string  The name of the view
     * @return
     */
    public function getAvailableLayouts($viewName)
    {
        $viewName   = (string) $viewName;
        $file       = SOCIAL_SITE . '/views/' . strtolower($viewName) . '/view.html.php';

        jimport('joomla.filesystem.file');

        if (!JFile::exists($file)) {
            return array();
        }

        require_once($file);


        $layouts    = get_class_methods('EasySocialView' . $viewName);

        return $layouts;

    }

}
