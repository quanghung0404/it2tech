<?php

/**
 * @version     $Id: default.php 19013 2012-11-28 04:48:47Z thailv $
 * @package     JSNUniform
 * @subpackage  Form
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
// Display messages
if ( JFactory::getApplication()->input->getInt( 'ajax' ) != 1 ) {
	echo $this->msgs;
}
$edition = defined( 'JSN_MOBILIZE_EDITION' ) ? JSN_MOBILIZE_EDITION : "free";
?>
<div class="jsn-page-settings jsn-bootstrap">
	<div id="setStyle">
		<style></style>
	</div>
	<form name="adminForm" method="post" id="adminForm" class="hide jsn-mobilize-form">
		<?php echo $this->_form->getInput( 'profile_id' ) ?>
		<div class="jsn-tabs">
			<ul>
				<li class="active">
					<a id="li-general" href="#general">
						<i class="icon-home"></i><?php echo JText::_( 'JSN_MOBILIZE_PROFILE_GENERAL' ); ?>
					</a>
				</li>
				<li>
					<a id="li-design" href="#design">
						<i class="icon-color-palette"></i><?php echo JText::_( 'JSN_MOBILIZE_PROFILE_DESIGN' ); ?>
					</a>
				</li>
			</ul>
			<div class="tab-pane active" id="general">
				<div class="row-fluid form-horizontal">
					<div class="span6">
						<fieldset>
							<legend><?php echo JText::_( 'JSN_MOBILIZE_PROFILE_DETAILS' ); ?></legend>
							<div class="control-group">
								<label class="control-label "
								       original-title="<?php echo JText::_( 'JSN_MOBILIZE_SET_THE_PROFILE_TITLE' ); ?>"><?php echo JText::_( 'JSN_MOBILIZE_PROFILE_TITLE' ); ?></label>

								<div class="controls">
									<?php echo $this->_form->getInput( 'profile_title' ) ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label "
								       original-title="<?php echo JText::_( 'JSN_MOBILIZE_SET_THE_PROFILE_DES' ); ?>"><?php echo JText::_( 'JSN_MOBILIZE_PROFILE_DESC' ); ?></label>

								<div class="controls">
									<?php echo $this->_form->getInput( 'profile_description' ) ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label "
								       original-title="<?php echo JText::_( 'JSN_MOBILIZE_SELECT_THE_PROFILE_LAYOUT' ); ?>"><?php echo JText::_( 'JLAYOUT' ); ?>
								</label>
								<div class="controls">
                                    <?php echo $this->_form->getInput( 'profile_device' ) ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label "
								       original-title="<?php echo JText::_( 'JSN_MOBILIZE_SELECT_THE_PROFILE_STATUS_TO_INDICATE' ); ?>"><?php echo JText::_( 'JSTATUS' ); ?></label>

								<div class="controls">
									<?php echo $this->_form->getInput( 'profile_state' ) ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label jsn-tipsy"
								       original-title="<?php echo JText::_( 'JSN_MOBILIZE_MINIFY_ASSETS_DESC' ); ?>"><?php echo JText::_( 'JSN_MOBILIZE_MINIFY_ASSETS_LABEL' ); ?></label>

								<div class="controls">
									<?php echo $this->_form->getInput( 'profile_minify' ) ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label jsn-tipsy"
								       original-title="<?php echo JText::_( 'JSN_MOBILIZE_OPTIMIZE_IMAGE_DESC' ); ?>"><?php echo JText::_( 'JSN_MOBILIZE_OPTIMIZE_IMAGE_LABEL' ); ?></label>

								<div class="controls">
									<?php echo $this->_form->getInput( 'profile_optimize_images' ) ?>
								</div>
							</div>
						</fieldset>
					</div>
					<div class="span6">
						<fieldset>
							<legend><?php echo JText::_( 'JSN_MOBILIZE_PROFILE_OPTIONS' ); ?></legend>
							<div class="control-group">
								<label class="control-label " original-title="<?php echo JText::_( 'JSN_MOBILIZE_SELECT_THE_PROFILE_OS_SUPPORT' ); ?>"><?php echo JText::_( 'JSN_MOBILIZE_PROFILE_OS_SUPPORT' ); ?></label>
								<div class="controls">
									<div id="os-support" class="jsn-items-list-container">
										<div class="jsn-items-list ui-sortable">
											<?php
											foreach ( $this->_os as $os ) {
												$check = "";
												if ( in_array( $os->os_id, $this->_osSupport ) || ! $this->_item->profile_id ) {
													$check = 'checked="true"';
												}
												?>
												<div class="jsn-item ui-state-default" style="">
													<label
													  class="checkbox">
														<input <?php echo $check;?> type="checkbox" name="ossupport[]" value="<?php echo $os->os_id;?>">
														<?php echo $os->os_title;?>
													</label>
												</div>
												<?php
											}
											?>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
			<div id="design">
				<div class="jsn-mobilize">
					<div class="jsn-form-bar">
						<div class="btn-group jsn-inline">
							<button type="button" class="btn mobilize_view_layout" id="mobile_ui_enabled">
								<i class="icon-mobile"></i><?php echo JText::_( 'JSN_MOBILIZE_TITLE_SMARTPHONE' ); ?>
							</button>
							<button type="button" class="btn mobilize_view_layout" id="tablet_ui_enabled">
								<i class="icon-tablet"></i><?php echo JText::_( 'JSN_MOBILIZE_TITLE_TABLET' ); ?>
							</button>
							<button onclick="return false;" class="btn" id="select_profile_style">
								<i class="icon-loop"></i><?php echo JText::_( 'JSN_MOBILIZE_LOAD_STYLE' );?>
							</button>
						</div>
						<div class="pull-right">
							<button onclick="return false;" class="btn jsn-iconbar" id="template_style">
								<?php echo JText::_( 'JSN_MOBILIZE_TEMPLATE_STYLE' );?>
							</button>
							<button onclick="return false;" class="btn" id="select_profile_css">
								</i><?php echo JText::_( 'JSN_MOBILIZE_ADD_CUSTOM_CSS' );?>
							</button>
							
							<div class="jsn-bootstrap" id="container-select-style">
								<div class="popover bottom">
									<div class="arrow"></div>
									<h3 class="popover-title"><?php echo JText::_( 'JSN_MOBILIZE_LOAD_STYLE' );?></h3>
								</div>
							</div>
							<div id="container-custom-css-hide" class="hide"><ul id="custom-css-list-file"><?php
								if ( ! empty( $this->_dataDesign[ 'mobilize-custom-css-files' ] ) ) {
									foreach ( $this->_dataDesign[ 'mobilize-custom-css-files' ] as $file ) {
										echo '<li class="jsn-item ui-state-default" ><label class="checkbox"><input type="hidden" value="' . $file . '" name="mobilize_custom_css_files[]">' . $file . '</label></li>';
									}
								}
								?></ul><input type="hidden" name="mobilize_custom_css_code" id="custom-css-code" value="<?php echo isset( $this->_dataDesign['mobilize-custom-css-code'] ) ? $this->_dataDesign['mobilize-custom-css-code'] : '';?>"></div>
							<div class="jsn-bootstrap" id="container-custom-css">
								<!-- CSS files -->
								<div class="control-group jsn-items-list-container ig-modal-content">
									<label class="control-label"><?php echo JText::_( 'JSN_MOBILIZE_CUSTOM_CSS_FILES' );?>
										<i data-title= "<?php echo JText::_( 'JSN_MOBILIZE_DATA_TITLE_CUSTOM_CSS_FILES' );?>" class="control-label jsn-tipsy icon-question-sign" original-title="<?php echo JText::_( 'JSN_MOBILIZE_DATA_TITLE_CUSTOM_CSS_FILES' );?>"></i></label>
										
									<div class="controls">
										<div class="jsn-buttonbar">
											<button class="btn btn-small" id="items-list-edit">
												<i class="icon-pencil"></i>Edit
											</button>
											<button class="btn btn-small btn-primary hide" id="items-list-save">
												<i class="icon-ok"></i>Done
											</button>
										</div>
										<ul class="jsn-items-list ui-sortable css-files-container">
											<?php
											if ( ! empty( $this->_dataDesign[ 'mobilize-custom-css-files' ] ) ) {
												foreach ( $this->_dataDesign[ 'mobilize-custom-css-files' ] as $file ) {
													echo '<li class="jsn-item ui-state-default" ><label class="checkbox"><input type="hidden" value="' . $file . '" name="mobilize_custom_css_files[]">' . $file . '</label></li>';
												}
											}
											?>
										</ul>
										<div class="items-list-edit-content hide">
											<textarea rows="5" class="jsn-input-xxlarge-fluid"></textarea></div>
									</div>
								</div>
								<!-- Custom CSS code -->
								<div class="control-group jsn-items-list-container ig-modal-content">
									<label class="control-label"><?php echo JText::_( 'JSN_MOBILIZE_CUSTOM_CSS_CODE' );?>
										<i data-title="<?php echo JText::_( 'JSN_MOBILIZE_DATA_TITLE_CUSTOM_CSS_CODE' );?>" class="jsn-tipsy icon-question-sign" original-title="<?php echo JText::_( 'JSN_MOBILIZE_DATA_TITLE_CUSTOM_CSS_CODE' );?>"></i></label>
									<div class="controls">
										<textarea rows="10" class="jsn-input-xxlarge-fluid css-code"  id="custom-css"><?php echo isset( $this->_dataDesign['mobilize-custom-css-code'] ) ? $this->_dataDesign['mobilize-custom-css-code'] : '';?></textarea>
									</div>
								</div>
							</div>
							
<!--Begin Dialog Load Style-->
							<div class="jsn-bootstrap" id="container-load-style">
								<div class="popover-content">
									<div id="profile-style-list" class="jsn-columns-container jsn-columns-count-three">
									</div>
								</div>
								
							</div>
<!--End Dialog Load Style-->
<!--Begin Dialog Social-->																					
							<div class="jsn-bootstrap" id="container-custom-social">
								<div class="control-group jsn_social">
									<?php
									$social = !empty( $this->_style->jsn_social ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_social ) : $this->_style->jsn_social : '{"fa-facebook":{"0":"","1":{"0":"choised","1":"none"}},"fa-twitter":{"0":"","1":{"0":"choised","1":"none"}},"fa-google-plus":{"0":"","1":{"0":"choised","1":"none"}},"fa-instagram":{"0":"","1":{"0":"choised","1":"none"}},"fa-youtube-play":{"0":"","1":{"0":"choised","1":"none"}},"fa-linkedin":{"0":"","1":{"0":"choised","1":"none"}},"fa-pinterest":{"0":"","1":{"0":"choised","1":"none"}},"fa-flickr":{"0":"","1":{"0":"choised","1":"none"}},"fa-tumblr":{"0":"","1":{"0":"choised","1":"none"}},"fa-vimeo-square":{"0":"","1":{"0":"choised","1":"none"}},"fa-deviantart":{"0":"","1":{"0":"choised","1":"none"}},"fa-digg":{"0":"","1":{"0":"choised","1":"none"}},"fa-dribbble":{"0":"","1":{"0":"choised","1":"none"}},"fa-behance":{"0":"","1":{"0":"choised","1":"none"}}}';
									if(isset($social) &&!empty($social)):
										$arr = json_decode($social);
										$i=0;
										foreach ($arr as $key => $val):
											$arrTitle = explode('-', $key);
											$arrVal = $this->arrVal($val);
											$status = $this->arrVal($arrVal[1]);
											if($arrVal[0]!=''){$i++;}
									?>
									<div class="jsn_block_social" id='social_fb'>
										<table border='0'>
											<tr>
												<td width='40%'><a href="#" id="<?= $key ?>" class="font-icon"><i class="fa <?= $key ?>"></i></a><span><?=ucfirst($arrTitle[1])?> URL</span></td>
												<td>
													<input type="text" name="social[]" data-title="<?= $key ?>" placeholder="https://" value="<?=$arrVal[0]?>"><br>
													<span class="error-<?= $key ?>"></span>
												</td>
											</tr>
											<tr>
												<td></td>
												<td id="social_status">
													<span>
														<button class="btn btn0" id="<?=$status[0]?>">Show</button>
														<button class="btn btn1" id="<?=$status[1]?>">Hide</button>
													</span>
												</td>
											</tr>
										</table>
									</div>
									<?php
										endforeach;
									endif; ?>
									<?php // echo $i;?>
									<div class="social_more"><table width='100%'><tr><td width='45%'><hr></td><td><a href="javascript:void(0)" id="showmore" >+ More</a></td><td width='45%'><hr></td></tr></table></div>
							</div>
						</div>
<!--End Dialog Social-->
					</div>
					<hr>
					<div class="container-fluid">
						<div class="jsn-mobilize-settings">
							<div id="mobilize" class="jsn-sortable">
								<div class="jsn-pane jsn-bgpattern pattern-sidebar">
									<div class="mobilize-title jsn-section-header">
										<h1>
											<?php echo JText::_( 'JSN_MOBILIZE_TITLE_TABLET' ); ?>
										</h1>
										<div class="jsn-page-actions jsn-buttonbar">
											<button class="btn mobilize-preview" text-disable="<?php echo JText::_( 'JSN_MOBILIZE_BTN_DISABLE_PREVIEW' ); ?>" text-enable="<?php echo JText::_( 'JSN_MOBILIZE_BTN_ENABLE_PREVIEW' ); ?>">
												<i class="icon-eye-open"></i><?php echo JText::_( 'JSN_MOBILIZE_BTN_ENABLE_PREVIEW' ); ?>
											</button>
											</a>
										</div>
									</div>
									<div id="mobilize-design" class="jsn-section-content">
										<div id="jsn-mobilize" class="jsn-layout">
											<div class="jsn-row-container row-fluid jsn_modul_template">
												<div id="jsn-template" class="jsn-column-container clearafter">
													<h2></h2>
													<p></p>
													<a></a>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action='template' id='jsn_template_click' href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleTemplate = ! empty( $this->_style->jsn_template ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_template ) : $this->_style->jsn_template : ''; ?>
													<input type="hidden" id="input_style_jsn_template" class="jsn-input-style" name="style[jsn_template]" value='<?php echo  htmlentities( $styleTemplate );?>' />
												</div>
											</div>
                                            <div class="jsn-row-container row-fluid jsn_modul_typestyle">
												<div class="jsn-iconbar jsn-vertical jsn-iconbar-hidden">
													<?php $styleType = ! empty( $this->_style->jsn_typestyle ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_typestyle ) : $this->_style->jsn_typestyle : ''; ?>
													<input type="hidden" id="input_style_jsn_typestyle" class="jsn-input-style" name="style[jsn_typestyle]" value='<?php echo  htmlentities( $styleType );?>' />
												</div>
											</div>
											<div class="jsn-row-container row-fluid">
												<div id="jsn-menu" class="jsn-column-container clearafter">
														<div id="jsn-logo">
															<div>
																<?php echo $this->_JSNMobilize->getItemsLogo( 'mobilize-logo', 'JSN_MOBILIZE_SELECT_LOGO', $this->_styleIcon ); ?>
															</div>
														</div>
													<ul class="mobilize-menu nav nav-pills jsn-sidetool pull-right">
														<?php echo $this->_JSNMobilize->getItemsMenuIcon( 'mobilize-login', 'JSN_MOBILIZE_MENU_LOGIN', 'text', 'icon-user', $this->_styleIcon ); ?>
														<?php echo $this->_JSNMobilize->getItemsMenuIcon( 'mobilize-search', 'JSN_MOBILIZE_MENU_SEARCH', 'text', 'icon-search', $this->_styleIcon ); ?>
														<?php echo $this->_JSNMobilize->getItemsMenuIcon( 'mobilize-menu', 'JSN_MOBILIZE_MENU', 'text', 'icon-list-alt', $this->_styleIcon ); ?>
													</ul>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="menu" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleMenu = ! empty( $this->_style->jsn_menu ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_menu ) : $this->_style->jsn_menu : ''; ?>
													<input type="hidden" id="input_style_jsn_menu" class="jsn-input-style" name="style[jsn_menu]" value='<?php echo  htmlentities( $styleMenu );?>' />
												</div>
											</div>
											
											<div class="jsn-row-container row-fluid">
												<div id="jsn-mobile-tool" class="jsn-column-container clearafter">
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-mobile-tool-left", "span6" ); ?>
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-mobile-tool-right", "span6" ); ?>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="module" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleTool = ! empty( $this->_style->jsn_mobile_tool ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_mobile_tool ) : $this->_style->jsn_mobile_tool : ''; ?>
													<input type="hidden" id="input_style_jsn_mobile_tool" class="jsn-input-style" name="style[jsn_mobile_tool]" value='<?php echo  htmlentities( $styleTool );?>' />
												</div>
											</div>

											<div class="jsn-row-container row-fluid">
												<div id="jsn-content-top" class="jsn-column-container clearafter">
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-content-top-left", "span6" ); ?>
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-content-top-right", "span6" ); ?>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="module" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleContentTop = ! empty( $this->_style->jsn_content_top ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_content_top ) : $this->_style->jsn_content_top : ''; ?>
													<input type="hidden" id="input_style_jsn_content_top" class="jsn-input-style" name="style[jsn_content_top]" value='<?php echo  htmlentities( $styleContentTop );?>' />
												</div>
											</div>
											<div class="jsn-row-container row-fluid">
												<div id="jsn-mainbody" class="jsn-column-container clearafter">
													<h2><?php echo JText::_( 'JSN_MOBILIZE_COMPONENT_OUTPUT_GO_HERE' ); ?></h2>

													<p>
														You can edit style for background, padding, border or text color of container and module content with Edit icon on the right.
													</p>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="mainbody" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleMainBody = ! empty( $this->_style->jsn_mainbody ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_mainbody ) : $this->_style->jsn_mainbody : ''; ?>
													<input type="hidden" id="input_style_jsn_mainbody" class="jsn-input-style" name="style[jsn_mainbody]" value='<?php echo  htmlentities( $styleMainBody );?>' />
												</div>
											</div>
											<div class="jsn-row-container row-fluid">
												<div id="jsn-user-top" class="jsn-column-container clearafter">
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-user-top-left", "span6" ); ?>
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-user-top-right", "span6" ); ?>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="module" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleUserTop = ! empty( $this->_style->jsn_user_top ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_user_top ) : $this->_style->jsn_user_top : ''; ?>
													<input type="hidden" id="input_style_jsn_user_top" class="jsn-input-style" name="style[jsn_user_top]" value='<?php echo  htmlentities( $styleUserTop );?>' />
												</div>
											</div>
											<div class="jsn-row-container row-fluid">
												<div id="jsn-user-bottom" class="jsn-column-container clearafter">
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-user-bottom-left", "span6" ); ?>
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-user-bottom-right", "span6" ); ?>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="module" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleUserBottom = ! empty( $this->_style->jsn_user_bottom ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_user_bottom ) : $this->_style->jsn_user_bottom : ''; ?>
													<input type="hidden" id="input_style_jsn_user_bottom" class="jsn-input-style" name="style[jsn_user_bottom]" value='<?php echo  htmlentities( $styleUserBottom );?>' />
												</div>
											</div>
											<div class="jsn-row-container row-fluid">

												<div id="jsn-content-bottom" class="jsn-column-container clearafter">
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-content-bottom-left", "span6" ); ?>
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-content-bottom-right", "span6" ); ?>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="module" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleContentBottom = ! empty( $this->_style->jsn_content_bottom ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_content_bottom ) : $this->_style->jsn_content_bottom : ''; ?>
													<input type="hidden" id="input_style_jsn_content_bottom" class="jsn-input-style" name="style[jsn_content_bottom]" value='<?php echo  htmlentities( $styleContentBottom );?>' />
												</div>
											</div>
											<div class="jsn-total">
											<div class="jsn-row-container row-fluid">
												<div id="jsn-footer" class="jsn-column-container clearafter" style="<?php echo isset( $this->_styleContainer[ "jsn_footer" ] ) ? implode( "; ", $this->_styleContainer[ "jsn_footer" ] ) : "";?>">
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-footer-left", "span6" ); ?>
													<?php echo $this->_JSNMobilize->getBlockContent( "mobilize-footer-right", "span6" ); ?>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="module" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleFooter = ! empty( $this->_style->jsn_footer ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_footer ) : $this->_style->jsn_footer : ''; ?>
													<input type="hidden" id="input_style_jsn_footer" class="jsn-input-style" name="style[jsn_footer]" value='<?php echo  htmlentities( $styleFooter );?>' />
												</div>
											</div>
											<div class="jsn-row-container row-fluid jsn-style">
												<div id="jsn-style">
													<a data-action='style' id='jsn_template_click' href="javascript:void(0);"></a>
													<?php $style = ! empty( $this->_style->jsn_style ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->style ) : $this->_style->jsn_style : ''; ?>
													<input type="hidden" id="input_style_jsn_style" class="jsn-input-style" name="style[jsn_style]" value='<?php echo  htmlentities( $style );?>' />
												</div>
											</div>
											<div class="jsn-row-container row-fluid" >
												<div id="jsn-social" class="jsn-column-container clearafter" >
                                                    <div class="social_div">
                                                        <?php $social = ! empty( $this->_style->jsn_social ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_social ) : $this->_style->jsn_social : '';
                                                            $cm = 0;
															if(isset($social) && !empty($social)):
                                                                $arr = json_decode($social);
                                                                foreach ($arr as $key=>$val):
																	$arrVl = $this->arrVal($val);
																	if($arrVl[0] !=''):
																		$cm = 1;
																		$status = $this->arrVal($arrVl[1]);
                                                        ?>
														<a href="<?=$arrVl[0]?>" id="<?=$key?>" target="_blank" class="font-icon <?=$status[0]?>"><i class="fa <?=$key?>"></i></a>                                                    
                                                        <?php endif;endforeach;
															if($cm == 1):
														?>
															<input type="hidden" class="jsn-input-style" name="style[jsn_social]" value='<?=$social?>' id="social_input" />
														<?php endif;endif;
															if($cm == 0):
														?>
                                                            <a href="https://facebook.com" target="_blank" id="fa-facebook" class="font-icon"><i class="fa fa-facebook"></i></a>                                                    
                                                            <a href="https://plus.google.com" target="_blank" id="fa-google-plus" class="font-icon"><i class="fa fa-google-plus"></i></a>                                                    
                                                            <a href="https://twitter.com" target="_blank" id="fa-twitter" class="font-icon"><i class="fa fa-twitter"></i></a>                                                    
															<input type="hidden" class="jsn-input-style" name="style[jsn_social]" value='{"fa-facebook":{"0":"","1":{"0":"choised","1":"none"}},"fa-twitter":{"0":"","1":{"0":"choised","1":"none"}},"fa-google-plus":{"0":"","1":{"0":"choised","1":"none"}},"fa-instagram":{"0":"","1":{"0":"choised","1":"none"}},"fa-youtube-play":{"0":"","1":{"0":"choised","1":"none"}},"fa-linkedin":{"0":"","1":{"0":"choised","1":"none"}},"fa-pinterest":{"0":"","1":{"0":"choised","1":"none"}},"fa-flickr":{"0":"","1":{"0":"choised","1":"none"}},"fa-tumblr":{"0":"","1":{"0":"choised","1":"none"}},"fa-vimeo-square":{"0":"","1":{"0":"choised","1":"none"}},"fa-deviantart":{"0":"","1":{"0":"choised","1":"none"}},"fa-digg":{"0":"","1":{"0":"choised","1":"none"}},"fa-dribbble":{"0":"","1":{"0":"choised","1":"none"}},"fa-behance":{"0":"","1":{"0":"choised","1":"none"}}}' id="social_input" />
                                                        <?php endif;?>
                                                    </div>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action='social' href="javascript:void(0);" id="select_profile_social" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_SOCIAL' );?>"><i class="icon-pencil"></i></a>
												</div>
											</div>
											<div class="jsn-row-container row-fluid">
												<div id="jsn-switcher" class="jsn-column-container clearafter">
													<?php echo $this->_JSNMobilize->getItemsMenuIcon( 'mobilize-switcher', 'JSN_MOBILIZE_SWITCHER', '' ); ?>
												</div>
												<div class="jsn-iconbar jsn-vertical">
													<a data-action="switcher" title="<?php echo jText::_( 'JSN_MOBILIZE_EDIT_STYLE' );?>" href="javascript:void(0);"><i class="icon-pencil"></i></a>
													<?php $styleSwitcher = ! empty( $this->_style->jsn_switcher ) ? ( get_magic_quotes_gpc() == true || get_magic_quotes_runtime() == true ) ? stripslashes( $this->_style->jsn_switcher ) : $this->_style->jsn_switcher : ''; ?>
													<input type="hidden" id="input_style_jsn_switcher" class="jsn-input-style" name="style[jsn_switcher]" value='<?php echo  htmlentities( $styleSwitcher );?>' />
												</div>
											</div>
											</div>
											<?php
											if ( strtolower( $edition ) == "free" ) {
												?>
												<div class="jsn-iconbar-trigger">
													<div class="jsn-text-center">
														<a target="_blank" href="http://www.joomlashine.com/joomla-extensions/jsn-mobilize.html">Mobile Joomla Display</a> by
														<a target="_blank" href="http://www.joomlashine.com">JoomlaShine</a>
													</div>
													<div class="jsn-iconbar">
														<a class="coppyright" title="Delete footer coppyright" onclick="return false;" href="#"><i class="icon-trash"></i></a>
													</div>
												</div>
												<?php
											}
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
</div>
<input type="hidden" name="option" value="com_mobilize" />
<input type="hidden" name="task" value="" />
<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>
<?php
if ( JFactory::getApplication()->input->getVar( 'tmpl', '' ) != 'component' ) {
	JSNHtmlGenerate::footer();
}
