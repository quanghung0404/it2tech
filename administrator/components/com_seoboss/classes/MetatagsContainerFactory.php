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

class MetatagsContainerFactory{
  public static function getContainerById($type){
    $features = MetatagsContainerFactory::getFeatures();
    $container = null;
    if(isset($features[$type])){
      require_once $features[$type]["file"];
      eval('$container = new '.$features[$type]["class"].'();');
    }
    return $container;
  }

  public static function getContainerByRequest($queryString=null){
    $params = array();
    $resultFeatureId = null;
    $resultFeaturePriority = -1;

    if($queryString!=null){
      parse_str($queryString, $params);
    }
    $features = MetatagsContainerFactory::getFeatures();
    foreach($features as $featureId=>$feature){
      $success = true;
      if(isset($feature["params"])){
        foreach($feature["params"] as $paramsArray){
          $success=true;
          foreach ($paramsArray as $key=>$value){
            if($queryString!=null){
              if($value!==null){
                $success = $success&&(isset($params[$key])&&$params[$key]==$value);
              }else{
                $success = $success&&isset($params[$key]);
              }
            }else{
              if($value!==null){
                $success = $success&&(JRequest::getCmd($key)==$value);
              }else{
                $success = $success&&(JRequest::getCmd($key, null)!==null);
              }
            }
          }
          if($success){
            $resultFeatureId = $featureId;
            break;
          }
        }
      }
      $featurePriority = isset($feature['priority'])?$feature['priority']:0;
      if($success && $featurePriority > $resultFeaturePriority ) {
        $resultFeatureId = $featureId;
        $resultFeaturePriority = $featurePriority;
      }
    }
    return self::getContainerById($resultFeatureId);
  }
  
  public static $metadataByQueryMap = array();
  
  public static function getMetadata($queryString){
    $result = array();
    if(isset(self::$metadataByQueryMap[$queryString])){
      $result = self::$metadataByQueryMap[$queryString];
    } else {
      $container = self::getContainerByRequest($queryString);
      if($container != null){
        $result = $container->getMetadataByRequest($queryString);
        self::$metadataByQueryMap[$queryString] = $result;
      }
    } 
    return $result;
  }
  
  public static function processBody($body, $queryString){
    $container = self::getContainerByRequest($queryString);
    if($container != null){
      $metadata = $container->getMetadataByRequest($queryString);
      //process meta title tag
      if($container->mustReplaceMetaTitle() && $metadata && $metadata["metatitle"]){
        $replaced = 0;
        $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",
            '<meta name="title" content="'.htmlspecialchars($metadata["metatitle"]).'" />', $body, 1, $replaced);
        if($replaced != 1){
          $body = preg_replace('/<head>/i', "<head>\n  <meta name=\"title\" content=\"".htmlspecialchars($metadata["metatitle"]).'" />', $body, 1);
        }
      }elseif($metadata){
        $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",'', $body, 1, $replaced);
      }
      //process meta description tag
      if($container->mustReplaceMetaDescription() && $metadata && $metadata["metadescription"]){
        $replaced = 0;
        $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+description[\\\"\\\']+[^>]*>/i",
            '<meta name="description" content="'.htmlspecialchars($metadata["metadescription"]).'" />', $body, 1, $replaced);
        if($replaced != 1){
          $body = preg_replace('/<head>/i', "<head>\n  <meta name=\"description\" content=\"".htmlspecialchars($metadata["metadescription"]).'" />', $body, 1);
        }
      }
      //process meta keywords tag
      if($container->mustReplaceMetaKeywords() && $metadata && $metadata["metakeywords"]){
        $replaced = 0;
        $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+keywords[\\\"\\\']+[^>]*>/i",
            '<meta name="keywords" content="'.htmlspecialchars($metadata["metakeywords"]).'" />', $body, 1, $replaced);
        if($replaced != 1){
          $body = preg_replace('/<head>/i', "<head>\n  <meta name=\"keywords\" content=\"".htmlspecialchars($metadata["metakeywords"]).'" />', $body, 1);
        }
      }
      
      
           
    }
    return $body;
  }
  
  public static function setMetadataByRequest($query, $metadata){
    $container = self::getContainerByRequest($query);
    if($container != null){
      $container->setMetadataByRequest($query, $metadata);
    }
  }
  

	public static function getFeatures(){
	  if(MetatagsContainerFactory::$features == null){
	    $features  = array();
	    
	    $directoryName = dirname(dirname(__FILE__)).'/features';
	    $db=JFactory::getDBO();
	    $db->setQuery("SELECT component FROM
	        #__seoboss_meta_extensions
	        WHERE available=1 AND enabled=1");
	    $items = $db->loadObjectList();
	    foreach($items as $item){
          include $directoryName."/".$item->component.".php";
        }

        MetatagsContainerFactory::$features = $features;
	  }
      return MetatagsContainerFactory::$features ;
	}
	
	public static function refreshFeatures(){
	  $result = array();
	  $db = JFactory::getDBO();
	  $db->setQuery("SELECT component, available FROM #__seoboss_meta_extensions");
	  $extensions = $db->loadObjectList();
	  foreach($extensions as $extension){
	    $features = array();
	    require(dirname(__FILE__)."/../features/".$extension->component.".php");
	    $available = true;
	    foreach($features as $feature){
	        if(is_file(dirname(__FILE__)."/".$feature["file"])){
	          require_once(dirname(__FILE__)."/".$feature["file"]);
	          $container = new $feature["class"]();
	          $available = $available && $container->isAvailable();
	        }else{
	          $available = false;
	        } 
	    }
	    
	    $db->setQuery("UPDATE #__seoboss_meta_extensions SET available=".($available?1:0)."
	        WHERE component=".$db->quote($extension->component));
	    $db->query();
	    $result[$extension->component] = $available;
	  }
	  return $result;
	}
	
	public static function getJoomlaVersion(){
	    if(MetatagsContainerFactory::$version==null){
	        jimport("joomla.version");
	        $version = new JVersion();
	        MetatagsContainerFactory::$version = $version->RELEASE;
	    }
	    return MetatagsContainerFactory::$version;
	}
	
	public static function componentExists($name, $j15name=null){
	  $db = JFactory::getDBO();
	  $sql = "";
	  if(self::getJoomlaVersion() == "1.5"){
	    $sql = "SELECT 1 FROM #__components where LOWER(name)=".$db->quote(strtolower($j15name!=null?$j15name:$name));
	  }else{
	    $sql = "SELECT 1 FROM #__assets where LOWER(name)=".$db->quote(strtolower($name));
	  }
	  $db->setQuery($sql);
	  return $db->loadResult() == "1";
	}
	
	public static function getAllFeatures(){
	  $db=JFactory::getDBO();
	  $db->setQuery("SELECT name, component, description, enabled FROM
	      #__seoboss_meta_extensions WHERE available=1");
      $features = $db->loadAssocList();
      return $features;
	}
	
	public static function enableFeature($feature){
	  $db = JFactory::getDBO();
	  $db->setQuery("UPDATE #__seoboss_meta_extensions SET enabled=1 WHERE component=".$db->quote($feature));
	  $db->query();
	}
	
	public static function disableFeature($feature){
	  $db = JFactory::getDBO();
	  $db->setQuery("UPDATE #__seoboss_meta_extensions SET enabled=0 WHERE component=".$db->quote($feature));
	  $db->query();
	}
	
	private static $features = null;
	private static $version = null;
}
?>
