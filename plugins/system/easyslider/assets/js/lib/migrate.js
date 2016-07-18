void function( exports, $ ) {

	exports.ES_DataMigration = function migrate( newData, oldData, map, replace, rootData ) {
		if (!rootData)
			rootData = oldData;

		_(map).each(function( toAttr, fromAttr ) {
			//log(fromAttr,toAttr)

			if (!hasAttribute(oldData,fromAttr)) {
				//setValue(newData, fromAttr, toAttr);
				return;
			}
			if (_.isUndefined(toAttr))
				return;
			if (_.isFunction(toAttr)) {
				return toAttr( getValue(oldData, fromAttr), newData, oldData, rootData )
			}
			if (_.isObject(toAttr)) {
				var itemMap = toAttr.map;
				var toItemAttr = toAttr.attr;
				var newItems = setValue(newData, toItemAttr, []);
				_.each(getValue(oldData, fromAttr), function ( itemData, index ) {
					migrate( newItems[index] = {}, itemData, itemMap, {}, rootData  );
				})
				return;
			}
			setValue(newData, toAttr, getValue(oldData, fromAttr));
		})
		_(oldData).each(function ( value, key ) {
			!map.hasOwnProperty(key) && ( newData[key] = value )
		});
		_(newData).extend(replace);
		return newData;
	}

	function getContainer( obj, key ) {
		//log(key, obj)
		return _.reduce(key.split('.'), function(memo, key, index, keys) {
			if (index == keys.length - 1)
				return memo;

			if (!memo.hasOwnProperty(key))
				memo[key] = {};

			return memo[key];
		}, obj);
	}
	function getContainerKey( obj, key ) {
		//log(key, obj)
		return _.reduce(key.split('.'), function(memo, key, index, keys) {
			if (index == keys.length - 1)
				return key;

			if (!memo.hasOwnProperty(key))
				memo[key] = {};

			return memo[key];
		}, obj);
	}
	function getValue( obj, key ) {
		//log(key, obj)
		return _.reduce(key.split('.'), function(memo, key, index, keys ) {
			if (!memo.hasOwnProperty(key) && index < keys.length-1)
				memo[key] = {};

			return memo[key];
		}, obj);
	}
	function setValue( obj, key, value ) {
		var container = getContainer(obj, key);
		var containerKey = getContainerKey(obj, key);
		container[containerKey] = value;
		return value;
	}
	function hasAttribute( obj, key ) {
		var container = getContainer(obj, key);
		var containerKey = getContainerKey(obj, key);
		return container.hasOwnProperty(containerKey);
	}

}(this,JSNES_jQuery);

data = {
	"version": 1,
	"fullWidth": false,
	"fullHeight": false,
	"width": null,
	"canvasWidth": "800px",
	"minWidth": null,
	"maxWidth": null,
	"height": null,
	"canvasHeight": "400px",
	"minHeight": null,
	"maxHeight": null,
	"tabletMode": false,
	"tabletUnder": "1024px",
	"tabletWidth": null,
	"tabletHeight": null,
	"mobileMode": false,
	"mobileUnder": "768px",
	"mobileWidth": null,
	"mobileHeight": null,
	"responsiveEditMode": "default",
	"viewportOffsetX": 0,
	"viewportOffsetY": 0,
	"zoom": 1,
	"slides": [ {
		"active": true,
		"index": 0,
		"currentTime": 2500,
		"backgroundColor": "#FFF",
		"backgroundPosition": "50% 50%",
		"backgroundSize": "cover",
		"backgroundImage": { "type": "placeholder", "url": "" },
		"transition": {
			"type": 1,
			"effect": "fade",
			"delay": 5000,
			"duration": 1000,
			"timing": "ease",
			"rows": 1,
			"cols": 4,
			"delayRandom": false,
			"delayY": 100,
			"delayX": 100,
			"cubeDepth": "auto",
			"cubeAnimation": "scale-rotate",
			"cubeFace": "right",
			"cubeAxis": "x",
			"cubeRotate": -1
		},
		"items": [ {
			"type": "text",
			"selected": false,
			"build": {
				"outStart": 5000,
				"outEnd": 5000,
				"inEffect": "",
				"inStart": 0,
				"inEnd": 0,
				"inEasing": "linear",
				"inTransform": { "opacity": "0", "origin": {}, "translate": {}, "rotate": {}, "scale": {}, "skew": {} },
				"outEffect": "",
				"outEasing": "linear",
				"outTransform": { "opacity": "0", "origin": {}, "translate": {}, "rotate": {}, "scale": {}, "skew": {} }
			},
			"textType": "caption",
			"origin": "0.5,0.5",
			"content": "Double-click to edit",
			"style": {
				"top": "13.75%",
				"left": "19.38%",
				"width": "250px",
				"height": "50px",
				"border": "1%",
				"color": "#000",
				"fontFamily": "Open Sans",
				"fontSize": "24px",
				"fontWeight": "normal",
				"lineHeight": "1em",
				"textAlign": "center",
				"verticalAlign": "middle",
				"visibility": "visible",
				"boxShadow": "0 1px 2px rgba(0,0,0,0.5), 3px 4px 1px #0f0"
			},
			"index": 1,
			"tagName": "DIV",
			"show": true,
			"lock": false,
			"style_T": { "visibility": "visible" },
			"style_M": { "visibility": "visible" },
			"video": {
				"type": "placeholder",
				"url": "",
				"volume": 0.8,
				"autoplay": true,
				"loop": false,
				"controls": false
			},
			"image": { "type": "placeholder", "url": "" }
		}, {
			"type": "image",
			"selected": true,
			"build": {
				"outStart": 5000,
				"outEnd": 5000,
				"inEffect": "",
				"inStart": 0,
				"inEnd": 0,
				"inEasing": "linear",
				"inTransform": { "opacity": "0", "origin": {}, "translate": {}, "rotate": {}, "scale": {}, "skew": {} },
				"outEffect": "",
				"outEasing": "linear",
				"outTransform": { "opacity": "0", "origin": {}, "translate": {}, "rotate": {}, "scale": {}, "skew": {} }
			},
			"name": "Image Item",
			"origin": "0.5,0.5",
			"image": { "type": "placeholder", "url": "", "original": null },
			"style": {
				"top": "71.25%",
				"left": "19.38%",
				"width": "200px",
				"height": "150px",
				"visibility": "visible"
			},
			"index": 0,
			"tagName": "DIV",
			"content": "",
			"show": true,
			"lock": false,
			"style_T": { "visibility": "visible" },
			"style_M": { "visibility": "visible" },
			"video": {
				"type": "placeholder",
				"url": "",
				"volume": 0.8,
				"autoplay": true,
				"loop": false,
				"controls": false
			}
		} ]
	} ],
	"fonts": [ "Open Sans" ],
	"settings": {
		"fullWidth": false,
		"fullHeight": false,
		"showBtnNext": true,
		"nextBtnLabel": "",
		"showBtnPrev": true,
		"showProgress": true,
		"prevBtnLabel": "",
		"showPagination": true,
		"touchNavigation": true,
		"loopSlider": true,
		"startAt": 1
	}
}