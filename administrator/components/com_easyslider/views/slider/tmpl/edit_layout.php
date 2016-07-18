<div id="es-splash-screen">
	<div class="spash-body">
		<div class="splash-box es-warning-box" style="display: none;">
			<i class="fa fa-exclamation-triangle"></i>
			<span>
				Your browser is outdated. Please consider using the newer MS Edge or Chrome, Firefox, Safari or Opera.<br>
				We support a wide variety of browsers but not Internet Explorer.
			</span>
		</div>
		<div class="splash-box es-loading-box">
			Loading...
		</div>
	</div>
</div>
<div class="jsn-bootstrap3">

	<div class="es-app">

		<!-- Main layout -->
		<div class="es-layout flex flex-layout flex-vertical">

			<!-- Header layout -->
			<div class="es-header flex flex-layout es-panel space-between">
				<div class="header-left flex">
					<div class="es-branding flex">

						<svg width="60" height="60" style="margin: -10px 0; ">
							<style>
								#logo {
									/*transformb*/
									/*-webkit-perspective: 50px;*/
									/*perspective: 50px;*/
								}
								.container {
									-webkit-transform: translate(0px,-5px) scale(0.8);
									-moz-transform: translate(0px,-5px) scale(0.8);
									-ms-transform: translate(0px,-5px) scale(0.8);
									transform: translate(0px,-5px) scale(0.8);
								}
								.g1 {

								}
								.g2 {
									-webkit-transform: translateY(12px);
									-moz-transform: translateY(12px);
									-ms-transform: translateY(12px);
									transform: translateY(12px);
								}
								.g3 {
									-webkit-transform: translateY(24px);
									-moz-transform: translateY(24px);
									-ms-transform: translateY(24px);
									transform: translateY(24px);
								}
								.l1,.l2,.l3 {
									fill: #007ba6; /*#828690;*/
									-webkit-transform: rotateX(-55deg) rotateZ(-45deg);
									-moz-transform: rotateX(-55deg) rotateZ(-45deg);
									-ms-transform: rotateX(-55deg) rotateZ(-45deg);
									transform: rotateX(-55deg) rotateZ(-45deg);

									-webkit-transform-origin: 50% 50%;
									-ms-transform-origin: 50% 50%;
									-moz-transform-origin: 50% 50%;
									transform-origin: 50% 50%;

									-webkit-transform-style: preserve-3d;
									-moz-transform-style: preserve-3d;
									-ms-transform-style: preserve-3d;
									transform-style: preserve-3d;
								}
								.l1,.l2,.l3  {
									/*-webkit-animation-name: spining-logo;
									-moz-animation-name: spining-logo;
									animation-name: spining-logo;
									-webkit-animation-duration: 15s;
									-moz-animation-duration: 15s;
									animation-duration: 15s;
									-webkit-animation-iteration-count: infinite;
									-moz-animation-iteration-count: infinite;
									animation-iteration-count: infinite;
									-webkit-animation-timing-function: cubic-bezier(.98,-0.38,.02,1.53);
									-moz-animation-timing-function: cubic-bezier(.98,-0.38,.02,1.53);
									animation-timing-function: cubic-bezier(.98,-0.38,.02,1.53);*/
								}
								@-webkit-keyframes spining-logo {
									0% {
										-webkit-transform: rotateX(-55deg) rotateZ(-45deg);
										transform: rotateX(-55deg) rotateZ(-45deg);
									}
									70% {
										-webkit-transform: rotateX(-55deg) rotateZ(-45deg) scale(1);
										transform: rotateX(-55deg) rotateZ(-45deg) scale(1);
										fill: #828690;
									}
									80% {
										-webkit-transform: rotateX(-55deg) rotateZ(-45deg) scale(1);
										transform: rotateX(-55deg) rotateZ(-45deg) scale(1);
									}
									90% {
										-webkit-transform: rotateX(-55deg) rotateZ(315deg) scale(1.1);
										transform: rotateX(-55deg) rotateZ(315deg) scale(1.1);
										fill: #007ba6;
									}
								}
								@keyframes spining-logo {
									0% {
										-webkit-transform: rotateX(-55deg) rotateZ(-45deg);
										transform: rotateX(-55deg) rotateZ(-45deg);
									}
									70% {
										-webkit-transform: rotateX(-55deg) rotateZ(-45deg) scale(1);
										transform: rotateX(-55deg) rotateZ(-45deg) scale(1);
										fill: #828690;
									}
									80% {
										-webkit-transform: rotateX(-55deg) rotateZ(-45deg) scale(1);
										transform: rotateX(-55deg) rotateZ(-45deg) scale(1);
									}
									90% {
										-webkit-transform: rotateX(-55deg) rotateZ(315deg) scale(1.1);
										transform: rotateX(-55deg) rotateZ(315deg) scale(1.1);
										fill: #007ba6;
									}
								}
							</style>
							<g class="container" transform="translate(0px,-5px) scale(0.8)">
								<g class="g1" transform="">
									<path class="l1" transform="rotateX(-55deg) rotateZ(-45deg)" d="M10 10 L60 10 L60 20 L25 20 L25 30 L60 30 L60 40 L25 40 L25 50 L60 50 L60 60 L10 60 Z"></path></g>
								<g class="g2" transform="translateY(12px)">
									<path class="l2" transform="rotateX(-55deg) rotateZ(-45deg)" d="M10 10 L20 10 L20 50 L60 50 L60 60 L10 60 Z"></path></g>
								<g class="g3" transform="translateY(24px)">
									<path class="l3" transform="rotateX(-55deg) rotateZ(-45deg)" d="M10 10 L20 10 L20 50 L60 50 L60 60 L10 60 Z"></path></g>
							</g>
						</svg>

					</div>
					<div class="flex">
						<h3 class="title-input" data-bind="text:title" contenteditable="true"></h3>
                        <a class="btn btn-default slider-redo-btn es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_SLIDER_REDO_BTN_DESC', true);?>">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="20" height="20" x="0px" y="0px" viewBox="0 0 459 459" style="fill: rgb(155, 160, 171);" xml:space="preserve">
                               <g id="share" style="fill: rgb(155, 160, 171);">
                                   <path d="M459,216.75L280.5,38.25v102c-178.5,25.5-255,153-280.5,280.5C63.75,331.5,153,290.7,280.5,290.7v104.55L459,216.75z" style="fill: rgb(155, 160, 171);"></path>
                               </g>
                            </svg>
                        </a>
                        <a class="btn btn-default slider-undo-btn es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_SLIDER_UNDO_BTN_DESC', true);?>">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20" height="20" viewBox="0 0 459 459" style="fill: rgb(155, 160, 171);" xml:space="preserve">
								<g id="reply" style="fill: rgb(155, 160, 171);">
                                    <path d="M178.5,140.25v-102L0,216.75l178.5,178.5V290.7c127.5,0,216.75,40.8,280.5,130.05C433.5,293.25,357,165.75,178.5,140.25z" style="fill: rgb(155, 160, 171);"></path>
                                </g>
							</svg>
                        </a>
					</div>
				</div>
				<div class="flexible"></div>
				<div class="header-center">
					<div class="btn-group">
						<a class="btn btn-default add-item-btn es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ADD_BOX_DESC', true);?>"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_BOX');?></a>
						<a class="btn btn-default add-text-btn es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ADD_TEXT_DESC', true);?>"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_TEXT');?></a>
						<a class="btn btn-default add-image-btn es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ADD_IMAGE_DESC', true);?>"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_IMAGE');?></a>
						<a class="btn btn-default add-video-btn es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ADD_VIDEO_DESC', true);?>"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_VIDEO');?></a>
<!--						<a class="btn btn-default add-btn add-item-btn"><span class="es-icon-icon-box"></span> </a>-->
<!--						<a class="btn btn-default add-btn add-text-btn"><span class="es-icon-icon-text"></span> </a>-->
<!--						<a class="btn btn-default add-btn add-image-btn"><span class="es-icon-icon-image"></span> </a>-->
<!--						<a class="btn btn-default add-btn add-video-btn"><span class="es-icon-icon-video"></span> </a>-->
					</div>
				</div>
				<div class="flexible"></div>
				<div class="header-right">
					<div class="btn-group">
						<a class="btn btn-default slider-quick-tour-btn">?</a>
						<a class="btn btn-default slider-settings-btn es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_SLIDER_SETTINGS_BTN_DESC', true);?>"><span class="fa fa-wrench"></span></a>
					</div>
					<div class="btn-group">
						<a class="btn btn-default exit-slider-btn"><?php echo JText::_('JSN_EASYSLIDER_CLOSE');?></a>
						<a class="btn btn-default save-slider-btn"><?php echo JText::_('JSN_EASYSLIDER_SAVE');?></a>
					</div>
				</div>
			</div>
			<!-- Header layout -->

			<!-- Main horizontal layout -->
			<div class="flex flexible flex-layout">

				<div class="es-thumbs-wrapper flex flex-layout align-bottom">
					<div class="es-thumbs-layout flex">
						<div class="es-thumbs-center">
							<div class="es-thumb flex slide-global-btn">
								<div class="thumb-preview"></div>
							</div>
							<div class="es-thumbs" id="thumbs">
								<div class="es-thumb flex align-top">
<!--									<div class="thumb-index hidden" data-bind="text:index">1</div>-->
									<div class="thumb-preview">
										<span class="fa fa-expeditedssl" data-bind="visible:hide"></span>
									</div>
								</div>
							</div>
							<div class="es-thumb flex add-slide-btn">
								<div class="thumb-preview flex">
<!--									<span class="fa fa-plus"></span>-->
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="es-canvas-wrapper flex flex-layout align-top flexible">

					<div class="es-canvas-layout flex">

						<div id="canvas" class="es-canvas">
							<div class="es-canvas-master-bg"></div>
							<div class="es-canvas-slide-bg"></div>
						</div>
						<div class="es-bounding-box"></div>
						<div class="es-overlay bottom">
							<div id="grids" class="es-grids"></div>
						</div>
						<div class="es-overlay middle">
							<div  id="items" class="es-items">
								<!-- ITEM VIEW -->
								<div class="es-item">
									<div class="item-offset">
										<div class="item-container">
											<div class="item-content flex" data-bind="html:content" data-bind-events="changecontent"></div>
										</div>
										<div class="item-animation hidden"></div>
									</div>
								</div>
								<!-- ITEM VIEW -->
							</div>
							<div  id="global-items" class="es-items es-global-items">
								<!-- ITEM VIEW -->
								<div class="es-item">
									<div class="item-offset">
										<div class="item-container">
											<div class="item-content flex" data-bind="html:content" data-bind-events="changecontent"></div>
										</div>
										<div class="item-animation hidden"></div>
									</div>
								</div>
								<!-- ITEM VIEW -->
							</div>
						</div>
						<div class="es-overlay top">
							<div id="selections" class="es-selections">
								<!-- Selection view -->
								<div class="es-selection">
									<div class="selection-offset"></div>
								</div>
								<!-- Selection view -->
							</div>
							<div class="es-nav es-nav-pagination">
								<ul id="nav-pagination" class="es-pagination">
									<li><a></a></li>
								</ul>
							</div>
							<nav id="nav-buttons" class="es-nav es-nav-buttons">
								<a class="es-prev prev"></a>
								<a class="es-next next"></a>
							</nav>
						</div>

					</div>

				</div>

				<div class="es-sidebar flex flex-column space-around">

					<div class="btn-group btn-group-vertical">
						<a class="btn btn-default open-view-mode-selector-btn hidden" data-bind="layout.desktop_w">
							<span class="fa fa-television"></span>
						</a>
						<a class="es-tooltip btn btn-default switch-to-mode" data-mode="desktop" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_MODE_DESKTOP_MOD_DESC', true);?>">
							<span class="fa fa-television"></span>
						</a>
						<a class="es-tooltip btn btn-default switch-to-mode" data-bind="visible:layout.laptop" data-mode="laptop" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_MODE_LAPTOP_MOD_DESC', true);?>">
							<span class="fa fa-laptop"></span>
						</a>
						<a class="es-tooltip btn btn-default switch-to-mode" data-bind="visible:layout.tablet" data-mode="tablet" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_MODE_TABLET_MOD_DESC', true);?>">
							<span class="fa fa-tablet"></span>
						</a>
						<a class="es-tooltip btn btn-default switch-to-mode" data-bind="visible:layout.mobile" data-mode="mobile" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_MODE_MOBILE_MOD_DESC', true);?>">
							<span class="fa fa-mobile"></span>
						</a>
					</div>
					<div></div>
					<div class="btn-group btn-group-vertical">
						<a class="es-tooltip btn btn-default open-grid-config-panel" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_OPEN_GRID_CONFIG_PANEL_DESC', true);?>">
							<span class="fa fa-cog"></span>
						</a>
					</div>

				</div>

			</div>
			<!-- Main horizontal layout -->


			<!-- Timeline layout -->
			<div id="timeline" class="es-timeline flex flex-layout es-panel space-between">
				<div class="timeline-toolbar flex space-between">
					<div class="timeline-preview-off">
						<a class="timeline-preview-btn btn btn-xs btn-primary">
							<span class="fa fa-play"> &nbsp; </span> <?php echo JText::_('JSN_EASYSLIDER_PREVIEW');?>
						</a>
					</div>
					<div class="hidden timeline-preview-on">
						<a class="timeline-resume-btn btn btn-xs btn-primary">
							<span class="fa fa-play"></span>
						</a>
						<a class="timeline-pause-btn btn btn-xs btn-success">
							<span class="fa fa-pause"></span>
						</a>
						<a class="timeline-preview-exit-btn btn btn-xs btn-danger">
							Exit Preview
						</a>
					</div>
					<span class="timeline-time">
						<span class="txt-min">00</span>:<span class="txt-sec">00</span>.<span class="txt-ms">000</span>
					</span>
				</div>
				<div class="timeline-slider flexible">
					<svg class="jsn-es-ruler">
						<defs>
							<linearGradient id="grad1" x1="0%" y1="0%" x2="0%" y2="100%" gradientUnits="userSpaceOnUse">
								<stop offset="0%" style="stop-color:#5A5D65;stop-opacity:0.8" />
								<stop offset="100%" style="stop-color:#5A5D65;stop-opacity:0" />
							</linearGradient>
						</defs>
					</svg>
					<div class="timeline-blocks">
						<span class="timeline-block transition-in hidden"></span>
						<span class="timeline-block duration">
							<div class="ui-resizable-handle ui-resizable-w"></div>
							<label class="block-label"><?php echo JText::_('JSN_EASYSLIDER_START');?></label>
						</span>
						<span class="timeline-block transition-out">
							<label class="block-label"><?php echo JText::_('JSN_EASYSLIDER_END');?> (<span class="txt-duration">4000ms</span>)</label>
						</span>
						<span class="timeline-block next-slide hidden">
							<label class="block-label"><?php echo JText::_('JSN_EASYSLIDER_NEXT_SLIDE');?></label>
						</span>
					</div>
					<div class="timeline-cursor">
						<div class="timeline-cursor-handle"></div>
					</div>
				</div>
				<div class="timeline-guide">
						<span class="guide-block transition-in"></span>
						<span class="guide-block duration"></span>
						<span class="guide-block transition-out"></span>
						<span class="guide-block next-slide"></span>
				</div>
				<a class="es-tooltip btn btn-primary timeline-toggle-btn timeline-show-btn"  title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_TIMELINE_TOGGLE_BTN_EXPAND_OUT_DESC', true);?>">
					<span class="fa fa-sliders"></span>
				</a>
				<a class="btn btn-primary timeline-toggle-btn timeline-hide-btn hidden">
					<span class="fa fa fa-chevron-down"></span>
				</a>
			</div>
			<!-- End timeline layout -->


			<!-- Layers layout -->
			<div class="es-layers-wrapper es-collapsed">
				<div class="es-layers-layout flex flex-layout">
					<div class="es-layers">
						<!-- JSN-ES-LAYER -->
						<div class="es-layer">
							<span class="layer-heading flex">
<!--								<label class="layer-drag-handle">-->
<!--									<span class="fa fa-ellipsis-v"></span>-->
<!--								</label>-->
								<span class="layer-drag-handle item-type"></span>
								<input class="layer-title flex-flexible" data-bind="dynamicName" placeholder="Untitled Item">
								<label data-bind="disabled:hidden">
									<input type="checkbox" data-bind="hidden" class="hidden">
									<span class="fa fa-eye es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_LAYER_EYE_DESC', true);?>"></span>
								</label>
								<label data-bind="enabled:locked">
									<input type="checkbox" data-bind="locked" class="hidden">
									<span class="fa fa-lock es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_LAYER_LOCK_DESC', true);?>"></span>
								</label>
							</span>
						</div>
						<!-- JSN-ES-LAYER -->
					</div>
					<div class="es-frames flex-flexible">
						<!-- JSN-ES-FRAME -->
						<div class="es-frame">
							<div class="time-blocks">
								<div class="time-block animation-in"></div>
								<div class="time-block animation-wait"></div>
								<div class="time-block animation-out"></div>
							</div>
						</div>
						<!-- JSN-ES-FRAME -->
					</div>
				</div>
			</div>
			<!-- End layers layout -->
		</div>
		<!-- End nain layout -->

		<!-- Floating components -->

		<div class="es-media-selector hidden">
			<iframe class="jsn-image-frame" width="100%" height="100%" frameborder="0"></iframe>
		</div>

		<div class="es-panels">

			<?php echo $this->loadTemplate('panel_pickers'); ?>
			<?php echo $this->loadTemplate('panel_editors'); ?>
			<?php echo $this->loadTemplate('panel_item_inspector'); ?>
			<?php echo $this->loadTemplate('panel_slide_inspector'); ?>
			<?php echo $this->loadTemplate('panel_animation_inspector'); ?>
			<?php echo $this->loadTemplate('panel_medium_toolbar'); ?>
			<?php echo $this->loadTemplate('panel_quick_settings'); ?>

			<?php echo $this->loadTemplate('panel_settings'); ?>
			<?php echo $this->loadTemplate('quick_tour_panel'); ?>
			<div class="panel panel-default es-panel" id="tooltip-panel">
				<span class="content">zxczczxc</span>
			</div>
			<div class="panel panel-default es-panel" id="box-shadow-inspector-panel">
				<div class="panel-body">

                    <div class="box-shadows" id="box-shadows">
                        <div class="box-shadow-item">
							<div class="row tab-pane form-inline">
								<div class="col-xs-11 flex space-between">
									<div class="input-group input-group-xs">
										<input type="number" class="form-control input-50" data-bind="x" placeholder="X">
									</div>
									<div class="input-group input-group-xs">
										<input type="number" class="form-control input-50" data-bind="y" placeholder="Y">
									</div>
									<div class="input-group input-group-xs">
										<input type="number" class="form-control input-50" min="0" data-bind="blur" placeholder="Blur">
									</div>
									<div class="input-group input-group-xs">
										<input type="text" class="form-control input-50 input-color" data-bind="color" placeholder="Color">
									</div>
									<div class="input-group input-group-xs">
                                        <select data-bind="isInset" class="form-control input-70">
                                            <option value="inset">inset</option>
                                            <option value="outset">outset</option>
                                        </select>
									</div>
								</div>
								<div class="col-xs-1 flex">
                                    <a class="remove-box-shadow-btn remove">
                                        <span class="fa fa-times-circle">&nbsp;</span>
                                    </a>
								</div>
							</div>
						</div>
					</div>

					<hr>
					<div class="row">
                        <div class="col-xs-6">
                            <a class="btn btn-xs btn-default add-box-shadow-btn">
                                <span><?php echo JText::_('JSN_EASYSLIDER_ADD_MORE');?></span>
                            </a>
                        </div>
                        <div class="col-xs-6">
                            <em><?php echo JText::_('JSN_EASYSLIDER_SLIDER_OVERWRITE', true); ?></em>
                        </div>
					</div>
				</div>
			</div>


            <div class="panel panel-default es-panel" id="text-shadow-inspector-panel">
                <div class="panel-body">

                    <div class="box-shadows" id="text-shadows">
                        <div class="box-shadow-item">
                            <div class="row tab-pane form-inline">
								<div class="col-xs-11 flex space-between">
                                    <div class="input-group input-group-xs">
                                        <input type="number" class="form-control input-50" data-bind="x" placeholder="X">
                                    </div>
                                    <div class="input-group input-group-xs">
                                        <input type="number" class="form-control input-50" data-bind="y" placeholder="Y">
                                    </div>
                                    <div class="input-group input-group-xs">
                                        <input type="number" class="form-control input-50" min="0" data-bind="blur" placeholder="Blur">
                                    </div>
                                    <div class="input-group input-group-xs">
                                        <input type="text" class="form-control input-50 input-color" data-bind="color" placeholder="Color">
                                    </div>

                                </div>
                                <div class="col-xs-1 flex">
                                    <a class="remove-text-shadow-btn remove">
                                        <span class="fa fa-times-circle">&nbsp;</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
					<hr>
					<div class="row">
                        <div class="col-xs-6">
                            <a class="btn btn-xs btn-default add-text-shadow-btn">
                                <span><?php echo JText::_('JSN_EASYSLIDER_ADD_MORE');?></span>
                            </a>
                        </div>
                        <div class="col-xs-6">
                            <em><?php echo JText::_('JSN_EASYSLIDER_SLIDER_OVERWRITE', true); ?></em>
                        </div>

					</div>
                </div>
            </div>

        </div>

		<!-- End floating components -->

		<?php echo $this->loadTemplate('svg'); ?>

	</div>

</div>