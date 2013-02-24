jsBackend.calendar = {
	init: function() {
		// variables
		var title = $('#title');
		jsBackend.calendar.controls.init();

		// do meta
		if(title.length > 0) title.doMeta();
	}
}

jsBackend.calendar.controls = {
	init: function() {
		var saveAsDraft = $('#saveAsDraft');

		saveAsDraft.click(function(e) {
			e.returnValue = false;
			e.preventDefault();

			$('input[name=status]').val('draft');
			$('form').submit();
		});
	}
}

$(jsBackend.calendar.init);