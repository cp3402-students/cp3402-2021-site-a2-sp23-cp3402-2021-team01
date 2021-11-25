(function( $ ) {
	// Configurations	
	var availableTransitions = ["fade"];
	var defaultTransition = "none";
	var defaultDuration = 400;

	function applyIfsoBlocksChanger($this) {
		var toShowClassNames = $this.data("show") || "";
		var toHideClassNames = $this.data("hide") || "";
		var transition = $this.data("transition") || defaultTransition;
		var duration = $this.data("duration") || defaultDuration;

		var toShowClassNamesJoined = "";
		var toHideClassNamesJoined = "";

		if (toShowClassNames != "")
			toShowClassNamesJoined = (presendChar(toShowClassNames.split(" "))).join(",");
		
		if (toHideClassNames != "")
			toHideClassNamesJoined = (presendChar(toHideClassNames.split(" "))).join(",");

		transitClasses(toShowClassNamesJoined,
					   toHideClassNamesJoined,
					   transition,
					   duration);
	}

	function presendChar(arr, c) {
		var newArr = [];
		var i;

		for (i = 0; i < arr.length; i++ ) {
			var className = arr[i];
			newArr.push("." + className);
		}

		return newArr;		
	}

	function transitClasses(toShowClassNames,
							toHideClassNames,
							transitionType,
							duration) {
		switch (transitionType) {
			case "fade":
				$(toHideClassNames).fadeOut(duration, function() {
					$(toShowClassNames).fadeIn(duration);
				});
				break;
			default:
				$(toHideClassNames).hide();
				$(toShowClassNames).show();
				break;
		}
	}

	$(".ifso-changer").on("click", function() {
		applyIfsoBlocksChanger($(this));
	});

	$(".ifso-hover-changer").on("hover", function() {
		applyIfsoBlocksChanger($(this));
	});
})( jQuery );