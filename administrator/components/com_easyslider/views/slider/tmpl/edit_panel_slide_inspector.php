
<div class="panel panel-default es-panel" id="thumb-menu">
	<div class="panel-body">
		<!-- Nav tabs -->
		<ul class="nav nav-tab nav-justified" role="tablist">
			<li role="presentation" class="active"><a data-target="#thumb-setting-main" aria-controls="thumb-setting-main" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_SLIDE_INSPECTOR_MAIN_DESC', true);?>"><span class="fa fa-info"></span></a></li>
			<li role="presentation"><a data-target="#thumb-setting-image" aria-controls="thumb-setting-image" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_SLIDE_INSPECTOR_IMAGE_DESC', true);?>"><span class="fa fa-image"></span></a></li>
			<li role="presentation"><a data-target="#thumb-setting-video" aria-controls="thumb-setting-video" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_SLIDE_INSPECTOR_VIDEO_DESC', true);?>"><span class="fa fa-video-camera"></span></a></li>
<!--			<li role="presentation"><a data-target="#thumb-setting-playback" aria-controls="thumb-setting-playback" role="tab" data-toggle="tab" class="es-tooltip" title="--><?php //echo JText::_('JSN_EASYSLIDER_SLIDER_SLIDE_INSPECTOR_PLAYBACK_DESC', true);?><!--"><span class="fa fa-play-circle"></span></a></li>-->
			<li role="presentation" id="slide-setting-transition-tab"><a data-target="#thumb-setting-transition" aria-controls="thumb-setting-transition" role="tab" data-toggle="tab" class="es-tooltip" title="<?php echo JText::_('JSN_EASYSLIDER_SLIDER_SLIDE_INSPECTOR_TRANSITION_DESC', true);?>"><span class="fa fa-exchange"></span></a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">

			<div role="tabpanel" class="tab-pane form-horizontal active" id="thumb-setting-main">
                <div class="row">
                    <label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_HIDE_SLIDE');?></label>
                    <div class="col-xs-8">
                        <input type="checkbox" data-bind="hide" >
                    </div>
                </div> <!-- row end -->
                <hr>
                <div class="row">
					<label class="col-xs-4">ID</label>
					<div class="col-xs-8">
						<input type="text" class="form-control input-xs" data-bind="attr.id" placeholder="">
					</div>
				</div> <!-- row end -->
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_CLASS');?></label>
					<div class="col-xs-8">
						<input type="text" class="form-control input-xs" data-bind="attr.class" placeholder="">
					</div>
				</div> <!-- row end -->
				<hr>
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_TITLE');?></label>
					<div class="col-xs-8">
						<input type="text" class="form-control input-xs" data-bind="name" placeholder="">
					</div>
				</div> <!-- row end -->
				<hr>
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_DURATION');?></label>
					<div class="col-xs-8">
						<div class="input-group inout-group-xs">
							<input type="number" class="form-control input-xs" data-bind="duration" min="1000" step="100">
							<span class="input-group-addon">ms</span>
						</div>
					</div>
				</div> <!-- End row -->
			</div>
			<div role="tabpanel" class="tab-pane form-horizontal" id="thumb-setting-image">
				<div class="row">
					<label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_COLOR');?></label>
					<div class="col-xs-6">
						<input type="text" class="form-control input-xs input-color" data-bind="background.color" placeholder="#000000">
					</div>
				</div> <!-- row end -->

				<hr>

				<div class="row">
					<label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_IMAGE');?></label>
					<div class="col-xs-9">
						<div class="input-group">
							<input type="text" class="form-control input-xs" data-bind="background.image.src">
							<span class="input-group-btn">
								<a class="btn btn-default btn-xs edit-slide-bg-btn">
									<span class="fa fa-folder-open-o"></span>
								</a>
							</span>
						</div>
					</div>
				</div> <!-- row end -->
                <div class="row">
                    <label class="col-xs-3">Pos</label>
                    <div class="col-xs-6" >
                        <input type="text" class="form-control input-xs" data-bind="background.position">
                    </div>
                </div> <!-- row end -->

				<div class="row">
					<label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_SIZE');?></label>
					<div class="col-xs-6">
						<select class="form-control input-xs" data-bind="background.size">
							<option value="auto">auto</option>
							<option>cover</option>
							<option>contain</option>
						</select>
					</div>
				</div> <!-- row end -->
                <hr>
                <div class="row">
                    <label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_KEN_BURNS');?></label>
                    <div class="col-xs-6">
                        <input type="checkbox" data-bind="background.kenburn.enable" >
                    </div>
                </div> <!-- row end -->
                <div class="row" data-bind="visible:background.kenburn.enable">
                    <label class="col-xs-3">Pos</label>
                    <div class="col-xs-6" >
                        <input type="text" class="form-control input-xs" data-bind="background.kenburn.position">
                    </div>
                </div> <!-- row end -->

                <div class="row" data-bind="visible:background.kenburn.enable">
                    <label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_SIZE');?></label>
                    <div class="col-xs-6">
                        <input type="text" class="form-control input-xs" data-bind="background.kenburn.size">
                    </div>
                </div> <!-- row end -->
                <div class="row" data-bind="visible:background.kenburn.enable">
                    <label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_DURATION');?></label>
                    <div class="col-xs-6">
                        <input type="number" class="form-control input-xs" data-bind="background.kenburn.duration" placeholder="3">
                    </div>
                </div> <!-- row end -->

                <div class="row" data-bind="visible:background.kenburn.enable">
                    <label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_EASING');?></label>
                    <div class="col-xs-6">
                        <select class="form-control input-xs easing-select" data-bind="background.kenburn.easing">
							<option value="linear">linear</option>
							<option value="ease">ease</option>
							<option value="ease-in">ease-in</option>
							<option value="ease-out">ease-out</option>
							<option value="ease-in-out">ease-in-out</option>
							<option value="step-start">step-start</option>
							<option value="step-end">step-end</option>
							<option value="initial">initial</option>
							<option value="inherit">inherit</option>
<!--                           step-start|step-end|steps(int,start|end)|cubic-bezier(n,n,n,n)|initial|inherit -->
                        </select>
                    </div>
                </div> <!-- row end -->
				<hr>
				<div class="row" >
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_PARALLAX');?></label>
					<div class="col-xs-6">
						<input type="checkbox" data-bind="background.parallax.enable" >
					</div>
				</div> <!-- row end -->
				<div class="row" data-bind="visible:background.parallax.enable">
					<label class="col-xs-3">Depth</label>
					<div class="col-xs-6">
						<input type="number" class="form-control input-xs" data-bind="background.parallax.depth" step="10">
					</div>
				</div> <!-- row end -->
				<div class="row" data-bind="visible:background.parallax.enable">

                    <label class="col-xs-3"><?php echo JText::_('JSN_EASYSLIDER_TYPE');?></label>
                    <div class="col-xs-6">
                        <select class="form-control input-xs" data-bind="background.parallax.type">
                            <option value="scroll"><?php echo JText::_('JSN_EASYSLIDER_PARALLAX_SCROLL');?></option>
                            <option value="mouse-move"><?php echo JText::_('JSN_EASYSLIDER_PARALLAX_MOUSE_MOVE');?></option>
                        </select>
                    </div>
				</div> <!-- row end -->
			</div>
			<div role="tabpanel" class="tab-pane" id="thumb-setting-video">
				<div class="row">
                    <div class="col-xs-2">
                        <input type="radio" class="form-control input-xs input-normal" name="slide-video-select" data-bind="background.video.selector" value="provider">
                    </div>
                    <label class="col-xs-10"><?php echo JText::_('JSN_EASYSLIDER_VIDEO_PROVIDER');?></label>

				</div>
				<div class="row">
                    <div class="col-xs-2">
                        <input type="radio" class="form-control input-xs input-normal" name="slide-video-select" data-bind="background.video.selector" value="local">
                    </div>
                    <label class="col-xs-10"><?php echo JText::_('JSN_EASYSLIDER_VIDEO_LOCAL');?></label>
				</div>

				<div class="form-group">
					<label><?php echo JText::_('JSN_EASYSLIDER_VIDEO_SOURCES');?></label>
				</div>
				<div class="form-group" data-bind="visible:background.video.isProvider">
					<div class="input-group input-group-xs">
						<span class="input-group-addon">
							<span class="fa fa-youtube-play"></span>
						</span>
						<input type="text" class="form-control input-xs" data-bind="background.video.videoURL" placeholder="Youtube URL or vimeo URL">
					</div>
				</div> <!-- End row -->

				<div class="form-group" data-bind="visible:background.video.isLocal">
					<div class="input-group input-group-xs">
						<span class="input-group-addon">
							<span class="fa fa-file-video-o"></span>
						</span>
						<input type="text" class="form-control input-xs" data-bind="background.video.mp4" placeholder="Local MP4 URL" />
						<input type="text" class="form-control input-xs" data-bind="background.video.ogg" placeholder="Local OGG URL" />
						<input type="text" class="form-control input-xs" data-bind="background.video.webm" placeholder="Local WebM URL" />
					</div>
				</div> <!-- End row -->
				<div class="row">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_MUTE');?></label>
					<div class="col-xs-5">
						<input type="checkbox" data-bind="background.video.mute" />
					</div>
				</div> <!-- End row -->
				<div class="row">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_VOLUMN');?></label>
					<div class="col-xs-7">
						<div class="input-group input-group-xs">
							<span class="input-group-addon"><i class="fa fa-minus"></i></span>
							<input type="range" class="form-control" data-bind="background.video.volume" min="0" max="100" step="10" />
							<span class="input-group-addon"><i class="fa fa-plus"></i></span>
						</div>
					</div>
				</div> <!-- End row -->
			</div>
			<div role="tabpanel" class="tab-pane form-horizontal" id="thumb-setting-playback">
				<div class="row">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_AUTOPLAY');?></label>
					<div class="col-xs-5">
						<input type="checkbox" data-bind="background.video.autoplay" />
					</div>
				</div> <!-- End row -->
				<div class="row">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_CONTROLS');?></label>
					<div class="col-xs-5">
						<input type="checkbox" data-bind="background.video.controls" />
					</div>
				</div> <!-- End row -->
				<div class="row">
					<label class="col-xs-5"><?php echo JText::_('JSN_EASYSLIDER_REPEAT');?></label>
					<div class="col-xs-5">
						<input type="checkbox" data-bind="background.video.loop" />
					</div>
				</div> <!-- End row -->
				<br>
				<br>

			</div>
			<div role="tabpanel" class="tab-pane form-horizontal" id="thumb-setting-transition">
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_EFFECT');?></label>
					<div class="col-xs-8">
						<select class="form-control input-xs" data-bind="transition.effect">
							<option value="fade">Fade</option>
							<option value="slide">Push</option>
							<option value="parallax">Parallax</option>
							<option value="slide-over">Move Over</option>
							<option value="slide-out">Move Out</option>
							<option value="cube">Cube</option>
							<option value="switch">Switch</option>
<!--							<option value="blur">Blur</option>-->
						</select>
					</div>
				</div> <!-- End row -->
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_FLOW');?></label>
					<div class="col-xs-8">
						<select class="form-control input-xs" data-bind="transition.flow">
							<option value="x">Horizontal</option>
							<option value="y">Vertical</option>
						</select>
					</div>
				</div> <!-- End row -->
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_DURATION');?></label>
					<div class="col-xs-8">
						<div class="input-group inout-group-xs">
							<input type="number" class="form-control input-xs" data-bind="transition.duration" />
							<span class="input-group-addon">ms</span>
						</div>
					</div>
				</div> <!-- End row -->
				<div class="row">
					<label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_DELAY');?></label>
					<div class="col-xs-8">
						<div class="input-group inout-group-xs">
							<input type="number" class="form-control input-xs" data-bind="duration" min="1000" step="100">
							<span class="input-group-addon">ms</span>
						</div>
					</div>
				</div> <!-- End row -->
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<div class="btn-group btn-group-xs flex flex-layout">
			<a class="btn btn-default duplicate-slide-btn flexible">
				<span class="fa fa-clone"></span>
				<?php echo JText::_('JSN_EASYSLIDER_DUPLICATE');?>
			</a>
			<a class="btn btn-danger remove-slide-btn flexible">
				<span class="fa fa-trash-o"></span>
				<?php echo JText::_('JSN_EASYSLIDER_DELETE');?>
			</a>
		</div>
	</div>
</div>