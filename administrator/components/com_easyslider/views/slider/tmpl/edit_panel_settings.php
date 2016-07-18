
<div class="panel panel-default es-panel" id="settings-panel">
	<div class="panel-body">
		<!-- Nav tabs -->
		<ul class="nav nav-pills" role="tablist">
			<li role="presentation" class="active"><a data-target="#es-responsive" role="tab" data-toggle="tab">Responsive</a></li>
			<li role="presentation"><a data-target="#es-other" role="tab" data-toggle="tab">Other</a></li>
			<li role="presentation"><a data-target="#es-global"  role="tab" data-toggle="tab">Global</a></li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="es-responsive">
				<div class="es-tab-item">
					<span class="es-icon">
						<i class="fa fa-tv"></i>
					</span>
					<div class="input-group">
						<input type="number" value="1240" name="large-width" class="form-control input-xs input-normal" data-bind="layout.desktop_w" />
						<input type="number" value="868" name="large-height" class="form-control input-xs input-normal" data-bind="layout.desktop_h" />
					</div>
				</div>
				<div class="es-tab-item">
					<span class="es-icon">
						<i class="fa fa-laptop"></i>
					</span>
					<div class="input-group">
						<input type="number" value="1024" name="desktop-width" class="form-control input-xs input-normal" data-bind="layout.laptop_w" />
						<input type="number" value="768" name="desktop-height" class="form-control input-xs input-normal" data-bind="layout.laptop_h" />
						<input type="checkbox" data-bind="layout.laptop" />
					</div>
				</div>
				<div class="es-tab-item">
					<span class="es-icon">
						<i class="fa fa-tablet"></i>
					</span>
					<div class="input-group">
						<input type="number" value="960" name="tablet-width" class="form-control input-xs input-normal" data-bind="layout.tablet_w" />
						<input type="number" value="768" name="tablet-height" class="form-control input-xs input-normal" data-bind="layout.tablet_h" />
						<input type="checkbox" data-bind="layout.tablet" />
					</div>
				</div>
				<div class="es-tab-item">
					<span class="es-icon">
						<i class="fa fa-mobile"></i>
					</span>
					<div class="input-group">
						<input type="number" value="480" name="mobile-width" class="form-control input-xs input-normal" data-bind="layout.mobile_w" />
						<input type="number" value="320" name="mobile-height" class="form-control input-xs input-normal" data-bind="layout.mobile_h" />
						<input type="checkbox" data-bind="layout.mobile" />
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="es-other">

			</div>
			<div role="tabpanel" class="tab-pane" id="es-global">

			</div>
		</div>
	</div>
</div>

<!---->