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
// No direct access
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$app = JFactory::getApplication();
$timeout = intval(JFactory::getApplication()->getCfg('lifetime') * 60 / 3 * 1000);

// VENDOR CSS
JSNHtmlAsset::addStyle(JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/font-awesome/css/font-awesome.css');
JSNHtmlAsset::addStyle(JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/bootstrap/css/jsn.bootstrap.css');
JSNHtmlAsset::addStyle(JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/arrows-nav/css/component.css');
JSNHtmlAsset::addStyle(JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/dot-nav/css/component.css');
JSNHtmlAsset::addStyle(JSNES_PLG_SYSTEM_ASSETS_URL . 'css/flex.css');

JSNHtmlAsset::addStyle(JURI::root(true) . '/media/editors/codemirror/lib/codemirror.min.css');
JSNHtmlAsset::addStyle(JURI::root(true) . '/media/editors/codemirror/lib/addons.min.css');
JSNHtmlAsset::addStyle(JURI::root(true) . '/media/editors/codemirror/addon/hint/show-hint.min.css');

// MAIN CSS
JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'slider/css/base.css');
JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'slider/css/layout.css');
JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'slider/css/theme.css');
JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'slider/css/custom.css');
JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'slider/css/es-icons.css');

JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'css/slider.css');

JSNHtmlAsset::addStyle(JSNES_ASSETS_URL . 'js/3rd-party/noty/animate.css');


// MAIN HTML
echo $this->loadTemplate('layout');
$token 	= JFactory::getSession()->getFormToken();
?>

<script src="<?php echo (JURI::root(true) . '/media/editors/codemirror/lib/codemirror.min.js'); ?>"></script>


<script src="<?php echo (JURI::root(true) . '/media/editors/codemirror/lib/codemirror.min.js'); ?>"></script>
<script src="<?php echo (JURI::root(true) . '/media/editors/codemirror/addon/edit/closebrackets.min.js'); ?>"></script>
<script src="<?php echo (JURI::root(true) . '/media/editors/codemirror/addon/hint/show-hint.min.js'); ?>"></script>
<script src="<?php echo (JURI::root(true) . '/media/editors/codemirror/addon/hint/javascript-hint.min.js'); ?>"></script>
<script src="<?php echo (JURI::root(true) . '/media/editors/codemirror/addon/hint/css-hint.min.js'); ?>"></script>
<script src="<?php echo (JURI::root(true) . '/media/editors/codemirror/mode/javascript/javascript.min.js'); ?>"></script>
<script src="<?php echo (JURI::root(true) . '/media/editors/codemirror/mode/css/css.min.js'); ?>"></script>

<!--<script src="--><?php //echo (JURI::root(true) . '/media/jui/js/jquery.min.js'); ?><!--"></script>-->

<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/bootstrap/js/bootstrap.min.js'); ?>"></script>

<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/underscore/underscore-min.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/backbone/backbone.js'); ?>"></script>

<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/jquery-ui/jquery-ui.min.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/jquery-ui/jquery-ui.resizable.snap.ext.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/mousetrap.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/tinycolor/colors.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/tinycolor/jqColorPicker.js'); ?>"></script>

<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/rangy/bundle.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/mediumjs/medium.min.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/model.js'); ?>"></script>

<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/utils.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/jquery.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/view.js'); ?>"></script>

<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/easing.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/tween.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/split.js'); ?>"></script>

<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/conflict.js'); ?>"></script>


<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/model/core.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/model/item.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/model/slide.js'); ?>"></script>
<script src="<?php echo (JSNES_PLG_SYSTEM_ASSETS_URL . 'js/model/slider.js'); ?>"></script>

<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/panel.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/picker.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/toolbar.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/canvas.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/grids.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/items.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/layers.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/frames.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/thumbs.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/overlays.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/timeline.js'); ?>"></script>
<script src="<?php echo (JSNES_ASSETS_URL . 'slider/js/view/app.js'); ?>"></script>

<script src="<?php echo (JSNES_ASSETS_URL . '/js/slider.js'); ?>"></script>

<script src="<?php echo (JSNES_ASSETS_URL . 'js/3rd-party/noty/jquery.noty.js'); ?>"></script>

<?php echo $this->loadTemplate('slider_edition');?>

<script type="text/javascript">
    (function($){

        //check IE browser
        var ua = navigator.userAgent,
            M  = ua.match(/(msie|trident(?=\/))\/?\s*(\d+)/i) || [];
        if(M.length > 0){
            $('#es-splash-screen .splash-box.es-warning-box').show();
            $('#es-splash-screen .splash-box.es-loading-box').hide();
        }
        else {
            Object.defineProperties(window, {
                ES_Config: {
                    value: Object.freeze({
                        URL: Object.freeze({
                            ROOT: '<?php echo JURI::root(true); ?>',
                            ADMIN: '<?php echo JURI::base(); ?>',
                            ASSETS: '<?php echo JURI::base() . 'components/com_easyslider/assets/'; ?>',
                            CREATE_SLIDER: 'index.php?option=com_easyslider&task=slider.createNewSlider&<?php echo $token; ?>=1',
                            GET_RATIO_VIDEO: 'index.php?option=com_easyslider&task=slider.getVideoRatio&<?php echo $token; ?>=1',
                            UPDATE_SLIDER: 'index.php?option=com_easyslider&task=slider.updateSliderData&<?php echo $token; ?>=1',
                            SLIDERS_VIEW: 'index.php?option=com_easyslider&view=sliders'
                        })
                    })
                }
            });

            window.app = new ES_App({
                el: '.es-app',
                id: <?php echo (int)$this->slider->slider_id; ?>,
                title: '<?php echo (isset( $this->slider->slider_title ) ? $this->slider->slider_title : 'Untitled EasySlider'); ?>',
                model: new ES_Slider(<?php echo $this->slider->slider_data; ?>)
            });
            setTimeout(function(){
                $('#es-splash-screen').hide();
            }, 500)
        }
    })(jQuery);
</script>

<script type="text/javascript">


(function($){
	$(document).ready(function ()
	{
		var JSNESSlider 	= new jQuery.JSNESSlider({pathRoot: '<?php echo JURI::base(); ?>'});
		JSNESSlider.initialize();
		setInterval(function () {JSNESSlider.refreshSession()}, <?php echo $timeout ?>);
	});
})(jQuery);
</script>