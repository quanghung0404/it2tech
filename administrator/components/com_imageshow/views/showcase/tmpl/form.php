<?php
/**
 * @version    $Id: form.php 16943 2012-10-12 05:00:19Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die( 'Restricted access' );
$edit 	= JRequest::getVar('edit',true);
$editor = JFactory::getEditor();
$cid 	= JRequest::getVar( 'cid', array(0), 'get', 'array' );
$objJSNUtils 		= JSNISFactory::getObj('classes.jsn_is_utils');
$products 			= $objJSNUtils->getCurrentElementsOfImageShow();
$showCaseID = (int) $this->items->showcase_id;
$user 		= JFactory::getUser();
$task		= JRequest::getVar('task');
$text				= '';
$tmpl			  = JRequest::getVar('tmpl','');
$tmpl			  = ($tmpl!='')?'&tmpl=' . $tmpl : '';
if (!is_null(@$themeProfile) || @$theme!='')
{
	$text = JText::_('TITLE_SHOWCASE_THEME_SETTINGS');
}
else
{
	$text = JText::_('TITLE_SHOWCASE_SELECT_THEME');
}
?>
<script type="text/javascript">
	var original_value = '';
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'cancel')
		{
			submitform( pressbutton );
			return;
		}

		if (form.showcase_title.value == "")
		{
			alert( "<?php echo JText::_('SHOWCASE_REQUIRED_FIELD_TITLE_CANNOT_BE_LEFT_BLANK', true); ?>");
			jQuery('#jsn_is_showcase_tabs').tabs({ selected: 0 });
			jQuery('#showcase_title').focus();
			return;
		} else {
			submitform( pressbutton );
		}
	}
	function saveform(){
		<?php if (!$showCaseID) {?>
		document.adminForm.mainSite.value = 'false';
		<?php }?>
		Joomla.submitbutton('apply');
	}
	parent.gIframeFunc = saveform;
	function getInputValue(object)
	{
		original_value = object.value;
	}

	function checkInputValue(object, percent)
	{
		var patt;
		var form 		= document.adminForm;
		var msg;
		if(percent == 1)
		{
			patt=/^[0-9]+(\%)?$/;
			msg = "<?php echo JText::_('SHOWCASE_ALLOW_ONLY_DIGITS_AND_THE_PERCENTAGE_CHARACTER', true); ?>";
		}
		else
		{
			patt=/^[0-9]+$/;
			msg = "<?php echo JText::_('SHOWCASE_ALLOW_ONLY_DIGITS', true); ?>";
		}
		if(!patt.test(object.value))
		{
			alert (msg);
			object.value = original_value;
			return;
		}
	}

	function checkOverallWidth()
	{
		var width	= document.adminForm.general_overall_width;
		var unit	= document.getElementById('overall_width_dimension');

		if (width.value > 100 && unit.value == '%')
		{
			alert("<?php echo JText::_('SHOWCASE_ALLOW_ONLY_VALUE_SMALLER_OR_EQUALLER_THAN_100');?>");
			width.value = 100;
		}
		return true;
	}

	(function($){
		$(document).ready(function () {
				<?php if($task=='add'){?>
				$('#jsn_is_showcase_tabs').tabs();
				<?php }else{?>
				$('#jsn_is_showcase_tabs').tabs({'selected':1});
				<?php } ?>
			});
		})((typeof JoomlaShine != 'undefined' && typeof JoomlaShine.jQuery != 'undefined') ? JoomlaShine.jQuery : jQuery);
</script>
<!--[if IE 7]>
	<link href="<?php echo JURI::base();?>components/com_imageshow/assets/css/fixie7.css" rel="stylesheet" type="text/css" />
<![endif]-->
				<?php

				if ($tmpl != '')
				{
					echo '<span id="toolbar-apply"></span>';
				}
				?>
<div id="jsn-showcase-settings" class="jsn-page-edit" class="form-horizontal">
	<form action="index.php?option=com_imageshow&controller=showcase"
		method="POST" name="adminForm" id="adminForm">
		<div id="jsn_is_showcase_tabs" class="jsn-tabs">
			<ul>
				<li><a href="#jsn-showcase-details-tab" class="jsn-bootstrap"><i class="icon-home jsn-imageshow-img-tab"></i><?php echo JText::_('SHOWCASE_TITLE_SHOWCASE_DETAILS'); ?></a></li>
				<li><a href="#jsn-showcase-themes-tab" class="jsn-bootstrap"><i class="icon-picture jsn-imageshow-img-tab"></i><?php echo $text; ?></a></li>
			</ul>
			<div id="jsn-showcase-details-tab"
				class="jsn-showcase-details jsn-section jsn-bootstrap">
				<div class="form-horizontal">
				<?php
				$uri	        = JURI::getInstance();
				$base['prefix'] = $uri->toString( array('scheme', 'host', 'port'));
				$base['path']   =  rtrim(dirname(str_replace(array('"', '<', '>', "'",'administrator'), '', $_SERVER["PHP_SELF"])), '/\\');
				$url 			= $base['prefix'] . $base['path'] . '/';
				?>

						<div class="row-fluid show-grid">
							<div class="span6">
								<fieldset>
									<legend>
									<?php echo JText::_('SHOWCASE_GENERAL_GENERAL');?>
									</legend>
									<?php
									if($showCaseID != 0){
										?>
									<div class="control-group">
										<label class="control-label"><?php echo JText::_('ID');?> </label>
										<div class="controls">
											<input type="text" value="<?php echo $showCaseID; ?>"
												class="readonly input-mini" size="10" readonly="readonly"
												aria-invalid="false">
										</div>
									</div>
									<?php
									}
									?>
									<div class="control-group">
										<label class="control-label"><?php echo JText::_('SHOWCASE_GENERAL_TITLE');?><span class="star"> *</span>
										</label>
										<div class="controls">
											<input class="jsn-input-xlarge-fluid" type="text"
												value="<?php echo $this->generalData['generalTitle']; ?>"
												name="showcase_title" id="showcase_title" />
										</div>
									</div>
									<div class="control-group">
										<label class="control-label"><?php echo JText::_('SHOWCASE_GENERAL_PUBLISHED');?>
										</label>
										<div class="controls">
										<?php echo $this->lists['published']; ?>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label"><?php echo JText::_('SHOWCASE_GENERAL_ORDER');?>
										</label>
										<div class="controls">
										<?php echo $this->lists['ordering']; ?>
										</div>
									</div>
								</fieldset>
							</div>
							<div class="span6">
								<fieldset>
									<legend>
									<?php echo JText::_('SHOWCASE_GENERAL_DIMENSION'); ?>
									</legend>
									<div class="control-group">
										<label class="control-label editlinktip hasTip"
											title="<?php echo htmlspecialchars(JText::_('SHOWCASE_GENERAL_OVERALL_WIDTH'));?>::<?php echo htmlspecialchars(JText::_('SHOWCASE_GENERAL_OVERALL_WIDTH_DESC')); ?>"><?php echo JText::_('SHOWCASE_GENERAL_OVERALL_WIDTH');?>
										</label>
										<div class="controls">
											<input type="text" class="input-mini" size="5"
												name="general_overall_width"
												value="<?php echo (int) $this->generalData['generalWidth']; ?>"
												onchange="checkInputValue(this, 0); checkOverallWidth();"
												onfocus="getInputValue(this);" />&nbsp;
												<?php echo $this->lists['overallWidthDimension'];?>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label editlinktip hasTip"
											title="<?php echo htmlspecialchars(JText::_('SHOWCASE_GENERAL_OVERALL_HEIGHT'));?>::<?php echo htmlspecialchars(JText::_('SHOWCASE_GENERAL_OVERALL_HEIGHT_DESC')); ?>"><?php echo JText::_('SHOWCASE_GENERAL_OVERALL_HEIGHT');?>
										</label>
										<div class="controls">
											<input type="text" class="input-mini" size="5"
												name="general_overall_height"
												value="<?php echo $this->generalData['generalHeight']; ?>"
												onchange="checkInputValue(this, 0);"
												onfocus="getInputValue(this);" /> <span class="help-inline"><?php echo JText::_('px'); ?>
											</span>
										</div>
									</div>
								</fieldset>
							</div>
						</div>
				</div>
			</div>
			<div id="jsn-showcase-themes-tab"
				class="jsn-showcase-themes jsn-section">
				<?php
				$objShowcaseTheme 	= JSNISFactory::getObj('classes.jsn_is_showcasetheme');
				$objShowcase		= JSNISFactory::getObj('classes.jsn_is_showcase');
				$themes 			= $objShowcaseTheme->listThemes(false);
				$countTheme 		= count($themes);
				$theme				= JRequest::getVar('theme');
				$themeProfile 		= $objShowcaseTheme->getThemeProfile($this->items->showcase_id);
				$totalThemes 		= count($this->needUpdateList) + count($this->needInstallList);
				?>
				<?php if (($totalThemes > 1 && $task == 'edit' && !is_null($themeProfile)) || ($theme != '')) { ?>
				<h2 class="jsn-section-header jsn-bootstrap">
				<?php echo JText::_('SHOWCASE_THEME_SETTINGS');?>
					&nbsp;
					<button type="button"
						onclick="JSNISImageShow.confirmChangeTheme('<?php echo JText::_('SHOWCASE_INSTALL_WARNING_CHANGE_THEME', true)?>', <?php echo $this->items->showcase_id?>);"
						class="btn">
						<i class="icon-picture"></i>
						<?php echo JText::_('SHOWCASE_CHANGE_THEME', true); ?>
					</button>
				</h2>
				<?php }
				$divOpenTag 	= '';
				$divCloseTag 	= '';
				if (empty($showCaseID))
				{
					echo '
					<div id="jsn-no-showcase" class="jsn-section-empty jsn-bootstrap">
						<p class="jsn-bglabel"><span class="jsn-icon64 jsn-icon-save"></span>' . JText::_('SHOWCASE_PLEASE_SAVE_THIS_SHOWCASE_BEFORE_SELECTING_THEME') . '</p>
						<div class="form-actions">
							<a id="jsn-go-link" class="btn" href="javascript: javascript:Joomla.submitbutton(' . "'apply'" . ');">' . JText::_('SHOWCASE_SAVE_SHOWCASE') . '</a>
						</div>
					</div>
					';
				}
				else if ($task == 'add')
				{
					$updateAdd = false;
					if($totalThemes > 1 && $theme == '')
					{
						echo $divOpenTag;
						echo $this->loadTemplate('themes');
						if (count($this->needUpdateList) && count($this->needInstallList)) echo '<hr />';
						echo $this->loadTemplate('install_themes');
						echo $divCloseTag;
					}
					elseif ($totalThemes <= 1 && $theme == '')
					{
						if (count($this->needUpdateList))
						{
							foreach ($this->needUpdateList as $value)
							{
								if ($value->identified_name == @$themes[0]['element'])
								{
									$updateAdd = $value->needUpdate;
									break;
								}
							}
						}
						if ($updateAdd)
						{
							echo $divOpenTag;
							echo $this->loadTemplate('themes');
							echo $divCloseTag;
						}
						else
						{
							$objShowcaseTheme->loadThemeByName(@$themes[0]['element']);
						}
					}
					else
					{
						$objShowcaseTheme->loadThemeByName($theme);
					}
				}
				else
				{
					$update = false;
					if (isset($themeProfile->theme_name) && $objShowcaseTheme->checkThemeExist($themeProfile->theme_name))
					{
						$objShowcaseTheme->loadThemeByName($themeProfile->theme_name, $themeProfile->theme_id);
					}
					else
					{
						if($theme != '')
						{
							$objShowcaseTheme->loadThemeByName($theme);
						}
						else
						{
							if ($totalThemes <= 1 && $theme == '' && count($this->needUpdateList) > 0)
							{
								$objShowcaseTheme->loadThemeByName(@$themes[0]['element']);
							}
							else
							{
								echo $divOpenTag;
								echo $this->loadTemplate('themes');
								//if (count($this->needUpdateList) && count($this->needInstallList)) echo '<hr />';
								echo $this->loadTemplate('install_themes');
								echo $divCloseTag;
							}
						}
					}
				}
				?>
			</div>
		</div>
		<input type="hidden" name="redirectLinkTheme" value="" /> <input
			type="hidden" id="redirectLink" name="redirectLink"
			value="<?php echo ((int)$this->items->showcase_id == 0)?'':'index.php?option=com_imageshow&controller=showcase&task=edit&cid[]=' . (int) $this->items->showcase_id . $tmpl . '&theme='; ?>" />
		<input type="hidden" name="option" value="com_imageshow" /> <input
			type="hidden" name="controller" value="showcase" /> <input
			type="hidden" name="cid[]"
			value="<?php echo (int) $this->items->showcase_id; ?>" /> <input
			type="hidden" name="task" value="" /> <input type="hidden"
			id="mainSite" name="mainSite"
			value="<?php
	if ($task == 'add')
	{
		echo 'true';
	}
	else
	{
		echo ($tmpl!='')?'false':'true';
	}
	?>" /> <input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
	<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
	<?php JSNHtmlGenerate::footer($products); ?>