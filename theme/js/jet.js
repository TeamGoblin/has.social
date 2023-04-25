$(document).ready(function() {
	$('#menu-click, #menu-close').on('click', function() {
		$("#cbp-spmenu-s1").toggleClass('cbp-spmenu-open');
		$('#menu-close').toggleClass('open');
	});

	$('#settings-click, #settings-close').on('click', function() {
		$("#cbp-spmenu-s2").toggleClass('cbp-spmenu-open');
		$('#settings-close').toggleClass('open');
	});

	$('.close').on('click', function(e) {
		$('#'+$(this).attr('data-dismiss')).fadeOut();
	});

	$('#btn').on('click', function(e) {
		console.log('clicked btn');
		$.ajax({
			url: "/api/user",
			type: 'GET',
			// Fetch the stored token from localStorage and set in the header
			headers: {"Authorization": 'Bearer ' + window.dataJET['jwt']},
			error : function(err) {
				console.log('Error!', err)
			},
			success: function(data) {
				console.log('Success!')
				console.log(data);
			}
		});
	});

	$('#btn2').on('click', function(e) {
		console.log('clicked btn2');
		$.ajax({
			url: "/api/user",
			type: 'GET',
			error : function(err) {
				console.log('Error!', err)
			},
			success: function(data) {
				console.log('Success!')
				console.log(data);
			}
		});
	})
});