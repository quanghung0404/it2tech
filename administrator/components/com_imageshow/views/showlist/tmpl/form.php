<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: form.php 16943 2012-10-12 05:00:19Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.pane');
$task 				= JRequest::getVar('task');
$showlistID 		= JRequest::getVar('cid');
$showlistID 		= $showlistID[0];
$msgChangeSource	= JText::_('SHOWLIST_MSG_CHANGE_SOURCE', true);
$changeSource 		= (!empty($this->items->image_source_name)) ? "<button type=\"button\" onclick=\"JSNISImageShow.confirmChangeSource('".$msgChangeSource."', ".(int)$showlistID.", ".(int) $this->countImage.");\" class=\"btn\"><i class=\"icon-folder-open\"></i> ".JText::_('SHOWLIST_CHANGE_SOURCE', true)."</button>" : "";
$text = '';
$objJSNUtils 		= JSNISFactory::getObj('classes.jsn_is_utils');
$products 			= $objJSNUtils->getCurrentElementsOfImageShow();

if (isset($this->items->image_source_name) && $this->items->image_source_name != '')
{
	$text = JText::_('SHOWLIST_TITLE_SHOWLIST_IMAGES');
}
else
{
	$text = JText::_('SHOWLIST_TITLE_SHOWLIST_SOURCES');
}
// Display messages
if (JRequest::getInt('ajax') != 1)
{
	echo $this->msgs;
}
?>
<!--[if IE]>
	<link href="<?php echo JURI::base();?>components/com_imageshow/assets/css/fixie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<div id="jsn-showlist-settings" class="jsn-page-edit jsn-bootstrap">
	<div id="jsn_is_showlist_tabs" class="jsn-tabs">
		<ul>
			<li><a href="#tab-showlist-setting"><i class="icon-home jsn-imageshow-img-tab"></i><?php echo JText::_('SHOWLIST_TITLE_SHOWLIST_DETAILS');?></a></li>
			<li><a href="#tab-showlist-images"><i class="icon-folder-open jsn-imageshow-img-tab"></i><?php echo $text;?></a></li>
		</ul>
		<?php
		echo '<div id="tab-showlist-setting">'.$this->loadTemplate('showlist').'</div>';
		echo '<div id="tab-showlist-images" class="jsn-showlist-images">';
		if($task == 'add'){
			echo '
				<div id="jsn-no-showlist" class="jsn-section-empty jsn-bootstrap">
					<p class="jsn-bglabel"><span class="jsn-icon64 jsn-icon-save"></span>'.JText::_('SHOWLIST_PLEASE_SAVE_THIS_SHOWLIST_BEFORE_SELECTING_IMAGES').'</p>
					<div class="form-actions">
						<a id="jsn-go-link" class="btn" href="javascript:Joomla.submitbutton(\'apply\');">'.JText::_('SHOWLIST_SAVE_SHOWLIST').'</a>
					</div>
				</div>';
		}
		else
		{
			if (isset($this->items->image_source_name) && $this->items->image_source_name != '') {
				echo '<h2 class="jsn-section-header jsn-bootstrap">'.JText::_('SHOWLIST_IMAGES_MANAGER').'&nbsp;'.$changeSource.'</h2>';
				echo $this->loadTemplate('sortable');
			} else {
				echo $this->loadTemplate('sources');
				echo $this->loadTemplate('install_sources');
			}
		}
		echo '</div></div>';
		?>
		<script type="text/javascript">
(function($){
	$(document).ready(function () {
		<?php if($task=='add'){?>
			$('#jsn_is_showlist_tabs').tabs({selected: 0 });
		<?php }else {?>
			$('#jsn_is_showlist_tabs').tabs({selected: 1 });
		<?php }?>
	})
})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
</script>
<?php
include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'contextmenu.php');
?>
	</div>
	<div>
		<div id="jsn-is-dialogbox" class="jsn-display-none"></div>
		<div class="ui-widget-overlay" id="jsn-is-tmp-sbox-window">
			<div class="img-box-loading" id="jsn-is-img-box-loading">
				<img src="components/com_imageshow/assets/images/icons-24/ajax-loader.gif">
			</div>
		</div>
	</div>
	<div id="tmp_id_auto_modal_window"></div>
	<?php JSNHtmlGenerate::footer($products); ?>