<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

include_once JPATH_ROOT . '/administrator/components/com_easyslider/classes/jsn.easyslider.slider.php';

class JSNEasySliderRender
{
    //private $_db = null;
    //private $_mediaObjectMicrodata = null;
    //private $_videoObjectMicrodata = null;
    //private $_imageObjectMicrodata = null;

    //private $_uriBase = '';

    /**
     * Contructor
     */
    public function __construct()
    {
        //$this->_db = JFactory::getDbo();
        //$this->_mediaObjectMicrodata = new JMicrodata('MediaObject');
        //$this->_videoObjectMicrodata = new JMicrodata('VideoObject');
        //$this->_imageObjectMicrodata = new JMicrodata('ImageObject');

        //$this->_uriBase = JURI::root();
        //$this->_uriBase = substr($this->_uriBase, strlen($this->_uriBase) - 1, strlen($this->_uriBase)) == '/' ?
            //substr($this->_uriBase, 0, strlen($this->_uriBase) - 1) :
            //$this->_uriBase;
    }

    /**
     * Render HTML structure
     * @param int $sliderID
     *
     * @return (string)
     */
    public function render($sliderID, $status = false)
    {
    	$content = '';
    	
    	if (is_numeric((int) $sliderID))
        {

        	$objJSNEasySliderSlider = new JSNEasySliderSlider();
        	$data = $objJSNEasySliderSlider->getSliderInfoByID((int) $sliderID);
        	
        	if (count($data))
        	{
        		if ($status)
        		{
	        		if (!(int) $data->published)
	        		{
	        			return $content;
	        		}
        		}
        		
	        	$randID	= $objJSNEasySliderSlider->randomString(10);
	        	$this->_loadAssets();
	        	$content	.= $this->renderArrow((int) $sliderID, $randID);
				$content	.= $this->renderBody((int) $sliderID, $randID);
				$content	.= $this->renderScriptTag((int) $sliderID, $randID, $data);
        	}
        }

        return $content;
    }

    public function renderArrow($sliderID, $randID)
    {
    	$content = '<div class="svg-wrap">
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-left-1" d="M46.077 55.738c0.858 0.867 0.858 2.266 0 3.133s-2.243 0.867-3.101 0l-25.056-25.302c-0.858-0.867-0.858-2.269 0-3.133l25.056-25.306c0.858-0.867 2.243-0.867 3.101 0s0.858 2.266 0 3.133l-22.848 23.738 22.848 23.738z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-right-1" d="M17.919 55.738c-0.858 0.867-0.858 2.266 0 3.133s2.243 0.867 3.101 0l25.056-25.302c0.858-0.867 0.858-2.269 0-3.133l-25.056-25.306c-0.858-0.867-2.243-0.867-3.101 0s-0.858 2.266 0 3.133l22.848 23.738-22.848 23.738z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-left-2" d="M26.667 10.667q1.104 0 1.885 0.781t0.781 1.885q0 1.125-0.792 1.896l-14.104 14.104h41.563q1.104 0 1.885 0.781t0.781 1.885-0.781 1.885-1.885 0.781h-41.563l14.104 14.104q0.792 0.771 0.792 1.896 0 1.104-0.781 1.885t-1.885 0.781q-1.125 0-1.896-0.771l-18.667-18.667q-0.771-0.813-0.771-1.896t0.771-1.896l18.667-18.667q0.792-0.771 1.896-0.771z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-right-2" d="M37.333 10.667q1.125 0 1.896 0.771l18.667 18.667q0.771 0.771 0.771 1.896t-0.771 1.896l-18.667 18.667q-0.771 0.771-1.896 0.771-1.146 0-1.906-0.76t-0.76-1.906q0-1.125 0.771-1.896l14.125-14.104h-41.563q-1.104 0-1.885-0.781t-0.781-1.885 0.781-1.885 1.885-0.781h41.563l-14.125-14.104q-0.771-0.771-0.771-1.896 0-1.146 0.76-1.906t1.906-0.76z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-left-3" d="M44.797 17.28l0.003 29.44-25.6-14.72z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-right-3" d="M19.203 17.28l-0.003 29.44 25.6-14.72z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-left-4" d="M15.946 48l0.003-10.33 47.411 0.003v-11.37h-47.414l0.003-10.304-15.309 16z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-right-4" d="M48.058 48l-0.003-10.33-47.414 0.003v-11.37h47.418l-0.003-10.304 15.306 16z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-left-5" d="M48 10.667q1.104 0 1.885 0.781t0.781 1.885-0.792 1.896l-16.771 16.771 16.771 16.771q0.792 0.792 0.792 1.896t-0.781 1.885-1.885 0.781q-1.125 0-1.896-0.771l-18.667-18.667q-0.771-0.771-0.771-1.896t0.771-1.896l18.667-18.667q0.771-0.771 1.896-0.771zM32 10.667q1.104 0 1.885 0.781t0.781 1.885-0.792 1.896l-16.771 16.771 16.771 16.771q0.792 0.792 0.792 1.896t-0.781 1.885-1.885 0.781q-1.125 0-1.896-0.771l-18.667-18.667q-0.771-0.771-0.771-1.896t0.771-1.896l18.667-18.667q0.771-0.771 1.896-0.771z" />
					</svg>
					<svg width="64" height="64" viewBox="0 0 64 64">
						<path id="arrow-right-5" d="M29.333 10.667q1.104 0 1.875 0.771l18.667 18.667q0.792 0.792 0.792 1.896t-0.792 1.896l-18.667 18.667q-0.771 0.771-1.875 0.771t-1.885-0.781-0.781-1.885q0-1.125 0.771-1.896l16.771-16.771-16.771-16.771q-0.771-0.771-0.771-1.896 0-1.146 0.76-1.906t1.906-0.76zM13.333 10.667q1.104 0 1.875 0.771l18.667 18.667q0.792 0.792 0.792 1.896t-0.792 1.896l-18.667 18.667q-0.771 0.771-1.875 0.771t-1.885-0.781-0.781-1.885q0-1.125 0.771-1.896l16.771-16.771-16.771-16.771q-0.771-0.771-0.771-1.896 0-1.146 0.76-1.906t1.906-0.76z" />
					</svg>
				</div>';
    	return $content;
    	
    }
    
    public function renderBody($sliderID, $randID)
    {
    	$content = '<div id="jsn-es-slider-' . $sliderID . '_' . $randID.'" class="jsn-es-slider jsn-es-slider-' . $sliderID . '">

			<div class="jsn-es-viewport">
                <div class="jsn-es-slide-progress">
                    <div class="jsn-es-slide-progress-bar"></div>
                </div>
				<div class="jsn-es-background slider-background"></div>
				<div class="jsn-es-stage">
					<ul class="jsn-es-slides">
						<li class="jsn-es-slide">
							<div class="jsn-es-swiper"></div>
							<div class="jsn-es-background slide-background">
							    <div class="slide-background-effect"></div>
							</div>

							<ul class="jsn-es-items">
								<li class="jsn-es-item">
									<div class="item-offset">
										<div class="item-animation">
											<a class="item-container">
												<div class="item-background jsn-es-background"></div>
												<div class="item-content"></div>
											</a>
										</div>
									</div>
								</li>
							</ul>
						</li>
					</ul>
					<div class="jsn-es-global jsn-es-slide">
						<ul class="jsn-es-items">
							<li class="jsn-es-item">
								<div class="item-offset">
									<div class="item-animation">
										<a class="item-container">
											<div class="item-background jsn-es-background"></div>
											<div class="item-content"></div>
										</a>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="jsn-es-nav jsn-es-nav-pagination">
					<ul class="jsn-es-pagination">
						<li><a></a></li>
					</ul>
				</div>
				<nav class="jsn-es-nav jsn-es-nav-buttons">
					<a class="jsn-es-prev prev"></a>
					<a class="jsn-es-next next"></a>
				</nav>
			</div>
		</div>';

        //fix render blurry text on iOS (safari)
//        $content .= '<div class="render-text" style="position: fixed;top: 0;left: -1px; width: 1px; height: 1px;"></div>';

    	return $content;
    }
    
    public function renderScriptTag($sliderID, $randID, $slider)
    {
    	$content = '<script type="text/javascript">
	    	window["es" + (++EasySlider.counter)] = new EasySlider({
	    			el: "#jsn-es-slider-' . $sliderID . '_' . $randID . '",
	    			model: new ES_Slider(' . $slider->slider_data . '),
	    			rootURL: "' . JURI::root() . '"
	    		});
	    	</script>';
    	return $content;
    }
    
    /**
     * Load Assets
     */
    protected function _loadAssets()
    {
        $pathOnly = JURI::root(true);
        $pathRoot = JURI::root();
        $document = JFactory::getDocument();

        $document->addStyleSheet($pathOnly . '/plugins/system/easyslider/assets/css/easyslider.css?v=2.07');
        $document->addStyleSheet($pathOnly . '/plugins/system/easyslider/assets/lib/arrows-nav/css/component.css');
        $document->addStyleSheet($pathOnly . '/plugins/system/easyslider/assets/lib/dot-nav/css/component.css');
        $document->addStyleSheet($pathOnly . '/plugins/system/easyslider/assets/lib/font-awesome/css/font-awesome.css');


        $document->addScript($pathOnly . '/media/jui/js/jquery.min.js');

        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/lib/underscore/underscore-min.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/lib/backbone/backbone.js');
        
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/lib/utils.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/lib/jquery.js');
        
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/lib/draggable.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/lib/easing.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/lib/tween.js');
        
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/lib/model.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/lib/view.js');
        
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/conflict.js');
        
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/model/core.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/model/item.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/model/slide.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/model/slider.js');
        
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/controller.js');
        $document->addScript($pathOnly . '/plugins/system/easyslider/assets/js/easyslider.js?v=2.07');
    }
}