<?php
/*------------------------------------------------------------------------
# SEO Boss
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$mainframe = JFactory::getApplication();
$mainframe->registerEvent( 'onAfterContentSave',
'pluginSeoBoss_onAfterContentSave' );
$mainframe->registerEvent( 'onContentAfterSave',
  'pluginSeoBoss_onContentAfterSave' );

function pluginSeoBoss_onAfterContentSave( $article, $isNew ){
    $file = JPATH_ADMINISTRATOR."/components/com_seoboss/classes/ArticleMetatagsContainer.php";
    if(is_object($article) && 
	isset($article->id) && $article->id && 
	isset($article->metakey) && $article->metakey &&
        is_file( $file )) {
        require_once( $file );
        $ac = new ArticleMetatagsContainer();
        $ac->saveKeywords($article->metakey, $article->id);
    }
  $db = JFactory::getDBO();
  $db->setQuery ("SELECT enable_google_ping from  #__seoboss_settings " );
  $settings = $db->loadObject();
  if($settings->enable_google_ping){
    $className = get_class($article);

    require_once JPATH_ADMINISTRATOR."/components/com_seoboss/classes/ExtensionsFactory.php";
    $extensions = ExtensionsFactory::getExtensions();

    if(is_array($extensions) && is_array($extensions['ping'])){
      foreach( $extensions['ping'] as $pingHandler ){
        if($pingHandler['class'] == $className){
          require_once JPATH_ADMINISTRATOR."/components/com_seoboss/".$pingHandler['file'];
          $url = '';
          $rss = '';
          if( isset($pingHandler['function']) &&
            function_exists( $pingHandler['function'] ) ){
            eval('$url='.$pingHandler['function'].'($article, $isNew);');
          }
          if( isset($pingHandler['rss_function']) &&
            function_exists($pingHandler['rss_function'] ) ){
            eval('$rss='.$pingHandler['rss_function'].'();');
          }
          if($url){
            
            $db->setQuery("SELECT `domain` FROM `#__seoboss_settings`");
            $domainName = $db->loadResult();
            $url= SeoBoss_getSEFURL($domainName, $url);
            
            require_once JPATH_ADMINISTRATOR."/components/com_seoboss/classes/Pinger.php";
            $config = JFactory::getConfig();
            $pinger = new Pinger;
            $result = $pinger->pingGoogle(
              $config->get( 'config.sitename' ),
              "http://$domainName",
              "http://{$domainName}$url",
              "http://{$domainName}$rss"
            );
            $db->setQuery("INSERT INTO #__seoboss_ping_status
                                    (`date`, `title`, `url`, `response_code`, `response_text`) VALUES (
                                    NOW(), ".$db->quote($article->title).", ".
              $db->quote($url).", ".
              $db->quote($result[0]).",".
              $db->quote($result[1]).")");
            $db->query();
          }
          break;
        }
      }
    }
  }
}
function SeoBoss_getSEFURL($domainName, $url){
  jimport('joomla.http');
  jimport('joomla.http.transport.curl');
  jimport('joomla.http.transport.socket');
  jimport('joomla.http.transport.stream');
 
  $registry = new JRegistry();
  try{
    $transport = new JHttpTransportCurl($registry);
  }catch(Exception $e){}
  if(!$transport){
    try{
      $transport = new JHttpTransportSocket($registry);
    }catch(Exception $e){}
  }
  if(!$transport){
    try{
      $transport = new JHttpTransportStream($registry);
    }catch(Exception $e){}
  }
  if($transport){
      $http=new JHttp($registry, $transport);
      $response = $http->get($domainName."/index.php?option=com_seoboss&task=getSEFURL&format=raw&url=".base64_encode($url));
      if($response->code==301){
          $response = $http->get($response->headers['Location']);
      }

      if($response->code==200){
        $url= $response->body;
      }
  }
  return $url;
}
function pluginSeoBoss_onContentAfterSave($context, $article, $isNew){
  pluginSeoBoss_onAfterContentSave($article, $isNew);

}
