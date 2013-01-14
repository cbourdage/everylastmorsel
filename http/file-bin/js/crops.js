
!function ($) {
	$(function() {

		$(document).on('click', 'a[href^="#crop-yields-"]', function(e) {
			e.preventDefault();
			$($(this).attr('href')).toggle();
		});

		$(document).on('click', 'table.data-table a[href="#addYieldModal"]', function(e) {
			e.preventDefault();
			$('#plot_crop_id').val($(this).attr('data-cropId'));
		});

		$(document).on('click', 'table.data-table a[href="#sellThese"]', function(e) {
			e.preventDefault();
			$('#purchasable_yield_id').val($(this).attr('data-yieldId'));
			$('#purchasable_quantity_unit').val($(this).attr('data-yieldUnit'));
			$('#yield-sell-these-form').find('.quantity-yield-unit').html($(this).attr('data-yieldUnit'));
		});

		/**
		 * Add yields form submit
		 */
		$('#add-yield-form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this),
				$modal = $('#addYieldModal'),
				$loader = $('<span class="loader green">Loading...</span>');

			$modal.find('.alert').slideUp('fast', function() {
				$(this).remove();
			});
			$modal.find('button').attr('disable', 'disable').after($loader);

			$.ajax({
				url : $form.attr('action'),
				data : $form.serialize(),
				type : 'post',
				dataType : 'json',
				complete: function(response) {
					$loader.remove();
					$modal.find('button').attr('disable', '');
				},
				success : function(response) {
					if (response.success) {
						Elm.success(response, $form, 'prepend');
						window.setTimeout(function(e) {
							$modal.modal('hide');
							$modal.find('.alert').remove();
							$.formReset($form);
						}, 2000);
					} else {
						Elm.error(response.message, $form, 'prepend');
					}
				},
				error : function() {
					Elm.error('There was an error with your request.', $form, 'prepend');
				}
			});
		});


		/**
		 * Set for sale form submit
		 */
		$('#yield-sell-these-form').on('submit', function(e) {
			e.preventDefault();
			var $form = $(this),
				$modal = $('#sellThese'),
				$loader = $('<span class="loader green">Loading...</span>');

			$modal.find('.alert').slideUp('fast', function() {
				$(this).remove();
			});
			$modal.find('button').attr('disable', 'disable').after($loader);

			$.ajax({
				url : $form.attr('action'),
				data : $form.serialize(),
				type : 'post',
				dataType : 'json',
				complete: function(response) {
					$loader.remove();
					$modal.find('button').attr('disable', '');
				},
				success : function(response) {
					if (response.success) {
						Elm.success(response, $form, 'prepend');
						window.setTimeout(function(e) {
							$modal.modal('hide');
							$modal.find('.alert').remove();
							$.formReset($form);
						}, 2000);
					} else {
						Elm.error(response.message, $form, 'prepend');
					}
				},
				error : function() {
					Elm.error('There was an error with your request.', $form, 'prepend');
				}
			})
		});


		/**
		 * Un-list yield purchasable item
		 */
		$(document).on('click', 'table.data-table a[href="#un-list"]', function(e) {
			e.preventDefault();
			var $anchor = $(this),
				$modal = $('#globalMessageModal');

			$('#action-loader').show();
			$.ajax({
				url : $anchor.attr('rel'),
				type : 'get',
				dataType : 'json',
				complete: function(response) {
					$('#action-loader').hide();
				},
				success : function(response) {
					if (response.success) {
						Elm.success(response);
						$modal.addClass('success-message').find('h3').html(response.message);
						$modal.modal('show');
						window.setTimeout(function(e) {
							$modal.modal('hide');
							$modal.removeClass('success-message').find('h3').html('');
						}, 4000);
					} else {
						alert(response.message);
					}
				},
				error : function() {
					alert('There was an error with your request.');
				}
			})
		});
	});
}(window.jQuery);