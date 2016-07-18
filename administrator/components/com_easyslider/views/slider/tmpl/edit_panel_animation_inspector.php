
<div class="panel panel-default es-panel" id="animation-inspector">

	<table class="table">
		<thead>
		<tr>
			<th>&nbsp;-</th>
			<th class="text-center"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_OFFSET');?></th>
			<th class="text-center"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_ROTATE');?></th>
			<th class="text-center"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_SCALE');?></th>
			<th class="text-center"><?php echo JText::_('JSN_EASYSLIDER_SLIDER_SKEW');?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<th>&nbsp;X</th>
			<td><input type="number" class="form-control input-xs" data-bind="transform.translate.x" tabindex="1"></td>
			<td><input type="number" class="form-control input-xs" data-bind="transform.rotate.x" tabindex="2"></td>
			<td><input type="number" class="form-control input-xs" data-bind="transform.scale.x" tabindex="3"></td>
			<td><input type="number" class="form-control input-xs" data-bind="transform.skew.x" tabindex="4"></td>
		</tr>
		<tr>
			<th>&nbsp;Y</th>
			<td><input type="number" class="form-control input-xs" data-bind="transform.translate.y" tabindex="1"></td>
			<td><input type="number" class="form-control input-xs" data-bind="transform.rotate.y" tabindex="2"></td>
			<td><input type="number" class="form-control input-xs" data-bind="transform.scale.y" tabindex="3"></td>
			<td><input type="number" class="form-control input-xs" data-bind="transform.skew.y" tabindex="4"></td>
		</tr>
		<tr>
			<th>&nbsp;Z</th>
			<td><input type="number" class="form-control input-xs" data-bind="transform.translate.z" tabindex="1"></td>
			<td><input type="number" class="form-control input-xs" data-bind="transform.rotate.z" tabindex="2"></td>
			<td colspan="2">
				<select class="form-control input-xs easing-select" data-bind="value:easing">
					<optgroup label="NONE">
						<option value="linear">Linear</option>
					</optgroup>
					<optgroup label="IN">
						<option value="easeInQuad">Ease.In.Quad</option>
						<option value="easeInCubic">Ease.In.Cubic</option>
						<option value="easeInQuart">Ease.In.Quart</option>
						<option value="easeInQuint">Ease.In.Quint</option>
						<option value="easeInExpo">Ease.In.Expo</option>
						<option value="easeInSine">Ease.In.Sine</option>
						<option value="easeInCirc">Ease.In.Circular</option>
						<option value="easeInElastic">Ease.In.Elastic</option>
						<option value="easeInBack">Ease.In.Back</option>
						<option value="easeInBounce">Ease.In.Bounce</option>
					</optgroup>
					<optgroup label="OUT">
						<option value="easeOutQuad">Ease.Out.Quad</option>
						<option value="easeOutCubic">Ease.Out.Cubic</option>
						<option value="easeOutQuart">Ease.Out.Quart</option>
						<option value="easeOutQuint">Ease.Out.Quint</option>
						<option value="easeOutExpo">Ease.Out.Expo</option>
						<option value="easeOutSine">Ease.Out.Sine</option>
						<option value="easeOutCirc">Ease.Out.Circular</option>
						<option value="easeOutElastic">Ease.Out.Elastic</option>
						<option value="easeOutBack">Ease.Out.Back</option>
						<option value="easeOutBounce">Ease.Out.Bounce</option>
					</optgroup>
					<optgroup label="IN.OUT">
						<option value="easeInOutQuad">Ease.In.Out.Quad</option>
						<option value="easeInOutCubic">Ease.In.Out.Cubic</option>
						<option value="easeInOutQuart">Ease.In.Out.Quart</option>
						<option value="easeInOutQuint">Ease.In.Out.Quint</option>
						<option value="easeInOutExpo">Ease.In.Out.Expo</option>
						<option value="easeInOutSine">Ease.In.Out.Sine</option>
						<option value="easeInOutCirc">Ease.In.Out.Circular</option>
						<option value="easeInOutElastic">Ease.In.Out.Elastic</option>
						<option value="easeInOutBack">Ease.In.Out.Back</option>
						<option value="easeInOutBounce">Ease.In.Out.Bounce</option>
					</optgroup>
				</select>
			</td>
		</tr>
		</tbody>
	</table>

    <div class="panel-body">
        <div class="row">
            <label class="col-xs-4"><?php echo JText::_('JSN_EASYSLIDER_OPACITY');?></label>
            <div class="col-xs-4">
                <input type="number" class="form-control input-xs" data-bind="transform.opacity" step="0.1" max="1" min="0">
            </div>
        </div>
    </div>

</div>