var JSNISInternalFolder = 
{
	listAjaxCheckThumb: {},
	
	disableImageManager: function() {
		$('jsn-showlist-media-layout').style.display='none';
	},
	
	triggerAjaxCheckThumb: function(ajaxID)
	{

		$('jsn-checkthumb').style.display = 'block';
		if (JSNISInternalFolder.listAjaxCheckThumb[ajaxID]) {
			JSNISInternalFolder.listAjaxCheckThumb[ajaxID].send();
		}
	},
	
	ajaxCheckThumb: function(total, count , imageID)
	{
		JSNISInternalFolder.listAjaxCheckThumb[count] = new Request({
			url: 'index.php?option=com_imageshow&controller=images&task=checkThumb&image_id='+ imageID +'&total=' + total +'&count='+ count + '&rand=' + Math.random(),
			method: 'get',
			noCache: true,
			link: 'chain',
			onComplete: function(response)
			{
				var data = JSON.decode(response);
				
				JSNISInternalFolder.progressBar(count, total, data);

				if (count < total) {
					JSNISInternalFolder.triggerAjaxCheckThumb(count + 1);
				}
			},
		});
	},
	
	createCheckThumbBox: function(labelText)
	{
		var thumb = $('jsn-checkthumb');
		
		if (!thumb)
		{
			var checkThumbBox = document.createElement("div");
			checkThumbBox.id = "jsn-checkthumb";
			
			var label = document.createElement("p");
			label.id = "jsn-checkthumb-label";
			label.innerHTML = labelText;
			
			var checkThumbCount = document.createElement("div");
			checkThumbCount.id = "jsn-checkthumb-count";
			
			var progressBar = document.createElement("div");
			progressBar.id = "jsn-checkthumb-progress-bar";
			
			checkThumbCount.appendChild(progressBar);
			checkThumbBox.appendChild(label);
			checkThumbBox.appendChild(checkThumbCount);
			document.body.appendChild(checkThumbBox);
		}
	},
	
	progressBar: function (step, total, data)
	{
		
		JSNISInternalFolder.progressBarFX = new Fx.Tween('jsn-checkthumb-progress-bar', {
													duration:300, 
													unit: '%',
													complete: JSNISInternalFolder.hideProgressBar(data)
													});
		
		var oldWidth = (step - 1) *(100/total);
		var newWidth = step * (100/total);
		
		JSNISInternalFolder.progressBarFX.start('width', oldWidth, newWidth);
	},
	
	hideProgressBar: function(data)
	{
		if (data && data.checkThumb == true)
		{
			JSNISImageShow.checkThumbCallBack();
			$('jsn-checkthumb').style.display    = 'none';
			window.location.reload(true);
		}
	}
};