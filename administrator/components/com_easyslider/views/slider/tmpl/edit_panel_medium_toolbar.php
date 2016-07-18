
<div class="panel panel-default es-panel" id="medium-toolbar">
	<div class="es-panel-arrow bottom"></div>
	<div class="panel-body form-inline">

		<div class="flex space-between hidden">
			<div class="form-group">
				<label>Tag</label><br>
				<select class="form-control input-xs tagname-select">
					<option>DIV</option>
					<option>A</option>
					<option>LI</option>
				</select>
			</div>
			<div class="form-group">
				<label>Class</label><br>
				<div class="input-group input-group-xs">
					<input type="text" class="form-control input-long classname-input" placeholder="Classes">
				</div>
			</div>
			<div class="form-group">
				<label>Link</label><br>
				<div class="input-group input-group-xs">
					<input type="text" class="form-control input-long href-input" placeholder="http://...">
				</div>
			</div>
		</div>
		<div class="flex space-between">
			<select class="form-control input-xs tagname-select">
				<option>DIV</option>
				<option>A</option>
				<option>LI</option>
			</select>
			<div class="input-group input-group-xs">
				<input type="text" class="form-control input-long classname-input" placeholder="Classes">
			</div>
			<div class="btn-group btn-group-xs">
				<a class="btn btn-default insert-fa-btn"><span class="fa fa-smile-o"></span></a>
			</div>
			<div class="btn-group btn-group-xs">
				<a class="btn btn-default invoke-btn bold-btn" data-tag="B"><span class="fa fa-bold"></span></a>
				<a class="btn btn-default invoke-btn italic-btn" data-tag="I"><span class="fa fa-italic"></span></a>
				<a class="btn btn-default invoke-btn underline-btn" data-tag="U"><span class="fa fa-underline"></span></a>
			</div>
			<div class="btn-group btn-group-xs">
				<a class="btn btn-default invoke-btn" data-tag="sup"><span class="fa fa-superscript"></span></a>
				<a class="btn btn-default invoke-btn" data-tag="sub"><span class="fa fa-subscript"></span></a>
			</div>
			<div class="btn-group btn-group-xs">
				<a class="btn btn-success done-btn">
					<!--					<span class="fa fa-check"></span>-->
					Done
				</a>
			</div>
		</div>

	</div>

</div>