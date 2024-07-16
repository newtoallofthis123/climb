/*addScopeJS(["Approach", "Mapper"], {});

Approach.Mapper = function (config = {}) {
	let $elf = this;

	$elf.config = {
		
		debug: true
	};

	$elf.managed = {
		
	};

	overwriteDefaults(config, $elf.config);

	$elf.init = () => {
		if ($elf.config.debug) console.groupCollapsed("Initializing Mapper Tool");

		// attach scroll event listener
		$(window).scroll(call.onScroll);

		if ($elf.config.debug) console.groupEnd();
	};


	$elf.call = {
		
	};

	let dispatch = {

	}

	$elf.init();
	return $elf;
};

// Use Approach Reflection to ask the Composition at any URL for part of its ComponentList
function ServeComposedRange(url, successFunction, mode = "publish_api") {
	// access_mode = publish_full | publish_api | publish_json | publish_embed | publish_silent
	let ReqData = {};
	url = url + "?test=123&access_mode=" + mode;

	let errorFunction = function (jqXHR, textStatus, errorThrown) {
		console.log("Something went wrong with the endless scroller ajax call");
		console.log(textStatus);
	}

	return $.ajax({
		url: url,
		type: "post",
		data: ReqData, //the json data
		dataType: "json",
		xhrFields: {
			withCredentials: true
		},
		crossDomain: true,
		success: successFunction,
		error: errorFunction
	});
}

export let Mapper = Approach.Mapper;
*/