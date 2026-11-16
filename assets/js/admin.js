/* Media picker for theme fields (files, CV, portrait). */
(function ($) {
	$(document).on('click', '.jn-media', function (e) {
		e.preventDefault();
		var target = $(this).data('target');
		var $btn = $(this);
		var frame = wp.media({ title: 'Choose a file', multiple: false });
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			$('#' + target).val(a.id);
			$btn.siblings('.jn-media-name').text(a.filename || a.title);
		});
		frame.open();
	});
	$(document).on('click', '.jn-media-clear', function (e) {
		e.preventDefault();
		var target = $(this).data('target');
		$('#' + target).val('');
		$(this).siblings('.jn-media-name').text('No file selected');
	});
})(jQuery);
