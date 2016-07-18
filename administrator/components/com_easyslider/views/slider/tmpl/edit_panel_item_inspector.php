
<div class="panel panel-default es-panel" id="item-inspector">
	<div class="panel-body">

		<!-- Nav tabs -->
		 
		<ul class="nav nav-tab nav-justified item-inspector-tabs" role="tablist">
			<li role="presentation"><a data-target="#item-inspector-content" aria-controls="item-inspector-content" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_CODE_DESC', true);?>" data-type-text><span class="fa fa-code"></span></a></li>
			<li role="presentation" class="active"><a data-target="#item-inspector-text" aria-controls="item-inspector-text" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_FONT_DESC', true);?>" data-type-text><span class="fa fa-font"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-video" aria-controls="item-inspector-video" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_VIDEO_DESC', true);?>" data-type-video><span class="fa fa-video-camera"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-background" aria-controls="item-inspector-image" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_IMAGE_DESC', true);?>" data-type-image><span class="fa fa-image"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-layout" aria-controls="item-inspector-layout" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_LAYOUT_DESC', true);?>" data-type-text><span class="fa fa-columns"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-background" aria-controls="item-inspector-background" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_BACKGROUND_DESC', true);?>" data-type-text data-type-video><span class="fa fa-image"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-style" aria-controls="item-inspector-style" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_STYLE_DESC', true);?>" data-type-common><span class="fa fa-paint-brush"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-arrange" aria-controls="item-inspector-arrange" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_ARRANGE_DESC', true);?>" data-type-common><span class="fa fa-object-ungroup"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-attributes" aria-controls="item-inspector-attributes" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_ATTRIBUTES_DESC', true);?>" data-type-common><span class="fa fa-tag"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-behavior" aria-controls="item-inspector-behavior" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_BEHAVIOR_DESC', true);?>" data-type-common><span class="fa fa-eye"></span></a></li>
			<li role="presentation"><a data-target="#item-inspector-animation" aria-controls="item-inspector-animation" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_ITEM_INSPECTOR_ANIMATION_DESC', true);?>" data-type-common><span class="fa fa-exchange"></span></a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">

			<div role="tabpanel" class="tab-pane" id="item-inspector-content">
				<div class="row flex">
					<textarea class="form-control" data-bind="content"></textarea>
				</div>
			</div><!-- font -->
			<div role="tabpanel" class="tab-pane form-inline" id="item-inspector-behavior">
				<div class="row flex">
					<div class="btn-group btn-group-xs text-center">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_DESKTOP');?></label>
						<input type="checkbox" data-bind="style_desktop.visible" />
					</div>
					<div class="btn-group btn-group-xs text-center">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_LAPTOP');?></label>
						<input type="checkbox" data-bind="style_laptop.visible" />
					</div>
					<div class="btn-group btn-group-xs text-center">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_TABLET');?></label>
						<input type="checkbox" data-bind="style_tablet.visible" />
					</div>
					<div class="btn-group btn-group-xs text-center">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_MOBILE');?></label>
						<input type="checkbox" data-bind="style_mobile.visible" />
					</div>
				</div>
				<div class="row flex">
					<div class="btn-group btn-group-xs">
						<label>&nbsp;</label>
						&nbsp;
					</div>
				</div>
			</div><!-- font -->
			<div role="tabpanel" class="tab-pane form-inline" id="item-inspector-arrange">
				<div class="row flex">
					<div class="btn-group btn-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_ORIGIN_X');?></label>
						<select class="form-control input-xs input-70" data-bind="style.position.x">
							<option value="0"><?php echo JText::_('JSN_EASYSLIDER_LEFT');?></option>
							<option value="0.5"><?php echo JText::_('JSN_EASYSLIDER_CENTER');?></option>
							<option value="1"><?php echo JText::_('JSN_EASYSLIDER_RIGHT');?></option>
						</select>
					</div>
					<div class="btn-group btn-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_ORIGIN_Y');?></label>
						<select class="form-control input-xs input-70" data-bind="style.position.y">
							<option value="0"><?php echo JText::_('JSN_EASYSLIDER_TOP');?></option>
							<option value="0.5"><?php echo JText::_('JSN_EASYSLIDER_CENTER');?></option>
							<option value="1"><?php echo JText::_('JSN_EASYSLIDER_BOTTOM');?></option>
						</select>
					</div>
					<div class="btn-group btn-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_OFFSET_X');?></label>
						<input type="number" class="form-control input-xs input-60" data-bind="style.offset.x">
					</div>
					<div class="btn-group btn-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_OFFSET_Y');?></label>
						<input type="number" class="form-control input-xs input-60" data-bind="style.offset.y">
					</div>
					<div class="btn-group btn-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_OFFSET_Z');?></label>
						<input type="number" class="form-control input-xs input-60" data-bind="style.offset.z">
					</div>
					<div class="btn-group btn-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_WIDTH');?></label>
						<input type="number" class="form-control input-xs input-60" data-bind="style.width">
					</div>
					<div class="btn-group btn-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_HEIGHT');?></label>
						<input type="number" class="form-control input-xs input-60" data-bind="style.height">
					</div>
				</div>
				<div class="row flex">
					<div class="btn-group btn-group-xs">
						<label>&nbsp;</label>
						&nbsp;
					</div>
				</div>
			</div><!-- font -->
			<div role="tabpanel" class="tab-pane form-inline active" id="item-inspector-text">
				<div class="row flex">
					<div class="btn-group btn-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_FONT');?></label>
						<a class="btn btn-default fonts-select-btn">
							<input type="text" class="font-name input-transparent input-160" data-bind="style.font.family">
							<span class="caret pull-right"></span>
						</a>
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SIZE');?></label>
						<input type="number" class="form-control input-50" data-bind="style.font.size" min="8" step="1">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_LINE_HEIGHT');?></label>
						<input type="number" class="form-control input-50" data-bind="style.line_height" min="0" step="0.1">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SPACING');?></label>
						<input type="number" class="form-control input-50" step="1" data-bind="style.letter_spacing" min="0" step="0.1">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_COLOR');?></label>
						<input type="text" class="form-control input-50 input-color" data-bind="style.font.color">
<!--						<input type="text" class="form-control input-50" data-bind="style.font.color">-->
					</div>
				</div>
				<div class="row flex">
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_ALIGN');?></label>
						<select class="form-control input-70" data-bind="style.smartAlign">
							<option value="left"><?php echo JText::_('JSN_EASYSLIDER_LEFT');?></option>
							<option value="center"><?php echo JText::_('JSN_EASYSLIDER_CENTER');?></option>
							<option value="right"><?php echo JText::_('JSN_EASYSLIDER_RIGHT');?></option>
						</select>
<!--						<select class="form-control input-70" data-bind="style.flex.alignItems">-->
<!--							<option value="flex-start" data-bind="text:style.flex.alignStart">--><?php //echo JText::_('JSN_EASYSLIDER_LEFT');?><!--</option>-->
<!--							<option value="center" data-bind="text:style.flex.alignCenter">--><?php //echo JText::_('JSN_EASYSLIDER_CENTER');?><!--</option>-->
<!--							<option value="flex-end" data-bind="text:style.flex.alignEnd">--><?php //echo JText::_('JSN_EASYSLIDER_RIGHT');?><!--</option>-->
<!--						</select>-->
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_WEIGHT');?></label>
						<select class="form-control input-70" data-bind="style.font.weight">
							<option>100</option>
							<option>200</option>
							<option>300</option>
							<option>400</option>
							<option>500</option>
							<option>600</option>
							<option>700</option>
							<option>800</option>
							<option>900</option>
						</select>
					</div>
					<div class="input-group input-group-xs input-50">
						<label><?php echo JText::_('JSN_EASYSLIDER_STYLE');?></label>
						<select class="form-control input-normal" data-bind="style.font.style">
							<option>Normal</option>
							<option>Italic</option>
							<option>oblique</option>
						</select>
					</div>


					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_TEXT_SHADOW');?></label>
						<a class="btn btn-xs btn-default show-text-shadow-settings">
							<span class="fa fa-cog"></span>
						</a>
					</div>
				</div>
			</div><!-- font -->
			<div role="tabpanel" class="tab-pane form-inline" id="item-inspector-background">
				<div class="row flex">
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_IMAGE_URL');?></label>
						<input type="text" class="form-control input-150" data-bind="style.background.image.src">
						<a class="btn btn-default btn-xs select-item-image-btn"><?php echo JText::_('JSN_EASYSLIDER_SELECT');?></a>
					</div>
					<div class="input-group input-group-xs hidden">
						<label><?php echo JText::_('JSN_EASYSLIDER_POSITION');?></label>
						<input type="text" class="form-control input-70" data-bind="style.background.position">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SIZE');?></label>
						<select class="form-control input-70" data-bind="style.background.size">
							<option>contain</option>
							<option>cover</option>
							<option value="100% 100%">stretch</option>
						</select>
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_REPEAT');?></label>
						<select class="form-control input-100" data-bind="style.background.repeat">
							<option>repeat</option>
							<option>repeat-x</option>
							<option>repeat-y</option>
							<option>no-repeat</option>
						</select>
					</div>

					<div class="input-group input-group-xs text-center">
						<label><?php echo JText::_('JSN_EASYSLIDER_ASPECT_RATIO');?></label>
						<input type="checkbox" data-bind="aspectRatio"/>
					</div>

				</div>
				<div class="row flex">
					<div class="btn-group btn-group-xs">
						<label>&nbsp;</label>
						&nbsp;
					</div>
				</div>
			</div><!-- background -->
			<div role="tabpanel" class="tab-pane form-inline" id="item-inspector-video">
				<div class="col-xs-8" style="padding: 0">
					<div class="row">
						<div class="col-xs-6">
							<input type="radio" class="form-control input-xs input-normal" name="item-video-select" data-bind="style.background.video.selector" value="provider">
							<label><?php echo JText::_('JSN_EASYSLIDER_VIDEO_PROVIDER');?></label>
						</div>
						<div class="col-xs-6">
							<input type="radio" class="form-control input-xs input-normal" name="item-video-select" data-bind="style.background.video.selector" value="local">
							<label><?php echo JText::_('JSN_EASYSLIDER_VIDEO_LOCAL');?></label>
						</div>
					</div>

					<div class="row" data-bind="visible:style.background.video.isProvider">

						<div class="input-group input-group-xs">
							<label><?php echo JText::_('JSN_EASYSLIDER_YOUTUBE_OR_VIMEO_URL');?></label>
							<input type="text" class="form-control input-150" data-bind="style.background.video.videoURL">
						</div>

					</div>
					<div class="row" data-bind="visible:style.background.video.isLocal">
						<div class="input-group input-group-xs">
							<label><?php echo JText::_('JSN_EASYSLIDER_MPEG');?></label>
							<input type="text" class="form-control input-90" data-bind="style.background.video.mp4">
						</div>
						<div class="input-group input-group-xs">
							<label><?php echo JText::_('JSN_EASYSLIDER_OGG');?></label>
							<input type="text" class="form-control input-90" data-bind="style.background.video.ogg">
						</div>
						<div class="input-group input-group-xs">
							<label><?php echo JText::_('JSN_EASYSLIDER_WEBM');?></label>
							<input type="text" class="form-control input-90" data-bind="style.background.video.webm">
						</div>

					</div>
				</div>
				<div class="col-xs-4">
					<div class="row flex">
						<div class="input-group input-group-xs text-center input-50">
							<label><?php echo JText::_('JSN_EASYSLIDER_AUTOPLAY');?></label>
							<input type="checkbox" data-bind="style.background.video.autoplay" />
						</div>
						<div class="input-group input-group-xs text-center input-50">
							<label><?php echo JText::_('JSN_EASYSLIDER_CONTROLS');?></label>
							<input type="checkbox" data-bind="style.background.video.controls" />
						</div>
						<div class="input-group input-group-xs text-center input-50">
							<label><?php echo JText::_('JSN_EASYSLIDER_LOOP');?></label>
							<input type="checkbox" data-bind="style.background.video.loop" />
						</div>
					</div>
					<div class="row flex">
						<div class="input-group input-group-xs text-center input-50">
							<label><?php echo JText::_('JSN_EASYSLIDER_MUTE');?></label>
							<input type="checkbox" data-bind="style.background.video.mute" />
						</div>
						<div class="input-group input-group-xs">
							<label><?php echo JText::_('JSN_EASYSLIDER_VOLUME');?> (<span data-bind="style.background.video.volume"></span>%)</label>
							<input type="range" class="form-control input-90" data-bind="style.background.video.volume" min="0" max="100">
						</div>
					</div>
				</div>
			</div><!-- video -->
			<div role="tabpanel" class="tab-pane form-inline" id="item-inspector-attributes">
				<div class="row flex">
					<div class="input-group input-group-xs">
						<label>ID</label>
						<input type="text" class="form-control input-100" data-bind="attr.id">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_CLASS');?></label>
						<input type="text" class="form-control input-150" data-bind="attr.class">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_URL');?></label>
						<input type="text" class="form-control input-125" data-bind="attr.href">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_TARGET');?></label>
						<select class="form-control input-80" data-bind="attr.target">
							<option value="_blank">Blank</option>
							<option value="_parent">Parent</option>
							<option value="_self">Self</option>
							<option value="_top">Top</option>
						</select>
					</div>
				</div>
				<div class="row flex">
					<div class="btn-group btn-group-xs">
						<label>&nbsp;</label>
						&nbsp;
					</div>
				</div>
			</div><!-- attributes -->
			<div role="tabpanel" class="tab-pane form-inline" id="item-inspector-layout">
				<div class="row flex space-around flex-start">
					<div class="col-xs-8">
						<div class="input-group input-group-xs">
							<label><?php echo JText::_('JSN_EASYSLIDER_FLEX_LAYOUT');?></label>
							<select class="form-control input-100" data-bind="style.flex.direction">
								<option value="column"><?php echo JText::_('JSN_EASYSLIDER_COLUMN');?></option>
								<option value="column-reverse"><?php echo JText::_('JSN_EASYSLIDER_COLUMN_REVERSED');?></option>
								<option value="row"><?php echo JText::_('JSN_EASYSLIDER_ROW');?></option>
								<option value="row-reverse"><?php echo JText::_('JSN_EASYSLIDER_ROW_REVERSED');?></option>
							</select>
						</div>
						<div class="input-group input-group-xs">
							<label><?php echo JText::_('JSN_EASYSLIDER_WRAP');?></label>
							<select class="form-control input-100" data-bind="style.flex.wrap">
								<option value="wrap"><?php echo JText::_('JSN_EASYSLIDER_WRAP');?></option>
								<option value="nowrap"><?php echo JText::_('JSN_EASYSLIDER_NO_WRAP');?></option>
							</select>
						</div>

					</div>
					<div class="col-xs-4">
					<div class="input-group input-group-xs text-center">
						<label><?php echo JText::_('JSN_EASYSLIDER_GROW');?></label>
						<input type="checkbox" data-bind="style.flex.grow" />
					</div>
					<div class="input-group input-group-xs text-center">
						<label><?php echo JText::_('JSN_EASYSLIDER_SHRINK');?></label>
						<input type="checkbox" data-bind="style.flex.shrink" />
					</div>

				</div>
				</div>
				<div class="row flex space-around flex-start">
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_ALIGN_ITEMS');?></label>
						<select class="form-control input-125" data-bind="style.flex.alignItems">
							<option value="flex-start"><?php echo JText::_('JSN_EASYSLIDER_START');?></option>
							<option value="center"><?php echo JText::_('JSN_EASYSLIDER_CENTER');?></option>
							<option value="flex-end"><?php echo JText::_('JSN_EASYSLIDER_END');?></option>
							<option value="baseline"><?php echo JText::_('JSN_EASYSLIDER_BASELINE');?></option>
							<option value="stretch"><?php echo JText::_('JSN_EASYSLIDER_STRETCH');?></option>
						</select>
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_JUSTIFY_CONTENT');?></label>
						<select class="form-control input-125" data-bind="style.flex.justifyContent">
							<option value="flex-start"><?php echo JText::_('JSN_EASYSLIDER_START');?></option>
							<option value="center"><?php echo JText::_('JSN_EASYSLIDER_CENTER');?></option>
							<option value="flex-end"><?php echo JText::_('JSN_EASYSLIDER_END');?></option>
							<option value="space-between"><?php echo JText::_('JSN_EASYSLIDER_SPACE_BETWEEN');?></option>
							<option value="space-around"><?php echo JText::_('JSN_EASYSLIDER_SPACE_AROUND');?></option>
						</select>
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_ALIGN_CONTENT');?></label>
						<select class="form-control input-125" data-bind="style.flex.alignContent">
							<option value="flex-start"><?php echo JText::_('JSN_EASYSLIDER_START');?></option>
							<option value="center"><?php echo JText::_('JSN_EASYSLIDER_CENTER');?></option>
							<option value="flex-end"><?php echo JText::_('JSN_EASYSLIDER_END');?></option>
							<option value="space-between"><?php echo JText::_('JSN_EASYSLIDER_SPACE_BETWEEN');?></option>
							<option value="space-around"><?php echo JText::_('JSN_EASYSLIDER_SPACE_AROUND');?></option>
						</select>
					</div>
				</div>
			</div><!-- layout -->
			<div role="tabpanel" class="tab-pane form-inline" id="item-inspector-style">
				<div class="row">
					<div class="col-xs-8">
						<div class="row">
							<div class="col-xs-12 flex" style="justify-content: flex-start;">
								<div class="input-group input-group-xs">
									<label><?php echo JText::_('JSN_EASYSLIDER_BACKGROUND_COLOR');?></label>
									<input type="text" class="form-control input-80 input-color" data-bind="style.background.color">
								</div>
								<div class="input-group input-group-xs">
									<label><?php echo JText::_('JSN_EASYSLIDER_SLIDER_BOX_SHADOW');?></label>
									<a class="btn btn-xs btn-default show-box-shadow-settings">
										<span class="fa fa-cog"></span>
									</a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 flex" style="justify-content: flex-start;">
								<div class="input-group input-group-xs">
									<label><?php echo JText::_('JSN_EASYSLIDER_BORDER');?></label>
									<select class="form-control input-80" data-bind="style.border.style">
										<option value="none"><?php echo JText::_('JSN_EASYSLIDER_NONE');?></option>
										<option value="solid"><?php echo JText::_('JSN_EASYSLIDER_SOLID');?></option>
										<option value="dotted"><?php echo JText::_('JSN_EASYSLIDER_DOTTED');?></option>
										<option value="dashed"><?php echo JText::_('JSN_EASYSLIDER_DASHED');?></option>
									</select>
								</div>
								<div class="input-group input-group-xs">
									<label><?php echo JText::_('JSN_EASYSLIDER_WIDTH');?></label>
									<input type="number" class="form-control input-50 inspector-border-group" data-bind="style.border.width" min="0">
								</div>
								<div class="input-group input-group-xs">
									<label><?php echo JText::_('JSN_EASYSLIDER_COLOR');?></label>
									<input type="text" class="form-control input-80 input-color inspector-border-group" data-bind="style.border.color">
								</div>
								<div class="input-group input-group-xs">
									<label><?php echo JText::_('JSN_EASYSLIDER_RADIUS');?></label>
									<input type="number" class="form-control input-50 inspector-border-group" data-bind="style.border.radius" min="0">
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-4" style="min-height: 103px;">
						<div class="input-group input-group-xs">
							<label class="text-center"><?php echo JText::_('JSN_EASYSLIDER_PADDING');?></label>
							<div class="flex">
								<div class="flex flex-vertical">
									<input type="number" class="form-control input-50" data-bind="style.padding.left" min="0">
								</div>
								<div class="flex flex-vertical">
									<input type="number" class="form-control input-50" data-bind="style.padding.top" min="0"><br>
									<input type="number" class="form-control input-50" data-bind="style.padding.bottom" min="0">
								</div>
								<div class="flex flex-vertical">
									<input type="number" class="form-control input-50" data-bind="style.padding.right" min="0">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><!-- style -->
			<div role="tabpanel" class="tab-pane form-inline" id="item-inspector-animation">
				<div class="row flex flex-start">
					<div class="input-group input-group-xs">
						<label>&nbsp;</label>
						<label style="width: 20px; line-height: 24px;"><?php echo JText::_('JSN_EASYSLIDER_IN');?></label>
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_EFFECT');?></label>
						<select class="form-control input-100 effect-select" data-bind="animation.in.effect"></select>
					</div>
					<div class="input-group input-group-xs">
						<label>&nbsp;</label>
						<a class="btn btn-xs btn-default edit-animation-in-btn">
							<span class="fa fa-cog"></span>
						</a>
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_DELAY');?> (s)</label>
						<input type="number" class="form-control input-60" data-bind="animation.in.delaySeconds" min="0" step="0.1">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_DURATION');?>(s)</label>
						<input type="number" class="form-control input-60" data-bind="animation.in.durationSeconds" min="0" step="0.1">
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_SPLIT');?></label>
						<select class="form-control input-70" data-bind="animation.in.split">
							<option value="0"><?php echo JText::_('JSN_EASYSLIDER_NONE');?></option>
							<option value="1"><?php echo JText::_('JSN_EASYSLIDER_ITEMS');?></option>
						</select>
					</div>
					<div class="input-group input-group-xs">
						<label><?php echo JText::_('JSN_EASYSLIDER_EVERY');?> (ms)</label>
						<input type="number" class="form-control input-60" data-bind="animation.in.splitDelay" min="0" step="10">
					</div>
				</div>
				<div class="row flex flex-start">
					<div class="input-group input-group-xs">
						<label style="width: 20px; line-height: 24px;"><?php echo JText::_('JSN_EASYSLIDER_OUT');?></label>
					</div>
					<div class="input-group input-group-xs">
						<select class="form-control input-100 effect-select" data-bind="animation.out.effect"></select>
					</div>
					<div class="input-group input-group-xs">
						<a class="btn btn-xs btn-default edit-animation-out-btn">
							<span class="fa fa-cog"></span>
						</a>
					</div>
					<div class="input-group input-group-xs">
						<input type="number" class="form-control input-60" data-bind="animation.out.delaySeconds" min="0" step="0.1">
					</div>
					<div class="input-group input-group-xs">
						<input type="number" class="form-control input-60" data-bind="animation.out.durationSeconds" min="0" step="0.1">
					</div>
					<div class="input-group input-group-xs">
						<select class="form-control input-70" data-bind="animation.out.split">
							<option value="0"><?php echo JText::_('JSN_EASYSLIDER_NONE');?></option>
							<option value="1"><?php echo JText::_('JSN_EASYSLIDER_ITEMS');?></option>
						</select>
					</div>
					<div class="input-group input-group-xs">
						<input type="number" class="form-control input-60" data-bind="animation.out.splitDelay" min="0" step="10">
					</div>
				</div>
			</div><!-- animation -->

		</div>
	</div>
</div>