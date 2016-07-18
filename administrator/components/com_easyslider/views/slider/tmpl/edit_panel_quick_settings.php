<div class="panel panel-default es-panel" id="grid-config">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_HELPER_GRID');?></h3>
	</div>
	<div class="panel-body form-horizontal">

		<div class="row">
			<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_SHOW');?></label>
			<div class="col-xs-8">
				<input type="checkbox" data-bind="show" />
			</div>
		</div> <!-- End row -->

		<div class="row">
			<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_COLOR');?></label>
			<div class="col-xs-8">
				<div class="input-group input-group-xs">
					<input type="text" class="form-control input-xs input-color" data-bind="color">
				</div>
			</div>
		</div> <!-- row end -->

		<div class="row">
			<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_SIZE');?></label>
			<div class="col-xs-8">
				<div class="input-group inout-group-xs">
					<input type="number" class="form-control input-xs" data-bind="size" step="10" />
					<span class="input-group-addon">px</span>
				</div>
			</div>
		</div> <!-- End row -->

		<div class="row">
			<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_GUTTER');?></label>
			<div class="col-xs-8">
				<input type="number" class="form-control input-xs" data-bind="gutter" />
			</div>
		</div> <!-- End row -->


<!--		<div class="input-group">-->
<!--			<label>Show grid</label>-->
<!--			<input type="checkbox" name="showgrid" class="form-control input-xs input-normal" data-bind="show">-->
<!--		</div>-->
<!--		<div class="input-group">-->
<!--			<label>Color</label>-->
<!--			<input type="color" name="color" class="form-control input-xs input-normal">-->
<!--		</div>-->
<!--		<div class="input-group">-->
<!--			<label>Gutter</label>-->
<!--			<input type="text" name="showgrid" class="form-control input-xs input-normal">-->
<!--		</div>-->
<!--		<div class="input-group">-->
<!--			<label>Line</label>-->
<!--			<input type="text" name="showgrid" class="form-control input-xs input-normal">-->
<!--		</div>-->
	</div>
</div>

<div class="panel panel-default es-panel" id="quick-setting-panel">
<!--	<div class="panel-heading">-->
<!--		<h3 class="panel-title">Config Slider</h3>-->
<!--	</div>-->
	<div class="panel-body">
		<!-- Nav tabs -->
		<ul class="nav nav-tab nav-justified" role="tablist">
			<li role="presentation" class="active"><a data-target="#setting-general-tab" aria-controls="setting-general-tab" role="tab" data-toggle="tab">Layout</a></li>
			<li role="presentation"><a data-target="#setting-size-tab" aria-controls="setting-size-tab" role="tab" data-toggle="tab">Size</a></li>
			<li role="presentation"><a data-target="#setting-style-tab" aria-controls="setting-style-tab" role="tab" data-toggle="tab">BG</a></li>
			<li role="presentation"><a data-target="#setting-nav-tab" aria-controls="setting-nav-tab" role="tab" data-toggle="tab">Navs</a></li>
			<li role="presentation"><a data-target="#setting-more-tab" aria-controls="setting-more-tab" role="tab" data-toggle="tab">More</a></li>
		</ul>

		<div class="tab-content">
			<div id="setting-general-tab" role="tabpanel" class="tab-pane active form-horizontal">

				<div class="row">
					<div class="col-xs-12">
						<div class="btn-group flex slider-layout-select">
							<a class="btn btn-default btn-md btn-iconic flexible slider-layout-option-1">
								<div class="btn-icon">
									<i class="es-icon-icon-autowidth"></i>
								</div>
								<label><?php echo JText::_('JSN_EASYSLIDER_SIDER_AUTO_WIDTH');?></label>
							</a>
							<a class="btn btn-default btn-md btn-iconic flexible flexible slider-layout-option-2">
								<div class="btn-icon">
									<span class="es-icon-icon-fullwidth"></span>
								</div>
								<label><?php echo JText::_('JSN_EASYSLIDER_SIDER_FULL_WIDTH');?></label>
							</a>
							<a class="btn btn-default btn-md btn-iconic flexible flexible slider-layout-option-3">
								<div class="btn-icon">
									<span class="es-icon-icon-fullscreen"></span>
								</div>
								<label><?php echo JText::_('JSN_EASYSLIDER_SIDER_FULL_SCREEN');?></label>
							</a>
						</div>
					</div>
				</div> <!-- end of row -->
				<hr>
				<div class="row">
					<div class="col-xs-12">
						<div class="btn-group flex slider-type-select">
							<a class="btn btn-default btn-lg btn-iconic flexible slider-type-option-standard">
								<div class="btn-icon">
									<span class="es-icon-icon-standard-slider"></span>
								</div>
								<label><?php echo JText::_('JSN_EASYSLIDER_STANDARD');?></label>
							</a>
							<a class="btn btn-default btn-lg btn-iconic flexible slider-type-option-carousel">
								<div class="btn-icon">
									<span class="es-icon-icon-carousel-slider"></span>
								</div>
								<label><?php echo JText::_('JSN_EASYSLIDER_CAROUSEL');?></label>
							</a>
						</div>
					</div>
				</div> <!-- end of row -->

				<div class="row" data-bind="visible:layout.isCarousel" style="margin-top: 20px;">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_TYPE');?>:</label>
					<div class="col-xs-7">
						<select class="form-control input-xs" data-bind="layout.mode">
							<option value="slide">Flat</option>
							<option value="carousel">Flat 3D</option>
							<option value="cube">Infinite Cube 3D</option>
							<option value="coverflow">Coverflow 3D</option>
							<option value="polygon">Polygon 3D</option>
						</select>
					</div>
				</div> <!-- end of row -->

				<div class="row" data-bind="visible:layout.isCarousel">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_FLOW');?>:</label>
					<div class="col-xs-7">
						<select class="form-control input-xs" data-bind="layout.flow">
							<option value="x">Horizontal</option>
							<option value="y" data-bind="visible:layout.isNotCoverflow">Vertical</option>
						</select>
					</div>
				</div> <!-- end of row -->

				<div class="row" data-bind="visible:layout.isCarousel">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_SPACING');?>:</label>
					<div class="col-xs-5">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.spacing">
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div> <!-- end of row -->

				<hr>
				<div class="row">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_PADDING');?>:</label>
					<div class="col-xs-5">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.padding">
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div> <!-- end of row -->

				<div class="row hidden">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="layout.full_w"> <?php echo JText::_('JSN_EASYSLIDER_SIDER_FULL_WIDTH');?>
						</label>
						<label class="checkbox">
							<input type="checkbox" data-bind="layout.full_h"> <?php echo JText::_('JSN_EASYSLIDER_SIDER_FULL_HEIGHT');?>
						</label>
						<label class="checkbox">
							<input type="checkbox" data-bind="layout.auto_w"> <?php echo JText::_('JSN_EASYSLIDER_SIDER_AUTO_WIDTH');?>
						</label>
						<label class="checkbox">
							<input type="checkbox" data-bind="layout.auto_h"> <?php echo JText::_('JSN_EASYSLIDER_SIDER_AUTO_HEIGHT');?>
						</label>
					</div>
				</div>


			</div>
			<div id="setting-size-tab" role="tabpanel" class="tab-pane form-horizontal">

				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" disabled checked="checked" /> Desktop (default)
						</label>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<label class="col-xs-3"></label>
					<div class="col-xs-4">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.desktop_w">
							<span class="input-group-addon">px</span>
						</div>
					</div>
					<div class="col-xs-4 col-xs-offset-1">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.desktop_h">
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div> <!-- end of row -->
				<hr>
				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="layout.laptop" /> Laptop ( <span data-bind="layout.laptop_under">480</span> px)
						</label>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<label class="col-xs-3"></label>
					<div class="col-xs-4">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.laptop_w">
							<span class="input-group-addon">px</span>
						</div>
					</div>
					<div class="col-xs-4 col-xs-offset-1">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.laptop_h">
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div> <!-- end of row -->
				<hr>
				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="layout.tablet" /> Tablet ( <span data-bind="layout.tablet_under">480</span> px)
						</label>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<label class="col-xs-3"></label>
					<div class="col-xs-4">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.tablet_w">
							<span class="input-group-addon">px</span>
						</div>
					</div>
					<div class="col-xs-4 col-xs-offset-1">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.tablet_h">
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div> <!-- end of row -->
				<hr>
				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="layout.mobile" /> Mobile ( <span data-bind="layout.mobile_under">480</span> px)
						</label>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<label class="col-xs-3"></label>
					<div class="col-xs-4">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.mobile_w">
							<span class="input-group-addon">px</span>
						</div>
					</div>
					<div class="col-xs-4 col-xs-offset-1">
						<div class="input-group">
							<input type="number" class="form-control input-xs" data-bind="layout.mobile_h">
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div> <!-- end of row -->

			</div>
			<div id="setting-style-tab" role="tabpanel" class="tab-pane form-horizontal">

				<div class="row">
					<label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_COLOR');?></label>
					<div class="col-xs-6">
						<div class="input-group input-group-xs">
							<input type="text" class="form-control input-xs input-color" data-bind="background.color" placeholder="#000000">
							<span class="input-group-addon">
<!--								<input type="text" class="form-control input-xs input-color" data-bind="background.color">-->
							</span>
						</div>
					</div>
				</div> <!-- row end -->

				<hr>
				<div class="row">
					<label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_IMAGE');?></label>
					<div class="col-xs-9">
						<div class="input-group">
							<input type="text" class="form-control input-xs" data-bind="background.image.src">
							<span class="input-group-btn">
								<a class="btn btn-default btn-xs edit-slider-bg-btn">
									<span class="fa fa-folder-open-o"></span>
								</a>
							</span>
						</div>
					</div>
				</div> <!-- row end -->
				<div class="row">
					<label class="col-xs-3">Pos</label>
					<div class="col-xs-6">
						<input type="text" class="form-control input-xs" data-bind="background.position">
					</div>
				</div> <!-- row end -->
				<div class="row">
					<label class="col-xs-3">Size</label>
					<div class="col-xs-6">
						<select class="form-control input-xs" data-bind="background.size">
							<option>cover</option>
							<option>contain</option>
						</select>
					</div>
				</div> <!-- row end -->

			</div>
			<div id="setting-nav-tab" role="tabpanel" class="tab-pane form-horizontal">

				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="interactive.enable" /> <?php echo JText::_('JSN_EASYSLIDER_INTERACTIVE_TOUCH');?>
						</label>
					</div>
				</div> <!-- end of row -->

				<hr>

				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="nav.enable" /> Next / Prev buttons
						</label>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_STYLE');?></label>
					<div class="col-xs-8">
						<select class="form-control input-xs" data-bind="nav.style">
							<option value="slide">Slide</option>
							<option value="fillpath">Fill Path</option>
							<option value="circlepop">Circle Pop</option>
							<option value="roundslide">Round Slide</option>
							<!--					<option value="slit">Slit</option>-->
							<!--					<option value="thumbflip">Thumb Flip</option>-->
						</select>
					</div>
				</div> <!-- end of row -->

				<hr>

				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="pagination.enable" /> <?php echo JText::_('JSN_EASYSLIDER_PAGINATION');?>
						</label>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_STYLE');?></label>
					<div class="col-xs-8">
						<select class="form-control input-xs" data-bind="pagination.style">
							<option value="stroke">Stroke</option>
							<option value="dotstroke">Dot Stroke</option>
							<option value="smalldotstroke">Small Dot Stroke</option>
							<option value="hop">Hop</option>
							<option value="puff">Puff</option>
							<option value="flip">Flip</option>
							<option value="fall">Fall</option>
							<option value="fillup">Fill Up</option>
							<option value="fillin">Fill In</option>
							<option value="scaleup">Scale Up</option>
							<option value="circlegrow">Circle Grow</option>
							<option value="tooltip">Tooltip</option>
						</select>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_SPACING');?></label>
					<div class="col-xs-4">
						<div class="input-group input-group-xs">
							<input type="number" class="form-control" data-bind="pagination.spacing" min="0" step="1" placeholder="auto" />
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<label class="col-xs-4">Size</label>
					<div class="col-xs-4">
						<div class="input-group input-group-xs">
							<input type="number" class="form-control" data-bind="pagination.size" min="0" step="1" placeholder="auto" />
							<span class="input-group-addon">px</span>
						</div>
					</div>
				</div> <!-- end of row -->
			</div>
			<div id="setting-more-tab" role="tabpanel" class="tab-pane form-horizontal">

				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="autoSlide.enable" checked="checked"> <?php echo JText::_('JSN_EASYSLIDER_SLIDER_AUTO');?>
						</label>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<div class="col-xs-12">
						<label class="checkbox">
							<input type="checkbox" data-bind="repeat.enable" checked="checked" class="repeat-slider"> <?php echo JText::_('JSN_EASYSLIDER_SLIDER_REPEAT');?>
						</label>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<div class="col-xs-12">
						<a class="btn btn-default btn-lg btn-block open-js-editor-btn">
							Custom Javascript Editor
						</a>
					</div>
				</div> <!-- end of row -->
				<div class="row">
					<div class="col-xs-12">
						<a class="btn btn-default btn-lg btn-block open-css-editor-btn">
							Custom CSS Editor
						</a>
					</div>
				</div> <!-- end of row -->

			</div>
		</div>
	</div>
	<div class="panel-footer hidden text-right">
		<a class="btn btn-default btn-sm open-advance-settings-btn"><?php echo JText::_('JSN_EASYSLIDER_ADVANCED_SETTINGS');?></a>
	</div>
</div>