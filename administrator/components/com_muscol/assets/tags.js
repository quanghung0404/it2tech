jQuery(document).ready(function (){ 
	
	//typeahead
	jQuery('#newtags').typeahead({
		source: function (query, process) {
			//alert(query) ;
			
			var url = "index.php?option=com_muscol&controller=tags&task=typeahead&searchword=" + query ;
			
			return jQuery.get(url, { query: query }, function (data) {
				obj = JSON.parse(data);
				//alert(obj);
				return process(obj);
			});
			
		},
		updater: function (theitem) {
			// implementation
			//alert(theitem) ;
			/*
			var start = theitem.indexOf("[");
			
			//alert(start);alert(end);
			var theid = theitem.substr(start+1);
			
			var end = theid.indexOf("]");
			
			theid = theid.substr(0, end);
			obtener_cliente(theid);
			*/

			tagApi.tagsManager("pushTag", theitem);
			
			return theitem;
		}
	});

});